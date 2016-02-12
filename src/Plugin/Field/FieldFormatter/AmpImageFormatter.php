<?php

/**
 * @file
 * Contains \Drupal\amp\Plugin\Field\FieldFormatter\AmpImageFormatter.
 */

namespace Drupal\amp\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatter;

/**
 * Plugin implementation of the 'amp_image' formatter.
 *
 * @FieldFormatter(
 *   id = "amp_image",
 *   label = @Translation("AMP Image"),
 *   description = @Translation("Display an AMP Image file."),
 *   field_types = {
 *     "image"
 *   }
 * )
 */
class AmpImageFormatter extends ImageFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items);
    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);

    $layout_url = 'https://www.ampproject.org/docs/guides/responsive/control_layout.html#size-and-position-elements';
    // Add configuration options for layout.
    $element['amp_layout'] = [
      '#title' => t('AMP Layout'),
      '#type' => 'select',
      '#default_value' => $this->getSetting('amp_layout'),
      '#empty_option' => t('None (no layout)'),
      '#options' => $this->getLayouts(),
      '#description' => $this->t('<a href=":url" target="_blank">Layout Information</a>', array(':url' => $layout_url)),
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    // Display this setting only if an AMP layout is set.
    $layout_options = $this->getLayouts();
    $layout_setting = $this->getSetting('amp_layout');
    if (isset($layout_options[$layout_setting])) {
      $summary[] = t('Layout: @setting', array('@setting' => $layout_options[$layout_setting]));
    }

    return $summary;
  }

  /**
   * Return a list of AMP layouts.
   */
  private function getLayouts() {
    return [
      'nodisplay' => 'nodisplay',
      'fixed' => 'fixed',
      'responsive' => 'responsive',
      'fixed-height' => 'fixed-height',
      'fill' => 'fill',
      'container' => 'container',
    ];
  }
}
