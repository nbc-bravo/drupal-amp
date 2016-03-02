<?php

/**
 * @file
 * Administrative page callbacks for the AMP module.
 */

/**
 * Form constructor for the AMP administration form.
 *
 * @ingroup forms
 * @see amp_admin_form_validate()
 * @see amp_admin_form_submit()
 */
function amp_admin_form($form, &$form_state) {
  $form = array();
  // AMP theme settings.
  $form['amp_theme'] = array(
    '#type' => 'select',
    '#options' => _amp_get_theme_options(),
    '#title' => t('AMP theme'),
    '#description' => t('Choose a theme to use for AMP pages.'),
    '#default_value' => variable_get('amp_theme', 'ampsubtheme_example'),
  );
  $form['amp_google_analytics_id'] = array(
    '#title' => t('Google Analytics Web Property ID'),
    '#type' => 'textfield',
    '#default_value' => variable_get('amp_google_analytics_id'),
    '#size' => 15,
    '#maxlength' => 20,
    '#description' => t('This ID is unique to each site you want to track separately, and is in the form of UA-xxxxxxx-yy. To get a Web Property ID, <a href="@analytics">register your site with Google Analytics</a>, or if you already have registered your site, go to your Google Analytics Settings page to see the ID next to every site profile. <a href="@webpropertyid">Find more information in the documentation</a>.', array('@analytics' => 'http://www.google.com/analytics/', '@webpropertyid' => url('https://developers.google.com/analytics/resources/concepts/gaConceptsAccounts', array('fragment' => 'webProperty')))),
  );
  $form['amp_google_adsense_id'] = array(
    '#type' => 'textfield',
    '#title' => t('Google AdSense Publisher ID'),
    '#default_value' => variable_get('amp_google_adsense_id'),
    '#maxlength' => 25,
    '#size' => 20,
    '#description' => t('This is the Google AdSense Publisher ID for the site owner. Get this in your Google Adsense account. It should be similar to pub-9999999999999'),
  );
  $form['amp_google_doubleclick_id'] = array(
    '#type' => 'textfield',
    '#title' => t('Google DoubleClick for Publishers Network ID'),
    '#default_value' => variable_get('amp_google_doubleclick_id', '/'),
    '#maxlength' => 25,
    '#size' => 20,
    '#description' => t('The Network ID to use on all tags. This value should begin with a /.'),
  );
  $form['pixel_group'] = array(
    '#type' => 'fieldset',
    '#title' => t('amp-pixel'),
  );
  $form['pixel_group']['amp_pixel'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable amp-pixel'),
    '#default_value' => variable_get('amp_pixel'),
    '#description' => t('The amp-pixel element is meant to be used as a typical tracking pixel -- to count page views. Find more information in the <a href="https://www.ampproject.org/docs/reference/amp-pixel.html">amp-pixel documentation</a>.'),
  );
  $form['pixel_group']['amp_pixel_domain_name'] = array(
    '#type' => 'textfield',
    '#title' => t('amp-pixel domain name'),
    '#default_value' => variable_get('amp_pixel_domain_name'),
    '#description' => t('The domain name where the tracking pixel will be loaded: do not include http or https.'),
    '#states' => array('visible' => array(
      ':input[name="amp_pixel"]' => array('checked' => TRUE))
    ),
  );
  $form['pixel_group']['amp_pixel_query_string'] = array(
    '#type' => 'textfield',
    '#title' => t('amp-pixel query path'),
    '#default_value' => variable_get('amp_pixel_query_string'),
    '#description' => t('The path at the domain where the GET request will be received, e.g. "pixel" in example.com/pixel?RANDOM.'),
    '#states' => array('visible' => array(
      ':input[name="amp_pixel"]' => array('checked' => TRUE))
    ),
  );
  $form['pixel_group']['amp_pixel_random_number'] = array(
    '#type' => 'checkbox',
    '#title' => t('Random number'),
    '#default_value' => variable_get('amp_pixel_random_number'),
    '#description' => t('Use the special string RANDOM to add a random number to the URL if required. Find more information in the <a href="https://github.com/ampproject/amphtml/blob/master/spec/amp-var-substitutions.md#random">amp-pixel documentation</a>.'),
    '#states' => array('visible' => array(
      ':input[name="amp_pixel"]' => array('checked' => TRUE))
    ),
  );
  $form['amp_test_page'] = array(
    '#type' => 'item',
    '#markup' => t('<a href="@url">Test that AMP is configured properly</a>', array('@url' => '/admin/amp/library/test')),
  );
  $form['amp_library_warnings_display'] = array(
    '#type' => 'checkbox',
    '#title' => t('<em>debugging</em>: show amp library warnings in all amp text formatters for all users'),
    '#default_value' => variable_get('amp_library_warnings_display'),
    '#description' => t('if you only want to see amp formatter specific warning for one node add query "warnfix" at end of a node url. e.g. <strong>node/12345/amp?warnfix</strong>'),
  );

  return system_settings_form($form);
}

/**
 * Form validation handler for amp_admin_form().
 *
 * @see amp_admin_form_submit()
 */
function amp_admin_form_validate($form, &$form_state) {
  // Validate the Google Analytics ID.
  if (!empty($form_state['values']['amp_google_analytics_id'])) {
    if (!preg_match('/^UA-\d+-\d+$/', $form_state['values']['amp_google_analytics_id'])) {
      form_set_error('amp_google_analytics_id', t('A valid Google Analytics Web Property ID is case sensitive and formatted like UA-xxxxxxx-yy.'));
    }
  }
  // Validate the Google Adsense ID.
  if (!empty($form_state['values']['amp_google_adsense_id'])) {
    if (!preg_match('/^pub-[0-9]+$/', $form_state['values']['amp_google_adsense_id'])) {
      form_set_error('amp_google_adsense_id', t('A valid Google AdSense Publisher ID is case sensitive and formatted like pub-9999999999999'));
    }
  }
}

/**
 * Helper function to get available theme options.
 */
function _amp_get_theme_options() {
  // Get all available themes.
  $themes = system_rebuild_theme_data();
  uasort($themes, '_amp_sort_theme_by_info_name');
  $theme_options = array();

  foreach ($themes as &$theme) {
    // Do not show hidden themes.
    if (!empty($theme->info['hidden'])) {
      continue;
    }

    // Do not show disabled themes.
    if (!empty($theme->status)) {
      $theme_options[$theme->name] = $theme->info['name'];
    }
  }

  return $theme_options;
}

/**
 * Array sorting callback; sorts themes by their name.
 */
function _amp_sort_theme_by_info_name($a, $b) {
  return strcasecmp($a->info['name'], $b->info['name']);
}
