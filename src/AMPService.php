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

  public function __construct() {
    $this->amp = new AMP();
  }
  public function getAMPConverter() {
    return $this->amp;
  }
}
