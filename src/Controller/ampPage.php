<?php

/**
 * @file
 * Contains \Drupal\amp\Controller\ampPage.
 */

namespace Drupal\amp\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityManager;
use Drupal\Core\Render\RendererInterface;
use Drupal\node\Controller\NodeViewController;

/**
 * Class ampPage.
 *
 * @package Drupal\amp\Controller
 */
class ampPage extends ControllerBase {

  /**
   * Drupal\Core\Entity\EntityManager definition.
   *
   * @var Drupal\Core\Entity\EntityManager
   */
  protected $entity_manager;

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityManager $entity_manager, RendererInterface $renderer) {
    $this->entity_manager = $entity_manager;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager'),
      $container->get('renderer')
    );
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
    $enabled_types = \Drupal::config('amp.settings')->get('node_types');
    $type = $node->getType();

    // Only use the AMP view mode for content that is AMP enabled.
    if ($enabled_types[$type] === $type) {
      $page = $node_view_controller->view($node, 'amp');
    }
    // Otherwise return the default view mode.
    else {
      $page = $node_view_controller->view($node, 'full');
    }
    unset($page['nodes'][$node->id()]['#cache']);
    return $page;
  }

}
