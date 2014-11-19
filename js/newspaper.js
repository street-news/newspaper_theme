(function ($) {

$(document).ready(function() {
	$("body").addClass("jquery-ran");
});

Drupal.behaviors.pullQuotes = {
  attach: function(context) {
    var $pullquotes = $('.node-full .pullquote-left-text:not(.pullquote-processed), .node-full .pullquote-right-text:not(.pullquote-processed)', context);

    if (!$pullquotes.length) {
      return;
    }

    $pullquotes.each(function(i, el) {
      var $el = $(el);
      var $pq = $('<span>' + $el.text() + '</span>');
      if ($el.hasClass('pullquote-left-text')) {
        $pq.addClass('pullquote-left');
      }
      else if ($el.hasClass('pullquote-right-text')) {
        $pq.addClass('pullquote-right');
      }
      $el.parent('p').prepend($pq);
    });

    $pullquotes.addClass('pullquote-processed');
  }
};

Drupal.behaviors.singleMobileMenu = {
  attach: function(context) {
    $('[id^="block-menu-block-newspaper-base-1"]', context).once('single-mobile-menu', function() {
      var $menu = $('select', this);

      // Header menu items
      $('[id^="block-menu-block-base-3"] option').each(function() {
        if ($(this).attr('value').length) {
          $(this)
            .clone()
            .removeAttr('selected')
            .appendTo($menu);
        }
      });

      // Footer menu items
      $('[id^="block-menu-block-base-2"] option').each(function() {
        if ($(this).attr('value').length) {
          $(this)
            .clone()
            .removeAttr('selected')
            .appendTo($menu);
        }
      });

    });
  }
};

})(jQuery);
