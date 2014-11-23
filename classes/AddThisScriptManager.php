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
  public function attachWidgetJs(&$element) {
    $widgetJs = new AddThisWidgetJsArgUtil($this->getWidgetJsUrl());

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
    $domready = FALSE;
    if ($domready) {
      $widgetJs->addAttribute('domready', 1);
    }

    // Only when the script is not loaded after the DOM is ready we include
    // the script with #attached.
    if (!$domready) {
      $element['#attached']['js'][$this->getWidgetJsUrl()] = array(
        'type' => 'external',
        'scope' => 'footer',
      );
    }
  }

}