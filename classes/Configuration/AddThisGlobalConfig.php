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
    self::addProperty('EnabledServices', '', array('variable_key' => 'addthis_enabled_services'));


    // http://support.addthis.com/customer/portal/articles/1337994-the-addthis_config-variable#.UwtVFB-M4y4

    // services_exclude
    // services_compact
    // services_expanded
    // services_custom (split off)

    // ui_click
    // ui_delay
    // ui_hover_direction
    // ui_language
    // ui_offset_top
    // ui_offset_left
    // ui_header_color
    // ui_header_background
    // ui_cobrand
    // ui_use_css
    // ui_use_addressbook
    // ui_508_compliant
    // data_track_clickback
    // data_ga_tracker
  // }

}
