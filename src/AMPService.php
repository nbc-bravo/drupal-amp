<?php

/**
 * @file
 * Contains \Drupal\amp\AMPService.
 */

namespace Drupal\amp;

use Lullabot\AMP\AMP;

/**
 * Class AMPService.
 *
 * @package Drupal\amp
 */
class AMPService  {
  /** @var AMP */
  protected $amp;

  // amp-analytics maps to the amp/amp.analytics library (and so forth) but it could be anything arbitrary in the future
  // This is why we're being extremely explicit. We're not going to employ any tricks to convert amp-xyz to amp/amp.xyz
  protected $library_names = [
      'amp-analytics' => 'amp/amp.analytics',
      'amp-anim' => 'amp/amp.anim',
      'amp-audio' => 'amp/amp.audio',
      'amp-brightcove' => 'amp/amp.brightcove',
      'amp-carousel' => 'amp/amp.carousel',
      'amp-dailymotion' => 'amp/amp.dailymotion',
      'amp-facebook' => 'amp/amp.facebook',
      'amp-fit-text' => 'amp/amp.fit-text',
      'amp-font' => 'amp/amp.font',
      'amp-iframe' => 'amp/amp.iframe',
      'amp-instagram' => 'amp/amp.instagram',
      'amp-install-serviceworker' => 'amp/amp.install-serviceworker',
      'amp-image-lightbox' => 'amp/amp.image-lightbox',
      'amp-lightbox' => 'amp/amp.lightbox',
      'amp-list' => 'amp/amp.list',
      'amp-pinterest' => 'amp/amp.pinterest',
      'amp-soundcloud' => 'amp/amp.soundcloud',
      'amp-twitter' => 'amp/amp.twitter',
      'amp-user-notification' => 'amp/amp.user-notification',
      'amp-vine' => 'amp/amp.vine',
      'amp-vimeo' => 'amp/amp.vimeo',
      'amp-youtube' => 'amp/amp.youtube',
      'template' => 'amp/amp.template', // exception to the above pattern
  ];

  public function __construct() {
    $this->amp = new AMP();
  }

  public function getAMPConverter() {
    return $this->amp;
  }

  /**
   * Given an array of components e.g. amp-iframe, make an array of library
   */
  public function addComponentLibraries(array $components) {
    $library_paths = [];
    /**
     * @var string $component_name
     * @var string $component_url We dont need this for now
     */
    foreach($components as $component_name => $component_url) {
      if (isset($this->library_names[$component_name])) {
        $library_paths[] = $this->library_names[$component_name];
      }
    }
    return $library_paths;
  }
}
