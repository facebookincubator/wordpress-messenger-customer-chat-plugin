<?php
/*
Plugin Name: The Official Facebook Chat Plugin
Description: With a few clicks, you can add the Facebook Chat Plugin to your website, enabling customers to message you while browsing your website. To see and reply to those messages, simply use the same messaging tools you use for your Facebook messaging, on desktop at facebook.com, Facebook Page Manager App (available on iOS and Android), or by adding your page account to Messenger. It's free, easy to install and comes with a user interface your customers are already familiar with.
Author: Facebook
Author URI: https://developers.facebook.com
Version: 2.0
Text Domain: facebook-messenger-customer-chat
Domain Path: /languages/
*/

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

class Facebook_Messenger_Customer_Chat {
  function __construct() {
    include( plugin_dir_path( __FILE__ ) . 'options.php' );
    add_action( 'wp_footer', array( $this, 'fbmcc_inject_messenger' ) );
    add_filter( 'plugin_action_links',
                array( $this, 'fbmcc_plugin_action_links'), 10, 2 );
    add_filter( 'plugin_row_meta',
                array( $this, 'fbmcc_register_plugin_links'), 10, 2 );
    add_action( 'plugins_loaded', 'load_plugin_textdomain' );
  }

  function fbmcc_plugin_action_links( $links, $file ) {
    $settings_url = 'admin.php?page=messenger-customer-chat-plugin';
    if ( current_user_can( 'manage_options' ) ) {
      $base = plugin_basename(__FILE__);
      if ( $file == $base ) {
        $settings_link = sprintf(
          '<a href="%s">%s</a>',
          $settings_url,
          esc_html__( 'Settings', 'facebook-messenger-customer-chat' )
        );
        array_unshift( $links, $settings_link );

      }
    }
    return $links;
  }

  function fbmcc_register_plugin_links( $links, $file ) {
    $settings_url = 'admin.php?page=messenger-customer-chat-plugin';
    $base = plugin_basename(__FILE__);
    if ( $file == $base ) {
      if ( current_user_can( 'manage_options' ) ) {
        $links[] = sprintf(
          '<a href="%s">%s</a>',
          $settings_url,
          esc_html__( 'Settings', 'facebook-messenger-customer-chat' )
        );
      }
      $links[] =
        sprintf(
          '<a href="%s">%s</a>',
          esc_url( 'https://wordpress.org/plugins/facebook-messenger-customer-chat/#faq' ),
          esc_html__( 'FAQ', 'facebook-messenger-customer-chat' )
        );
      $links[] =
        sprintf(
          '<a href="%s">%s</a>',
          esc_url( 'https://wordpress.org/support/plugin/facebook-messenger-customer-chat/' ),
          esc_html__( 'Support', 'facebook-messenger-customer-chat' )
        );
    }
    return $links;
  }

  function fbmcc_should_display() {
    $fbmcc_page_types = get_option( 'fbmcc_page_types' );
    global $wp_query;

    if( !$fbmcc_page_types || $fbmcc_page_types['all'] == "1") {
      return true;
    }

    if (
      $fbmcc_page_types['front_page'] == "1" &&
      (is_home() || is_front_page())
    ) {
      return true;
    }

    if($fbmcc_page_types['posts'] == "1" && is_single()) {
      return true;
    }

    if($fbmcc_page_types['product_pages'] == "1") {
      if ( function_exists ( 'is_product' ) && is_product() )  {
        return true;
      }
    }

    $active_pages = $fbmcc_page_types["pages"];
    $current_page = $wp_query->get_queried_object()->ID;
    $pages_all = $fbmcc_page_types['pages_all'];
    if(is_page()) {
      if( $pages_all == "1" ) {
        return true;
      } else {
        if( $active_pages && in_array( $current_page, $active_pages) ) {
          return true;
        }
      }
    }

    if( ($fbmcc_page_types['category_view'] == "1") && is_category() ) {
      return true;
    }

    if( $fbmcc_page_types['tag_view'] == "1" && is_tag() ) {
      return true;
    }

    return false;
  }

  function fbmcc_inject_messenger() {
    if( !get_option( 'fbmcc_pageID' ) ||
        get_option( 'fbmcc_pageID' ) == '' ) {
      return;
    }

    if( $this->fbmcc_should_display() ) {
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

  function load_plugin_textdomain() {
    load_plugin_textdomain( 'facebook-messenger-customer-chat', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
  }
}

new Facebook_Messenger_Customer_Chat();
?>
