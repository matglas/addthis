<?php

class AddThisGlobalConfig {

  function __construct() {
    drupal_set_message(t('it works'), 'status', FALSE);
  }

}