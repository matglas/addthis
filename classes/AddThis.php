<?php
/**
 * @file
 * An AddThis-class.
 *
 * @author Jani Palsamäki
 */

class AddThis {

  const BLOCK_NAME = 'addthis_block';
  const DEFAULT_FORMATTER = 'addthis_default';
  const MODULE_NAME = 'addthis';
  const STYLE_KEY = 'addthis_style';

  // AddThis attribute and parameter names (as defined in AddThis APIs)
  const PROFILE_ID_QUERY_PARAMETER = 'pubid';
  const TITLE_ATTRIBUTE = 'addthis:title';

  // Persistent variable keys
  const BLOCK_WIDGET_TYPE_KEY = 'addthis_block_widget_type';
  const BOOKMARK_URL_KEY = 'addthis_bookmark_url';
  const ENABLED_SERVICES_KEY = 'addthis_enabled_services';
  const PROFILE_ID_KEY = 'addthis_profile_id';
  const SERVICES_JSON_URL_KEY = 'addthis_services_json_url';

  // External resources
  const DEFAULT_BOOKMARK_URL = 'http://www.addthis.com/bookmark.php?v=250';
  const DEFAULT_SERVICES_JSON_URL = 'http://cache.addthiscdn.com/services/v1/sharing.en.json';
  const SERVICES_CSS_URL = 'http://cache.addthiscdn.com/icons/v1/sprites/services.css';
  const WIDGET_JS_URL = 'http://s7.addthis.com/js/250/addthis_widget.js';

  // Internal resources
  const ADMIN_CSS_FILE = 'addthis.admin.css';
  const ADMIN_INCLUDE_FILE = 'addthis.admin.inc';

  // Widget types
  const WIDGET_TYPE_COMPACT_BUTTON = 'compact_button';
  const WIDGET_TYPE_DISABLED = 'disabled';
  const WIDGET_TYPE_LARGE_BUTTON = 'large_button';
  const WIDGET_TYPE_TOOLBOX = 'toolbox';
  const WIDGET_TYPE_SHARECOUNT = 'sharecount';

  // Markup constants
  const AMP_ENTITY = '&amp;';
  const HASH = '#';
  const HREF = 'href';

  public static function getWidgetTypes() {
    return array(
      self::WIDGET_TYPE_DISABLED => t('Disabled'),
      self::WIDGET_TYPE_COMPACT_BUTTON => t('Compact button'),
      self::WIDGET_TYPE_LARGE_BUTTON => t('Large button'),
      self::WIDGET_TYPE_TOOLBOX => t('Toolbox'),
      self::WIDGET_TYPE_SHARECOUNT => t('Sharecount'),
    );
  }

  public static function getBlockWidgetType() {
    return variable_get(self::BLOCK_WIDGET_TYPE_KEY, self::WIDGET_TYPE_COMPACT_BUTTON);
  }

  public static function getWidgetMarkup($widgetType = '', $entity = NULL) {
    switch ($widgetType) {
      case self::WIDGET_TYPE_LARGE_BUTTON:
        $markup =
          '<a class="addthis_button" '
          . self::getAddThisAttributesMarkup($entity)
          . MarkupGenerator::generateAttribute(self::HREF, self::getFullBookmarkUrl())
          . '><img src="http://s7.addthis.com/static/btn/v2/lg-share-en.gif" width="125" height="16" alt="'
          . t('Bookmark and Share')
          . '" style="border:0"/></a>'
          . self::getWidgetScriptElement();
        break;
      case self::WIDGET_TYPE_COMPACT_BUTTON:
        $markup =
          '<a class="addthis_button" '
          . self::getAddThisAttributesMarkup($entity)
          . MarkupGenerator::generateAttribute(self::HREF, self::getFullBookmarkUrl())
          . '><img src="http://s7.addthis.com/static/btn/sm-share-en.gif" width="83" height="16" alt="'
          . t('Bookmark and Share')
          . '" style="border:0"/></a>'
          . self::getWidgetScriptElement();
        break;
      case self::WIDGET_TYPE_TOOLBOX:
        $markup =
          '<div class="addthis_toolbox addthis_default_style"><a '
          . MarkupGenerator::generateAttribute(self::HREF, self::getFullBookmarkUrl())
          . ' class="addthis_button_compact" '
          . self::getAddThisAttributesMarkup($entity)
          . '>'
          . t('Share')
          . '</a><span class="addthis_separator">|</span>' 
          . '<a class="addthis_button_preferred_1" '
          . self::getAddThisAttributesMarkup($entity)
          . '></a>'
          . '<a class="addthis_button_preferred_2" '
          . self::getAddThisAttributesMarkup($entity)
          . '></a>'
          . '<a class="addthis_button_preferred_3" '
          . self::getAddThisAttributesMarkup($entity)
          . '></a>'
          . '<a class="addthis_button_preferred_4" '
          . self::getAddThisAttributesMarkup($entity)
          . '></a></div>'
          . self::getWidgetScriptElement();
        break;
      case self::WIDGET_TYPE_SHARECOUNT:
        $markup =
          '<div class="addthis_toolbox addthis_default_style"><a class="addthis_counter" '
          . self::getAddThisAttributesMarkup($entity)
          . '></a></div>'
          . self::getWidgetScriptElement();
        break;
      default:
        $markup = '';
        break;
    }
    return $markup;
  }

  public static function getProfileId() {
    return variable_get(AddThis::PROFILE_ID_KEY);
  }

  public static function getServicesJsonUrl() {
    return variable_get(AddThis::SERVICES_JSON_URL_KEY, self::DEFAULT_SERVICES_JSON_URL);
  }

  public static function getServiceOptions() {
    return self::getServices();
  }

  public static function getEnabledServiceOptions() {
    return self::getEnabledServices();
  }

  public static function addStylesheets() {
    drupal_add_css(self::SERVICES_CSS_URL, 'external');
    drupal_add_css(self::getAdminCssFilePath(), 'file');
  }

  public static function addConfigurationOptionsJs() {
    $enabledServices = self::getServiceNamesAsCommaSeparatedString();
    drupal_add_js("var addthis_config = {services_compact: '" . $enabledServices . "more'}", 'inline');
  }

  private static function getServiceNamesAsCommaSeparatedString() {
    $enabledServiceNames = array_values(self::getEnabledServices());
    $enabledServicesAsCommaSeparatedString = '';
    foreach ($enabledServiceNames as $enabledServiceName) {
      if ($enabledServiceName != '0') {
        $enabledServicesAsCommaSeparatedString .= $enabledServiceName . ',';
      }
    }
    return $enabledServicesAsCommaSeparatedString;
  }

  private static function getAdminCssFilePath() {
    return drupal_get_path('module', self::MODULE_NAME) . '/' . self::ADMIN_CSS_FILE;
  }

  private static function getServices() {
    $rows = array();
    $json = new Json();
    $services = $json->decode(self::getServicesJsonUrl());
    if ($services != NULL) {
      foreach ($services['data'] AS $service) {
        $serviceCode = check_plain($service['code']);
        $serviceName = check_plain($service['name']);
        $rows[$serviceCode] = '<span class="addthis_service_icon icon_' . $serviceCode . '"></span> ' . $serviceName;
      }
    }
    return $rows;
  }

  private static function getEnabledServices() {
    return variable_get(self::ENABLED_SERVICES_KEY, array());
  }

  public static function getBaseBookmarkUrl() {
    return variable_get(self::BOOKMARK_URL_KEY, self::DEFAULT_BOOKMARK_URL);
  }

  private static function getFullBookmarkUrl() {
    return self::getBaseBookmarkUrl() . self::getProfileIdQueryParameterPrefixedWithAmp();
  }

  private static function getProfileIdQueryParameter($prefix) {
    $profileId = self::getProfileId();
    return $profileId != NULL ? $prefix . self::PROFILE_ID_QUERY_PARAMETER . '=' . $profileId : '';
  }

  private static function getProfileIdQueryParameterPrefixedWithAmp() {
    return self::getProfileIdQueryParameter(self::AMP_ENTITY);
  }

  private static function getProfileIdQueryParameterPrefixedWithHash() {
    return self::getProfileIdQueryParameter(self::HASH);
  }

  private static function getAddThisAttributesMarkup($entity) {
    if (is_object($entity)) {
      return self::getAddThisTitleAttributeMarkup($entity) . ' ';
    }
    return '';
  }

  private static function getAddThisTitleAttributeMarkup($entity) {
    return MarkupGenerator::generateAttribute(self::TITLE_ATTRIBUTE, drupal_get_title() . ' - ' . check_plain($entity->title));
  }

  private static function getWidgetScriptElement() {
    return '<script type="text/javascript" src="' . self::getWidgetUrl() . '"></script>';
  }

  private static function getWidgetUrl() {
    return self::WIDGET_JS_URL . self::getProfileIdQueryParameterPrefixedWithHash();
  }
}
