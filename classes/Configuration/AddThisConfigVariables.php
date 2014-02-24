<?php
/**
 * @file
 * Abstract configuration class to manage settings through a class that works
 * with the variable store.
 */

abstract class AddThisConfigVariables implements AddThisConfigInterface {

  protected $properties = array();

  /**
   * Contruct the Config object.
   */
  public function __construct() {

  }

  /**
   * Get all properties.
   */
  public function getProperties() {
    return $this->properties;
  }

  /**
   * Add configuration value.
   */
  public function addProperty($key, $default, $settings = array()) {

    // If we dont have a variable_key we can not proceed.
    if (!isset($settings['variable_key'])) {
      throw new Exception(
        format_string(
          'Variable key is not provided for Property \'!key\'',
          array('!key' => $key)
        )
      );
    }

    // Set the property array.
    $this->properties[$key] = array(
      'value' => variable_get($settings['variable_key'], $default),
      'default' => $default,
      'settings' => $settings,
    );
  }

  /**
   * Get all the info about a property.
   */
  public function getProperty($key) {
    // Set the property array.
    return $this->properties[$key];
  }

  /**
   * Get a configuration setting by the method name.
   */
  public function getPropertyByKey($property_key) {
    return $this->properties[$property_key];
  }

  /**
   * Reset the value to the default.
   */
  public function resetValue($key) {
    $setting = $this->properties[$key];

    $setting['value'] = $setting['default'];
    variable_set($settings['variable_key'], $settings['default']);
  }

  /**
   * Set the value of a configuration key.
   */
  public function set($key, $value) {
    if (isset($this->properties[$key])) {
      $property = &$this->properties[$key];

      $property['value'] = $value;
      variable_set($property['settings']['variable_key'], $value);
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Return the value from configuration key.
   */
  public function get($key) {
    $setting = $this->properties[$key];
    return $setting['value'];
  }

  /**
   * Get the keys of configurations.
   */
  public function getPropertyKeys() {
    return array_keys($this->properties);
  }

  /**
   * Catch missing function definitions.
   *
   * http://www.garfieldtech.com/blog/magical-php-call
   */
  public function __call($method, $argument) {
    $method_key = $this->parseMethod($method, 3);
    $config_info = $this->getPropertyByKey($method_key);

    // If end with Key and start with get.
    if (substr($method, strlen($method) - 1, 3) == 'Key' && strpos($method, 'get') === 0) {
      return $method_key;
    }
    // If only starts with get.
    elseif (strpos($method, 'get') === 0) {
      return $this->get($method_key);
    }
    // If only starts with set.
    elseif (strpos($method, 'set') === 0) {
      return $this->set($method_key, $argument);
    }
  }

  /**
   * Parse the method name for a method_key.
   */
  private function parseMethod($method, $start, $minus = 0) {
    $name = substr($method, $minus);
    $name = substr($name, $start);

    return $name;
  }

}
