<?php

/**
 * @file
 * Contains \Drupal\amp\Element\AmpProcessedText.
 */

namespace Drupal\amp\Element;

use Drupal\filter\Element\ProcessedText;
use Lullabot\AMP\AMP;
use Drupal\amp\Service\AMPService;

/**
 * Provides an amp-processed text render element.
 *
 * @RenderElement("amp_processed_text")
 */
class AmpProcessedText extends ProcessedText {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return array(
      '#text' => '',
      '#format' => NULL,
      '#filter_types_to_skip' => array(),
      '#langcode' => '',
      '#pre_render' => array(
        array($class, 'preRenderText'),
        array($class, 'preRenderAmpText'),
      ),
    );
  }

  /**
   * Pre-render callback: Processes the amp markup and attaches libraries.
   */
  public static function preRenderAmpText($element) {

    /** @var AMPService $amp_service */
    $amp_service = \Drupal::getContainer()->get('amp.utilities');
    /** @var AMP $amp */
    $amp = $amp_service->createAMPConverter();

    $amp->loadHtml($element['#markup']);
    $element['#markup'] = $amp->convertToAmpHtml();
    if (!empty($amp->getComponentJs())) {
      $element['#attached']['library'] = $amp_service->addComponentLibraries($amp->getComponentJs());
    }

    return $element;
  }
}
