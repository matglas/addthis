<?php
/**
 * @file
 * Class definition of a script manager.
 */

class AddThisScriptManager {

  private $addthis = NULL;

  /**
   * Construct method.
   */
  function __construct() {
    $this->addthis = AddThis::getInstance();
  }

  /**
   * Get the current widget js url.
   * 
   * @return string
   *   A url reference to the widget js.
   */
  public function getWidgetJsUrl() {
    return check_url(variable_get(AddThis::WIDGET_JS_URL_KEY, AddThis::DEFAULT_WIDGET_JS_URL));
  }

  /**
   * Return if we are on https connection.
   * 
   * @return bool
   *   TRUE if the current request is on https. 
   */
  public function isHttps() {
    $is_https = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on';

    return $is_https;
  }

  /**
   * Change the schema from http to https if we are on https.
   * 
   * @param  string $url
   *   A full url.
   * 
   * @return string
   *   The changed url.
   */
  public function correctSchemaIfHttps($url) {
    if (is_string($url) && $this->isHttps()) {
      return str_replace('http://', 'https://', $url);
    } 
    else {
      return $url;
    }
    throw new InvalidArgumentException('The argument was not a string value');
  }

  /**
   * Attach the widget js to the element.
   * 
   * @todo Change the scope of the addthis.js.
   *   See if we can get the scope of the addthis.js into the header
   *   just below the settings so that the settings can be used in the loaded
   *   addthis.js of our module.
   *
   * @param array $element
   *   The element to attach the JavaScript to.
   */
  public function attachJsToElement(&$element) {
    $widget_js = new AddThisWidgetJsUrl($this->getWidgetJsUrl());

    // @todo Replace by settings.
    $pubid = $this->addthis->getProfileId();
    if (isset($pubid) && !empty($pubid) && is_string($pubid)) {
      $widget_js->addAttribute('pubid', $pubid);
    }

    // @todo Replace by settings.
    $async = FALSE;
    if ($async) {
      $widget_js->addAttribute('async', 1);
    }

    // @todo Replace by retrieving settings.
    $domready = TRUE;
    if ($domready) {
      $widget_js->addAttribute('domready', 1);
    }

    // Always load addthis.js when we start working with addthis.
    $addthis_js_path = drupal_get_path('module', 'addthis') . '/addthis.js';
    $element['#attached']['js'][$addthis_js_path] = array(
      'type' => 'file',
      'scope' => 'footer',
    );

    // Only when the script is not loaded after the DOM is ready we include
    // the script with #attached.
    if (!$domready) {
      $element['#attached']['js'][$this->getWidgetJsUrl()] = array(
        'type' => 'external',
        'scope' => 'footer',
      );
    }

    // Every setting value passed here overrides previously set values but
    // leaves the values that are already set somewhere else and that are not
    // passed here.
    $element['#attached']['js'][] = array(
      'type' => 'setting',
      'data' => array(
        'addthis' => array(
          'async' => $async,
          'domready' => $domready,
          'widget_url' => $this->getWidgetJsUrl(),

          'addthis_config' => $this->getJsAddThisConfig(),
          'addthis_share' => $this->getJsAddThisShare(),
        )
      )
    );
  }

  /**
   * Get a array with all addthis_config values.
   *
   * Allow alter through 'addthis_configuration'.
   *
   * @todo Add static cache.
   *
   * @todo Make the adding of configuration dynamic.
   *   SRP is lost here.
   */
  private function getJsAddThisConfig() {
    global $language;

    $enabled_services = $this->addthis->getServiceNamesAsCommaSeparatedString($this->addthis->getEnabledServices()) . 'more';
    $excluded_services = $this->addthis->getServiceNamesAsCommaSeparatedString($this->addthis->getExcludedServices());

    $configuration = array(
      'pubid' => $this->addthis->getProfileId(),
      'services_compact' => $enabled_services,
      'services_exclude' => $excluded_services,
      'data_track_clickback' => $this->addthis->isClickbackTrackingEnabled(),
      'ui_508_compliant' => $this->addthis->get508Compliant(),
      'ui_click' => $this->addthis->isClickToOpenCompactMenuEnabled(),
      'ui_cobrand' => $this->addthis->getCoBrand(),
      'ui_delay' => $this->addthis->getUiDelay(),
      'ui_header_background' => $this->addthis->getUiHeaderBackgroundColor(),
      'ui_header_color' => $this->addthis->getUiHeaderColor(),
      'ui_open_windows' => $this->addthis->isOpenWindowsEnabled(),
      'ui_use_css' => $this->addthis->isStandardCssEnabled(),
      'ui_use_addressbook' => $this->addthis->isAddressbookEnabled(),
      'ui_language' => $language->language,
    );
    if (module_exists('googleanalytics')) {
      if ($this->addthis->isGoogleAnalyticsTrackingEnabled()) {
        $configuration['data_ga_property'] = variable_get('googleanalytics_account', '');
        $configuration['data_ga_social'] = $this->addthis->isGoogleAnalyticsSocialTrackingEnabled();
      }
    }

    drupal_alter('addthis_configuration', $configuration);
    return $configuration;
  }

  /**
   * Get a array with all addthis_share values.
   *
   * Allow alter through 'addthis_configuration_share'.
   *
   * @todo Add static cache.
   *
   * @todo Make the adding of configuration dynamic.
   *   SRP is lost here.
   */
  private function getJsAddThisShare() {

    $configuration = $this->getJsAddThisConfig();

    if (isset($configuration['templates'])) {
      $addthis_share = array(
        'templates' => $configuration['templates'],
      );
    }
    $addthis_share['templates']['twitter'] = $this->addthis->getTwitterTemplate();

    drupal_alter('addthis_configuration_share', $configuration);
    return $addthis_share;
  }

}
