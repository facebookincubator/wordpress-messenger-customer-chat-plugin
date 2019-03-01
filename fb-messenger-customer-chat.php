<?php
/*
Plugin Name: Messenger Customer Chat
Description: Messenger Customer Chat is the official free Messenger customer chat plugin for WordPress by Facebook. This plugin allows you to interact with your customers using Messenger by integrating it on your WordPress website in three simple steps. To get started, go to your Wordpress Dashboard -> Customer Chat -> click on "Setup Customer Chat."
Author: Facebook
Author URI: https://developers.facebook.com
Version: 1.2

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
  const URL_PREFIX = 'https://connect.facebook.net/';
  const URL_SUFFIX = '/sdk.js#xfbml=1&version=v2.12&autoLogAppEvents=1';

  function __construct() {
    include( plugin_dir_path( __FILE__ ) . 'options.php' );
    add_action( 'wp_footer', array( $this, 'fbmcc_inject_messenger' ) );
  }

  function fbmcc_inject_messenger() {
    if( get_option( 'fbmcc_enabled' ) == '1'
      && get_option( 'fbmcc_generatedCode' ) != ''
    ) {
      _e( stripslashes( get_option( 'fbmcc_generatedCode' ) ) );
    }
  }
}

new Facebook_Messenger_Customer_Chat();
?>
