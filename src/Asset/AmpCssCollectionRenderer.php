<?php

namespace Drupal\amp\Asset;

use Drupal\Core\Asset\CssCollectionRenderer;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\amp\Routing\AmpContext;
use Drupal\Core\State\StateInterface;
use Drupal\Component\Utility\Html;

/**
 * Renders CSS assets.
 *
 * This class retrieves all local css and renders it inline in the head of the
 * page. Neither style links nor @import are allowed in AMP, except for a few
 * whitelisted font providers.
 */
class AmpCssCollectionRenderer extends CssCollectionRenderer {

  /**
   * Whitelist of allowed external style links.
   *
   * @see https://www.ampproject.org/docs/design/responsive/custom_fonts
   */
  protected $whitelist = [
    'cloud.typography.com',
    'fast.fonts.net',
    'fonts.googleapis.com',
    'use.typekit.net',
    'maxcdn.bootstrapcdn.com',
  ];

  /**
   * The inner service that we are decorating.
   *
   * @var \Drupal\Core\Asset\CssCollectionRenderer
   */
  protected $cssCollectionRenderer;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The route amp context to determine whether a route is an amp one.
   *
   * @var \Drupal\amp\Routing\AmpContext
   */
  protected $ampContext;

  /**
   * Constructs a CssCollectionRenderer.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The state key/value store.
   */
  public function __construct(CssCollectionRenderer $cssCollectionRenderer, ConfigFactoryInterface $configFactory, AmpContext $ampContext, StateInterface $state) {
    $this->cssCollectionRenderer = $cssCollectionRenderer;
    $this->configFactory = $configFactory;
    $this->ampContext = $ampContext;
    parent::__construct($state);
  }

  /**
   * {@inheritdoc}
   */
  public function render(array $css_assets) {

    // Retrieve the normal css render array.
    $elements = parent::render($css_assets);

    // Intervene only if this is AMP page and option to render the css inline.
    $amp_settings = $this->configFactory->get('amp.settings');
    if (empty($amp_settings->get('amp_render_css')) || !$this->ampContext->isAmpRoute()) {
      return $elements;
    }

    // Start a variable to measure the size of the inline css.
    $size = 0;

    foreach ($elements as $key => $element) {
      // Process @import url() values.
      if (array_key_exists('#value', $element)) {
        $url = preg_match_all('/@import url\("(.+)\?/', $element['#value'], $matches);
        $all_css = [];
        foreach ($matches[1] as $file) {
          $css = file_get_contents(DRUPAL_ROOT . $file);
          $css = $this->minify($css);
          $size += strlen($css);
          $all_css[] = $css;
        }
        // Implode and minify results.
        $value = implode("", $all_css);
        $value = $this->minify($value);

        $element['#value'] = $value;
        $elements[$key] = $element;
      }
      // Process links.
      elseif (array_key_exists('#href', $element['#attributes'])) {
        // External files rendered as links only if they are on the whitelist.
        $provider = parse_url($element['#attributes']['#href'], PHP_URL_HOST);
        if (!in_array($provider, $this->whitelist)) {
          unset($elements[$key]);
        }
      }
    }
    // Merge the inline results into a single style element with an
    // "amp-custom" attribute, using the amp_custom_style #type.
    $merged = '';
    $replacement_key = NULL;
    foreach ($elements as $key => $element) {
      if (isset($element['#value'])) {
        // Any of the original keys is fine as the replacement key.
        // The last one found will survive.
        $replacement_key = $key;
        $merged .= $element['#value'];
        unset ($elements[$key]);
      }
    }
    $elements[$replacement_key] = [
      '#tag' => 'style',
      '#type' => 'amp_custom_style',
      '#value' => $merged,
    ];

    // Display info about inline css if &warnfix is appended to url.
    $current_page = \Drupal::request()->getRequestUri();
    if (!empty(stristr($current_page, 'warnfix'))) {
      $output = '<h2>' . 'CSS Filesize' . '</h2>';
      $output .= t('<p>The size of the css on this page is :size. The AMP limit is 50,000. The css files and their sizes are listed for ease in finding large files to optimize.</p>', [':size' => number_format($size, 0)]);
      $files = array_flip($files);
      //krsort($files);
      $output .= "<pre>" . var_dump($files) . "</pre>\n";
      debug($output);
    }
    return $elements;
  }

  /**
   * Minify css.
   *
   * @param string $value
   *   The css to minify.
   *
   * @return string
   *   The minified css.
   */
  public function minify($value) {
    // Remove comments
    $value = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $value);
    // Remove space after colons
    $value = str_replace(': ', ':', $value);
    // Remove whitespace
    $value = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $value);
    return $value;
  }

}
