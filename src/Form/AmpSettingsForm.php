<?php

/**
 * @file
 * Contains \Drupal\amp\Form\AmpSettingsForm.
 */

namespace Drupal\amp\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\NodeType;
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
  public function __construct(ConfigFactoryInterface $config_factory, ThemeHandlerInterface $theme_handler) {
    parent::__construct($config_factory);

    $this->themeHandler = $theme_handler;
    $this->themeOptions = $this->getThemeOptions();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('theme_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $amp_config = $this->config('amp.settings');
    $node_types = node_type_get_names();
    $form['node_types'] = array(
      '#type' => 'checkboxes',
      '#multiple' => TRUE,
      '#title' => $this->t('Enable and disable content types (and their configuration) that have AMP versions by default:'),
      '#default_value' => !empty($amp_config->get('node_types')) ? $amp_config->get('node_types') : [],
      '#options' => $node_types,
    );

    $amp_theme_options = $this->themeOptions;
    $amptheme_config = $this->config('amp.theme');
    $form['amptheme'] = array(
      '#type' => 'select',
      '#options' => $amp_theme_options,
      '#title' => $this->t('AMP theme'),
      '#description' => $this->t('Choose a theme to use for AMP pages.'),
      '#default_value' => $amptheme_config->get('amptheme'),
    );

    $google_analytics_id = $amp_config->get('google_analytics_id');
    $form['google_analytics_id'] = [
      '#type' => 'textfield',
      '#default_value' => $amp_config->get('google_analytics_id'),
      '#title' => $this->t('Google Anlalytics Web Property ID'),
      '#description' => $this->t('This ID is unique to each site you want to track separately, and is in the form of UA-xxxxxxx-yy. To get a Web Property ID, <a href=":analytics">register your site with Google Analytics</a>, or if you already have registered your site, go to your Google Analytics Settings page to see the ID next to every site profile. <a href=":webpropertyid">Find more information in the documentation</a>.', [':analytics' => 'http://www.google.com/analytics/', ':webpropertyid' => Url::fromUri('https://developers.google.com/analytics/resources/concepts/gaConceptsAccounts', ['fragment' => 'webProperty'])->toString()]),
      '#maxlength' => 20,
      '#size' => 15,
      '#placeholder' => 'UA-',
    ];

    // Adsense configuration.
    $form['adsense'] = array(
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );
    $form['adsense']['adsense_checkbox'] = array(
      '#type' => 'checkbox',
      '#title' => t('Google Adsense'),
    );
    $google_adsense_id = $amp_config->get('google_adsense_id');
    $form['adsense']['google_adsense_id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Google AdSense Publisher ID'),
      '#default_value' => $amp_config->get('google_adsense_id'),
      '#maxlength' => 25,
      '#size' => 20,
      '#placeholder' => 'pub-',
      '#description' => $this->t('This is the Google AdSense Publisher ID for the site owner. Get this in your Google Adsense account. It should be similar to pub-9999999999999'),
      '#states' => array('visible' => array(
        ':input[name="adsense_checkbox"]' => array('checked' => TRUE))
      ),
    );
    $form['adsense']['google_adsense_width'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Width'),
      '#default_value' => $amp_config->get('google_adsense_width'),
      '#maxlength' => 25,
      '#size' => 20,
      '#states' => array('visible' => array(
        ':input[name="adsense_checkbox"]' => array('checked' => TRUE))
      ),
    );
    $form['adsense']['google_adsense_height'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Height'),
      '#default_value' => $amp_config->get('google_adsense_height'),
      '#maxlength' => 25,
      '#size' => 20,
      '#states' => array('visible' => array(
        ':input[name="adsense_checkbox"]' => array('checked' => TRUE))
      ),
    );
    $form['adsense']['google_adsense_dataadclient'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Data ad client'),
      '#default_value' => $amp_config->get('google_adsense_dataadclient'),
      '#maxlength' => 25,
      '#size' => 20,
      '#states' => array('visible' => array(
        ':input[name="adsense_checkbox"]' => array('checked' => TRUE))
      ),
    );
    $form['adsense']['google_adsense_dataadslot'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Data ad slot'),
      '#default_value' => $amp_config->get('google_adsense_dataadslot'),
      '#maxlength' => 25,
      '#size' => 20,
      '#states' => array('visible' => array(
        ':input[name="adsense_checkbox"]' => array('checked' => TRUE))
      ),
    );

    // DoubleClick configuration.
    $form['doubleclick'] = array(
      '#type' => 'fieldset',
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    );
    $form['doubleclick']['doubleclick_checkbox'] = array(
      '#type' => 'checkbox',
      '#title' => t('DoubleClick for Publishers'),
    );
    $google_doubleclick_id = $amp_config->get('google_doubleclick_id');
    $form['doubleclick']['google_doubleclick_id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Google DoubleClick for Publishers Network ID'),
      '#default_value' => $amp_config->get('google_doubleclick_id'),
      '#maxlength' => 25,
      '#size' => 20,
      '#placeholder' => '/',
      '#description' => $this->t('The Network ID to use on all tags. This value should begin with a /.'),
      '#states' => array('visible' => array(
        ':input[name="doubleclick_checkbox"]' => array('checked' => TRUE))
      ),
    );
    $form['doubleclick']['google_doubleclick_width'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Width'),
      '#default_value' => $amp_config->get('google_doubleclick_width'),
      '#maxlength' => 25,
      '#size' => 20,
      '#states' => array('visible' => array(
        ':input[name="doubleclick_checkbox"]' => array('checked' => TRUE))
      ),
    );
    $form['doubleclick']['google_doubleclick_height'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Height'),
      '#default_value' => $amp_config->get('google_doubleclick_height'),
      '#maxlength' => 25,
      '#size' => 20,
      '#states' => array('visible' => array(
        ':input[name="doubleclick_checkbox"]' => array('checked' => TRUE))
      ),
    );
    $form['doubleclick']['google_doubleclick_dataslot'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Data-slot'),
      '#default_value' => $amp_config->get('google_doubleclick_dataslot'),
      '#maxlength' => 25,
      '#size' => 20,
      '#states' => array('visible' => array(
        ':input[name="doubleclick_checkbox"]' => array('checked' => TRUE))
      ),
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

    if ($form_state->hasValue('node_types') && $form_state->hasValue('amptheme')) {
      $node_types = $form_state->getValue('node_types');
      $amp_config = $this->config('amp.settings');

      // Get a list of changes. The first time this form is accessed, this will
      // be empty because we will not know all of the node types.
      if (!empty($amp_config->get('node_types'))) {
        $changes = array_diff_assoc($node_types, $amp_config->get('node_types'));
      }
      else {
        $changes = array_filter($node_types);
      }
      foreach ($changes as $bundle => $value) {
        // Get a list of view modes for the bundle.
        $view_modes = \Drupal::entityManager()->getViewModeOptionsByBundle('node', $bundle);
        // For nodes that have added AMP versions, create the AMP view mode.
        if (!empty($value)) {
          if (!isset($view_modes['amp'])) {
            if (\Drupal\Core\Entity\Entity\EntityViewDisplay::create(array(
                'targetEntityType' => 'node',
                'bundle' => $bundle,
                'mode' => 'amp',
              ))->setStatus(TRUE)->save()) {
              drupal_set_message(t('The content type <strong>!bundle</strong> is now AMP enabled.', array('!bundle' => $bundle)), 'status');
            }
          }
        }
        elseif (\Drupal::configFactory()->getEditable('core.entity_view_display.node.' . $bundle . '.amp')->delete()) {
          drupal_set_message(t('The content type <strong>!bundle</strong> is no longer AMP enabled.', array('!bundle' => $bundle)), 'status');
        }
      }

      $amp_config->setData(['node_types' => $node_types])->save();

      $amptheme = $form_state->getValue('amptheme');
      $amptheme_config = $this->config('amp.theme');
      $amptheme_config->setData(['amptheme' => $amptheme]);
      $amptheme_config->save();

      // Submit Analytics configuration.
      $amp_config->set('google_analytics_id', $form_state->getValue('google_analytics_id'))->save();
      // Submit Adsense configuration.
      $amp_config->set('google_adsense_id', $form_state->getValue('google_adsense_id'))->save();
      $amp_config->set('google_adsense_width', $form_state->getValue('google_adsense_width'))->save();
      $amp_config->set('google_adsense_height', $form_state->getValue('google_adsense_height'))->save();
      $amp_config->set('google_adsense_dataadclient', $form_state->getValue('google_adsense_dataadclient'))->save();
      $amp_config->set('google_adsense_dataadslot', $form_state->getValue('google_adsense_dataadslot'))->save();
      // Submit DoubleClick configuration.
      $amp_config->set('google_doubleclick_id', $form_state->getValue('google_doubleclick_id'))->save();
      $amp_config->set('google_doubleclick_width', $form_state->getValue('google_doubleclick_width'))->save();
      $amp_config->set('google_doubleclick_height', $form_state->getValue('google_doubleclick_height'))->save();
      $amp_config->set('google_doubleclick_dataslot', $form_state->getValue('google_doubleclick_dataslot'))->save();

      parent::submitForm($form, $form_state);
    }
  }
}
