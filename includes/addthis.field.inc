<?php

/**
 * @file
 * Field related hook implementations for the AddThis-module.
 */
 
/**
 * Implements hook_field_info().
 *
 * Field settings:
 *   - max_length: the maximum length for a varchar field.
 *
 * Instance settings:
 *   - AddThis::STYLE_KEY: The style of AddThis widget to render for this field.
 */
function addthis_field_info() {
  return array(
    'addthis' => array(
      'label' => t('AddThis'),
      'description' => t('This field stores addthis settings in the database.'),
      'settings' => array('max_length' => 255),
      'instance_settings' => array(AddThis::STYLE_KEY => AddThis::WIDGET_TYPE_COMPACT_BUTTON),
      'default_widget' => 'addthis_button',
      'default_formatter' => AddThis::DEFAULT_FORMATTER,
    ),
  );
}

/**
 * Implements hook_field_is_empty().
 */
function addthis_field_is_empty($item, $field) {
  return empty($item['value']) && (string) $item['value'] !== '0';
}

/**
 * Implements hook_field_formatter_info().
 */
function addthis_field_formatter_info() {
  return array(
    AddThis::DEFAULT_FORMATTER => array(
      'label' => t('Default'),
      'field types' => array('addthis'),
    ),
  );
}

/**
 * Implements hook_field_formatter_view().
 */
function addthis_field_formatter_view($entity_type, $entity, $field, $instance, $langcode, $items, $display) {
  $element = array();

  foreach ($items as $delta => $item) {
    $widgetType = $item['value'];
    $markup = AddThis::getWidgetMarkup($widgetType, $entity);
    $element[$delta] = array('#markup' => $markup);
  }
  return $element;
}

/**
 * Implements hook_field_widget_info().
 */
function addthis_field_widget_info() {
  return array(
    'addthis_button' => array(
      'label' => t('AddThis button'),
      'field types' => array('addthis'),
    ),
  );
}

/**
 * Implements hook_field_widget_form().
 */
function addthis_field_widget_form(&$form, &$form_state, $field, $instance, $langcode, $items, $delta, $element) {

  if ($instance['widget']['type'] == 'addthis_button') {
    $main_widget = $element + array(
      '#type' => 'select',
      '#options' => AddThis::getWidgetTypes(),
      '#default_value' => isset($items[$delta]) ? $items[$delta] : $instance['settings'][AddThis::STYLE_KEY],
    );
    $element['value'] = $main_widget;
  }

  return $element;
}

/**
 * Implements hook_field_widget_error().
 */
function addthis_field_widget_error($element, $error, $form, &$form_state) {
  switch ($error['error']) {
    default:
      $error_element = $element[$element['#columns'][0]];
      break;
  }

  form_error($error_element, $error['message']);
}