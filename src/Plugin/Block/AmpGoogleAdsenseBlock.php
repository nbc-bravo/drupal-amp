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
    $amp_settings = \Drupal::config('amp.settings');
    $adsense_id = $amp_settings->get('adsense_id');
    $adsense_width = $amp_settings->get('google_adsense_width');
    $adsense_height = $amp_settings->get('google_adsense_height');
    $adsense_dataadclient = $amp_settings->get('google_adsense_dataadclient');
    $adsense_dataadslot = $amp_settings->get('google_adsense_dataadslot');
    return array(
      '#markup' => $this->t('<!-- google_adsense_id is @adsense -->', array('@adsense' => $adsense_id))
    );
  }
}
