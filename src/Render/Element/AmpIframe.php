<?php

/**
 * @file
 * Contains \Drupal\amp\Render\Element\AmpIframe.
 */

namespace Drupal\amp\Render\Element;

use Drupal\filter\Element\ProcessedText;

/**
 * Provides a render element for an iframe rendered as an amp-iframe.
 *
 * By default, this element sets #theme so that the 'amp_iframe' theme hook is used
 * for rendering, and attaches the js needed for the amp-iframe component. See
 * template_preprocess_amp_iframe() for documentation on the  properties used in
 * theming.
 *
 * Properties:
 * - #iframe: An array with iframe details. See template_preprocess_amp_iframe()
 *   for documentation of the properties in this array.
 *
 * @see \Drupal\Core\Render\Element\Operations
 *
 * @RenderElement("amp_iframe")
 */
class AmpIframe extends ProcessedText {

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
        array($class, 'preRenderAmpIframe'),
      ),
      '#theme' => 'amp_iframe',
    );
  }

  /**
   * Pre-render callback: Attaches the amp-iframe library and required markup.
   */
  public static function preRenderIframe($element) {
    $element['#attached']['library'][] = 'amp/amp.iframe';

    return $element;
  }
}
