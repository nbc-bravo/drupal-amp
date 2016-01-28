<?php

/**
 * @file
 * Contains \Drupal\amp\Controller\AMPTestLibrary.
 */

namespace Drupal\amp\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\amp\AMPService;

/**
 * Class AMPTestLibrary.
 *
 * @package Drupal\amp\Controller
 */
class AMPTestLibrary extends ControllerBase {

  /**
   * Drupal\amp\AMPService definition.
   *
   * @var Drupal\amp\AMPService
   */
  protected $amp;
  /**
   * {@inheritdoc}
   */
  public function __construct(AMPService $amp_utilities) {
    $this->amp = $amp_utilities->getAMP();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('amp.utilities')
    );
  }

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function hello() {
    return [
        '#type' => 'markup',
        '#markup' => $this->amp->convertToAMP('<p>Hello from the AMP Library</p>')
    ];
  }

}
