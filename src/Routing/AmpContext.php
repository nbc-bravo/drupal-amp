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
    // Get a list of content types that are AMP enabled.
    $enabled_types = \Drupal::config('amp.settings')->get('node_types');
    // Load the current node.
    $node = \Drupal\node\Entity\Node::load($this->routeMatch->getParameter('node'));
    if (!empty($node)) {
      $type = $node->getType();
      // Only show AMP routes for content that is AMP enabled.
      return empty($enabled_types[$type]) ? FALSE : TRUE;
    }
    return FALSE;
  }

}
