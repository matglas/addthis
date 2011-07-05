<?php

/**
 * @file
 * Block related hook implementations for the AddThis-module.
 */

/**
 * Implements hook_block_info().
 */
function addthis_block_info() {
  $blockInfo = array();
  $blockInfo[AddThis::BLOCK_NAME] = array(
    'info' => t('An AddThis-block.'),
    'cache' => DRUPAL_NO_CACHE,
  );
  return $blockInfo;
}

/**
 * Implements hook_block_view().
 */
function addthis_block_view($blockName = '') {
  if ($blockName == AddThis::BLOCK_NAME) {
    $widgetType = AddThis::getBlockWidgetType();
    $markup = AddThis::getWidgetMarkup($widgetType);
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
  $form[AddThis::BLOCK_WIDGET_TYPE_KEY] = array(
    '#type' => 'select',
    '#title' => t('AddThis widget type'),
    '#options' => AddThis::getWidgetTypes(),
    '#default_value' => AddThis::getBlockWidgetType(),
  );
  return $form;
}

/**
 * Implements hook_block_save().
 */
function addthis_block_save($delta = '', $edit = array()) {
  variable_set(AddThis::BLOCK_WIDGET_TYPE_KEY, $edit[AddThis::BLOCK_WIDGET_TYPE_KEY]);
}
