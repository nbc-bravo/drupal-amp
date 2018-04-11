<?php

/**
 * @file
 * Contains \Drupal\amp\AMPService.
 */

namespace Drupal\amp\Service;

use Lullabot\AMP\AMP;

/**
 * Class AMPService.
 *
 * @package Drupal\amp
 */
class AMPService  {
  // amp-analytics maps to the amp/amp.analytics library (and so forth) but it could be anything arbitrary in the future
  // This is why we're being extremely explicit. We're not going to employ any tricks to convert amp-xyz to amp/amp.xyz
  protected $library_names = [
      'amp-ad' => 'amp/ad',
      'amp-access' => 'amp/access',
      'amp-accordion' => 'amp/accordion',
      'amp-analytics' => 'amp/analytics',
      'amp-anim' => 'amp/anim',
      'amp-audio' => 'amp/audio',
      'amp-brid-player' => 'amp/brid-player'
      'amp-brightcove' => 'amp/brightcove',
      'amp-carousel' => 'amp/carousel',
      'amp-dailymotion' => 'amp/dailymotion',
      'amp-dynamic-css-classes' => 'amp/dynamic-css-classes',
      'amp-embed' => 'amp/embed',
      'amp-facebook' => 'amp/facebook',
      'amp-fit-text' => 'amp/fit-text',
      'amp-font' => 'amp/font',
      'amp-iframe' => 'amp/iframe',
      'amp-instagram' => 'amp/instagram',
      'amp-install-serviceworker' => 'amp/install-serviceworker',
      'amp-image-lightbox' => 'amp/image-lightbox',
      'amp-jwplayer', => 'amp/jwplayer',
      'amp-kaltura-player' => 'amp/kaltura-player',
      'amp-lightbox' => 'amp/lightbox',
      'amp-list' => 'amp/list',
      'amp-pinterest' => 'amp/pinterest',
      'amp-pixel' => 'amp/pixel',
      'amp-sidebar' => 'amp/sidebar',
      'amp-slides' => 'amp/slides',
      'amp-social-share' => 'amp/social-share',
      'amp-soundcloud' => 'amp/soundcloud',
      'amp-springboard-player' => 'amp/springboard-player',
      'amp-sticky-ad' => 'amp/sticky-ad',
      'amp-twitter' => 'amp/twitter',
      'amp-user-notification' => 'amp/user-notification',
      'amp-video' => 'amp/video',
      'amp-vine' => 'amp/vine',
      'amp-vimeo' => 'amp/vimeo',
      'amp-youtube' => 'amp/youtube',
      'template' => 'amp/template', // exception to the above pattern
  ];

  /**
   * This is your starting point.
   * Its cheap to create AMP objects now.
   * Just create a new one every time you're asked for it.
   *
   * @return AMP
   */
  public function createAMPConverter() {
    return new AMP();
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
