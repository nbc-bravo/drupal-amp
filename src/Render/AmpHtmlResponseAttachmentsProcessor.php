<?php
/**
 * @file
 * Contains \Drupal\amp\Render\AmpHtmlResponseAttachmentsProcessor.
 */

namespace Drupal\amp\Render;

use Drupal\Core\Asset\AttachedAssetsInterface;
use Drupal\Core\Render\HtmlResponseAttachmentsProcessor;

/**
 * Processes attachments of AMP HTML responses.
 *
 * This class is used by the rendering service to process the #attached part of
 * the render array, for AMP HTML responses.
 *
 * To render attachments to HTML for testing without a controller, use the
 * 'bare_html_page_renderer' service to generate a
 * Drupal\Core\Render\HtmlResponse object. Then use its getContent(),
 * getStatusCode(), and/or the headers property to access the result.
 *
 * @see template_preprocess_html()
 * @see \Drupal\Core\Render\AttachmentsResponseProcessorInterface
 * @see \Drupal\Core\Render\BareHtmlPageRenderer
 * @see \Drupal\Core\Render\HtmlResponse
 * @see \Drupal\Core\Render\MainContent\HtmlRenderer
 */
class AmpHtmlResponseAttachmentsProcessor extends HtmlResponseAttachmentsProcessor {

  /**
   * Processes asset libraries into render arrays.
   *
   * @param \Drupal\Core\Asset\AttachedAssetsInterface $assets
   *   The attached assets collection for the current response.
   * @param array $placeholders
   *   The placeholders that exist in the response.
   *
   * @return array
   *   An array keyed by asset type, with keys:
   *     - scripts
   */
  protected function processAssetLibraries(AttachedAssetsInterface $assets, array $placeholders) {
    $variables = [];
    foreach ($assets->libraries as $delta => $library) {
      if (strpos($library, 'amp/') === false) {
        unset($assets->libraries[$delta]);
      }
    }

    // Print amp scripts - if any are present.
    if (isset($placeholders['scripts']) || isset($placeholders['scripts_bottom'])) {
      // Do not optimize JS.
      $optimize_js = FALSE;
      list($js_assets_header, $js_assets_footer) = $this->assetResolver->getJsAssets($assets, $optimize_js);
      $variables['scripts'] = $this->jsCollectionRenderer->render($js_assets_footer);
    }

    return $variables;
  }
}
