<?php

/**
 * @file
 * Administrative page callbacks for the AddThis-module.
 */
 
/**
 * Implements hook_admin_settings_form().
 */
function addthis_admin_settings_form($form_state) {
  AddThis::addStylesheets();
  $form[AddThis::PROFILE_ID_KEY] = array(
    '#type' => 'textfield',
    '#title' => t('AddThis Profile ID'),
    '#default_value' => AddThis::getProfileId(),
    '#required' => FALSE,
    '#description' => t('Profile ID at <a href="http://addthis.com/" target="_blank">AddThis.com</a>. Required for statistics.'),
  );
  $form[AddThis::BOOKMARK_URL_KEY] = array(
    '#type' => 'textfield',
    '#title' => t('AddThis bookmark URL'),
    '#default_value' => AddThis::getBaseBookmarkUrl(),
    '#required' => TRUE,
    '#description' => t('AddThis bookmark URL.'),
  );
  $form[AddThis::SERVICES_CSS_URL_KEY] = array(
    '#type' => 'textfield',
    '#title' => t('AddThis services stylesheet URL'),
    '#default_value' => AddThis::getServicesCssUrl(),
    '#required' => TRUE,
    '#description' => t('AddThis services stylesheet URL.'),
  );
  $form[AddThis::SERVICES_JSON_URL_KEY] = array(
    '#type' => 'textfield',
    '#title' => t('AddThis services json URL'),
    '#default_value' => AddThis::getServicesJsonUrl(),
    '#required' => TRUE,
    '#description' => t('AddThis services json URL.'),
  );
  $form[AddThis::WIDGET_JS_URL_KEY] = array(
    '#type' => 'textfield',
    '#title' => t('AddThis javascript widget URL'),
    '#default_value' => AddThis::getBaseWidgetJsUrl(),
    '#required' => TRUE,
    '#description' => t('AddThis javascript widget URL.'),
  );
  $form[AddThis::ENABLED_SERVICES_KEY] = array(
    '#type' => 'checkboxes',
    '#title' => t('Enabled services'),
    '#options' => AddThis::getServiceOptions(),
    '#default_value' => AddThis::getEnabledServiceOptions(),
    '#required' => FALSE,
    '#description' => t('Check enabled services.'),
  );
  return system_settings_form($form);
}