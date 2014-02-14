<?php
/**
 * @file
 * AddThis configuration for the Global settings.
 */

class AddThisGlobalConfig extends AddThisConfig {

  /**
   * Construct the Global configuration class.
   */
  public function __construct() {
    parent::__construct();

    self::addConfiguration(self::getProfileIdKey(), '');
  }

  /**
   * Get the Profile ID.
   */
  public function getProfileId() {
    return self::getValue(self::getProfileIdKey());
  }

  /**
   * Set the Profile ID.
   */
  public function setProfileId($profile_id) {
    return self::setValue(self::getProfileIdKey(), $profile_id);
  }

  /**
   * Get the configuration key Profile ID.
   */
  public function getProfileIdKey() {
    return 'addthis_profile_id';
  }

}
