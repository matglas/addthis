<?php

function addthis_displays_addthis_display_markup($display, $variables = NULL) {
  return null;
}

/**
 * Implementation of hook_addthis_display_markup__[display]().
 */
function addthis_displays_addthis_display_markup__addthis_basic_toolbox($options = array()) { 
  $addthis = AddThis::getInstance();
  $entity = $options['#entity'];
  $url = $options['#url'];

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
        ($options['#display']['settings']['buttons_size'] == AddThis::CSS_32x32 ? AddThis::CSS_32x32 : NULL)
      )
    ),
  );
  $element['#attributes'] += $addthis->getAddThisAttributesMarkup($entity);

  $services = trim($options['#display']['settings']['share_services']);
  $services = str_replace(' ', '', $services);
  $services = explode(',', $services);

  // All service elements
  $items = array();
  foreach($services as $service) {
    $items[] = array(
      '#theme' => 'addthis_element',
      '#tag' => 'a',
      '#value' => '',
      '#attributes' => array(
        'href' => $url,
        'class' => array(
          'addthis_button_' . $service
        ),
      ),
    );
  }
  $element += $items;

  return $element;
}
