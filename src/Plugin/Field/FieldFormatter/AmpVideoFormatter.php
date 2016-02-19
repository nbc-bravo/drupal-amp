<?php

/**
 * @file
 * Contains \Drupal\amp\Plugin\Field\FieldFormatter\AmpVideoFormatter.
 */

namespace Drupal\amp\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
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
    $elements = parent::viewElements($items, $langcode);

    foreach ($elements as $delta => $element) {
      $elements[$delta]['#theme'] = 'amp_video';
      $elements[$delta]['#attributes']['height'] = $this->getSetting('height');
      $elements[$delta]['#attributes']['width'] = $this->getSetting('width');
    }

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'height' => 175,
      'width' => 350,
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);

    $element['height'] = array(
      '#type' => 'number',
      '#title' => t('Height'),
      '#size' => 10,
      '#default_value' => $this->getSetting('height'),
    );

    $element['width'] = array(
      '#type' => 'number',
      '#title' => t('Width'),
      '#size' => 10,
      '#default_value' => $this->getSetting('width'),
    );

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    $height_setting = $this->getSetting('height');
    if (isset($height_setting)) {
      $summary[] = t('Height: @height' . 'px', array('@height' => $height_setting));
    }

    $width_setting = $this->getSetting('width');
    if (isset($width_setting)) {
      $summary[] = t('Width: @width' . 'px', array('@width' => $width_setting));
    }

    return $summary;
  }
}
