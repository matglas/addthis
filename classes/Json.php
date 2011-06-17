<?php
/**
 * @file
 * A class containing utility methods for json-related functionality.
 *
 * @author Jani Palsamäki
 */
 
class Json {

  public function decode($url) {
    $response = drupal_http_request($url);
    $responseOk = $response->code == 200;
    return $responseOk ? json_decode($response->data, TRUE) : NULL;
  }
}
