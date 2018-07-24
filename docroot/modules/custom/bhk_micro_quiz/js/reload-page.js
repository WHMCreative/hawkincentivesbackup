/**
 * @file
 * Custom ajax command to reload the current page.
 */
(function ($, Drupal) {
  'use strict';

  /**
   * Reload the current page.
   *
   * @param {Drupal.Ajax} ajax
   *   {@link Drupal.Ajax} object created by {@link Drupal.ajax}.
   * @param {object} response
   *   The response from the Ajax request.
   * @param {number} [status]
   *   The XMLHttpRequest status.
   */
  Drupal.AjaxCommands.prototype.reload = function (ajax, response, status) {
    window.location.reload();
  }

})(jQuery, Drupal);