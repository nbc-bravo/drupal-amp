<?php

namespace Drupal\amp\Service;

use Drupal\amp\Service\DrupalAMP;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Class AMPService.
 *
 * @package Drupal\amp
 */
class AMPService extends ServiceProviderBase  {

  /**
   * Map Drupal library names to the urls of the javascript they include.
   *
   * @return array
   *   An array keyed by library names of the javascript urls in each library.
   */
  protected function mapJSToNames() {
    $libraries = [];
    $definitions = \Drupal::service('library.discovery')->getLibrariesByExtension('amp');
    foreach ($definitions as $name => $definition) {
      if (!empty($definition['js'])) {
        $url = $definition['js'][0]['data'];
        $libraries[$url] = 'amp/' . $name;
      }
    }
    return $libraries;
  }

  /**
   * This is your starting point.
   * Its cheap to create AMP objects now.
   * Just create a new one every time you're asked for it.
   *
   * @return AMP
   */
  public function createAMPConverter() {
    return new DrupalAMP();
  }

  /**
   * Given an array of discovered JS requirements, add the related libraries.
   *
   * @param array $components
   *   An array of javascript urls that the AMP library discovered.
   *
   * @return array
   *   An array of the Drupal libraries that include this javascript.
   */
  public function addComponentLibraries(array $components) {
    $library_names = [];
    $map = $this->mapJSToNames();
    foreach ($components as $component_url) {
      if (isset($map[$component_url])) {
        $library_names[] = $map[$component_url];
      }
    }
    return $library_names;
  }

}
