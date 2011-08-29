<?php
 
class AddThisConfigurationGenerator {

  public function generate($key, $value) {
    if ($value == NULL) {
      return '';
    }
    return ', ' . $key . ': ' . "'$value'";
  }
}
