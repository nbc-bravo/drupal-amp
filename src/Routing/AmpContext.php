<?php

/**
 * @file
 * Contains \Drupal\amp\Routing\AmpContext.
 */

namespace Drupal\Core\Routing;

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
    return (bool) $route->getOption('_amp_route');
  }

}
