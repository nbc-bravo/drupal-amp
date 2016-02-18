<?php
/**
 * Provides an AMP Google Adsense block
 *
 * @Block(
 *   id = "amp_google_adsense_block",
 *   admin_label = @Translation("AMP Google Adsense block"),
 * )
 */

namespace Drupal\amp\Plugin\Block;

use Drupal\Core\Block\BlockBase;

class AmpGoogleAdsenseBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $amp_config = \Drupal::config('amp.settings');
    if (empty($amp_config->get('google_adsense_id'))) {
      return array(
        '#markup' => $this->t('This block requires a Google Adsense ID.')
      );
    }
    $adsense_id = \Drupal::config('amp.settings')->get('adsense_id');
    $adsense_width = \Drupal::config('amp.settings')->get('google_adsense_width');
    $adsense_height = \Drupal::config('amp.settings')->get('google_adsense_height');
    $adsense_dataadclient = \Drupal::config('amp.settings')->get('google_adsense_dataadclient');
    $adsense_dataadslot = \Drupal::config('amp.settings')->get('google_adsense_dataadslot');
    return array(
      '#markup' => $this->t('<!-- google_adsense_id is @adsense -->', array('@adsense' => $adsense_id))
    );
  }
}
