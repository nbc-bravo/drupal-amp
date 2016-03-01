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

  $form['google_analytics_id'] = array(
    '#title' => t('Google Analytics Web Property ID'),
    '#type' => 'textfield',
    '#default_value' => variable_get('google_analytics_id', 'UA-'),
    '#size' => 15,
    '#maxlength' => 20,
    '#description' => t('This ID is unique to each site you want to track separately, and is in the form of UA-xxxxxxx-yy. To get a Web Property ID, <a href="@analytics">register your site with Google Analytics</a>, or if you already have registered your site, go to your Google Analytics Settings page to see the ID next to every site profile. <a href="@webpropertyid">Find more information in the documentation</a>.', array('@analytics' => 'http://www.google.com/analytics/', '@webpropertyid' => url('https://developers.google.com/analytics/resources/concepts/gaConceptsAccounts', array('fragment' => 'webProperty')))),
  );

  $form['google_adsense_id'] = array(
    '#type' => 'textfield',
    '#title' => t('Google AdSense Publisher ID'),
    '#default_value' => variable_get('google_adsense_id', 'pub-'),
    '#maxlength' => 25,
    '#size' => 20,
    '#description' => t('This is the Google AdSense Publisher ID for the site owner. Get this in your Google Adsense account. It should be similar to pub-9999999999999'),
  );

  $form['google_doubleclick_id'] = array(
    '#type' => 'textfield',
    '#title' => t('Google DoubleClick for Publishers Network ID'),
    '#default_value' => variable_get('google_doubleclick_id', '/'),
    '#maxlength' => 25,
    '#size' => 20,
    '#description' => t('The Network ID to use on all tags. This value should begin with a /.'),
  );

  $form['test_page'] = array(
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
  if (!preg_match('/^UA-\d+-\d+$/', $form_state['values']['google_analytics_id'])) {
    form_set_error('google_analytics_id', t('A valid Google Analytics Web Property ID is case sensitive and formatted like UA-xxxxxxx-yy.'));
  }
  // Validate the Google Adsense ID.
  if (!preg_match('/^pub-[0-9]+$/', $form_state['values']['google_adsense_id'])) {
    form_set_error('google_adsense_id', t('A valid Google AdSense Publisher ID is case sensitive and formatted like pub-9999999999999'));
  }
}

/**
 * Form submission handler for amp_admin_form().
 *
 * @see amp_admin_form_validate()
 */
function amp_admin_form_submit($form, &$form_state) {
  // Make sure everything from cached nodes to link tags are rebuilt.
  drupal_flush_all_caches();
}
