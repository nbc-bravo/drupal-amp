<?php

namespace Drupal\amp\Render;

use Drupal\Core\Render\MainContent\HtmlRenderer;
use Drupal\Core\Controller\TitleResolverInterface;
use Drupal\Component\Plugin\PluginManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Render\RenderCacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Render\RenderContext;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Render\HtmlResponse;
use Drupal\amp\Service\AMPService;
use Lullabot\AMP\Validate\Scope;

/**
 * Default main content renderer for AMPHTML requests.
 *
 * @see template_preprocess_html()
 * @see \Drupal\Core\Render\MainContent\HtmlRenderer
 */
class AmpHtmlRenderer extends HtmlRenderer {

  /**
   * @var \Drupal\amp\Service\AMPService
   */
  protected $ampService;

  /**
   * Constructs a new HtmlRenderer.
   *
   * @param \Drupal\Core\Controller\TitleResolverInterface $title_resolver
   *   The title resolver.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $display_variant_manager
   *   The display variant manager.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Drupal\Core\Render\RenderCacheInterface $render_cache
   *   The render cache service.
   * @param array $renderer_config
   *   The renderer configuration array.
   * @param \Drupal\amp\Service\AMPService $amp_service
   *   The AMP service.
   */
  public function __construct(TitleResolverInterface $title_resolver, PluginManagerInterface $display_variant_manager, EventDispatcherInterface $event_dispatcher, ModuleHandlerInterface $module_handler, RendererInterface $renderer, RenderCacheInterface $render_cache, array $renderer_config, AMPService $amp_service) {
    $this->titleResolver = $title_resolver;
    $this->displayVariantManager = $display_variant_manager;
    $this->eventDispatcher = $event_dispatcher;
    $this->moduleHandler = $module_handler;
    $this->renderer = $renderer;
    $this->renderCache = $render_cache;
    $this->rendererConfig = $renderer_config;
    $this->ampService = $amp_service;
  }

  /**
   * {@inheritdoc}
   *
   * Copy of Drupal\Core\Render\MainContent\HtmlRenderer:renderResponse()
   * with two important differences:
   *
   * - the page is run through renderRoot() instead of render() to force
   *   placeholders to be replaced on the server, because Big Pipe and other
   *   placeholder replacement javascript won't be available on the client.
   *
   * - the final page markup may be also be run through the AMP converter,
   *   depending on configuration in the AMP Replace module.
   *
   * @TODO Need to watch for changes to parent method and mirror them here.
   */
  public function renderResponse(array $main_content, Request $request, RouteMatchInterface $route_match) {
    list($page, $title) = $this->prepare($main_content, $request, $route_match);

    if (!isset($page['#type']) || $page['#type'] !== 'page') {
      throw new \LogicException('Must be #type page');
    }

    $page['#title'] = $title;

    // Now render the rendered page.html.twig template inside the html.html.twig
    // template, and use the bubbled #attached metadata from $page to ensure we
    // load all attached assets.
    $html = [
      '#type' => 'html',
      'page' => $page,
    ];

    // The special page regions will appear directly in html.html.twig, not in
    // page.html.twig, hence add them here, just before rendering html.html.twig.
    $this->buildPageTopAndBottom($html);

    // Render and replace placeholders using RendererInterface::renderRoot()
    // instead of RendererInterface::render().
    // @see \Drupal\Core\Render\HtmlResponseAttachmentsProcessor.
    $render_context = new RenderContext();
    $this->renderer->executeInRenderContext($render_context, function () use (&$html) {
      // @todo Simplify this when https://www.drupal.org/node/2495001 lands.
      $this->renderer->renderRoot($html);
    });
    $content = $this->renderCache->getCacheableRenderArray($html);

    // See if the final page markup should be run through the AMP converter.
    if (!empty($this->ampService->ampConfig('process_full_html'))) {
      $markup = $content['#markup']->__toString();
      $options = ['scope' => Scope::HTML_SCOPE];
      $amp = $this->ampService->createAMPConverter();
      $amp->clear();
      $amp->loadHtml($markup, $options);
      $content['#markup'] = $amp->convertToAmpHtml();

      $this->ampService->devMessage('<pre>' . $amp->warningsHumanHtml() . '</pre>');
      $this->ampService->devMessage('<pre>' . $amp->getInputOutputHtmlDiff() . '</pre>');

    }

    // Also associate the "rendered" cache tag. This allows us to invalidate the
    // entire render cache, regardless of the cache bin.
    $content['#cache']['tags'][] = 'rendered';

    $response = new HtmlResponse($content, 200, [
      'Content-Type' => 'text/html; charset=UTF-8',
    ]);

    return $response;
  }

}
