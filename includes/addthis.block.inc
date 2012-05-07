<?php

/**
 * @file
 * Block related hook implementations for the AddThis-module.
 */

/**
 * Implements hook_block_info().
 */
function addthis_block_info() {
  $block_info = array();
  $block_info[AddThis::BLOCK_NAME] = array(
    'info' => t('AddThis'),
    'cache' => DRUPAL_NO_CACHE,
  );
  return $block_info;
}

/**
 * Implements hook_block_view().
 *
 * #block key allows alter hooks to react on this information. Someone might
 * want to change this specific display.
 */
function addthis_block_view($block_name = '') {
  if ($block_name == AddThis::BLOCK_NAME) {
    $widget_type = AddThis::getInstance()->getBlockDisplayType();
    $markup = AddThis::getInstance()->getDisplayMarkup($widget_type, array('#block' => AddThis::BLOCK_NAME));
    return array(
      'subject' => '',
      'content' => $markup,
    );
  }
}

/**
 * Implements hook_block_configure().
 */
function addthis_block_configure($delta = '') {
  $form = array();
  $form_state = array();

  $form[AddThis::BLOCK_WIDGET_TYPE_KEY] = array(
    '#type' => 'select',
    '#title' => t('AddThis display type'),
    '#options' => AddThis::getInstance()->getDisplayTypes(),
    '#default_value' => AddThis::getInstance()->getBlockDisplayType(),
    '#attributes' => array('class' => array('addthis-display-type')),
  );
  
  dpm('block_config');
  $form['addthis'] = array();
  $form['addthis'] += _addthis_block_display_type_form($form, $form_state, AddThis::getInstance()->getBlockDisplayType());
  $form['addthis']['#prefix'] = '<div id="addthis-display-type-settings">';
  $form['addthis']['#suffix'] = '</div>';
  
  // @see field_ui_display_overview_form:975 - 1091
  //drupal_get_form($form_id);
  return $form;
}

function _addthis_block_display_type_form($form, &$form_state, $display_type) {
  dpm($form, 'start');
  $new_form = array();
  
  module_load_include('inc', 'field_ui', 'field_ui.admin');
  $name = 'addthis_settings';

  /*
  $formatter_options = AddThis::getInstance()->getDisplayTypes();
  $new_form[$name] = array(
    'type' => array(
      '#type' => 'select',
      '#title' => t('Formatter for @title', array('@title' => 'AddThis block')),
      '#title_display' => 'invisible',
      '#options' => $formatter_options,
      '#default_value' => $display_type,
      '#parents' => array('settings', AddThis::BLOCK_WIDGET_TYPE_KEY),
      '#attributes' => array('class' => array('addthis-display-type')),
    ),
    'settings_edit_form' => array(),
  );
  */

  // Formatter settings.

  // Check the currently selected formatter, and merge persisted values for
  // formatter settings.
  dpm($form_state, 'form_state');
  if (isset($form_state['values'][AddThis::BLOCK_WIDGET_TYPE_KEY])) {
    $formatter_type = $form_state['values'][AddThis::BLOCK_WIDGET_TYPE_KEY];
  }
  else {
    $formatter_type = $display_type;
  }

  // Get the settings if set.
  if (isset($form_state['values']['settings'][$name]['settings'])) {
    $settings = $form_state['values']['settings'][$name]['settings'];
  }
  else {
    $settings = array();
  }
  $settings += field_info_formatter_settings($formatter_type);
  dpm($settings, 'settings');

  $field = array();
  $instance = array();
  $view_mode = 'block';

  $instance['display'][$view_mode]['type'] = $formatter_type;
  $formatter = field_info_formatter_types($formatter_type);
  $instance['display'][$view_mode]['module'] = $formatter['module'];
  $instance['display'][$view_mode]['settings'] = $settings;

  // Base button element for the various formatter settings actions.
  $base_button = array(
    '#submit' => array('addthis_block_display_type_submit'),
    '#ajax' => array(
      'callback' => 'addthis_block_display_type_submit',
      'wrapper' => 'addthis-display-type-settings',
      'effect' => 'fade',
    ),
    '#field_name' => $name,
  );

  // We are currently editing this field's formatter settings. Display the
  // settings form and submit buttons.
  $new_form[$name]['settings_edit_form'] = array();

  $settings_form = array();
  $function = $formatter['module'] . '_field_formatter_settings_form';
  if (function_exists($function)) {
    $settings_form = $function($field, $instance, $view_mode, $new_form, $form_state);
  }
  dpm($settings_form, 'settings_form');

  if ($settings_form) {
    $new_form[$name]['settings_edit_form'] = array(
      '#tree' => TRUE,
      '#type' => 'container',
      '#attributes' => array('class' => array('addhis-display-settings-form')),
      '#parents' => array('settings', $name),
      'label' => array(
        '#markup' => t('Format settings:') . ' <span class="formatter-name">' . $formatter['label'] . '</span>',
      ),
      'settings' => $settings_form,
      'actions' => array(
        '#type' => 'actions',
        'save_settings' => $base_button + array(
          '#type' => 'submit',
          '#name' => $name . '_settings_update',
          '#value' => t('Update'),
          '#op' => 'update',
        ),
      ),
    );
    $new_form[$name]['#attributes']['class'][] = 'addthis-display-settings-editing';
  }
  
 return $new_form;
}

function addthis_block_display_type_submit($form, &$form_state) {
  $trigger = $form_state['triggering_element'];
  $op = $trigger['#op'];
  
  dpm('ajax form');
  $form['rebuild'] = TRUE;
  return _addthis_block_display_type_form($form, $form_state, NULL);
}

function addthis_block_display_type_submit_js($form, &$form_state) {
  dpm($form_state['values'], 'values');
  $form['rebuild'] = TRUE;
  return $form;
}

/**
 * Implements hook_block_save().
 */
function addthis_block_save($delta = '', $edit = array()) {
  dpm('block_save');
  variable_set(AddThis::BLOCK_WIDGET_TYPE_KEY, $edit[AddThis::BLOCK_WIDGET_TYPE_KEY]);
}
