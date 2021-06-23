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
        'action' : 'fbmcc_update_options',
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

(function($)
{
	var ChatPlugin =
	{
		init: function()
	  {
      this.deactivationFormSubmit();
			this.deactivationModalOpenHandler();
      this.deactivationModalCloseHandler();
      this.deactivationModalFreetextOptionOpenHandler();
    },

    deactivationFormSubmit: function() {
			$( '#fbmcc-deactivationFormSubmit' ).click(
        function () {
          $('#fbmcc-deactivationFormSubmit').attr('disabled','disabled');
          var reason = "";
          if ($('input[name=fbmcc-deactivationReason]:checked', '#fbmcc-deactivationForm').val() == 3) {
            reason = $('#fbmcc-deactivationReason-preferredPluginName').val();
          } else if ($('input[name=fbmcc-deactivationReason]:checked', '#fbmcc-deactivationForm').val() == 5) {
            reason = $('#fbmcc-deactivationReason-other').val();
          }
          $.ajax(
            {
              method: 'POST',
              url: 'https://www.facebook.com/plugins/chat/wordpress_deactivation/',
              data: $.param(
                {
                  page_id: $('#fbmcc-deactivationForm-pageId').val(),
                  reason: reason,
                  selected_option: $('input[name=fbmcc-deactivationReason]:checked', '#fbmcc-deactivationForm').val()
                }
              ),
              complete: function () {
                $('#fbmcc-deactivationFormContainer').addClass('hidden');
                $('#fbmcc-deactivationModal-thankYou').removeClass('hidden');
              },
              error: function () {
                $('#fbmcc-deactivationFormContainer').addClass('hidden');
                $('#fbmcc-deactivationModal-thankYou').removeClass('hidden');
              }
            }
          );
				}
			)
    },
		deactivationModalOpenHandler: function() {
			$('table.plugins tr[data-slug=facebook-messenger-customer-chat] span.deactivate a').click(
				function (e) {
          e.preventDefault();
					$( '#fbmcc-deactivationModalOverlay' ).toggleClass( 'fbmcc-deactivationModalOverlay-display' );
				}
			)
    },
    deactivationModalCloseHandler: function() {
			$('#fbmcc-deactivationModalOverlay').click(
				function (e) {
					if (
            $('#fbmcc-deactivationModalOverlay').hasClass( 'fbmcc-deactivationModalOverlay-display' ) &&
					(
					! $( e.target ).closest( '#fbmcc-deactivationModalContainer' ).length ||
					$( e.target ).closest( '.fbmcc-deactivationModal-closeButton' ).length
					)
					) {
						$( '#fbmcc-deactivationModalOverlay' ).toggleClass( 'fbmcc-deactivationModalOverlay-display' );
            return window.location.replace(
              $( 'table.plugins tr[data-slug=facebook-messenger-customer-chat] span.deactivate a' ).attr( 'href' )
            );
					}
				}
			);
    },
		deactivationModalFreetextOptionOpenHandler: function() {
			$("#fbmcc-deactivationModal ul li input[name='fbmcc-deactivationReason']").click(
				function () {
          $('div.fbmcc-deactivationReason-commentContainer').removeClass( 'fbmcc-display' );
          $( '#fbmcc-deactivationReason-commentContainer'
            + $('input[name=fbmcc-deactivationReason]:checked', '#fbmcc-deactivationForm').val() ).toggleClass( 'fbmcc-display' );
				}
			)
    },
	};

})( jQuery );
