<?php

/**
 * @file
 * Contains \Drupal\amp\Controller\AMPTestLibrary.
 */

namespace Drupal\amp\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\amp\Service\AMPService;

/**
 * Class AMPTestLibrary.
 *
 * @package Drupal\amp\Controller
 */
class AMPTestLibrary extends ControllerBase {

  /**
   * Drupal\amp\AMPService definition.
   *
   * @var AMPService
   */
  protected $amp;
  /**
   * {@inheritdoc}
   */
  public function __construct(AMPService $amp_utilities) {
    $this->amp = $amp_utilities->getAMPConverter();
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
   */
  public function hello() {
    $html =
      '<p><a href="javascript:run();">Run</a></p>' . PHP_EOL .
      '<p><a style="margin: 2px;" href="http://www.cnn.com" target="_parent">CNN</a></p>' . PHL_EOL .
      '<p><a href="http://www.bbcnews.com" target="_blank">BBC</a></p>' . PHP_EOL .
      '<p><INPUT type="submit" value="submit"></p>' . PHP_EOL .
      '<p>This is a <!-- test comment --> <!-- [if IE9] --> sample <div onmouseover="hello();">sample</div> paragraph</p>';

    $this->amp->loadHtml($html);
    $this->amp->convertToAmpHtml();
    $diff = $this->amp->getInputOutputHtmlDiff();
    return [
        '#type' => 'markup',
        '#markup' => "<pre>$diff</pre>" . $this->amp->warningsHuman()
    ];
  }

}
