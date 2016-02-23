<?php

/**
 * @file
 * Contains Drupal\amp\Plugin\Field\FieldFormatter\AmpTextTrimmedFormatter
 */

namespace Drupal\amp\Plugin\Field\FieldFormatter;

use Drupal\text\Plugin\Field\FieldFormatter\TextTrimmedFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Lullabot\AMP\AMP;
use Drupal;

/**
 * Plugin implementation of the 'amp_text_trimmed' formatter.
 *
 * @FieldFormatter(
 *   id = "amp_text_trimmed",
 *   label = @Translation("AMP Trimmed Text"),
 *   description = @Translation("Display AMP Trimmed text."),
 *   field_types = {
 *     "string",
 *     "text",
 *     "text_long",
 *     "text_with_summary"
 *   }
 * )
 */
class AmpTextTrimmedFormatter extends TextTrimmedFormatter {
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);

    /** @var Drupal\amp\AMPService $amp_service */
    $amp_service = Drupal::getContainer()->get('amp.utilities');
    $warning_title = '<strong>(For debugging purposes only. These warnings are for the body text of the node and will not ' .
        'appear in production version of module. Developers will still be able to see this in a smart way, yet not implemented)</strong>';
    /** @var AMP $amp */
    $amp = $amp_service->getAMPConverter();

    foreach ($elements as &$element) {
      $amp->loadHtml($element['#text']);
      $element['#text'] = $amp->convertToAmpHtml() . '<div class="warnings">'. $warning_title . '<strong></strong>' . $amp->warningsHuman() . '</div>';
      if (!empty($amp->getComponentJs())) {
        $element['#attached']['library'] = $amp_service->addComponentLibraries($amp->getComponentJs());
      }
    }

    $amp->clear();
    return $elements;
  }
}


