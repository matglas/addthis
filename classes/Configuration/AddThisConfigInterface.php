<?php
/**
 * @file
 * Interface to define the get and set of configuration properties.
 */

interface AddThisConfigInterface {

  /**
   * Get the list of properties that are in set.
   */
  public function getProperties();

  public function addProperty($property_name, $default_value, $settings = array());

  public function getProperty($property_name);

  public function get($property_name);

  public function set($property_name, $value);

}