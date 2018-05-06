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
  protected $link_domain_whitelist = [
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

    // For tracking the size and contents of the inlined css:
    $size = 0;
    $files = [];
    foreach ($elements as $key => $element) {
      // Process @import url() values.
      if ($element['#tag'] == 'style' && array_key_exists('#value', $element)) {
        $urls = preg_match_all('/@import url\("(.+)\?/', $element['#value'], $matches);
        $all_css = [];
        foreach ($matches[1] as $url) {
          $css = file_get_contents(DRUPAL_ROOT . $url);
          $css = $this->minify($css);
          $css = $this->strip($css);
          $size += strlen($css);
          $all_css[] = $css;
          $files[$url] = $this->format(strlen($css));
         }
        // Implode, wrap in @media, and minify results.
        $value = implode("", $all_css);
        $value = '@media ' . $element['#attributes']['media'] . " {\n" . $value . "\n}\n";
        $value = $this->minify($value);

        $element['#value'] = $value;
        $elements[$key] = $element;
        $elements[$key]['#merged'] = TRUE;
      }
      // Process links.
      elseif ($element['#tag'] == 'link' && array_key_exists('href', $element['#attributes'])) {
        $url = $element['#attributes']['href'];
        $provider = parse_url($url, PHP_URL_HOST);
        if (!empty($provider)) {
          // External files rendered as links only if they are on the whitelist.
          if (!in_array($provider, $this->link_domain_whitelist)) {
            unset($elements[$key]);
          }
        }
        else {
          // Strip any querystring off the url.
          list($url, $query) = explode('?', $url);
          $css = file_get_contents(DRUPAL_ROOT . $url);
          $css = $this->minify($css);
          $css = $this->strip($css);
          $size += strlen($css);
          $all_css[] = $css;
          $files[$url] = $this->format(strlen($css));
          $element['#value'] = $css;
          $elements[$key] = $element;
          $elements[$key]['#merged'] = TRUE;
        }
      }
    }
    // Merge the inline results into a single style element with an
    // "amp-custom" attribute, using the amp_custom_style #type.
    $merged = '';
    $replacement_key = NULL;
    foreach ($elements as $key => $element) {
      if (isset($element['#merged'])) {
        $merged .= $element['#value'];
        unset ($elements[$key]);
        // The first key found will become the new element's key.
        if (empty($replacement_key)) {
          $replacement_key = $key;
        }
      }
    }
    $elements[$replacement_key] = [
      '#tag' => 'style',
      '#type' => 'amp_custom_style',
      '#value' => $merged,
    ];

    // Display info about inline css if &development is appended to url.
    $current_page = \Drupal::request()->getRequestUri();
    if (!empty(stristr($current_page, 'development'))) {
      $output = '<h2>' . 'CSS Filesize' . '</h2>';
      $difference = ($size - 50000);
      $over = $difference > 0 ? t('so your css is :difference too big', [':difference' => $this->format(abs($difference))]) : '';
      $under = $difference <= 0 ? t('so you have :difference to spare', [':difference' => $this->format(abs($difference))]) : '';
      $output .= t('The size of the css on this page is :size. The AMP limit is :limit, :overunder. The included css files and their sizes are listed for ease in finding large files to optimize. For the best information about individual files sizes, visit this page while optimization is turned off.', [':size' => $this->format($size), ':limit' => $this->format(50000), ':overunder' => $over . $under]);
      $files = array_flip($files);
      //krsort($files);
      if (function_exists('dpm')) {
        dpm($output);
        dpm($files);
      }
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

  /**
   * Strip css which won't validate as AMPHTML.
   *
   * @param string $value
   *   The css to strip.
   *
   * @return string
   *   The stripped css.
   */
  public function strip($value) {
    // Remove css that won't validate as AMPHTML.
    $invalid = [
      '!important',
    ];
    $value = str_replace($invalid, '', $value);
    return $value;
  }

  /**
   * Format values consistently.
   *
   * @param string $value
   *   The number to minify.
   *
   * @return string
   *   The formatted number.
   */
  public function format($value) {
    return number_format($value, 0) . ' ' . t('bytes');
  }
}
