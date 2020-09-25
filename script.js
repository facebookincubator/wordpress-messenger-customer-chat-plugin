/*
* Copyright (C) 2017-present, Facebook, Inc.
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; version 2 of the License.

* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*/

function fbmcc_setupCustomerChat() {
  const FACEBOOK_URL = "https://www.facebook.com";
  var baseURL = "https://www.facebook.com/customer_chat/dialog/?domain=";
  var urlParam = encodeURI(
    window.location.protocol
      + '//'
      + window.location.hostname
      + (window.location.port ? ':' + window.location.port : '')
  );
  var customerWindow = window.open(
    baseURL + urlParam,
    "_blank",
    "width=1200,height=800"
  );

  jQuery(window).on("message", function(e) {
    if (e.originalEvent.origin === FACEBOOK_URL) {
      $data_json = JSON.parse(e.originalEvent.data);
      var data = {
        'action' : 'update_options',
        'pageID' : fbmcc_sanitizeNumbersOnly($data_json["pageID"]),
        'locale' : fbmcc_sanitizeLocale($data_json["locale"]),
        '_wpnonce' : ajax_object.nonce,
      };
      jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        success: function(results) {
          jQuery('#fbmcc-page-params').css('display', 'inline-block');
        }
      });
    }
  });
}

function fbmcc_sanitizeNumbersOnly( number ) {
  if( /^\d+$/.test(number) ) {
    return number;
  } else {
    return '';
  }
}

function fbmcc_sanitizeLocale( locale ) {
  if( /^[A-Za-z_]{4,5}$/.test(locale) ) {
    return locale;
  } else {
    return '';
  }
}
