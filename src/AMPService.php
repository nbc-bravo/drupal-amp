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
  protected $ampObject;

  /**
   * Constructor.
   */
  public function __construct() {
    $this->ampObject = new AMP();
  }

  public function getAMP() {
    return $this->ampObject;
  }
}
