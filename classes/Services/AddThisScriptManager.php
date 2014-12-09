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
   * @param  array $element 
   *   The element to attach the JavaScript to.
   */
  public function attachJsToElement(&$element) {
    $widgetJs = new AddThisWidgetJsUrl($this->getWidgetJsUrl());

    // @todo Replace by settings.
    $pubid = 'as1';
    if (isset($pubid) && !empty($pubid) && is_string($pubid)) {
      $widgetJs->addAttribute('pubid', $pubid);
    }

    // @todo Replace by settings.
    $async = TRUE;
    if ($async) {
      $widgetJs->addAttribute('async', 1);
    }
    
    // @todo Replace by retrieving settings.
    $domready = TRUE;
    if ($domready) {
      $widgetJs->addAttribute('domready', 1);
    }

    // Always load addthis.js when we start working with addthis.
    $addthis_js_path = drupal_get_path('module', 'addthis') . '/addthis.js';
    $element['#attached']['js'][$addthis_js_path] = array(
        'type' => 'file',
        // @todo See if we can get this into the header below the settings.
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
   */
  private function getJsAddThisConfig() {
    // @todo Add static cache.

    // @todo Make the adding of configuration dynamic.
    //   SRP is lost here.
    return array('publicid' => 'as1');
  }

  /**
   * Get a array with all addthis_share values.
   */
  private function getJsAddThisShare() {
    // @todo Add static cache.

    // @todo Make the adding of configuration dynamic.
    //   SRP is lost here.
    return array('twitter' => 'is_on');
  }

}