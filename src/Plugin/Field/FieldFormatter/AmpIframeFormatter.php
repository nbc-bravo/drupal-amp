<?php

/**
 * @file
 * Contains \Drupal\amp\Plugin\Field\FieldFormatter\AmpIframeFormatter.
 */

namespace Drupal\amp\Plugin\Field\FieldFormatter;

use Drupal\text\Plugin\Field\FieldFormatter\TextDefaultFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Lullabot\AMP\AMP;
use Drupal;

/**
 * Plugin implementation of the 'amp_iframe' formatter.
 *
 * @FieldFormatter(
 *   id = "amp_iframe",
 *   label = @Translation("AMP Iframe"),
 *   description = @Translation("Display amp-iframe content."),
 *   field_types = {
 *     "string",
 *     "text",
 *     "text_long",
 *     "text_with_summary",
 *   },
 * )
 */
class AmpIframeFormatter extends TextDefaultFormatter {
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $amp_service = Drupal::getContainer()->get('amp.utilities');
    $amp = $amp_service->getAMPConverter();
    $elements = parent::viewElements($items, $langcode);

    foreach ($elements as &$element) {
      $element['#type'] = 'amp_iframe';
      $amp->loadHtml($element['#text']);
      $element['#text'] = $amp->convertToAmpHtml();
      if (!empty($amp->getComponentJs())) {
        $element['#attached']['library'] = $amp_service->addComponentLibraries($amp->getComponentJs());
      }
    }
    $amp->clear();
    return $elements;
  }

}


