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

  $display_test = 'addthis_basic_button';
  $display_test = 'addthis_basic_toolbox';
  $form['addthis_test'] = array();
  $form['addthis_test'] += _addthis_block_display_type_form($form, $form_state, $display_test);

  // @see field_ui_display_overview_form(). :975 - 1091
  //drupal_get_form($form_id);
  return $form;
}

function _addthis_block_display_type_form($form, &$form_state, $display_type) {
  $new_form = array();

  if (isset($form_state)) {
    $new_form['test'] = array(
      '#markup' => 'new:' . $display_type,
    );
  } else {
    $new_form['test'] = array(
      '#markup' => 'update:' . $display_type,
    );
  }

  /*
  module_load_include('inc', 'field_ui', 'field_ui.admin');
  $name = 'addthis_settings';

  $list_types = FALSE;
  if ($list_types) {
    $formatter_options = AddThis::getInstance()->getDisplayTypes();
    $new_form = array(
      'type' => array(
        '#type' => 'select',
        '#title' => t('Formatter for @title', array('@title' => 'AddThis block')),
        '#title_display' => 'invisible',
        '#options' => $formatter_options,
        '#default_value' => $display_type,
        '#parents' => array('settings', AddThis::BLOCK_WIDGET_TYPE_KEY),
        '#attributes' => array('class' => array('addthis-display-type')),
      ),
      // The settings elements will be added here.
      'settings_edit_form' => array(),
    );
  }

  // Formatter settings.

  // Check the currently selected formatter, and merge persisted values for
  // formatter settings.
  if (isset($form_state['values'][AddThis::BLOCK_WIDGET_TYPE_KEY])) {
    $formatter_type = $form_state['values'][AddThis::BLOCK_WIDGET_TYPE_KEY];
  }
  else {
    $formatter_type = $display_type;
  }
  dpm($form_state, 'form_state:' . $formatter_type);

  // Get the settings if set.
  if (isset($form_state['values'][$name])) {
    $settings = $form_state['values'][$name];
    dpm($settings, 'settings:from:values');
  }
  else {
    $settings = array();
    dpm($settings, 'settings:null');
  }
  $formatter_settings = field_info_formatter_settings($formatter_type);
  dpm($formatter_settings, 'settings:formatter only');
  $settings += $formatter_settings;
  dpm($settings, 'settings:plus formatter');

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
      'callback' => 'addthis_block_display_type_submit_js',
      'wrapper' => 'addthis-display-type-settings',
      'effect' => 'fade',
    ),
    '#field_name' => $name,
  );

  // We are currently editing this field's formatter settings. Display the
  // settings form and submit buttons.
  $new_form['settings_edit_form'] = array();

  $settings_form = array();
  $function = $formatter['module'] . '_field_formatter_settings_form';
  if (function_exists($function)) {
    $settings_form = $function($field, $instance, $view_mode, $new_form, $form_state);
  }
  dpm($settings_form, 'settings_form');

  if ($settings_form) {
    $new_form['settings_edit_form'] = array(
      '#tree' => TRUE,
      '#type' => 'container',
      '#attributes' => array('class' => array('addhis-display-settings-form')),
      '#parents' => array($name),
      'label' => array(
        '#markup' => t('Format settings:') . ' <span class="formatter-name">' . $formatter['label'] . '</span>',
      ),
      'settings' => $settings_form,
      'actions' => array(
        '#type' => 'actions',
        'save_settings' => $base_button + array(
          '#type' => 'submit',
          '#name' => $name . '_update',
          '#value' => t('Update'),
          '#op' => 'update',
        ),
      ),
      '#prefix' => '<div id="addthis-display-type-settings">',
      '#suffix' => '</div>'
    );
    $new_form['settings_edit_form']['#attributes']['class'][] = 'addthis-display-settings-editing';
  }
  */

  // Base button element for the various formatter settings actions.
  $base_button = array(
    '#submit' => array('addthis_block_display_type_submit'),
    '#ajax' => array(
      'callback' => 'addthis_block_display_type_submit_js',
      'wrapper' => 'addthis-display-type-settings',
      'effect' => 'fade',
    ),
  );
  $new_form['#theme_wrappers'] = array('container');
  $new_form['#id'] = 'addthis-display-type-settings';
  $new_form['actions'] = array(
    '#type' => 'actions',
    'save_settings' => $base_button + array(
      '#type' => 'submit',
      '#name' => 'update',
      '#value' => t('Update'),
      '#op' => 'update',
    )
  );

  return $new_form;
}

function addthis_block_display_type_submit($form, &$form_state) {
  $form['rebuild'] = TRUE;
  return $form;
}

function addthis_block_display_type_submit_js($form, &$form_state) {
  $triggering_element = isset($form_state['triggering_element']) ? $form_state['triggering_element'] : NULL;

  dpm($form_state);
  if (isset($triggering_element) && $triggering_element['#op'] == 'update') {
    $new_display_type = $form_state['values'][AddThis::BLOCK_WIDGET_TYPE_KEY];
    $form['addthis_test'] = _addthis_block_display_type_form($form, $form_state, $new_display_type);
    dpm(_addthis_block_display_type_form($form, $form_state, $new_display_type));
  }
  return $form['addthis_test'];
}

/**
 * Implements hook_block_save().
 */
function addthis_block_save($delta = '', $edit = array()) {
  variable_set(AddThis::BLOCK_WIDGET_TYPE_KEY, $edit[AddThis::BLOCK_WIDGET_TYPE_KEY]);
}
