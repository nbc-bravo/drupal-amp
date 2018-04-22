<?php

namespace Drupal\amp\Routing;

use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Theme\ThemeManager;
use Symfony\Component\Routing\Route;

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
   * Construct a new amp context helper instance.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory.
   * @param \Drupal\Core\Theme\ThemeManager $themeManager
   *   The theme manager.
   */
  public function __construct(ConfigFactoryInterface $configFactory, ThemeManager $themeManager) {
    $this->configFactory = $configFactory;
    $this->themeManager = $themeManager;
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

    $current_theme = $this->themeManager->getActiveTheme($route)->getName();
    $amp_theme = $this->configFactory->get('amp.theme')->get('amptheme');
    if ($amp_theme == $current_theme) {
      return TRUE;
    }
    return FALSE;

  }
}
