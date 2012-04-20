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
  $form['addthis'][AddThis::BLOCK_WIDGET_TYPE_KEY] = array(
    '#type' => 'select',
    '#title' => t('AddThis display type'),
    '#options' => AddThis::getInstance()->getDisplayTypes(),
    '#default_value' => AddThis::getInstance()->getBlockDisplayType(),
    '#attributes' => array('class' => array('addthis-display-type')),
  );
  $form['addthis'][] = _addthis_block_display_type_form(AddThis::getInstance()->getBlockDisplayType());
  $form['addthis']['#prefix'] = '<div id="addthis-display-type-settings">';
  $form['addthis']['#suffix'] = '</div>';
  
  // @see field_ui_display_overview_form:975 - 1091
  //drupal_get_form($form_id);
  return $form;
}

function _addthis_block_display_type_form($display_type) {
    module_load_include('inc', 'field_ui', 'field_ui.admin');
    $name = 'addthis_block';

    /*
    $table['addthis']['format'] = array(
      'type' => array(
        '#type' => 'select',
        '#title' => t('Formatter for @title', array('@title' => 'Addthis block')),
        '#title_display' => 'invisible',
        '#options' => AddThis::getInstance()->getDisplayTypes(),
        '#default_value' => $display_type,
        '#parents' => array('fields', 'addthis', 'type'),
        '#attributes' => array('class' => array('field-formatter-type')),
      ),
      'settings_edit_form' => array(),
    );
    */

    // Formatter settings.

    // Check the currently selected formatter, and merge persisted values for
    // formatter settings.
    $formatter_type = $display_type;
    $settings = field_info_formatter_settings($formatter_type);

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

    $field = array();
    $form = array();

    //if ($form_state['formatter_settings_edit'] == $name) {
    if (TRUE) {
      // We are currently editing this field's formatter settings. Display the
      // settings form and submit buttons.
      $table[$name]['format']['settings_edit_form'] = array();

      $settings_form = array();
      $function = 'addthis_displays_field_formatter_settings_form';
      if (function_exists($function)) {
        //$settings_form = $function($field, $instance, $view_mode, $form, $form_state);
        $settings_form = $function($field, $instance, $view_mode, $form, $form_state);
      }

      if ($settings_form) {
        $table[$name]['format']['#cell_attributes'] = array('colspan' => 3);
        $table[$name]['format']['settings_edit_form'] = array(
          '#type' => 'container',
          '#attributes' => array('class' => array('field-formatter-settings-edit-form')),
          '#parents' => array('fields', $name, 'settings_edit_form'),
          'label' => array(
            '#markup' => t('Format settings:') . ' <span class="formatter-name">' . $formatter['label'] . '</span>',
          ),
          'settings' => $settings_form,
          'actions' => array(
            '#type' => 'actions',
            'save_settings' => $base_button + array(
              '#type' => 'submit',
              '#name' => $name . '_formatter_settings_update',
              '#value' => t('Update'),
              '#op' => 'update',
            ),
            'cancel_settings' => $base_button + array(
              '#type' => 'submit',
              '#name' => $name . '_formatter_settings_cancel',
              '#value' => t('Cancel'),
              '#op' => 'cancel',
              // Do not check errors for the 'Cancel' button, but make sure we
              // get the value of the 'formatter type' select.
              '#limit_validation_errors' => array(array('fields', $name, 'type')),
            ),
          ),
        );
        $table[$name]['#attributes']['class'][] = 'field-formatter-settings-editing';
      }
    }
    return $table;
}

function addthis_block_display_type_submit($form, &$form_state) {
  $trigger = $form_state['triggering_element'];
  $op = $trigger['#op'];
  
  //$form = array('#markup' => 'Finished');
  
  return $form;
}

function addthis_block_display_type_submit_js($form, &$form_state) {
  dpm($form);
  //$form = array('#markup' => 'Finished');
  return $form;
}

/**
 * Implements hook_block_save().
 */
function addthis_block_save($delta = '', $edit = array()) {
  variable_set(AddThis::BLOCK_WIDGET_TYPE_KEY, $edit[AddThis::BLOCK_WIDGET_TYPE_KEY]);
}
