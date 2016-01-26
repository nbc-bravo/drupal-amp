<?php

/**
 * @file
 * Contains \Drupal\amp\Plugin\Field\FieldFormatter\AmpVideoFormatter.
 */

namespace Drupal\amp\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\file\Plugin\Field\FieldFormatter\GenericFileFormatter;

/**
 * Plugin implementation of the 'amp_video' formatter.
 *
 * @FieldFormatter(
 *   id = "amp_video",
 *   label = @Translation("AMP Video"),
 *   description = @Translation("Display an AMP video file."),
 *   field_types = {
 *     "file"
 *   }
 * )
 */
class AmpVideoFormatter extends GenericFileFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items);
    return $elements;
  }
}
