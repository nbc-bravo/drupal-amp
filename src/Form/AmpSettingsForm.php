<?php

/**
 * @file
 * Contains \Drupal\amp\Form\AmpSettingsForm.
 */

namespace Drupal\amp\Form;

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

  /** @var CacheTagsInvalidatorInterface */
  protected $tagInvalidate;

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
      else if (!empty($theme->status)) {
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
   */
  public function __construct(ConfigFactoryInterface $config_factory, ThemeHandlerInterface $theme_handler, CacheTagsInvalidatorInterface $tag_invalidate) {
    parent::__construct($config_factory);

    $this->themeHandler = $theme_handler;
    $this->themeOptions = $this->getThemeOptions();
    $this->tagInvalidate = $tag_invalidate;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('theme_handler'),
      $container->get('cache_tags.invalidator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $amp_config = $this->config('amp.settings');
    $node_types = node_type_get_names();
    $form['amp_content_amp_status'] = array(
      '#title' => $this->t('AMP Status by Content Type'),
      '#theme' => 'item_list',
      '#items' => amp_get_formatted_status_list(),
    );

    $amptheme_config = $this->config('amp.theme');
    $form['amptheme'] = array(
      '#type' => 'select',
      '#options' => $this->themeOptions,
      '#title' => $this->t('AMP theme'),
      '#description' => $this->t('Choose a theme to use for AMP pages.'),
      '#default_value' => $amptheme_config->get('amptheme'),
    );

    $form['google_analytics_id'] = [
      '#type' => 'textfield',
      '#default_value' => $amp_config->get('google_analytics_id'),
      '#title' => $this->t('Google Analytics Web Property ID'),
      '#description' => $this->t('This ID is unique to each site you want to track separately, and is in the form of UA-xxxxxxx-yy. To get a Web Property ID, <a href=":analytics">register your site with Google Analytics</a>, or if you already have registered your site, go to your Google Analytics Settings page to see the ID next to every site profile. <a href=":webpropertyid">Find more information in the documentation</a>.', [':analytics' => 'http://www.google.com/analytics/', ':webpropertyid' => Url::fromUri('https://developers.google.com/analytics/resources/concepts/gaConceptsAccounts', ['fragment' => 'webProperty'])->toString()]),
      '#maxlength' => 20,
      '#size' => 15,
      '#placeholder' => 'UA-',
    ];

    $form['google_adsense_id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Google AdSense Publisher ID'),
      '#default_value' => $amp_config->get('google_adsense_id'),
      '#maxlength' => 25,
      '#size' => 20,
      '#placeholder' => 'pub-',
      '#description' => $this->t('This is the Google AdSense Publisher ID for the site owner. Get this in your Google Adsense account. It should be similar to pub-9999999999999'),
    );

    $form['google_doubleclick_id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Google DoubleClick for Publishers Network ID'),
      '#default_value' => $amp_config->get('google_doubleclick_id'),
      '#maxlength' => 25,
      '#size' => 20,
      '#placeholder' => '/',
      '#description' => $this->t('The Network ID to use on all tags. This value should begin with a /.'),
    );

    $form['pixel_group'] = array(
      '#type' => 'fieldset',
      '#title' => t('amp-pixel'),
    );
    $form['pixel_group']['amp_pixel'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enable amp-pixel'),
      '#default_value' => $amp_config->get('amp_pixel'),
      '#description' => $this->t('The amp-pixel element is meant to be used as a typical tracking pixel -- to count page views. Find more information in the <a href="https://www.ampproject.org/docs/reference/amp-pixel.html">amp-pixel documentation</a>.'),
    );
    $form['pixel_group']['amp_pixel_domain_name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('amp-pixel domain name'),
      '#default_value' => $amp_config->get('amp_pixel_domain_name'),
      '#description' => $this->t('The domain name where the tracking pixel will be loaded: do not include http or https.'),
      '#states' => array('visible' => array(
        ':input[name="amp_pixel"]' => array('checked' => TRUE))
      ),
    );
    $form['pixel_group']['amp_pixel_query_string'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('amp-pixel query path'),
      '#default_value' => $amp_config->get('amp_pixel_query_string'),
      '#description' => $this->t('The path at the domain where the GET request will be received, e.g. "pixel" in example.com/pixel?RANDOM.'),
      '#states' => array('visible' => array(
        ':input[name="amp_pixel"]' => array('checked' => TRUE))
      ),
    );
    $form['pixel_group']['amp_pixel_random_number'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Random number'),
      '#default_value' => $amp_config->get('amp_pixel_random_number'),
      '#description' => $this->t('Use the special string RANDOM to add a random number to the URL if required. Find more information in the <a href="https://github.com/ampproject/amphtml/blob/master/spec/amp-var-substitutions.md#random">amp-pixel documentation</a>.'),
      '#states' => array('visible' => array(
        ':input[name="amp_pixel"]' => array('checked' => TRUE))
      ),
    );

    $form['test_page'] = array(
      '#type' => 'item',
      '#markup' => t('<a href="/admin/amp/library/test">Test that AMP is configured properly</a>'),
    );

    $form['amp_library_warnings_display'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('<em>Debugging</em>: Show AMP Library warnings in all AMP text formatters for all users'),
      '#default_value' => $amp_config->get('amp_library_warnings_display'),
      '#description' => $this->t('If you only want to see AMP formatter specific warning for one node add query "warnfix" at end of a node url. e.g. <strong>node/12345/amp?warnfix</strong>'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Validate the Google Analytics ID.
    if (!empty($form_state->getValue('google_analytics_id'))) {
      $form_state->setValue('google_analytics_id', trim($form_state->getValue('google_analytics_id')));
      // Replace all type of dashes (n-dash, m-dash, minus) with normal dashes.
      $form_state->setValue('google_analytics_id', str_replace(['–', '—', '−'], '-', $form_state->getValue('google_analytics_id')));
      if (!preg_match('/^UA-\d+-\d+$/', $form_state->getValue('google_analytics_id'))) {
        $form_state->setErrorByName('google_analytics_id', t('A valid Google Analytics Web Property ID is case sensitive and formatted like UA-xxxxxxx-yy.'));
      }
    }

    // Validate the Google Adsense ID.
    if (!empty($form_state->getValue('google_adsense_id'))) {
      $form_state->setValue('google_adsense_id', trim($form_state->getValue('google_adsense_id')));
      if (!preg_match('/^pub-[0-9]+$/', $form_state->getValue('google_adsense_id'))) {
        $form_state->setErrorByName('google_adsense_id', t('A valid Google AdSense Publisher ID is case sensitive and formatted like pub-9999999999999'));
      }
    }
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

    // Other module settings.
    $amp_config = $this->config('amp.settings');

    $amp_config->set('google_analytics_id', $form_state->getValue('google_analytics_id'))->save();
    $amp_config->set('google_adsense_id', $form_state->getValue('google_adsense_id'))->save();
    $amp_config->set('google_doubleclick_id', $form_state->getValue('google_doubleclick_id'))->save();

    $amp_config->set('amp_pixel', $form_state->getValue('amp_pixel'))->save();
    $amp_config->set('amp_pixel_domain_name', $form_state->getValue('amp_pixel_domain_name'))->save();
    $amp_config->set('amp_pixel_query_string', $form_state->getValue('amp_pixel_query_string'))->save();
    $amp_config->set('amp_pixel_random_number', $form_state->getValue('amp_pixel_random_number'))->save();

    if ($form_state->getValue('amp_library_warnings_display') !== $amp_config->get('amp_library_warnings_display')) {
      $amp_config->set('amp_library_warnings_display', $form_state->getValue('amp_library_warnings_display'))->save();
      $this->tagInvalidate->invalidateTags(['amp-warnings']);
    }

    parent::submitForm($form, $form_state);
  }
}
