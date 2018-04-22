<?php

namespace Drupal\amp\Theme;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\amp\EntityTypeInfo;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\Routing\Route;
use Drupal\Core\Theme\ThemeNegotiatorInterface;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Sets the active theme on amp pages.
 */
class AmpNegotiator extends ServiceProviderBase implements ThemeNegotiatorInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Information about AMP-enabled content types.
   *
   * @var \Drupal\amp\EntityTypeInfo
   */
  protected $entityTypeInfo;

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Creates a new AmpNegotiator instance.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\amp\EntityTypeInfo $entity_type_info
   *   Information about AMP-enabled content types.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityTypeInfo $entity_type_info, RouteMatchInterface $route_match) {
    $this->configFactory = $config_factory;
    $this->entityTypeInfo = $entity_type_info;
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    $is_amp_route = $this->isAmpRoute($route_match->getRouteObject());
    if ($is_amp_route) {
      // Disable big pipe on AMP pages.
      // @todo Rely on https://www.drupal.org/node/2729441 instead, when it is
      //   resolved.
      $route_match->getRouteObject()->setOption('_no_big_pipe', TRUE);
    }
    return $is_amp_route;
  }

  public function isAmpRoute(Route $route = NULL) {
    if (!$route) {
      $route = $this->routeMatch->getRouteObject();
      if (!$route) {
        return FALSE;
      }
    }

    // Check if the globally-defined AMP status has been changed to TRUE (it
    // is FALSE by default).
    if ($route->getOption('_amp_route')) {
      return TRUE;
    }

    // We only want to consider path with amp in the query string.
    if (!(isset($_GET['amp']))) {
      return FALSE;
    }

    // Load the current node.
    $node = $this->routeMatch->getParameter('node');
    // If we only got back the node ID, load the node.
    if (!is_object($node) && is_numeric($node)) {
      $node = Node::load($node);
    }
    // Check if we have a node. Will not be true on admin pages for example.
    if (is_object($node)) {
      $type = $node->getType();
      // Only show AMP routes for content that is AMP enabled.
      return $this->entityTypeInfo->isAmpEnabledType($type);
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route_match) {
    return $this->configFactory->get('amp.theme')->get('amptheme');
  }

}
