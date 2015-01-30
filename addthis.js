/**
 * @file
 * AddThis javascript actions.
 *
 * @todo Should async be in here?
 *   Question if we need to do anything with the async setting. The developer
 *   might need to take action itself with addthis.init().
 */

(function ($) {
  // This load the config in time to run any addthis functionality.
  addthis_config = Drupal.settings.addthis.addthis_config;
  addthis_share = Drupal.settings.addthis.addthis_share;

  Drupal.behaviors.addthis = {
    attach: function(context, settings) {

      // Trigger ready on ajax attach.
      if (context != window.document) {
        Drupal.behaviors.addthis.ajaxLoad(context, settings);
      }

    },

    // Load the js library when the dom is ready.
    loadDomready: function() {
      // If settings asks for loading the script after the dom is loaded, then
      // load the script here.
      if (Drupal.settings.addthis.domready) {
        $.getScript(Drupal.settings.addthis.widget_url, Drupal.behaviors.addthis.scriptReady);
      }
    },

    // Called when a ajax request returned.
    ajaxLoad: function(context, settings) {
      if (typeof window.addthis != 'undefined' &&
          typeof window.addthis.toolbox == 'function')
      {
          window.addthis.toolbox('.addthis_toolbox');
      }
    }

  }

  // Document ready in case we want to load AddThis into the dom after every
  // thing is loaded.
  // 
  // Is executed once after the attach happend.
  $(document).ready(function() {
    Drupal.behaviors.addthis.loadDomready();
  });

}(jQuery));
