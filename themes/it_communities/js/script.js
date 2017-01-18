/**
 * @file
 * Custom JS behaviour for IT Communities Sub-theme.
 */

(function ($) {

  Drupal.behaviors.Init = {
    attach: function (context, settings) {

      // Smartphone toggle menu.
      $('.main-menu-trigger').click(function () {
        $('.main-menu ul').toggleClass('expand');
      });

      // Assign input placeholder attributes.
      $(".search-bar #edit-search-api-views-fulltext").attr("placeholder", Drupal.t('Search'));
      $(".page-services .form-item-title #edit-title").attr("placeholder", Drupal.t('Service title'));
      $(".page-solutions .form-item-title #edit-title").attr("placeholder", Drupal.t('e.g.: Transoft'));

      // Tabs.
      $('#tabs li a:not(:first)').addClass('inactive');
      $('.container').hide();
      $('.container:first').show();
      $('#tabs li a').click(function () {
        var t = $(this).attr('id');
        if ($(this).hasClass('inactive')) {
          $('#tabs li a').addClass('inactive');
          $(this).removeClass('inactive');

          $('.container').hide();
          $('#' + t + 'C').show();
        }
      });

      // Toggle Solution fields (specifications) inside of tabs.
      var label = $("#toggle-link").text();
      $("#toggle-link").toggle(
        function () {
          $('#field-togglable').show();
          label = $(this).text();
          $('#toggle-link').text('Hide specifications');
        }, function () {
          $('#field-togglable').hide();
          $('#toggle-link').text(label);
        }
      );
    }
  };

  /**
   * Prevents consecutive form submissions of identical form values.
   *
   * From drupal.org discussion: https://www.drupal.org/node/1705618
   * https://www.drupal.org/files/issues/form-single-submit-1705618-85.patch.
   *
   * Repetitive form submissions that would submit the identical form values are
   * prevented, unless the form values are different to the previously submitted
   * values.
   *
   * This is a simplified re-implementation of a user-agent behavior that should
   * be natively supported by major web browsers, but at this time, only Firefox
   * has a built-in protection.
   *
   * A form value-based approach ensures that the constraint is triggered for
   * consecutive, identical form submissions only. Compared to that, a form
   * button-based approach would (1) rely on [visible] buttons to exist where
   * technically not required and (2) require more complex state management if
   * there are multiple buttons in a form.
   *
   * This implementation is based on form-level submit events only and relies on
   * jQuery's serialize() method to determine submitted form values. As such, the
   * following limitations exist:
   *
   * - Event handlers on form buttons that preventDefault() do not receive a
   *   double-submit protection. That is deemed to be fine, since such button
   *   events typically trigger reversible client-side or server-side operations
   *   that are local to the context of a form only.
   * - Changed values in advanced form controls, such as file inputs, are not part
   *   of the form values being compared between consecutive form submits (due to
   *   limitations of jQuery.serialize()). That is deemed to be acceptable,
   *   because if the user forgot to attach a file, then the size of HTTP payload
   *   will most likely be small enough to be fully passed to the server endpoint
   *   within (milli)seconds. If a user mistakenly attached a wrong file and is
   *   technically versed enough to cancel the form submission (and HTTP payload)
   *   in order to attach a different file, then that edge-case is not supported
   *   here.
   *
   * Lastly, all forms submitted via HTTP GET are idempotent by definition of HTTP
   * standards, so excluded in this implementation.
   */
  Drupal.behaviors.formSingleSubmit = {
    attach: function () {
      function onFormSubmit (e) {
        var $form = $(e.currentTarget);
        var formValues = $form.serialize();
        var previousValues = $form.attr('data-drupal-form-submit-last');
        if (previousValues === formValues) {
          e.preventDefault();
        }
        else {
          $form.attr('data-drupal-form-submit-last', formValues);
        }
      }

      $('body').once('form-single-submit')
        .delegate('form:not([method~="GET"])', 'submit.singleSubmit', onFormSubmit);

    }
  };

})(jQuery);
