<?php

/**
 * @file
 * Contains \Drupal\amp\Controller\ampPage.
 */

namespace Drupal\amp\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\node\Controller\NodeViewController;
use Drupal\Core\Cache\Cache;

/**
 * Class ampPage.
 *
 * @package Drupal\amp\Controller
 */
class ampPage extends ControllerBase {

  /** @var EntityManagerInterface  */
  protected $entity_manager;

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /** @var ConfigFactoryInterface $configFactory */
  protected $configFactory;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityManagerInterface $entity_manager, RendererInterface $renderer, ConfigFactoryInterface $configFactoryInterface) {
    $this->entity_manager = $entity_manager;
    $this->renderer = $renderer;
    $this->configFactory = $configFactoryInterface;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager'),
      $container->get('renderer'),
      $container->get('config.factory')
    );
  }

  public function warningsOn()
  {
    // First check the config if library warnings are on
    $amp_config = $this->configFactory->get('amp.settings');
    if ($amp_config->get('amp_library_warnings_display')) {
      return true;
    }

    // Then check the URL if library warnings are enabled
    /** @var Request $request */
    $request = \Drupal::request();
    $user_wants_amp_library_warnings = $request->get('warnfix');
    if (isset($user_wants_amp_library_warnings)) {
      return true;
    }

    return false;
  }

  /**
   * Amp page display.
   *
   * @return render array.
   */
  public function amp($node) {

    // Copied from the revisionShow() method on the NodeController.
    // The node_view_controller also sets the canonical link to the primary node.
    $node = $this->entity_manager->getStorage('node')->load($node);
    $node_view_controller = new NodeViewController($this->entity_manager, $this->renderer);

    // Get a list of content types that are AMP enabled.
    $enabled_types = amp_get_enabled_types();
    $type = $node->getType();

    // Only use the AMP view mode for content that is AMP enabled.
    if ($enabled_types[$type] === $type) {
      $page = $node_view_controller->view($node, 'amp');
    }
    // Otherwise return the default view mode.
    else {
      $page = $node_view_controller->view($node, 'full');
    }

    // Otherwise adding a ?warnfix query parameter at the end of URL will have no effect
    $page['#cache']['contexts'] = Cache::mergeContexts($page['#cache']['contexts'], ['url.query_args:warnfix']);
    if ($this->warningsOn()) {
      $page['#cache']['keys'][] = 'amp-warnings-on';
    }
    else {
      $page['#cache']['keys'][] = 'amp-warnings-off';
    }

    // @todo is this required?
    unset($page['nodes'][$node->id()]['#cache']);
    return $page;
  }

}
