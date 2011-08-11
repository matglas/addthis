<?php
/**
 * @file
 * An AddThis-class.
 *
 * @author Jani Palsamäki
 */

class AddThis {

  const BLOCK_NAME = 'addthis_block';
  const DEFAULT_CUSTOM_CONFIGURATION_CODE = 'var addthis_config = {}';
  const DEFAULT_FORMATTER = 'addthis_default_formatter';
  const FIELD_TYPE = 'addthis';
  const MODULE_NAME = 'addthis';
  const PERMISSION_ADMINISTER_ADDTHIS = 'administer addthis';
  const PERMISSION_ADMINISTER_ADVANCED_ADDTHIS = 'administer advanced addthis';
  const STYLE_KEY = 'addthis_style';
  const WIDGET_TYPE = 'addthis_button_widget';

  // AddThis attribute and parameter names (as defined in AddThis APIs)
  const PROFILE_ID_QUERY_PARAMETER = 'pubid';
  const TITLE_ATTRIBUTE = 'addthis:title';

  // Persistent variable keys
  const BLOCK_WIDGET_TYPE_KEY = 'addthis_block_widget_type';
  const BOOKMARK_URL_KEY = 'addthis_bookmark_url';
  const CUSTOM_CONFIGURATION_CODE_ENABLED_KEY = 'addthis_custom_configuration_code_enabled';
  const CUSTOM_CONFIGURATION_CODE_KEY = 'addthis_custom_configuration_code';
  const ENABLED_SERVICES_KEY = 'addthis_enabled_services';
  const LARGE_ICONS_ENABLED_KEY = 'addthis_large_icons_enabled';
  const PROFILE_ID_KEY = 'addthis_profile_id';
  const SERVICES_CSS_URL_KEY = 'addthis_services_css_url';
  const SERVICES_JSON_URL_KEY = 'addthis_services_json_url';
  const UI_HEADER_BACKGROUND_COLOR_KEY = 'addthis_ui_header_background_color';
  const UI_HEADER_COLOR_KEY = 'addthis_ui_header_color';
  const WIDGET_JS_URL_KEY = 'addthis_widget_js_url';

  // External resources
  const DEFAULT_BOOKMARK_URL = 'http://www.addthis.com/bookmark.php?v=250';
  const DEFAULT_SERVICES_CSS_URL = 'http://cache.addthiscdn.com/icons/v1/sprites/services.css';
  const DEFAULT_SERVICES_JSON_URL = 'http://cache.addthiscdn.com/services/v1/sharing.en.json';
  const DEFAULT_WIDGET_JS_URL = 'http://s7.addthis.com/js/250/addthis_widget.js';

  // Internal resources
  const ADMIN_CSS_FILE = 'addthis.admin.css';
  const ADMIN_INCLUDE_FILE = 'includes/addthis.admin.inc';

  // Widget types
  const WIDGET_TYPE_COMPACT_BUTTON = 'compact_button';
  const WIDGET_TYPE_DISABLED = 'disabled';
  const WIDGET_TYPE_LARGE_BUTTON = 'large_button';
  const WIDGET_TYPE_SHARECOUNT = 'sharecount';
  const WIDGET_TYPE_TOOLBOX = 'toolbox';

  private static $instance;

  private $json;
  private $markupGenerator;

  // Private constructor
  private function __construct() {
    $this->json = new Json();
    $this->markupGenerator = new MarkupGenerator();
  }

  public static function getInstance() {
    if (!isset(self::$instance)) {
      $class = __CLASS__;
      self::$instance = new $class;
    }
    return self::$instance;
  }

  public function getWidgetTypes() {
    return array(
      self::WIDGET_TYPE_DISABLED => t('Disabled'),
      self::WIDGET_TYPE_COMPACT_BUTTON => t('Compact button'),
      self::WIDGET_TYPE_LARGE_BUTTON => t('Large button'),
      self::WIDGET_TYPE_TOOLBOX => t('Toolbox'),
      self::WIDGET_TYPE_SHARECOUNT => t('Sharecount'),
    );
  }

  public function getBlockWidgetType() {
    return variable_get(self::BLOCK_WIDGET_TYPE_KEY, self::WIDGET_TYPE_COMPACT_BUTTON);
  }

  public function getWidgetMarkup($widgetType = '', $entity = NULL) {
    $href = 'href';
    switch ($widgetType) {
      case self::WIDGET_TYPE_LARGE_BUTTON:
        $markup =
          '<a class="addthis_button" '
          . $this->getAddThisAttributesMarkup($entity)
          . $this->markupGenerator->generateAttribute($href, self::getFullBookmarkUrl())
          . '><img src="http://s7.addthis.com/static/btn/v2/lg-share-en.gif" width="125" height="16" alt="'
          . t('Bookmark and Share')
          . '" style="border:0"/></a>'
          . self::getWidgetScriptElement();
        break;
      case self::WIDGET_TYPE_COMPACT_BUTTON:
        $markup =
          '<a class="addthis_button" '
          . self::getAddThisAttributesMarkup($entity)
          . $this->markupGenerator->generateAttribute($href, $this->getFullBookmarkUrl())
          . '><img src="http://s7.addthis.com/static/btn/sm-share-en.gif" width="83" height="16" alt="'
          . t('Bookmark and Share')
          . '" style="border:0"/></a>'
          . $this->getWidgetScriptElement();
        break;
      case self::WIDGET_TYPE_TOOLBOX:
        $markup =
          '<div class="addthis_toolbox addthis_default_style'
          . $this->getLargeButtonsClass()
          . '"><a '
          . $this->markupGenerator->generateAttribute($href, $this->getFullBookmarkUrl())
          . ' class="addthis_button_compact" '
          . $this->getAddThisAttributesMarkup($entity)
          . '></a>'
          //. '<a class="addthis_button_facebook_like"></a>'
          . '<a class="addthis_button_preferred_1" '
          . $this->getAddThisAttributesMarkup($entity)
          . '></a>'
          . '<a class="addthis_button_preferred_2" '
          . $this->getAddThisAttributesMarkup($entity)
          . '></a>'
          . '<a class="addthis_button_preferred_3" '
          . $this->getAddThisAttributesMarkup($entity)
          . '></a>'
          . '<a class="addthis_button_preferred_4" '
          . $this->getAddThisAttributesMarkup($entity)
          . '></a></div>'
          . $this->getWidgetScriptElement();
        break;
      case self::WIDGET_TYPE_SHARECOUNT:
        $markup =
          '<div class="addthis_toolbox addthis_default_style"><a class="addthis_counter" '
          . $this->getAddThisAttributesMarkup($entity)
          . '></a></div>'
          . $this->getWidgetScriptElement();
        break;
      default:
        $markup = '';
        break;
    }
    return $markup;
  }

  public function getProfileId() {
    return check_plain(variable_get(AddThis::PROFILE_ID_KEY));
  }

  public function getServicesCssUrl() {
    return check_url(variable_get(AddThis::SERVICES_CSS_URL_KEY, self::DEFAULT_SERVICES_CSS_URL));
  }

  public function getServicesJsonUrl() {
    return check_url(variable_get(AddThis::SERVICES_JSON_URL_KEY, self::DEFAULT_SERVICES_JSON_URL));
  }

  public function getServiceOptions() {
    return $this->getServices();
  }

  public function getEnabledServiceOptions() {
    return $this->getEnabledServices();
  }

  public function addStylesheets() {
    drupal_add_css($this->getServicesCssUrl(), 'external');
    drupal_add_css($this->getAdminCssFilePath(), 'file');
  }

  public function addConfigurationOptionsJs() {
    if ($this->isCustomConfigurationCodeEnabled()) {
      $javascript = $this->getCustomConfigurationCode();
    } else {
      $enabledServices = $this->getServiceNamesAsCommaSeparatedString();
      $javascript =
        "var addthis_config = {services_compact: '" . $enabledServices . "more'"
        . $this->getUiHeaderColorConfigurationOptions()
        . '}';
    }
    drupal_add_js($javascript, 'inline');
  }

  public function areLargeIconsEnabled() {
    return variable_get(self::LARGE_ICONS_ENABLED_KEY, FALSE);
  }

  public function getUiHeaderColor() {
    return check_plain(variable_get(self::UI_HEADER_COLOR_KEY));
  }

  public function getUiHeaderBackgroundColor() {
    return check_plain(variable_get(self::UI_HEADER_BACKGROUND_COLOR_KEY));
  }

  public function getCustomConfigurationCode() {
    return variable_get(self::CUSTOM_CONFIGURATION_CODE_KEY, self::DEFAULT_CUSTOM_CONFIGURATION_CODE);
  }

  public function isCustomConfigurationCodeEnabled() {
    return variable_get(self::CUSTOM_CONFIGURATION_CODE_ENABLED_KEY, FALSE);
  }

  public function getBaseWidgetJsUrl() {
    return check_url(variable_get(self::WIDGET_JS_URL_KEY, self::DEFAULT_WIDGET_JS_URL));
  }

  public function getBaseBookmarkUrl() {
    return check_url(variable_get(self::BOOKMARK_URL_KEY, self::DEFAULT_BOOKMARK_URL));
  }

  private function getUiHeaderColorConfigurationOptions() {
    $configurationOptions = ',';
    $uiHeaderColor = $this->getUiHeaderColor();
    $uiHeaderBackgroundColor = $this->getUiHeaderBackgroundColor();
    if ($uiHeaderColor != NULL) {
      $configurationOptions .= "ui_header_color: '$uiHeaderColor'";
    }
    if ($uiHeaderBackgroundColor != NULL) {
      $configurationOptions .= ", ui_header_background: '$uiHeaderBackgroundColor'";
    }
    return $configurationOptions;
  }

  private function getLargeButtonsClass() {
    return $this->areLargeIconsEnabled() ? ' addthis_32x32_style ' : '';
  }

  private function getServiceNamesAsCommaSeparatedString() {
    $enabledServiceNames = array_values($this->getEnabledServices());
    $enabledServicesAsCommaSeparatedString = '';
    foreach ($enabledServiceNames as $enabledServiceName) {
      if ($enabledServiceName != '0') {
        $enabledServicesAsCommaSeparatedString .= $enabledServiceName . ',';
      }
    }
    return $enabledServicesAsCommaSeparatedString;
  }

  private function getAdminCssFilePath() {
    return drupal_get_path('module', self::MODULE_NAME) . '/' . self::ADMIN_CSS_FILE;
  }

  private function getServices() {
    $rows = array();
    $services = $this->json->decode($this->getServicesJsonUrl());
    if ($services != NULL) {
      foreach ($services['data'] AS $service) {
        $serviceCode = check_plain($service['code']);
        $serviceName = check_plain($service['name']);
        $rows[$serviceCode] = '<span class="addthis_service_icon icon_' . $serviceCode . '"></span> ' . $serviceName;
      }
    }
    return $rows;
  }

  private function getEnabledServices() {
    return variable_get(self::ENABLED_SERVICES_KEY, array());
  }

  private function getFullBookmarkUrl() {
    return check_url($this->getBaseBookmarkUrl() . $this->getProfileIdQueryParameterPrefixedWithAmp());
  }

  private function getProfileIdQueryParameter($prefix) {
    $profileId = $this->getProfileId();
    return $profileId != NULL ? $prefix . $this->PROFILE_ID_QUERY_PARAMETER . '=' . $profileId : '';
  }

  private function getProfileIdQueryParameterPrefixedWithAmp() {
    return $this->getProfileIdQueryParameter('&');
  }

  private function getProfileIdQueryParameterPrefixedWithHash() {
    return $this->getProfileIdQueryParameter('#');
  }

  private function getAddThisAttributesMarkup($entity) {
    if (is_object($entity)) {
      return $this->getAddThisTitleAttributeMarkup($entity) . ' ';
    }
    return '';
  }

  private function getAddThisTitleAttributeMarkup($entity) {
    return $this->markupGenerator->generateAttribute(
      self::TITLE_ATTRIBUTE, drupal_get_title() . ' - ' . check_plain($entity->title)
    );
  }

  private function getWidgetScriptElement() {
    return '<script type="text/javascript" src="' . $this->getWidgetUrl() . '"></script>';
  }

  private function getWidgetUrl() {
    return check_url($this->getBaseWidgetJsUrl() . $this->getProfileIdQueryParameterPrefixedWithHash());
  }
}
