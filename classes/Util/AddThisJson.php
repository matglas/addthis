<?php
/**
 * @file
 * A class containing utility methods for json-related functionality.
 */

class AddThisJson {

  /**
   * Retrieve a decoded version of a json response from a url.
   */
  public static function decode($url) {
    $response = drupal_http_request($url);
    $response_ok = $response->code == 200;
    return $response_ok ? drupal_json_decode($response->data) : NULL;
  }
}
