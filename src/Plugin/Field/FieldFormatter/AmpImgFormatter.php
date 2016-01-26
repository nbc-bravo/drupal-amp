<?php

/**
 * @file
 * Contains \Drupal\amp\Plugin\Field\FieldFormatter\AmpImgFormatter.
 */

namespace Drupal\amp\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatter;

/**
 * Plugin implementation of the 'amp_img' formatter.
 *
 * @FieldFormatter(
 *   id = "amp_img",
 *   label = @Translation("AMP Image"),
 *   description = @Translation("Display an AMP Image file."),
 *   field_types = {
 *     "image"
 *   }
 * )
 */
class AmpImgFormatter extends ImageFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items);
    return $elements;
  }
}
