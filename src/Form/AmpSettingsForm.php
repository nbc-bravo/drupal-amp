<?php

/**
 * @file
 * Contains \Drupal\amp\Form\AmpSettingsForm.
 */

namespace Drupal\amp\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;

/**
 * Defines the configuration export form.
 */
class AmpSettingsForm extends ConfigFormBase {

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
    return ['amp.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('amp.settings');

    $node_types = node_type_get_names();

    $form['node_types'] = array(
      '#type' => 'checkboxes',
      '#multiple' => TRUE,
      '#title' => $this->t('Select nodes that have AMP versions by default:'),
      '#default_value' => $config->get('node_types'),
      '#options' => $node_types,
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    if ($form_state->hasValue('node_types')) {
      $node_types = $form_state->getValue('node_types');
      $config = $this->config('amp.settings');
      $config->setData(['node_types' => $node_types]);
      $config->save();

      parent::submitForm($form, $form_state);
    }
  }

}
