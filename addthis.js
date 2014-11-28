(function ($) {


  Drupal.behaviors.addthis = {
    attach: function(context, settings) {
      console.log('attach');



      // If addthis settings are provided a display is loaded.
      if (typeof Drupal.settings.addthis != 'undefined') {

        var settings = Drupal.settings.addthis;

        if (typeof Drupal.settings.addthis.load_type != 'undefined') {
          if (Drupal.settings.addthis.load_type == 'async') {
            if (typeof addthis != 'undefined') {
              addthis.init();
            }
          }
          if (Drupal.settings.addthis.load_type == 'domready') {
            $.getScript(
              Drupal.settings.addthis.widget_url,
              function(data, textStatus) {});
          }
          // Trigger ready on ajax attach.
          if (context != window.document &&
              typeof window.addthis != 'undefined' &&
              typeof window.addthis.toolbox == 'function')
          {
              window.addthis.toolbox('.addthis_toolbox');
          }
        }
      }

    },

    // Load the js library when the dom is ready.
    loadDomready: function() {
      // If settings ask for after dom load load the script here.
      // Call script ready when finished.
    },

    scriptReady: function(data, textStatus) {
      // Called when we script is ready to initalize the share buttons.
      // Only executed when we set it to async load. Otherwise its already
      // done.
    }.

    // Called when a ajax request returned.
    ajaxLoad: function(context, settings) {

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
