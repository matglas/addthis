(function ($) {

  Drupal.behaviors.addthis = {
    attach: function(context, settings) {
      $.getScript(
        Drupal.settings.addthis.widget_url,
        function(data, textStatus) {
          addthis.init();
        }
      );
      // Trigger ready on ajax attach.
      if (context != window.document && window.addthis != null) {
        window.addthis.ost = 0;
        window.addthis.ready();
      }
      // Attach on onchange
      $('.addthis-display-type', context).change(Drupal.addThis.displayType.onChange);
    }
  };

  Drupal.addThis = {

    displayType: {
      
      onChange: function() {
        var $trigger = $(this);
        console.debug(this);
      }
      
    }
    
  }

}(jQuery));
