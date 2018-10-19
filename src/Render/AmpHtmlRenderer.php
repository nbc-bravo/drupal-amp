<?php

namespace Drupal\amp\Render;

use Drupal\Core\Render\MainContent\HtmlRenderer;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Render\RenderContext;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Render\HtmlResponse;

/**
 * Default main content renderer for HTML requests.
 *
 * For attachment handling of HTML responses:
 * @see template_preprocess_html()
 * @see \Drupal\Core\Render\MainContent\HtmlRenderer
 */
class AmpHtmlRenderer extends HtmlRenderer {

  /**
   * {@inheritdoc}
   *
   * Exact copy of Drupal\Core\Render\MainContent\HtmlRenderer:renderResponse(),
   * except this method calls runs renderRoot() instead of render() to force
   * placeholders to be replaced on the server because Big Pipe and other
   * placeholder replacement javascript won't be available on the client.
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

    // Render and replace placeholders. To replace placeholders, we use
    // RendererInterface::renderRoot() instead of RendererInterface::render().
    // @see \Drupal\Core\Render\HtmlResponseAttachmentsProcessor.
    $render_context = new RenderContext();
    $this->renderer->executeInRenderContext($render_context, function () use (&$html) {
      // RendererInterface::render() renders the $html render array and updates
      // it in place. We don't care about the return value (which is just
      // $html['#markup']), but about the resulting render array.
      // @todo Simplify this when https://www.drupal.org/node/2495001 lands.
      $this->renderer->renderRoot($html);
    });

    $content = $this->renderCache->getCacheableRenderArray($html);

    // Also associate the required cache contexts.
    // (Because we use ::render() above and not ::renderRoot(), we manually must
    // ensure the HTML response varies by the required cache contexts.)
    $content['#cache']['contexts'] = Cache::mergeContexts($content['#cache']['contexts'], $this->rendererConfig['required_cache_contexts']);

    // Also associate the "rendered" cache tag. This allows us to invalidate the
    // entire render cache, regardless of the cache bin.
    $content['#cache']['tags'][] = 'rendered';

    $response = new HtmlResponse($content, 200, [
      'Content-Type' => 'text/html; charset=UTF-8',
    ]);

    return $response;
  }

}
