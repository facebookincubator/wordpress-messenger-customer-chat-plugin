<?php
/*
Plugin Name: The Official Facebook Chat Plugin
Description: With a few clicks, you can add the Facebook Chat Plugin to your website, enabling customers to message you while browsing your website. To see and reply to those messages, simply use the same messaging tools you use for your Facebook messaging, on desktop at facebook.com, Facebook Page Manager App (available on iOS and Android), or by adding your page account to Messenger. It's free, easy to install and comes with a user interface your customers are already familiar with.
Author: Facebook
Author URI: https://developers.facebook.com
Version: 1.8

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
    add_filter( 'plugin_action_links',
                array( $this, 'fbmcc_plugin_action_links'), 10, 2 );
    add_filter( 'plugin_row_meta',
                array( $this, 'fbmcc_register_plugin_links'), 10, 2 );
  }

  function fbmcc_plugin_action_links( $links, $file ) {
    if ( current_user_can( 'manage_options' ) ) {
      $base = plugin_basename(__FILE__);
      if ( $file == $base ) {
        $settings_link = '<a href="admin.php?
          page=messenger-customer-chat-plugin">Settings</a>';
        array_unshift( $links, $settings_link );
      }
    }
    return $links;
  }

  function fbmcc_register_plugin_links( $links, $file ) {
    $base = plugin_basename(__FILE__);
    if ( $file == $base ) {
      if ( current_user_can( 'manage_options' ) ) {
        $links[] = '<a href="admin.php?page=messenger-customer-chat-plugin">
          Settings</a>';
      }
      $links[] =
        '<a href=
          "https://wordpress.org/plugins/facebook-messenger-customer-chat/#faq"
          target="_blank">FAQ</a>';
      $links[] =
        '<a href="https://wordpress.org/support/plugin/facebook-messenger-customer-chat/"
        target="_blank">Support</a>';
    }
    return $links;
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
            attribution_version='1.8'
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
