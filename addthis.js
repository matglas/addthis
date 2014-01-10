(function ($) {

  Drupal.behaviors.addthis = {
    attach: function(context, settings) {

      if (typeof Drupal.settings.addthis.load_type != 'undefined') {
        if (Drupal.settings.addthis.load_type == 'async') {
          addthis.init();
        }
        if (Drupal.settings.addthis.load_type == 'domready') {
          $.getScript(
            Drupal.settings.addthis.widget_url,
            function(data, textStatus) {});
        }
        if (context != window.document && window.addthis != null) {
          window.addthis.ost = 0;
          window.addthis.ready();
        }
      }

    }
  }
}
(jQuery));
