<?php
/**
 * @file
 * An AddThis-class.
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
  const URL_ATTRIBUTE = 'addthis:url';

  // Persistent variable keys
  const ADDRESSBOOK_ENABLED_KEY = 'addthis_addressbook_enabled';
  const BLOCK_WIDGET_TYPE_KEY = 'addthis_block_widget_type';
  const BOOKMARK_URL_KEY = 'addthis_bookmark_url';
  const CLICKBACK_TRACKING_ENABLED_KEY = 'addthis_clickback_tracking_enabled';
  const CLICK_TO_OPEN_COMPACT_MENU_ENABLED_KEY = 'addthis_click_to_open_compact_menu_enabled';
  const CO_BRAND_KEY = 'addthis_co_brand';
  const COMPLIANT_508_KEY = 'addthis_508_compliant';
  const CUSTOM_CONFIGURATION_CODE_ENABLED_KEY = 'addthis_custom_configuration_code_enabled';
  const CUSTOM_CONFIGURATION_CODE_KEY = 'addthis_custom_configuration_code';
  const ENABLED_SERVICES_KEY = 'addthis_enabled_services';
  const FACEBOOK_LIKE_ENABLED_KEY = 'addthis_facebook_like_enabled';
  const GOOGLE_PLUS_ONE_ENABLED_KEY = 'addthis_google_plus_one_enabled';
  const LARGE_ICONS_ENABLED_KEY = 'addthis_large_icons_enabled';
  const NUMBER_OF_PREFERRED_SERVICES_KEY = 'addthis_number_of_preferred_services';
  const OPEN_WINDOWS_ENABLED_KEY = 'addthis_open_windows_enabled';
  const PROFILE_ID_KEY = 'addthis_profile_id';
  const SERVICES_CSS_URL_KEY = 'addthis_services_css_url';
  const SERVICES_JSON_URL_KEY = 'addthis_services_json_url';
  const STANDARD_CSS_ENABLED_KEY = 'addthis_standard_css_enabled';
  const TWITTER_ENABLED_KEY = 'addthis_twitter_enabled';
  const UI_DELAY_KEY = 'addthis_ui_delay';
  const UI_HEADER_BACKGROUND_COLOR_KEY = 'addthis_ui_header_background_color';
  const UI_HEADER_COLOR_KEY = 'addthis_ui_header_color';
  const WIDGET_JS_URL_KEY = 'addthis_widget_js_url';
  const WIDGET_JS_ASYNC = 'addthis_widget_async';

  // External resources
  const DEFAULT_BOOKMARK_URL = 'http://www.addthis.com/bookmark.php?v=250';
  const DEFAULT_SERVICES_CSS_URL = 'http://cache.addthiscdn.com/icons/v1/sprites/services.css';
  const DEFAULT_SERVICES_JSON_URL = 'http://cache.addthiscdn.com/services/v1/sharing.en.json';
  const DEFAULT_WIDGET_JS_URL = 'http://s7.addthis.com/js/250/addthis_widget.js';
  const DEFAULT_WIDGET_JS_ASYNC = TRUE;

  // Internal resources
  const ADMIN_CSS_FILE = 'addthis.admin.css';
  const ADMIN_INCLUDE_FILE = 'includes/addthis.admin.inc';

  // Widget types
  const WIDGET_TYPE_COMPACT_BUTTON = 'addthis_compact_button';
  const WIDGET_TYPE_DISABLED = 'addthis_disabled';
  const WIDGET_TYPE_LARGE_BUTTON = 'addthis_large_button';
  const WIDGET_TYPE_SHARECOUNT = 'addthis_sharecount';
  const WIDGET_TYPE_TOOLBOX = 'addthis_toolbox';

  // Styles
  const CSS_32x32 = 'addthis_32x32_style';

  private static $instance;

  /* @var AddThisJson */
  private $json;

  /**
   * @return AddThis
   */
  public static function getInstance() {
    if (!isset(self::$instance)) {
      $addThis = new AddThis();
      $addThis->setJson(new AddThisJson());
      self::$instance = $addThis;
    }
    return self::$instance;
  }

  public function setJson(AddThisJson $json) {
    $this->json = $json;
  }

  //@TODO: Refactor all WidgetType names into DisplayType
  // A widget type is the element shown to edit a value.
  // We use the widget as a type way to define the way to Display.
  // Therefore we need to keep a good descriptive name as DisplayType.

  /*
   * Get all the DisplayTypes that are available.
   */
  public function getWidgetTypes() {
    return array(
      self::WIDGET_TYPE_DISABLED => t('Disabled'),
      self::WIDGET_TYPE_COMPACT_BUTTON => t('Compact button'),
      self::WIDGET_TYPE_LARGE_BUTTON => t('Large button'),
      self::WIDGET_TYPE_TOOLBOX => t('Toolbox'),
      self::WIDGET_TYPE_SHARECOUNT => t('Sharecount'),
    );
  }

  /*
   * Return me the markup for a certain display type.
   *
   * Variables contains #entity and #settings as keys when applicable.
   * When #entity is not there we link to the current url. When #settings
   * is not there we use the default settings.
   */
  public function getDisplayMarkup($display, $options = array()) {
    $formatters = addthis_field_info_formatter_field_type();

    // When we have the entity and entity_type we can send it to the url.
    if (isset($options['#entity']) && isset($options['#entity_type'])) {
      // See if we can create the url and send it through a hook so others
      // can play with it.
      $uri = entity_uri($options['#entity_type'], $options['#entity']);
      $uri['options'] += array(
        'absolute' => TRUE
      );
      // Add hook here to alter the uri maybe also based on fields from the
      // entity. Like a custom share link. Pass $options and $uri. Return
      // a uri object to which we can reset it. Maybe use the alter structure.

      $options['#url'] = url($uri['path'], $uri['options']);
    }
    // @todo Hash the options array and cache the markup.
    // This will save all the extra calls to modules and alters.

    // Give other module the option to alter our markup options.
    drupal_alter('addthis_markup_options', $options);

    if (array_key_exists($display, $formatters)) {
      // The display type is found. Now get it and get the markup.
      $display_inf = $formatters[$display];

      // Get all hook implementation to verify later if we can call it.
      $implementations = module_implements('addthis_display_markup');

      $markup = array();
      // First we look for a targeted implementation to call.
      if (function_exists($display_inf['module'] . '_addthis_display_markup__' . $display)) {
        $markup = call_user_func_array($display_inf['module'] . '_addthis_display_markup__' . $display, array($options));

      // This should be the default implementation that is called.
      } elseif (in_array($display_inf['module'], $implementations)) {
        $markup = module_invoke($display_inf['module'], 'addthis_display_markup', $display, $options);
      }
      // Give other module the option to later our markup.
      drupal_alter('addthis_markup', $markup);
      return $markup;

    } else {
      // Return empty
      return array();
    }
    // If no display is found or something went wrong we go here.
    return array();
  }

  /*
   * Get the type DisplayType used for our AddThis block.
   */
  public function getBlockDisplayType() {
    return variable_get(self::BLOCK_WIDGET_TYPE_KEY, self::WIDGET_TYPE_COMPACT_BUTTON);
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
    if (empty($services)) {
      drupal_set_message(t('AddThis services could not be loaded from ' . $this->getServicesJsonUrl()), 'warning');
    } else {
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

  public function getWidgetJsAsync() {
    return variable_get(self::WIDGET_JS_ASYNC, self::DEFAULT_WIDGET_JS_ASYNC);
  }

  public function addStylesheets() {
    drupal_add_css($this->getServicesCssUrl(), 'external');
    drupal_add_css($this->getAdminCssFilePath(), 'file');
  }

  public function addWidgetJs() {
    // Define if we load async or not.
    $url = self::getWidgetUrl() . (self::getWidgetJsAsync() ? '?async=1' : '');
    if (self::getWidgetJsAsync()) {
      drupal_add_js(
        array(
          'addthis' => array(
            'widget_url' => $url,
          )
        ),
        'setting'
      );
    }
    else {
      // Add AddThis.com resources
      drupal_add_js(
        $url,
        array(
          'type' => 'external',
          'group' => JS_LIBRARY,
          'every_page' => TRUE,
          'weight' => 9
        )
      );
    }
    // Add local internal behaviours
    if (self::getWidgetJsAsync()) {
      drupal_add_js(
        drupal_get_path('module', 'addthis') . '/addthis.js',
        array(
          'group' => JS_DEFAULT,
          'weight' => 10,
          'every_page' => TRUE,
          'preprocess' => TRUE
        )
      );
    }
  }

  public function addConfigurationOptionsJs() {
    if ($this->isCustomConfigurationCodeEnabled()) {
      $javascript = $this->getCustomConfigurationCode();
    }
    else {
      $enabledServices = $this->getServiceNamesAsCommaSeparatedString() . 'more';

      global $language;
      $configuration = array(
        'services_compact' => $enabledServices,
        'data_track_clickback' => $this->isClickbackTrackingEnabled(),
        'ui_508_compliant' => $this->get508Compliant(),
        'ui_click' => $this->isClickToOpenCompactMenuEnabled(),
        'ui_cobrand' =>  $this->getCoBrand(),
        'ui_delay' => $this->getUiDelay(),
        'ui_header_background' => $this->getUiHeaderBackgroundColor(),
        'ui_header_color' => $this->getUiHeaderColor(),
        'ui_open_windows' => $this->isOpenWindowsEnabled(),
        'ui_use_css' => $this->isStandardCssEnabled(),
        'ui_use_addressbook' => $this->isAddressbookEnabled(),
        'ui_language' => $language->language
      );
      // @todo provide hook to alter the default configuration.

      $javascript = 'var addthis_config = ' . drupal_json_encode($configuration);
    }
    drupal_add_js(
      $javascript,
      array(
      'type' => 'inline',
      'scope' => 'footer',
      'every_page' => TRUE
      )
    );
  }

  public function areLargeIconsEnabled() {
    return (boolean) variable_get(self::LARGE_ICONS_ENABLED_KEY, TRUE);
  }

  public function isClickToOpenCompactMenuEnabled() {
    return (boolean) variable_get(self::CLICK_TO_OPEN_COMPACT_MENU_ENABLED_KEY, FALSE);
  }

  public function isOpenWindowsEnabled() {
    return (boolean) variable_get(self::OPEN_WINDOWS_ENABLED_KEY, FALSE);
  }

  public function isFacebookLikeEnabled() {
    return (boolean) variable_get(self::FACEBOOK_LIKE_ENABLED_KEY, FALSE);
  }

  public function isGooglePlusOneEnabled() {
    return (boolean) variable_get(self::GOOGLE_PLUS_ONE_ENABLED_KEY, FALSE);
  }

  public function isTwitterEnabled() {
    return (boolean) variable_get(self::TWITTER_ENABLED_KEY, FALSE);
  }

  public function getUiDelay() {
    return (int) check_plain(variable_get(self::UI_DELAY_KEY));
  }

  public function getUiHeaderColor() {
    return check_plain(variable_get(self::UI_HEADER_COLOR_KEY));
  }

  public function getUiHeaderBackgroundColor() {
    return check_plain(variable_get(self::UI_HEADER_BACKGROUND_COLOR_KEY));
  }

  public function isStandardCssEnabled() {
    return (boolean) variable_get(self::STANDARD_CSS_ENABLED_KEY, TRUE);
  }

  public function getCustomConfigurationCode() {
    return variable_get(self::CUSTOM_CONFIGURATION_CODE_KEY, self::DEFAULT_CUSTOM_CONFIGURATION_CODE);
  }

  public function isCustomConfigurationCodeEnabled() {
    return (boolean) variable_get(self::CUSTOM_CONFIGURATION_CODE_ENABLED_KEY, FALSE);
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
    return (boolean) variable_get(self::COMPLIANT_508_KEY, FALSE);
  }

  public function isClickbackTrackingEnabled() {
    return (boolean) variable_get(self::CLICKBACK_TRACKING_ENABLED_KEY, FALSE);
  }

  public function isAddressbookEnabled() {
    return (boolean) variable_get(self::ADDRESSBOOK_ENABLED_KEY, FALSE);
  }

  /**
   * Define the following object in the $optoins.
   *
   * #entity_type
   * #entity
   * #url
   */
  public function getAddThisAttributesMarkup($options) {
    if (isset($options)) {
      $attributes = array();

      // Add title
      if (isset($options['#entity'])) {
        $attributes += $this->getAttributeTitle($options['#entity']);
      }
      $attributes += $this->getAttributeUrl($options);

      // Return the array with attributes
      return $attributes;
    }
    return array();
  }

  private function getAttributeTitle($entity) {
    if (isset($entity->title)) {
      return array(
        self::TITLE_ATTRIBUTE => (check_plain($entity->title)  . ' - ' . variable_get('site_name'))
      );
    }
    return array();
  }

  private function getAttributeUrl($options) {
    if (isset($options['#url'])) {
      return array(
        self::URL_ATTRIBUTE => $options['#url']
      );
    }
    return array();
  }

  public function getLargeButtonsClass() {
    return $this->areLargeIconsEnabled() ? ' addthis_32x32_style ' : '';
  }

  public function getTwitterButtonMarkup() {
    $element = NULL;
    if ($this->isTwitterEnabled()) {
      $element = array(
        '#theme' => 'addthis_element',
        '#tag' => 'a',
        '#value' => '',
        '#attributes' => array(
          'class' => array('addthis_button_tweet')
        ),
      );
    }
    return $element;
  }

  public function getFacebookLikeButtonMarkup() {
    $element = NULL;
    if ($this->isFacebookLikeEnabled()) {
      $element = array(
        '#theme' => 'addthis_element',
        '#tag' => 'a',
        '#value' => '',
        '#attributes' => array(
          'class' => array(
           'addthis_button_facebook_like'
          ),
          'fb:like:layout' => 'button_count'
        )
      );
    }
    return $element;
  }

  public function getGooglePlusOneButtonMarkup() {
    $element = NULL;
    if ($this->isGooglePlusOneEnabled()) {
      $element = array(
        '#theme' => 'addthis_element',
        '#tag' => 'a',
        '#value' => '',
        '#attributes' => array(
          'class' => array('addthis_button_google_plusone')
        )
      );
    }
    return $element;
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

  /*
   * Helper function. Get a bookmark url appended with the ProfileId
   */
  public function getFullBookmarkUrl() {
    return $this->getBaseBookmarkUrl() . $this->getProfileIdQueryParameterPrefixedWithAmp();
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
