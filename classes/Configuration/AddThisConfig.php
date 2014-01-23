<?php
/**
 * @file
 * Abstract configuration class to manage settings through a class.
 */

abstract class AddThisConfig {

  protected $configuration = array();

  /**
   * Contruct the Config object.
   */
  public function __construct() {

  }

  /**
   * Add configuration value.
   */
  protected function addConfiguration($key, $default) {
    $this->configuration[$key] = array(
      'value' => variable_get($key, $default),
      'default' => $default,
    );
  }

  /**
   * Reset the value to the default.
   */
  public function resetValue($key) {
    $setting = $this->configuration[$key];

    $setting['value'] = $setting['default'];
    variable_set($key, $settings['default']);
  }

  /**
   * Set the value of a configuration key.
   */
  public function setValue($key, $value) {
    if (isset($this->configuration[$key])) {
      $setting = &$this->configuration[$key];

      $settings['value'];
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Return the value from configuration key.
   */
  public function getValue($key) {
    $setting = $this->configuration[$key];
    return $setting['value'];
  }

  /**
   * Get the keys of configurations.
   */
  public function getConfigurationKeys() {
    return array_keys($this->configuration);
  }

}
