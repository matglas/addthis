<?php
/**
 * @file
 * Class implementation of the Toolbox.
 */

class AddThisDisplayToolbox implements AddThisDisplayInterface {

  /**
   * Get the label of the display.
   */
  public function getLabel() {
    return t('Basic Toolbox');
  }

  /**
   * Get the machine name for this display.
   */
  public function getName() {
    return 'addthis_basic_toolbox';
  }

  /**
   * Get the default settings for this display.
   *
   * @return array
   *   The array with default settings.
   */
  public function getDefaultSettings() {
    return array(
      'share_services' => 'facebook,twitter',
      'buttons_size' => 'addthis_16x16_style',
      'counter_orientation' => 'horizontal',
      'extra_css' => '',
    );
  }

  /**
   * Get the settings form for the display.
   *
   * @return array
   *   The form array used by FormAPI.
   */
  public function getSettingsForm($field, $instance, $view_mode, $form, &$form_state) {

    $display = $instance['display'][$view_mode];
    $settings = $display['settings'];
    $element = array();

    $element['share_services'] = array(
      '#title' => t('Services'),
      '#type' => 'textfield',
      '#size' => 80,
      '#default_value' => $settings['share_services'],
      '#required' => TRUE,
      '#element_validate' => array('_addthis_display_element_validate_services'),
      '#description' => t('Specify the names of the sharing services and seperate them with a , (comma). <a href="http://www.addthis.com/services/list">The names on this list are valid.</a>'),
    );
    $element['buttons_size'] = array(
      '#title' => t('Buttons size'),
      '#type' => 'select',
      '#default_value' => $settings['buttons_size'],
      '#options' => array(
        AddThis::CSS_16x16 => t('Small (16x16)'),
        AddThis::CSS_32x32 => t('Big (32x32)'),
      ),
    );
    $element['counter_orientation'] = array(
      '#title' => t('Counter orientation'),
      '#description' => t('Specify the way service counters are oriented.'),
      '#type' => 'select',
      '#default_value' => $settings['counter_orientation'],
      '#options' => array(
        'horizontal' => t('Horizontal'),
        'vertical' => t('Vertical'),
      )
    );
    $element['extra_css'] = array(
      '#title' => t('Extra CSS declaration'),
      '#type' => 'textfield',
      '#size' => 40,
      '#default_value' => $settings['extra_css'],
      '#description' => t('Specify extra CSS classes to apply to the toolbox'),
    );

    return $element;
  }

  /**
   * Get the render array.
   *
   * @return array
   *   The render array usable by drupal_render.
   */
  public function getMarkup($options) {
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

    // All service elements.
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
      //   Still missing: tweetme, hyves and stubleupon.
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
            'tw:count' => ($orientation ? 'horizontal' : 'vertical'),
            'tw:via' => AddThis::getInstance()->getTwitterVia(),
          );
          break;
      }
    }
    $element += $items;

    return $element;
  }

}
