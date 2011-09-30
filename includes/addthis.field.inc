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
    AddThis::FIELD_TYPE => array(
      'label' => t('AddThis'),
      'description' => t('This field stores addthis settings in the database.'),
      'settings' => array('max_length' => 255),
      'instance_settings' => array(
        AddThis::STYLE_KEY => AddThis::WIDGET_TYPE_COMPACT_BUTTON
       ),
      'default_widget' => AddThis::WIDGET_TYPE,
      'default_formatter' => AddThis::WIDGET_TYPE_DISABLED,
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
  $formatters = array();

  // Create a formatter element for all the default style types we have.
  foreach (AddThis::getInstance()->getWidgetTypes() as $key => $label) {
    $formatters[$key] = array(
      'label' => $label,
      'field types' => array(AddThis::FIELD_TYPE)
    );
  }

  return $formatters;
}

/**
 * Implementation to retrieve formatters for a certain type of field.
 */
function addthis_field_info_formatter_field_type($field_type = NULL) {
  $formatters = field_info_formatter_types();
  foreach($formatters as $key => $formatter) {
    if (!in_array((!isset($field_type) ? AddThis::FIELD_TYPE : $field_type), $formatter['field types'])) {
      unset($formatters[$key]);
    }
  }
  return $formatters;
}

/**
 * Implements hook_field_formatter_view().
 */
function addthis_field_formatter_view($entity_type, $entity, $field, $instance, $langcode, $items, $display) {
  // We have only one item to display, the share item.
  // Get the markup for the share and add that to the $element[0] to display.
  $element = array();
  $display_type = $display['type'];
  $markup = AddThis::getInstance()->getDisplayMarkup($display_type, $entity);
  if (!isset($element[0])) {
    $element[0] = $markup;
  }
  return $element;
}

/**
 * Implementation of hook_field_prepare_view().
 *
 * This make sure we don't miss node that have no saved value on the AddThis
 * field. We need the value to be able to render something.
 */
function addthis_field_prepare_view($entity_type, $entities, $field, $instances, $langcode, &$items) {
  $dummy_value = "Don't mind this value. Is't a placeholder.";
  foreach($items as $key => $item) {
    if (!isset($item[0]['value'])) {
      $items[$key][0]['value'] = $dummy_value;
    }
  }
}


/**
 * Implements hook_field_presave().
 */
function addthis_field_presave($entity_type, $entity, $field, $instance, $langcode, &$items) {
  $dummy_value = "Don't mind this value. Is't a placeholder.";
  // We don't add items in the instance form therefore we check for a item
  // if not there we (which is the case almost always) we add it. This item
  // is need for the field_formatter in $items to attach a renderable item to.
  if (count($items) == 0) {
    $items += array(array('value' => $dummy_value));
  }
  else {
    foreach ($items as $delta => $value) {
      $items[$delta]['value'] = $dummy_value;
    }
  }
}

/**
 * Implements hook_field_widget_info().
 */
function addthis_field_widget_info() {
  return array(
    AddThis::WIDGET_TYPE => array(
      'label' => t('AddThis button'),
      'field types' => array(AddThis::FIELD_TYPE),
    ),
  );
}

/**
 * Implements hook_field_widget_form().
 */
function addthis_field_widget_form(&$form, &$form_state, $field, $instance, $langcode, $items, $delta, $element) {

  // @todo Write explaining comments
  if ($instance['widget']['type'] == AddThis::WIDGET_TYPE) {
    $main_widget = $element + array(
      '#access' => FALSE,
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
