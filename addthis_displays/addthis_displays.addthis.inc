<?php

function addthis_displays_addthis_display_markup($display, $variables = NULL) {
  return null;
}

/**
 * Implementation of hook_addthis_display_markup__[display]().
 */
function addthis_displays_addthis_display_markup__addthis_basic_toolbox($options = array()) {
  $addthis = AddThis::getInstance();

  // Creating render array for the widget.
  $element = array(
    // We use #theme_wrappers to include the rendered children otherwise
    // we only get a non empty element like <div></div>.
    '#theme' => 'addthis_wrapper',
    '#tag' => 'div',
    '#attributes' => array(
      'class' => array(
        'addthis_toolbox',
        'addthis_default_style',
        ($options['#display']['settings']['buttons_size'] == AddThis::CSS_32x32 ? AddThis::CSS_32x32 : NULL),
        $options['#display']['settings']['extra_css'],
      ),
    ),
  );
  $element['#attributes'] += $addthis->getAddThisAttributesMarkup($options);

  $services = trim($options['#display']['settings']['share_services']);
  $services = str_replace(' ', '', $services);
  $services = explode(',', $services);

  // All service elements
  $items = array();
  foreach ($services as $service) {
    $items[$service] = array(
      '#theme' => 'addthis_element',
      '#tag' => 'a',
      '#value' => '',
      '#attributes' => array(
        'href' => $addthis->getBaseBookmarkUrl(),
        'class' => array(
          'addthis_button_' . $service,
        ),
      ),
      '#addthis_service' => $service,
    );

    // Basic implementations of bubble counter orientation.
    // @todo Figure all the bubbles out and add them.
    //   We miss tweetmeme, hyves and stubleupon. I did not find the right
    //   style settings yet.
    //
    // @todo Fix a way to use addthis_bubble_style.
    //   There is a conflict now with using the class addthis_button_[service].
    //   You can't add this bubble style now.
    $orientation = ($options['#display']['settings']['counter_orientation'] == 'horizontal' ? TRUE : FALSE);
    switch ($service) {
      case 'facebook_like':
        $items[$service]['#attributes'] += array(
          'fb:like:layout' => ($orientation ? 'button_count' : 'box_count')
        );
        break;
      case 'google_plusone':
        $items[$service]['#attributes'] += array(
          'g:plusone:size' => ($orientation ? 'standard' : 'tall')
        );
        break;
      case 'tweet':
        $items[$service]['#attributes'] += array(
          'tw:count' => ($orientation ? 'horizontal' : 'vertical')
        );
        break;
    }
  }
  $element += $items;

  return $element;
}

/**
 * Implementation of hook_addthis_display_markup__[display]().
 */
function addthis_displays_addthis_display_markup__addthis_basic_button($options = array()) {
  $addthis = AddThis::getInstance();
  $settings = (isset($options['#display']['settings']) ? $options['#display']['settings'] : NULL);

  $button_img = 'http://s7.addthis.com/static/btn/sm-share-en.gif';
  if ($options['#display']['settings']['button_size'] == 'big') {
    $button_img = 'http://s7.addthis.com/static/btn/v2/lg-share-en.gif';
  }

  $element = array(
    '#theme' => 'addthis_wrapper',
    '#tag' => 'a',
    '#attributes' => array(
      'class' => array(
        'addthis_button',
        $options['#display']['settings']['extra_css'],
      ),
    ),
  );
  $element['#attributes'] += $addthis->getAddThisAttributesMarkup($options);

  // Create img button
  $image = array(
    '#theme' => 'addthis_element',
    '#tag' => 'img',
    '#attributes' => array(
      'src' => $button_img,
    ),
  );
  $element[] = $image;

  // Return element to render.
  return $element;
}
