<?php
 
class AddThisConfigurationGenerator {

  public function generate($key, $value) {
    return $this->generateInternal($key, $value);
  }

  public function generateWithoutTrailingComma($key, $value) {
    return $this->generateInternal($key, $value, FALSE);
  }

  private function generateInternal($key, $value, $trailingComma = TRUE) {
    if ($value == NULL) {
      return '';
    }
    return $key . ': ' . "'$value'" . ($trailingComma ? ', ' : '');
  }
}
