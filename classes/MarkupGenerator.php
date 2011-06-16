<?php
/**
 * @file
 * An attribute markup generator.
 *
 * @author Jani Palsamäki
 */
 
class MarkupGenerator {

  public static function generateAttribute($name, $value) {
    return $name != NULL && $value != NULL ? $name . '="' . $value  . '"' : '';
  }
}
