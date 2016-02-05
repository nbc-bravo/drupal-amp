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
  public function createAMPConverter() {
    return new AMP();
  }
}
