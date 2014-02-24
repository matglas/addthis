<?php
/**
 * @file
 * AddThis configuration for the Global settings.
 */

class AddThisGlobalConfig extends AddThisConfigVariables {

  /**
   * Construct the Global configuration class.
   */
  public function __construct() {
    parent::__construct();

    self::addProperty('ProfileId', '', array('variable_key' => 'addthis_profile_id'));
  }

}
