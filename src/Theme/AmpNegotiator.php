<?php

/**
 * @file
 * Contains \Drupal\amp\Theme\AmpNegotiator.
 */

namespace Drupal\user\Theme;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\amp\Routing\AmpContext;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;

/**
 * Sets the active theme on amp pages.
 */
class AmpNegotiator implements ThemeNegotiatorInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $user;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * The route amp context to determine whether a route is an amp one.
   *
   * @var \Drupal\Core\Routing\AmpContext
   */
  protected $ampContext;

  /**
   * Creates a new AmpNegotiator instance.
   *
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The current user.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Routing\AmpContext $amp_context
   *   The route amp context to determine whether the route is an amp one.
   */
  public function __construct(AccountInterface $user, ConfigFactoryInterface $config_factory, EntityManagerInterface $entity_manager, AmpContext $amp_context) {
    $this->user = $user;
    $this->configFactory = $config_factory;
    $this->entityManager = $entity_manager;
    $this->ampContext = $amp_context;
  }

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    return $this->ampContext->isAmpRoute($route_match->getRouteObject());
  }

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route_match) {
    return $this->configFactory->get('amp.theme')->get('amptheme');
  }

}
