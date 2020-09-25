<?php
/*
Plugin Name: The Official Facebook Chat Plugin
Description: With one click, you can add the Facebook Chat Plugin to your website, enabling customers to message you while browsing your website. To see and reply to those messages, simply use the same messaging tools you use for your Facebook messaging, on desktop at facebook.com, Facebook Page Manager App (available on iOS and Android), or by adding your page account to Messenger. It's free, easy to install and comes with a user interface your customers are already familiar with.
Author: Facebook
Author URI: https://developers.facebook.com
Version: 1.7

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

class Facebook_Messenger_Customer_Chat {
  function __construct() {
    include( plugin_dir_path( __FILE__ ) . 'options.php' );
    add_action( 'wp_footer', array( $this, 'fbmcc_inject_messenger' ) );
  }

  function fbmcc_inject_messenger() {
    if( get_option( 'fbmcc_pageID' ) != '' ) {
      $genCode = "";
      $genCode .= "
        <div id='fb-root'></div>
          <script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            js = d.createElement(s); js.id = id;
            js.src = 'https://connect.facebook.net/".fbmcc_sanitize_locale(get_option( 'fbmcc_locale' ))."/sdk/xfbml.customerchat.js#xfbml=1&version=v6.0&autoLogAppEvents=1';
            fjs.parentNode.insertBefore(js, fjs);
          }(document, 'script', 'facebook-jssdk'));</script>
          <div class='fb-customerchat'
            attribution='wordpress'
            attribution_version='1.7'
            page_id=".fbmcc_sanitize_page_id(get_option( 'fbmcc_pageID' ))."
          >
        </div>
        ";
      _e($genCode);
    }
  }
}

new Facebook_Messenger_Customer_Chat();
?>
