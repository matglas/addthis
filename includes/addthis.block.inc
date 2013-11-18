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
  // We miss the form and form_state.
  $form = array();
  $form_state = array();

  /*
  $form[AddThis::BLOCK_WIDGET_TYPE_KEY] = array(
    '#type' => 'select',
    '#title' => t('AddThis display type'),
    '#options' => AddThis::getInstance()->getDisplayTypes(),
    '#default_value' => AddThis::getInstance()->getBlockDisplayType(),
    '#attributes' => array('class' => array('addthis-display-type')),
  );
  */

  $form['addthis_settings'] = array(
    '#type' => 'fieldset',
    '#title' => 'Display settings',
  );

  // Create a addthis settings form based on the available configuration.
  $element = _addthis_settings_form(
    isset($form['addthis_settings']['form']) ? $form['addthis_settings']['form'] : array(),
    $form_state,
    isset($addthis_settings['type']) ? $addthis_settings['type'] : NULL,
    isset($addthis_settings['settings']) ? $addthis_settings['settings'] : NULL
  );
  $element['choose']['#submit'] = array('_addthis_settings_form_block_submit');
  $element['choose']['#ajax']['callback'] = '_addthis_settings_form_block_submit_callback';
  $form['addthis_settings']['form'] = $element;

  return $form;

  // @see field_ui_display_overview_form(). :975 - 1091
  //drupal_get_form($form_id);
  return $form;
}

/**
 * Implements hook_block_save().
 */
function addthis_block_save($delta = '', $edit = array()) {
  variable_set(AddThis::BLOCK_WIDGET_TYPE_KEY, $edit[AddThis::BLOCK_WIDGET_TYPE_KEY]);
}

function _addthis_settings_form_block_submit($form, &$form_state) {
  $form_state['rebuild'] = TRUE;
}

function _addthis_settings_form_block_submit_callback($form, &$form_state) {
  $form_state['rebuild'] = TRUE;

  // Look at submitted display type and change the form.
  if (isset($form_state['values']['addthis_settings']['type'])) {
    $type = $form_state['values']['addthis_settings']['type'];
    $element = _addthis_settings_form(
      isset($form['addthis_settings']['form']) ? $form['addthis_settings']['form'] : array(),
      $form_state
    );
    $element['choose']['#submit'] = array('_addthis_settings_form_block_submit');
    $element['choose']['#ajax']['callback'] = '_addthis_settings_form_block_submit_callback';
    $form['addthis_settings']['form'] = $element;
  }

  return $form['addthis_settings']['form'];
}
