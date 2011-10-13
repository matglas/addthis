(function ($) {

  Drupal.behaviors.addthis = {
    attach: function(context, settings) {
      if ($.getScript(Drupal.settings.addthis.widget_url)) {
        addthis.init();
      }
    }
  };

}(jQuery));