(function($) {

  $(function() {
    var estimate = function()   {
      var words = parseInt($('#pagination-count').text() );
      var total = parseInt($('#edit-body textarea.text-full').val().length );
      var pages = Math.ceil( total / (words * 6) );
      var page  = (pages > 1) ? Drupal.t("pages") : Drupal.t("page");
      $('#pagination-guess').text(pages + " " + page);
    }

    $('#edit-body textarea.text-full').blur(estimate).focus(estimate);
    estimate();
  });

})(jQuery);