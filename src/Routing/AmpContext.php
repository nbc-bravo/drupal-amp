<?php

namespace Drupal\amp\Routing;

use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Theme\ThemeManager;
use Symfony\Component\Routing\Route;
use Drupal\amp\EntityTypeInfo;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a helper class to determine whether the route is an amp one.
 */
class AmpContext extends ServiceProviderBase {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Theme manager.
   *
   * @var \Drupal\Core\Theme\ThemeManager
   */
  protected $themeManager;

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
   * Construct a new amp context helper instance.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Core\Theme\ThemeManager $themeManager
   *   The theme manager.
   * @param \Drupal\amp\EntityTypeInfo $entity_type_info
   *   Information about AMP-enabled content types.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   */
  public function __construct(ConfigFactoryInterface $configFactory, ThemeManager $themeManager, EntityTypeInfo $entityTypeInfo, RouteMatchInterface $routeMatch) {
    $this->configFactory = $configFactory;
    $this->themeManager = $themeManager;
    $this->entityTypeInfo = $entityTypeInfo;
    $this->routeMatch = $routeMatch;
 }

  /**
   * Determines whether the active route is an AMP route.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param mixed $entity
   *   The entity to assess, if any.
   * @param boolean $checkTheme
   *   Whether or not to check the active theme as a part of the test.
   *
   * @return bool
   *   Returns TRUE if the route is an AMP route, otherwise FALSE.
   */
  public function isAmpRoute(RouteMatchInterface $routeMatch = NULL, $entity = NULL, $checkTheme = TRUE) {
    if (!$routeMatch) {
      $routeMatch = $this->routeMatch;
    }

    // Some routes cannot be AMP.
    if ($route_is_not_amp = $this->routeIsNotAmp($routeMatch)) {
      return FALSE;
    }

    // If we have an entity, we can test it.
    $entity_is_amp = $this->entityIsAmp($entity);
    $route_entity_is_amp = $this->routeEntityIsAmp($routeMatch);
    if ($entity_is_amp || $route_entity_is_amp) {
      return TRUE;
    }
    // Otherwise, check the active theme.
    if ($checkTheme) {
      return $this->routeThemeisAmp($routeMatch);
    }

    return FALSE;

  }

  /**
   * See if this route uses the AMP theme.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   *
   * @return boolean
   */
  public function routeThemeisAmp(RouteMatchInterface $routeMatch) {
    $current_theme = $this->themeManager->getActiveTheme($routeMatch)->getName();
    $amp_theme = $this->configFactory->get('amp.theme')->get('amptheme');
    if ($amp_theme == $current_theme) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Not an AMP route?
   *
   * Check off things that indicate this can't be an AMP route.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   *
   * @return boolean
   */
  public function routeIsNotAmp(RouteMatchInterface $routeMatch) {
    $route = $routeMatch->getRouteObject();
    if (!$route) {
       return TRUE;
    }
    // Check if the globally-defined AMP status has been changed to TRUE (it
    // is FALSE by default).
    if ($route->getOption('_amp_route')) {
      return FALSE;
    }
    // We only want to consider path with amp in the query string, unless
    // all pages are AMP.
    $everywhere = $this->configFactory->get('amp.settings')->get('amp_everywhere');
    if (!$everywhere && !isset($_GET['amp'])) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * See if this route has an AMP entity.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   *
   * @return boolean
   */
  public function routeEntityIsAmp(RouteMatchInterface $routeMatch) {
    if ($node = $routeMatch->getParameter('node')) {
      return $this->entityIsAmp($node);
    }
    return FALSE;
  }

  /**
   * See if this entity is AMP.
   *
   * @param mixed $entity
   *   An entity
   *
   * @return boolean
   */
  public function entityIsAmp($entity) {
    if ($entity instanceof \Drupal\node\NodeInterface) {
      $type = $entity->getType();
      return $this->entityTypeInfo->isAmpEnabledType($type);
    }
    return FALSE;
  }

}
