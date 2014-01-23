<?php
/**
 * @file
 * Interface for implementation of new formatters.
 */

interface AddThisDisplayInterface {

  /**
   * Get the label of the display.
   */
  public function getLabel();

  /**
   * Get the machine name for this display.
   */
  public function getName();

  /**
   * Get the default settings for this display.
   *
   * @return array
   *   The array with default settings.
   */
  public function getDefaultSettings();

  /**
   * Get the settings form for the display.
   *
   * @return array
   *   The form array used by FormAPI.
   */
  public function getSettingsForm($field, $instance, $view_mode, $form, &$form_state);

  /**
   * Get the render array.
   *
   * @return array
   *   The render array usable by drupal_render.
   */
  public function getMarkup($options);

}
