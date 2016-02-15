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
    $nodetype_config = $this->config('amp.settings');
    $node_types = node_type_get_names();
    $form['node_types'] = array(
      '#type' => 'checkboxes',
      '#multiple' => TRUE,
      '#title' => $this->t('Enable and disable content types (and their configuration) that have AMP versions by default:'),
      '#default_value' => !empty($nodetype_config->get('node_types')) ? $nodetype_config->get('node_types') : [],
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

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    if ($form_state->hasValue('node_types') && $form_state->hasValue('amptheme')) {
      $node_types = $form_state->getValue('node_types');
      $nodetype_config = $this->config('amp.settings');

      // Get a list of changes. The first time this form is accessed, this will
      // be empty because we will not know all of the node types.
      if (!empty($nodetype_config->get('node_types'))) {
        $changes = array_diff_assoc($node_types, $nodetype_config->get('node_types'));
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

      $nodetype_config->setData(['node_types' => $node_types]);
      $nodetype_config->save();

      $amptheme = $form_state->getValue('amptheme');
      $amptheme_config = $this->config('amp.theme');
      $amptheme_config->setData(['amptheme' => $amptheme]);
      $amptheme_config->save();

      parent::submitForm($form, $form_state);
    }
  }

}
