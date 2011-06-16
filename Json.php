<?php
/**
 * @file
 * A class containing utility methods for json-formatted data requesting.
 *
 * @author Jani Palsamäki
 */
 
class Json {

  public static function request($url) {
    $response = drupal_http_request($url);
    $responseOk = $response->code == 200;
    return $responseOk ? json_decode($response->data, TRUE) : NULL;
  }
}
