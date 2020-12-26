'use strict';

import jQuery from 'jquery';

jQuery('document').ready(function ($) {
  $(document).on('click', '[data-control="social.deeplink"]', function (e) {
    e.preventDefault();

    let deeplink = $(this).data('deeplink') || null,
        fallback = $(this).attr('href');

    if (deeplink) {
      setTimeout(function () {
        window.location = fallback;
      }, 25);
      window.location = deeplink;
    } else {
      window.open(fallback, '_blank');
    }
  });

  let socialWindow = function (url) {
    let left = (screen.width - 570) / 2,
        top = (screen.height - 570) / 2,
        params = "menubar=no,toolbar=no,status=no,width=570,height=570,top=" + top + ",left=" + left;
    window.open(url, "NewWindow", params);
  };

  $(document).on('click', '[data-control="social.share"]', function (e) {
    e.preventDefault();

    socialWindow($(this).attr('href'));
  });
});