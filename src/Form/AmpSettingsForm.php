<?php

namespace Drupal\amp\Form;

use Drupal\amp\EntityTypeInfo;
use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the configuration export form.
 */
class AmpSettingsForm extends ConfigFormBase {

  /**
   * The theme handler service.
   *
   * @var \Drupal\Core\Extension\ThemeHandlerInterface
   */
  protected $themeHandler;

  /**
   * The array of valid theme options.
   *
   * @array $themeOptions
   */
  private $themeOptions;

  /**
   * The cache tags invalidator.
   *
   * @var \Drupal\Core\Cache\CacheTagsInvalidatorInterface
   */
  protected $tagInvalidate;

  /**
   * Information about AMP-enabled content types.
   *
   * @var \Drupal\amp\EntityTypeInfo
   */
  protected $entityTypeInfo;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'amp_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['amp.settings', 'amp.theme'];
  }

  /*
   * Helper function to get available theme options.
   *
   * @return array
   *   Array of valid themes.
   */
  private function getThemeOptions() {
    // Get all available themes.
    $themes = $this->themeHandler->rebuildThemeData();
    uasort($themes, 'system_sort_modules_by_info_name');
    $theme_options = [];

    foreach ($themes as $theme) {
      if (!empty($theme->info['hidden'])) {
        continue;
      }
      elseif (!empty($theme->status)) {
        $theme_options[$theme->getName()] = $theme->info['name'];
      }
    }

    return $theme_options;
  }

  /**
   * Constructs a AmpSettingsForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler
   *   The theme handler.
   * @param \Drupal\Core\Cache\CacheTagsInvalidatorInterface $tag_invalidate
   *   The cache tags invalidator.
   * @param \Drupal\amp\EntityTypeInfo $entity_type_info
   *   Information about AMP-enabled content types.
   */
  public function __construct(ConfigFactoryInterface $config_factory, ThemeHandlerInterface $theme_handler, CacheTagsInvalidatorInterface $tag_invalidate, EntityTypeInfo $entity_type_info) {
    parent::__construct($config_factory);

    $this->themeHandler = $theme_handler;
    $this->themeOptions = $this->getThemeOptions();
    $this->tagInvalidate = $tag_invalidate;
    $this->entityTypeInfo = $entity_type_info;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('theme_handler'),
      $container->get('cache_tags.invalidator'),
      $container->get('amp.entity_type')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $amp_config = $this->config('amp.settings');
    $module_handler = \Drupal::moduleHandler();

    $page_prefix = $this->t('<p>This page contains configuration for AMP ' .
      'pages. Extensive documentation about AMP is available on the <a ' .
      'href=":doclink1">AMP Project</a>.</p>', [
        ':doclink1' => 'https://www.ampproject.org',
      ]);
    $page_prefix .= '<ul>';
    if (!$module_handler->moduleExists('schema_metatag')) {
      $page_prefix .= '<li>';
      $page_prefix .= $this->t('Valid AMP requires Schema.org markup, which ' .
        'can be provided by the <a href=":doclink2">Schema.org Metatag ' .
        'module</a>.', [
          ':doclink2' => 'https://www.drupal.org/project/schema_metatag'
        ]);
      $page_prefix .= '</li>';
    }
    if ($module_handler->moduleExists('toolbar') && !$module_handler->moduleExists('amp_toolbar')) {
      $page_prefix .= '<li>';
      $page_prefix .=  $this->t('If you have the Toolbar module enabled, ' .
        'enable the <a href=":doclink3">AMP Toolbar</a> module.', [
          ':doclink3' => '/admin/modules'
        ]);
      $page_prefix .= '</li>';
    }
    if ($module_handler->moduleExists('rdf') && !$module_handler->moduleExists('amp_rdf')) {
      $page_prefix .= '<li>';
      $page_prefix .=  $this->t('If you have the RDF module enabled, enable ' .
        'the <a href=":doclink4">AMP RDF</a> module.', [
          ':doclink4' => '/admin/modules'
        ]);
      $page_prefix .= '</li>';
    }
    $page_prefix .= '</ul>';

    $amptheme_config = $this->config('amp.theme');
    $description = $this->t('Choose a theme to use for AMP pages. Themes must ' .
      'be installed (but not necessarily set as the default theme) before ' .
      'they will appear in this list and be usable by AMP. You can choose ' .
      'between AMP Base, an installed subtheme of AMP Base, such as the ' .
      'ExAMPle Subtheme, or any theme that complies with AMP rules. See ' .
      '<a href=":link">AMPTheme</a> for examples and pre-configured themes.', [
        ':link' => 'https://www.drupal.org/project/amptheme'
      ]);

    $form['amptheme'] = [
      '#type' => 'select',
      '#options' => $this->themeOptions,
      '#required' => TRUE,
      '#title' => $this->t('AMP theme'),
      '#description' => $description,
      '#default_value' => $amptheme_config->get('amptheme'),
      '#prefix' => $page_prefix,
    ];

    $prefix = $this->t('<p>Currently, only node pages can be displayed as ' .
      'AMP pages. Select the content types you want to enable for AMP in the ' .
      'list below. Once enabled, links are provided so you can configure the ' .
      'fields and formatters for the AMP display of that content type.</p>', [
        ':doclink1' => 'https://www.ampproject.org',
      ]);
    if ($module_handler->moduleExists('field_ui')) {
      $form['amp_content_amp_status'] = [
        '#title' => $this->t('AMP Status by Content Type'),
        '#theme' => 'item_list',
        '#items' => $this->entityTypeInfo->getFormattedAmpEnabledTypes(),
        '#prefix' => $prefix,
      ];
    }
    else {
      $form['amp_content_amp_status'] = [
        '#type' => 'item',
        '#title' => $this->t('AMP Status by Content Type'),
        '#markup' => $this->t('(In order to enable and disable AMP content ' .
          'types in the UI, the Field UI module must be enabled.)'),
      ];
    }

    $page_suffix = $this->t('This code uses the ' .
      '<a href="https://github.com/Lullabot/amp-library">AMP Library</a>. '.
      'This library will be installed by Composer if the AMP module is ' .
      'installed by Composer as follows:</p><p><code>composer install ' .
      'drupal/amp --with-dependencies</code></p> ');
    $page_suffix .= $this->t('Test that the AMP library is <a href=":url">' .
      'configured properly</a>. Look for the words <strong>The Library is ' .
      'working.</strong> at the top of the page. You will see that the ' .
      'library detected markup that fails AMP standards. If the library is ' .
      'not detected, retry adding the AMP module using Composer, as indicated ' .
      'above.', [
        ':url' => Url::fromRoute('amp.test_library_hello')->toString()
      ]);
    $page_suffix .= '</p><p>';
    $page_suffix .= $this->t('If you want to see AMP debugging information ' .
      'for any node add "&debug#development=1" at end of the AMP node url, ' .
      'e.g. <em>node/12345?amp&debug#development=1</em>. This will provide ' .
      'Drupal messages on the page and AMP messages in the javascript ' .
      'console. Check the AMP Project documentation for more information.</p>');
    $page_suffix .= '</p>';

    $form['advanced'] = [
      '#type' => 'details',
      '#title' => $this->t('For Administrators and Developers'),
      '#description' => $page_suffix,
      '#open' => TRUE,
    ];
    $form['advanced']['process_full_html'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('<strong>Advanced option</strong>: Run the page body through the AMP library'),
      '#default_value' => $amp_config->get('process_full_html'),
      '#description' => $this->t('The AMP PHP library will fix some AMP HTML ' .
        'non-compliance issues by removing disallowed attributes, tags ' .
        'and property values. This is an option for fixing stubborn ' .
        'AMP-unfriendly HTML. This feature can be problematic, the library ' .
        'is often over-aggressive and removes some code you may still want, '.
        'so test carefully.')
    ];

    // Hide these and switch to sub modules for each.
    // @TODO Remove from this page once sub modules are created.
    $form['google_analytics_id'] = [
      '#type' => 'textfield',
      '#default_value' => $amp_config->get('google_analytics_id'),
      '#title' => $this->t('Google Analytics Web Property ID'),
      '#description' => $this->t('This ID is unique to each site you want to ' .
        'track separately, and is in the form of UA-xxxxxxx-yy. To get a Web ' .
        'Property ID, <a href=":analytics">register your site with Google ' .
        'Analytics</a>, or if you already have registered your site, go to ' .
        'your Google Analytics Settings page to see the ID next to every site ' .
        'profile. <a href=":webpropertyid">Find more information in the ' .
        'documentation</a>.', [
          ':analytics' => 'http://www.google.com/analytics/',
          ':webpropertyid' => Url::fromUri('https://developers.google.com/analytics/resources/concepts/gaConceptsAccounts', ['fragment' => 'webProperty'])->toString()
        ]),
      '#maxlength' => 20,
      '#size' => 15,
      '#placeholder' => 'UA-',
      '#access' => FALSE,
    ];

    // @TODO Display again once this is possible.
    // The configuration option still exists so it can be used in AMP logic.
    $form['experimental'] = [
      '#type' => 'fieldset',
      '#title' => $this->t("Experimental features"),
      '#access' => FALSE,
    ];
    $form['experimental']['amp_everywhere'] = [
      '#type' => 'checkbox',
      '#default_value' => $amp_config->get('amp_everywhere'),
      '#title' => $this->t('Generate all pages as AMP pages?'),
      '#description' => $this->t('Set this to FALSE if you want AMP pages displayed as an alternative to your normal pages, on a different path (two pages for each item, the traditional way of deploying AMP). Choose TRUE if you want your normal pages to also be the AMP pages (there is only one page for each item, which is both the canonical page and the AMP page). If you are not sure what what this means, leave it set to FALSE.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // AMP theme settings.
    $amptheme = $form_state->getValue('amptheme');
    $amptheme_config = $this->config('amp.theme');
    $amptheme_config->setData(['amptheme' => $amptheme]);
    $amptheme_config->save();

    $amp_config = $this->config('amp.settings');
    $amp_config->set('process_full_html', $form_state->getValue('process_full_html'))->save();

    //$amp_config->set('amp_everywhere', $form_state->getValue('amp_everywhere'))->save();

    parent::submitForm($form, $form_state);
  }
}
