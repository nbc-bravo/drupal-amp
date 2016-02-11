<?php

/**
 * @file
 * Contains \Drupal\amp\Plugin\Field\FieldFormatter\AmpTextFormatter.
 */

namespace Drupal\amp\Plugin\Field\FieldFormatter;

use Drupal\text\Plugin\Field\FieldFormatter\TextDefaultFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Lullabot\AMP\AMP;
use Drupal;

/**
 * Plugin implementation of the 'amp_text' formatter.
 *
 * @FieldFormatter(
 *   id = "amp_text",
 *   label = @Translation("AMP Text"),
 *   description = @Translation("Display AMP text."),
 *   field_types = {
 *     "string",
 *     "text",
 *     "text_long",
 *     "text_with_summary",
 *   },
 * )
 */
class AmpTextFormatter extends TextDefaultFormatter {
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    /** @var Drupal\amp\AMPService $amp_service */
    $amp_service = Drupal::getContainer()->get('amp.utilities');
    $warning_title = '<strong>(For debugging purposes only. These warnings are for the body text of the node and will not ' .
        'appear in production version of module. Developers will still be able to see this in a smart way, yet not implemented)</strong>';
    /** @var AMP $amp */
    $amp = $amp_service->getAMPConverter();
    $elements = parent::viewElements($items);
    foreach ($elements as &$element) {
      $amp->loadHtml($element['#text']);
      $element['#text'] = $amp->convertToAmpHtml() . '<div class="warnings">'. $warning_title . '<strong></strong>' . $amp->warningsHuman() . '</div>';
    }

    $amp->clear();
    return $elements;
  }

}


