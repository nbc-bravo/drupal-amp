<?php

namespace Drupal\amp_replace\Form;

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
class AmpReplaceSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'amp_replace_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['amp_replace.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $amp_config = $this->config('amp_replace.settings');

    $form['amp_library_group']['test_page'] = array(
      '#type' => 'item',
      '#markup' => t('<a href=":url">Test that AMP is configured properly</a>', array(':url' => Url::fromRoute('amp.test_library_hello')->toString()))
    );

    $form['amp_library_group']['amp_library_process_full_html'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('<strong><em>Power User:</em> Run the whole HTML page through the AMP library</strong>'),
      '#default_value' => $amp_config->get('amp_library_process_full_html'),
      '#description' => $this->t('The AMP PHP library will fix many AMP HTML standard non-compliance issues by ' .
          'removing illegal or disallowed attributes, tags and property value pairs. This is useful for processing the output of modules that ' .
          'generate AMP unfriendly HTML. Please test when enabling on your site as some modules may depend on ' .
          'the HTML removed by the library and thus break in possibly subtle ways.')
    );

    $form['amp_library_group']['amp_library_process_statistics'] = array(
        '#type' => 'checkbox',
        '#title' => $this->t('<em>Statistics:</em> Add an <a href="https://www.drupal.org/files/issues/time_taken.png">HTML comment</a> at the end of Drupal page output indicating various performance statistics like time taken, number of tags processed etc.'),
        '#default_value' => $amp_config->get('amp_library_process_statistics'),
        '#states' => array('visible' => array(
            ':input[name="amp_library_process_full_html"]' => array('checked' => TRUE))
        ),
    );

    $form['amp_library_group']['amp_library_process_full_html_warnings'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('<em>Debugging:</em> Add a notice in the drupal log for each processed AMP page showing the AMP warnings (and fixes) generated'),
      '#default_value' => $amp_config->get('amp_library_process_full_html_warnings'),
      '#description' => $this->t('A Drupal log entry will be generated for <em>each</em> non-anonymous AMP request. ' .
          'However <em>anonymous</em> page requests will be cached by Drupal page_cache module and will not repeatedly call the AMP library.'),
      '#states' => array('visible' => array(
          ':input[name="amp_library_process_full_html"]' => array('checked' => TRUE))
      ),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $amp_config = $this->config('amp_replace.settings');

    $amp_config->set('amp_library_process_full_html', $form_state->getValue('amp_library_process_full_html'))->save();
    $amp_config->set('amp_library_process_full_html_warnings', $form_state->getValue('amp_library_process_full_html_warnings'))->save();
    $amp_config->set('amp_library_process_statistics', $form_state->getValue('amp_library_process_statistics'))->save();

    parent::submitForm($form, $form_state);
  }
}
