<?php

/**
 * @file
 * Contains Drupal\amp\EntityTypeInfo.
 */

namespace Drupal\amp;

/**
 * Service class for retrieving and manipulating entity type information.
 */
class EntityTypeInfo {

  /**
   * Returns a list of AMP-enabled content types.
   *
   * @return array
   *   An array of bundles that have AMP view modes enabled.
   */
  public function getAmpEnabledTypes() {
    if ($cache = \Drupal::cache()->get('amp_enabled_types')) {
      $enabled_types = $cache->data;
    }
    else {
      $node_types = array_keys(node_type_get_names());
      foreach ($node_types as $node_type) {
        $amp_display = entity_get_display('node', $node_type, 'amp');
        //$amp_display = \Drupal::entityManager()
          //->getStorage('entity_view_display')
          //->load('node.' . $node_type . '.amp');
        if ($amp_display->status()) {
          $enabled_types[] = $node_type;
        }
      }
      \Drupal::cache()->set('amp_enabled_types', $enabled_types);
    }
    return array_combine($enabled_types, $enabled_types);
  }
}
