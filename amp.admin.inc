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
