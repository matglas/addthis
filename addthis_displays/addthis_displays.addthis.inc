<?php

/**
 * @file
 * Implements AddThis markup functions.
 */

function addthis_displays_addthis_display_markup($display, $variables = NULL) {
  return NULL;
}

/**
 * Implements hook_addthis_display_markup__[display]().
 */
function addthis_displays_addthis_display_markup__addthis_basic_toolbox($options = array()) {
  $addthis = AddThis::getInstance();

  // Create a render array for the widget.
  $element = array(
    // Use #theme_wrappers to include the rendered children. Otherwise the
    // result is an empty element like <div></div>.
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
    //   Still missing: tweetme, hyves and stubleupon, google_plusone_badge.
    //
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
          'tw:count' => ($orientation ? 'horizontal' : 'vertical'),
          'tw:via' => AddThis::getInstance()->getTwitterVia(),
        );
        break;
      case 'bubble_style':
        $items[$service]['#attributes']['class'] = array(
          'addthis_counter', 'addthis_bubble_style'
        );
        break;
      case 'pill_style':
        $items[$service]['#attributes']['class'] = array(
          'addthis_counter', 'addthis_pill_style'
        );
        break;
    }
  }
  $element += $items;

  return $element;
}

/**
 * Implements hook_addthis_display_markup__[display]().
 */
function addthis_displays_addthis_display_markup__addthis_basic_button($options = array()) {

  $addthis = AddThis::getInstance();
  $settings = $options['#display']['settings'];

  $button_img = 'http://s7.addthis.com/static/btn/sm-share-en.gif';
  if (isset($settings['buttons_size']) && $settings['buttons_size'] == 'big') {
    $button_img = 'http://s7.addthis.com/static/btn/v2/lg-share-en.gif';
  }
  $button_img = $addthis->transformToSecureUrl($button_img);

  $extra_css = isset($settings['extra_css']) ? $settings['extra_css'] : '';
  $element = array(
    '#theme' => 'addthis_wrapper',
    '#tag' => 'a',
    '#attributes' => array(
      'class' => array(
        'addthis_button',
        $extra_css,
      ),
    ),
  );
  $element['#attributes'] += $addthis->getAddThisAttributesMarkup($options);

  // Create img button.
  $image = array(
    '#theme' => 'addthis_element',
    '#tag' => 'img',
    '#attributes' => array(
      'src' => $button_img,
      'alt' => t('Share page with AddThis'),
    ),
  );
  $element[] = $image;

  return $element;
}

/**
 * Implements hook_addthis_display_markup__[display]().
 *  Render a basic_toolbox using global settings.
 */
function addthis_displays_addthis_display_markup__addthis_global_toolbox($options = array()) {
  // Load options from variables and pass to basic_toolbox.
  $options['#display']['settings']['share_services'] = variable_get(AddThis::GLOBAL_TOOLBOX_SERVICES_KEY, 'twitter,facbook,compact');
  $options['#display']['settings']['buttons_size'] = variable_get(AddThis::GLOBAL_TOOLBOX_BUTTON_SIZE_KEY, AddThis::CSS_16x16);
  $options['#display']['settings']['counter_orientation'] = variable_get(AddThis::GLOBAL_TOOLBOX_COUNTER_ORIENTATION_KEY, 'FALSE');
  $options['#display']['settings']['extra_css'] = variable_get(AddThis::GLOBAL_TOOLBOX_EXTRA_CSS_KEY, NULL);
  return addthis_displays_addthis_display_markup__addthis_basic_toolbox($options);
}
