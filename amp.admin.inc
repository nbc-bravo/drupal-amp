<?php

/**
 * @file
 * Administrative page callbacks for the AMP module.
 */

/**
 * Page callbak: Displays AMP configuration options.
 *
 * @see amp_menu().
 */
function amp_admin() {
  $form = array();

  $form['google_analytics_id'] = array(
    '#title' => t('Google Analytics Web Property ID'),
    '#type' => 'textfield',
    '#default_value' => variable_get('google_analytics_id', 'UA-'),
    '#size' => 15,
    '#maxlength' => 20,
    '#description' => t('This ID is unique to each site you want to track separately, and is in the form of UA-xxxxxxx-yy. To get a Web Property ID, <a href="@analytics">register your site with Google Analytics</a>, or if you already have registered your site, go to your Google Analytics Settings page to see the ID next to every site profile. <a href="@webpropertyid">Find more information in the documentation</a>.', array('@analytics' => 'http://www.google.com/analytics/', '@webpropertyid' => url('https://developers.google.com/analytics/resources/concepts/gaConceptsAccounts', array('fragment' => 'webProperty')))),
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
