<?php
/**
 * Provides an AMP Google DoubleClick for Publishers block
 *
 * @Block(
 *   id = "amp_google_doubleclick_block",
 *   admin_label = @Translation("AMP Google DoubleClick for Publishers block"),
 * )
 */

namespace Drupal\amp\Plugin\Block;

use Drupal\Core\Block\BlockBase;

class AmpGoogleDoubleClickBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $amp_config = \Drupal::config('amp.settings');
    if (empty($amp_config->get('google_doubleclick_id'))) {
      return array(
        '#markup' => $this->t('This block requires a Google DoubleClick Network ID.')
      );
    }
    $amp_settings = \Drupal::config('amp.settings');
    $doubleclick_id = $amp_settings->get('google_doubleclick_id');
    $doubleclick_id = $amp_settings->get('google_doubleclick_width');
    $doubleclick_id = $amp_settings->get('google_doubleclick_height');
    $doubleclick_id = $amp_settings->get('google_doubleclick_dataslot');
    return array(
      '#markup' => $this->t('<!-- google_doubleclick_id is @dfpid -->', array('@dfpid' => $doubleclick_id))
    );
  }
}
