<?php

namespace Drupal\amp_pixel\Form;

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
class AmpPixelSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'amp_pixel_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['amp_pixel.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('amp_pixel.settings');
    $form['amp_pixel'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Enable amp-pixel'),
      '#default_value' => $config->get('amp_pixel'),
      '#description' => $this->t('The amp-pixel element is meant to be used as a typical tracking pixel -- to count page views. Find more information in the <a href="https://www.ampproject.org/docs/reference/amp-pixel.html">amp-pixel documentation</a>.'),
    );
    $form['amp_pixel_domain_name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('amp-pixel domain name'),
      '#default_value' => $config->get('amp_pixel_domain_name'),
      '#description' => $this->t('The domain name where the tracking pixel will be loaded: do not include http or https.'),
      '#states' => array('visible' => array(
        ':input[name="amp_pixel"]' => array('checked' => TRUE))
      ),
    );
    $form['amp_pixel_query_string'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('amp-pixel query path'),
      '#default_value' => $config->get('amp_pixel_query_string'),
      '#description' => $this->t('The path at the domain where the GET request will be received, e.g. "pixel" in example.com/pixel?RANDOM.'),
      '#states' => array('visible' => array(
        ':input[name="amp_pixel"]' => array('checked' => TRUE))
      ),
    );
    $form['amp_pixel_random_number'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Random number'),
      '#default_value' => $config->get('amp_pixel_random_number'),
      '#description' => $this->t('Use the special string RANDOM to add a random number to the URL if required. Find more information in the <a href="https://github.com/ampproject/amphtml/blob/master/spec/amp-var-substitutions.md#random">amp-pixel documentation</a>.'),
      '#states' => array('visible' => array(
        ':input[name="amp_pixel"]' => array('checked' => TRUE))
      ),
    );

    return parent::buildForm($form, $form_state);
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $config = $this->config('amp_pixel.settings');
    $config->set('amp_pixel', $form_state->getValue('amp_pixel'))->save();
    $config->set('amp_pixel_domain_name', $form_state->getValue('amp_pixel_domain_name'))->save();
    $config->set('amp_pixel_query_string', $form_state->getValue('amp_pixel_query_string'))->save();
    $config->set('amp_pixel_random_number', $form_state->getValue('amp_pixel_random_number'))->save();

    parent::submitForm($form, $form_state);
  }
}
