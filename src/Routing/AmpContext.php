<?php

/**
 * @file
 * Contains \Drupal\amp\Routing\AmpContext.
 */

namespace Drupal\amp\Routing;

use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\Routing\Route;

/**
 * Provides a helper class to determine whether the route is an amp one.
 */
class AmpContext {

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Construct a new amp context helper instance.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   */
  public function __construct(RouteMatchInterface $route_match) {
    $this->routeMatch = $route_match;
  }

  /**
   * Determines whether the active route is an amp one.
   *
   * @param \Symfony\Component\Routing\Route $route
   *   (optional) The route to determine whether it is an amp one. Per default
   *   this falls back to the route object on the active request.
   *
   * @return bool
   *   Returns TRUE if the route is an amp one, otherwise FALSE.
   */
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

    // We only want to consider URLs that end with 'amp'.
    $current_path = \Drupal::service('path.current')->getPath();
    if (substr($current_path, -3) != 'amp') {
      return FALSE;
    }

    // Get a list of content types that are AMP enabled.
    $enabled_types = \Drupal::config('amp.settings')->get('node_types');
    // Load the current node.
    $node = $this->routeMatch->getParameter('node');
    // If we only got back the node ID, load the node.
    if (!is_object($node)) {
      $node = \Drupal\node\Entity\Node::load($node);
    }
    $type = $node->getType();
    // Only show AMP routes for content that is AMP enabled.
    if ($enabled_types[$type] === $type) {
      return TRUE;
    }
    return FALSE;
  }

}
