(function ($) {

  Drupal.behaviors.addthis = {
    attach: function(context, settings) {
      $.getScript(
        Drupal.settings.addthis.widget_url,
        function(data, textStatus) {
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
