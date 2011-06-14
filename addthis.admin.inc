<?php

/**
 * @file
 * Administrative page callbacks for the AddThis-module.
 */
 
/**
 * Implements hook_admin_settings_form().
 */
function addthis_admin_settings_form($form_state) {
  $form[AddThis::PROFILE_ID_KEY] = array(
    '#type' => 'textfield',
    '#title' => t('AddThis Profile ID'),
    '#default_value' => AddThis::getProfileId(),
    '#required' => FALSE,
    '#description' => t('Profile ID at <a href="http://addthis.com/" target="_blank">AddThis.com</a>. Required for statistics.'),
  );
  return system_settings_form($form);
}