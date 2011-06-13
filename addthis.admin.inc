<?php

/**
 * @file
 * Administrative page callbacks for the AddThis-module.
 */
 
/**
 * Implements hook_admin_settings_form().
 */
function addthis_admin_settings_form($form_state) {
  $form['addthis_username'] = array(
    '#type' => 'textfield',
    '#title' => t('AddThis username'),
    '#default_value' => variable_get('addthis_username', ''),
    '#required' => TRUE,
    '#description' => t('Username at <a href="http://addthis.com/" target="_blank">AddThis.com</a>. Required for statistics.'),
  );
  return system_settings_form($form);
}