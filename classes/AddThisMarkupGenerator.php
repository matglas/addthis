<?php
/**
 * @file
 * An attribute markup generator.
 *
 * @author Jani Palsamäki
 */
 
class AddThisMarkupGenerator {

  public function generateAttribute($name, $value) {
    return $name != NULL && $value != NULL ? check_plain($name) . '="' . $value  . '"' : '';
  }
}
