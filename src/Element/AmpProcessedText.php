<?php

/**
 * @file
 * Contains \Drupal\amp\Element\AmpProcessedText.
 */

namespace Drupal\amp\Element;

use Drupal\filter\Element\ProcessedText;
use Lullabot\AMP\AMP;

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

    /** @var Drupal\amp\AMPService $amp_service */
    $amp_service = \Drupal::getContainer()->get('amp.utilities');
    $warning_title = '<strong>(For debugging purposes only. These warnings are for the body text of the node and will not ' .
      'appear in production version of module. Developers will still be able to see this in a smart way, yet not implemented)</strong>';
    /** @var AMP $amp */
    $amp = $amp_service->getAMPConverter();

    $amp->loadHtml($element['#markup']);
    $element['#markup'] = $amp->convertToAmpHtml() . '<div class="warnings">'. $warning_title . '<strong></strong>' . $amp->warningsHuman() . '</div>';
    if (!empty($amp->getComponentJs())) {
      $element['#attached']['library'] = $amp_service->addComponentLibraries($amp->getComponentJs());
    }

    $amp->clear();

    return $element;
  }
}
