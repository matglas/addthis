<?php
/**
 * Implementation of hook_addthis_display_markup().
 */
function addthis_addthis_display_markup($display, $variables = NULL) {
  return array();
}

/**
 * Implementation of hook_addthis_display_markup__[display]().
 */
function addthis_addthis_display_markup__addthis_large_button($variables = NULL) {
  $addthis = AddThis::getInstance();
  $entity = $variables['#entity'];

  // This is a render array representation.
  $element = array(
    // Note that theme_wrappers needs an array.
    '#theme' => 'addthis_wrapper',
    '#tag' => 'a',
    '#attributes' => array(
      'href' => $addthis->getFullBookmarkUrl(),
      'class' => array(
        'addthis_button',
      )
    )
  );
  $element['#attributes'] += $addthis->getAddThisAttributesMarkup($entity);

  // Here we start the children of the a element.
  $image = array(
      '#theme' => 'addthis_element',
      '#tag' => 'img',
      '#attributes' => array(
        'src' => 'http://s7.addthis.com/static/btn/v2/lg-share-en.gif',
        'width' => '125',
        'height' => '16',
        'alt' => t('Bookmark and Share'),
        'style' => 'border:0;'
      )
    );
  $element[] = $image;

  return $element;
}

/**
 * Implementation of hook_addthis_display_markup__[display]().
 */
function addthis_addthis_display_markup__addthis_compact_button($variables = NULL) {
  $addthis = AddThis::getInstance();
  $entity = $variables['#entity'];

  // Create wrapper element a
  $element = array(
    '#theme' => 'addthis_wrapper',
    '#tag' => 'a',
    '#attributes' => array(
      'href' => $addthis->getFullBookmarkUrl(),
      'class' => array(
        'addthis_button'
      )
    )
  );
  $element['#attributes'] += $addthis->getAddThisAttributesMarkup($entity);

  // Create img button
  $image = array(
    '#theme' => 'addthis_element',
    '#tag' => 'img',
    '#attributes' => array(
      'src' => 'http://s7.addthis.com/static/btn/sm-share-en.gif',
      'width' => '83',
      'height' => '16',
      'alt' => t('Bookmark and Share'),
      'style' => 'border:0;',
    ),
  );
  $element[] = $image;

  // Return element to render.
  return $element;
}
/**
 * Implementation of hook_addthis_display_markup__[display]().
 */
function addthis_addthis_display_markup__addthis_toolbox($variables = NULL) {
  $addthis = AddThis::getInstance();
  $entity = $variables['#entity'];

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
        trim($addthis->getLargeButtonsClass()),
      )
    ),
  );
  $element['#attributes'] += $addthis->getAddThisAttributesMarkup($entity);

  // All service elements
  $items = array();
  $items[] = array(
    '#theme' => 'addthis_element',
    '#tag' => 'a',
    '#value' => '',
    '#attributes' => array(
      'href' => $addthis->getFullBookmarkUrl(),
      'class' => array(
        'addthis_button_compact'
      ),
    ),
  );
  $numberOfPreferredServices = $addthis->getNumberOfPreferredServices();
  for ($i = 1; $i <= $numberOfPreferredServices; $i++) {
    $items[] = array(
      '#theme' => 'addthis_element',
      '#tag' => 'a',
      '#value' => '',
      '#attributes' => array(
        'href' => $addthis->getFullBookmarkUrl(),
        'class' => array(
          ('addthis_button_preferred_' . $i),
        )
      ),
    );
  }
  $items[] = $addthis->getTwitterButtonMarkup();
  $items[] = $addthis->getFacebookLikeButtonMarkup();
  $items[] = $addthis->getGooglePlusOneButtonMarkup();
  $element += $items;

  return $element;
}

/**
 * Implementation of hook_addthis_display_markup__[display]().
 */
function addthis_addthis_display_markup__addthis_sharecount($variables = NULL) {
  $addthis = AddThis::getInstance();
  $entity = $variables['#entity'];

  $element = array(
    '#theme' => 'addthis_wrapper',
    '#tag' => 'div',
    '#attributes' => array(
      'class' => array(
        'addthis_toolbox',
        'addthis_default_style'
      )
    ),
  );
  // <a> element with counter
  $item = array(
    '#theme' => 'addthis_element',
    '#tag' => 'a',
    '#value' => '',
    '#attributes' => array(
      'class' => array(
        'addthis_counter'
      ),
    ),
  );
  $item['#attributes'] += $addthis->getAddThisAttributesMarkup($entity);
  $element[] = $item;

  return $element;
}