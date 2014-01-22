<?php
/**
 * @file
 * AddThis configuration for the Global settings.
 */

require_once __DIR__ . '/AddThisConfig.php';

class AddThisGlobalConfig extends AddThisConfig {

  const PROFILE_ID_KEY = 'addthis_profile_id';

  /**
   * Construct the Global configuration class.
   */
  public function __construct() {
    parent::__construct();

    self::addConfiguration(self::getProfileIdKey(), '');
    self::addConfiguration(self::getWidgetJsLoadTypeKey(), 'async');
    self::addConfiguration(self::getWidgetJsUrlKey(), 'http://s7.addthis.com/js/300/addthis_widget.js');
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


  /**
   * Get the Widget load type.
   */
  public function getWidgetJsLoadType() {
    return self::getValue(self::getWidgetJsLoadTypeKey());
  }

  /**
   * Set the Widget load type.
   */
  public function setWidgetJsLoadType($load_type) {
    return self::setValue(self::getWidgetJsLoadTypeKey(), $load_type);
  }

  /**
   * Get the configuration key Widget load type.
   */
  public function getWidgetJsLoadTypeKey() {
    return 'addthis_widget_load_type';
  }


  /**
   * Get the Widget js url.
   */
  public function getWidgetJsUrl() {
    return self::getValue(self::getWidgetJsUrlKey());
  }

  /**
   * Set the Widget js url.
   */
  public function setWidgetJsUrl($js_url) {
    return self::setValue(self::getWidgetJsUrlKey(), $js_url);
  }

  /**
   * Get the configuration key Widget js url.
   */
  public function getWidgetJsUrlKey() {
    return 'addthis_widget_js_url';
  }


}
