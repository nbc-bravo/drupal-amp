<?php

/**
 * @file
 * Preprocessors and helper functions to make theming easier.
 */

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;

/**
 * Prepares variables for AMP Views Carousel templates.
 *
 * Default template: amp-views-carousel.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - view: A View object.
 */
function template_preprocess_amp_views_carousel(&$variables) {
  $view = $variables['view'];

  // If in a Views live preview, don't try to render the carousel.
  if (!empty($view->live_preview)) {
    $variables['theme_hook_original'] = 'views_view_unformatted';
    template_preprocess_views_view_unformatted($variables);
    return;
  }

  $options = $view->style_plugin->options;
  $handler = $view->style_plugin;

  // Fetch wrapper classes from handler options.
  if ($handler->options['wrapper_class']) {
    $wrapper_class = explode(' ', $handler->options['wrapper_class']);
    $variables['attributes']['class'] = array_map('\Drupal\Component\Utility\Html::cleanCssIdentifier', $wrapper_class);
  }

  $layout = $handler->getSetting('layout');
  $width = $handler->validWidth($handler->getSetting('width'), $layout);
  $height = $handler->validHeight($handler->getSetting('height'), $layout);

  $attributes['type'] = $handler->getSetting('type');
  $attributes['layout'] = $layout;
  $attributes['width'] = $width;
  $attributes['height'] = $height;
  $attributes['controls'] = $handler->getSetting('controls');
  $attributes['loop'] = $handler->getSetting('loop');
  $attributes = array_filter($attributes);

  $variables['carousel']['attributes'] = new Attribute($attributes);

  //$variables['carousel']['#theme'] = 'amp_image_carousel';
  $variables['#attached']['library'] = $handler->getLibraries();

  template_preprocess_views_view_unformatted($variables);

}

