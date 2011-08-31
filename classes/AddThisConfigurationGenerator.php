<?php
 
class AddThisConfigurationGenerator {

  public function generate($key, $value) {
    return $this->generateInternal($key, $value);
  }

  public function generateWithoutTrailingComma($key, $value) {
    return $this->generateInternal($key, $value, FALSE);
  }

  private function generateInternal($key, $value, $trailingComma = TRUE) {
    if (is_null($value)) {
      return '';
    }
    return $key . ': ' . $this->getValue($value) . ($trailingComma ? ', ' : '');
  }

  private function getValue($value) {
    if (is_bool($value)) {
      return $value ? 'true' : 'false';
    } elseif (is_string($value)) {
      return "'$value'";
    } elseif (is_numeric($value)) {
      return $value;
    }
  }
}
