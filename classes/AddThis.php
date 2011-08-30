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
  const DEFAULT_NUMBER_OF_PREFERRED_SERVICES = 4;
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
  const ADDRESSBOOK_ENABLED_KEY = 'addthis_addressbook_enabled';
  const BLOCK_WIDGET_TYPE_KEY = 'addthis_block_widget_type';
  const BOOKMARK_URL_KEY = 'addthis_bookmark_url';
  const CLICKBACK_TRACKING_ENABLED_KEY = 'addthis_clickback_tracking_enabled';
  const CO_BRAND_KEY = 'addthis_co_brand';
  const COMPLIANT_508_KEY = 'addthis_508_compliant';
  const CUSTOM_CONFIGURATION_CODE_ENABLED_KEY = 'addthis_custom_configuration_code_enabled';
  const CUSTOM_CONFIGURATION_CODE_KEY = 'addthis_custom_configuration_code';
  const ENABLED_SERVICES_KEY = 'addthis_enabled_services';
  const LARGE_ICONS_ENABLED_KEY = 'addthis_large_icons_enabled';
  const NUMBER_OF_PREFERRED_SERVICES_KEY = 'addthis_number_of_preferred_services';
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

  /* @var AddThisConfigurationGenerator */
  private $addThisConfigurationGenerator;

  /* @var Json */
  private $json;

  /* @var MarkupGenerator */
  private $markupGenerator;

  /**
   * @return AddThis
   */
  public static function getInstance() {
    if (!isset(self::$instance)) {
      $addThis = new AddThis();
      $addThis->setAddThisConfigurationGenerator(new AddThisConfigurationGenerator());
      $addThis->setJson(new Json());
      $addThis->setMarkupGenerator(new MarkupGenerator());
      self::$instance = $addThis;
    }
    return self::$instance;
  }

  public function setAddThisConfigurationGenerator(AddThisConfigurationGenerator $addThisConfigurationGenerator) {
    $this->addThisConfigurationGenerator = $addThisConfigurationGenerator;
  }

  public function setJson(Json $json) {
    $this->json = $json;
  }

  public function setMarkupGenerator(MarkupGenerator $markupGenerator) {
    $this->markupGenerator = $markupGenerator;
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
    $markup = '';
    if (self::WIDGET_TYPE_LARGE_BUTTON == $widgetType) {
      $markup = $this->getLargeButtonWidgetMarkup($entity);
    } elseif (self::WIDGET_TYPE_COMPACT_BUTTON == $widgetType) {
      $markup = $this->getCompactButtonWidgetMarkup($entity);
    } elseif (self::WIDGET_TYPE_TOOLBOX == $widgetType) {
      $markup = $this->getToolboxWidgetMarkup($entity);
    } elseif (self::WIDGET_TYPE_SHARECOUNT == $widgetType) {
      $markup = $this->getSharecountWidgetMarkup($entity);
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

  public function getServices() {
    $rows = array();
    $services = $this->json->decode($this->getServicesJsonUrl());
    if (!empty($services)) {
      foreach ($services['data'] AS $service) {
        $serviceCode = check_plain($service['code']);
        $serviceName = check_plain($service['name']);
        $rows[$serviceCode] = '<span class="addthis_service_icon icon_' . $serviceCode . '"></span> ' . $serviceName;
      }
    }
    return $rows;
  }

  public function getEnabledServices() {
    return variable_get(self::ENABLED_SERVICES_KEY, array());
  }

  public function addStylesheets() {
    drupal_add_css($this->getServicesCssUrl(), 'external');
    drupal_add_css($this->getAdminCssFilePath(), 'file');
  }

  public function addWidgetJs() {
    drupal_add_js(self::getWidgetUrl(), array('type' => 'external', 'scope' => 'footer'));
  }

  public function addConfigurationOptionsJs() {
    if ($this->isCustomConfigurationCodeEnabled()) {
      $javascript = $this->getCustomConfigurationCode();
    } else {
      $enabledServices = $this->getServiceNamesAsCommaSeparatedString();
      $javascript = "var addthis_config = {services_compact: '" . $enabledServices . "more'"
        . $this->addThisConfigurationGenerator->generate('ui_header_color', $this->getUiHeaderColor())
        . $this->addThisConfigurationGenerator->generate('ui_header_background', $this->getUiHeaderBackgroundColor())
        . $this->addThisConfigurationGenerator->generate('ui_cobrand', $this->getCoBrand())
        . $this->addThisConfigurationGenerator->generate('ui_508_compliant', $this->get508Compliant())
        . $this->addThisConfigurationGenerator->generate('data_track_clickback', $this->isClickbackTrackingEnabled())
        . $this->addThisConfigurationGenerator->generate('ui_use_addressbook', $this->isAddressbookEnabled())
        . '}'
      ;
    }
    drupal_add_js($javascript, array('type' => 'inline'));
  }

  public function areLargeIconsEnabled() {
    return variable_get(self::LARGE_ICONS_ENABLED_KEY, TRUE);
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

  public function getNumberOfPreferredServices() {
    return variable_get(self::NUMBER_OF_PREFERRED_SERVICES_KEY, self::DEFAULT_NUMBER_OF_PREFERRED_SERVICES);
  }

  public function getCoBrand() {
    return variable_get(self::CO_BRAND_KEY);
  }

  public function get508Compliant() {
    return variable_get(self::COMPLIANT_508_KEY, FALSE);
  }

  public function isClickbackTrackingEnabled() {
    return variable_get(self::CLICKBACK_TRACKING_ENABLED_KEY, FALSE);
  }

  public function isAddressbookEnabled() {
    return variable_get(self::ADDRESSBOOK_ENABLED_KEY, FALSE);
  }

  private function getLargeButtonWidgetMarkup($entity) {
    return '<a class="addthis_button" '
           . $this->getAddThisAttributesMarkup($entity)
           . $this->markupGenerator->generateAttribute('href', self::getFullBookmarkUrl())
           . '><img src="http://s7.addthis.com/static/btn/v2/lg-share-en.gif" width="125" height="16" alt="'
           . t('Bookmark and Share')
           . '" style="border:0"/></a>';
  }

  private function getCompactButtonWidgetMarkup($entity) {
    return '<a class="addthis_button" '
           . self::getAddThisAttributesMarkup($entity)
           . $this->markupGenerator->generateAttribute('href', $this->getFullBookmarkUrl())
           . '><img src="http://s7.addthis.com/static/btn/sm-share-en.gif" width="83" height="16" alt="'
           . t('Bookmark and Share')
           . '" style="border:0"/></a>';
  }

  private function getToolboxWidgetMarkup($entity) {
    $markup = '<div class="addthis_toolbox addthis_default_style '
           . $this->getLargeButtonsClass()
           . '" '
           . $this->getAddThisAttributesMarkup($entity)
           . '><a '
           . $this->markupGenerator->generateAttribute('href', $this->getFullBookmarkUrl())
           . ' class="addthis_button_compact"></a>';

    $numberOfPreferredServices = self::getNumberOfPreferredServices();

    for ($i = 1; $i <= $numberOfPreferredServices; $i++) {
      $markup .= "<a class=\"addthis_button_preferred_$i\"></a>";
    }

    $markup .= '</div>';

    return $markup;
  }

  private function getSharecountWidgetMarkup($entity) {
    return '<div class="addthis_toolbox addthis_default_style"><a class="addthis_counter" '
           . $this->getAddThisAttributesMarkup($entity)
           . '></a></div>';
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

  private function getFullBookmarkUrl() {
    return check_url($this->getBaseBookmarkUrl() . $this->getProfileIdQueryParameterPrefixedWithAmp());
  }

  private function getProfileIdQueryParameter($prefix) {
    $profileId = $this->getProfileId();
    return !empty($profileId) ? $prefix . self::PROFILE_ID_QUERY_PARAMETER . '=' . $profileId : '';
  }

  private function getProfileIdQueryParameterPrefixedWithAmp() {
    return $this->getProfileIdQueryParameter('&');
  }

  private function getProfileIdQueryParameterPrefixedWithHash() {
    return $this->getProfileIdQueryParameter('#');
  }

  private function getWidgetUrl() {
    return check_url($this->getBaseWidgetJsUrl() . $this->getProfileIdQueryParameterPrefixedWithHash());
  }
}
