(function ($) {

  Drupal.behaviors.addthis = {
    attach: function(context, settings) {

      if (typeof Drupal.settings.addthis.load_type != 'undefined') {
        if (Drupal.settings.addthis.load_type == 'async') {
          addthis.init();
        }
      );
      if (context != window.document && window.addthis != null) {
        window.addthis.ost = 0;
        if(typeof window.addthis.ready === 'function') {
          window.addthis.ready();
        }
      }

    }
  }
}
(jQuery));
