(function ($) {

  Drupal.behaviors.addthis = {
    attach: function(context, settings) {
      $.getScript(Drupal.settings.addthis.widget_url);
      addthis.init();
    }
  };

}(jQuery));