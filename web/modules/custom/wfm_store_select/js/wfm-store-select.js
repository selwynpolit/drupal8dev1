(function ($, Drupal, drupalSettings) {
//(function ($) {

  'use strict';

  /**
   * @namespace
   */
  Drupal.storeSelect = {};
  Drupal.behaviors.makeThisMyStore = {
    attach: function (event) {
      $('.add_store').on('click', function (event) {
        var href = $(this).attr('href');
        var url = $(this).parents('.views-row').find('.store-title').find('a').attr('href');
        console.log(url);
        var storeTLC = href.substr(href.length - 3);
        //var uid = drupalSettings.user.uid;
        // Are we logged in?
        var guest = $("body.not-logged-in").length;
        console.log(guest)

        setCookie('store', storeTLC,7);
        if (guest > 0) {
          event.preventDefault();
          // redirect guest to store page
          //window.location.href(url);
          window.location(url);
        }
      });
    }
  };
})(jQuery,Drupal);

function setCookie(cname, cvalue, exdays) {
  console.log('writing cookie');
  var d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  var expires = "expires="+d.toUTCString();
  document.cookie = cname + "=" + cvalue + "; " + expires;
}
