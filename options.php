<?php
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

// Settings page
add_action( 'admin_menu', function() {
  wp_register_script(
    'launch_script',
    plugins_url( '/script.js?2', __FILE__ ),
    array( 'jquery' )
  );
  wp_enqueue_script( 'launch_script' );

  add_menu_page(
    'Plugin settings',
    'Customer Chat',
    'manage_options',
    'messenger-customer-chat-plugin',
    'fbmcc_integration_settings',
    'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgi'
    . 'Pz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRw'
    . 'Oi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgdmVy'
    . 'c2lvbj0iMS4xIiBpZD0iTWVzc2VuZ2VyX01hcmsiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9y'
    . 'Zy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsi'
    . 'IHg9IjBweCIgeT0iMHB4IiB3aWR0aD0iMTAwMHB4IiBoZWlnaHQ9IjEwMDBweCIgdmlld0Jv'
    . 'eD0iMCAwIDEwMDAgMTAwMCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMTAwMCAxMDAw'
    . 'IiB4bWw6c3BhY2U9InByZXNlcnZlIj48cGF0aCBpZD0iQnViYmxlX1NoYXBlIiBmaWxsPSIj'
    . 'MjMxRjIwIiBkPSJNNDk5LjUsMTAzLjUwM2MtMjE3LjA0OSwwLTM5My4wMDIsMTY0LjUzMy0z'
    . 'OTMuMDAyLDM2Ny40OTZjMCwxMTUuNDYsNTYuOTQ1LDIxOC40ODIsMTQ2LjAwMiwyODUuODU0'
    . 'Vjg5Ny41bDEzNC4xMTgtNzQuMzk0YzM1Ljc1NCwxMC4wMDksNzMuNjQ2LDE1LjM4OSwxMTIu'
    . 'ODgyLDE1LjM4OWMyMTcuMDQ5LDAsMzkzLjAwMi0xNjQuNTM0LDM5My4wMDItMzY3LjQ5N1M3'
    . 'MTYuNTQ5LDEwMy41MDMsNDk5LjUsMTAzLjUwM3ogTTU0MC44OTEsNTk2LjMwOEw0MzkuMjQ3'
    . 'LDQ5MC43MTRMMjQzLjUsNTk4Ljk2N2wyMTQuNjA5LTIyNy43NDFMNTU5Ljc1NCw0NzYuODJM'
    . 'NzU1LjUsMzY4LjU2N0w1NDAuODkxLDU5Ni4zMDh6Ii8+PC9zdmc+'
  );
});

add_action( 'admin_enqueue_scripts', 'fbmcc_add_styles' );

add_action( 'admin_init', function() {
  register_setting( 'messenger-integration-plugin-settings', 'fbmcc_enabled' );
  register_setting( 'messenger-integration-plugin-settings', 'fbmcc_generatedCode' );
});

add_action( 'wp_ajax_update_options', 'fbmcc_update_options');

function fbmcc_update_options() {
  update_option( 'fbmcc_enabled', "1" );
  update_option( 'fbmcc_generatedCode', sanitize_textarea_field( $_POST['fbmcc_generatedCode'] ) );
  wp_die();
}

function fbmcc_add_styles() {
  wp_enqueue_style(
    'admin-styles',
    plugins_url( '/settings.css', __FILE__ ),
    false,
    '1.0',
    'all'
  );
}

function fbmcc_integration_settings() {
  ?>
  <div class="wrap">
    <form action="options.php" method="post">
      <h2>Messenger Customer Chat Settings</h2>
      <?php
        settings_fields( 'messenger-integration-plugin-settings' );
        do_settings_sections( 'messenger-integration-plugin-settings' );
      ?>
      <div class="fbmcc-card card">
        <div class="intro">
          <div>
            <h2>Getting Started?</h2>
            <p class="fbmcc-instructions">Let people start a conversation on your
              website and continue in Messenger. It's easy to set up. We'll
              give you the code to add to your website.</p>
          </div>
          <div class="fbmcc-buttonContainer">
            <button
              class="fbmcc-setupButton"
              type="button"
              onclick="fbmcc_setupCustomerChat()"
            >
              <?php
                if( get_option( 'fbmcc_generatedCode' ) == "" ) {
                  _e( 'Setup Customer Chat' );
                } else {
                  _e( 'Edit Customer Chat' );
                }
              ?>
            </button>
          </div>
        </div>
      </div>
      <div
        id="fbmcc-page-params"
        class="fbmcc-card card"
        <?php if( get_option( 'fbmcc_generatedCode' ) == "" ) {
          _e( 'style="display:none;"' );
        } ?>>
        <div>
          <p class="fbmcc-instructions">The code has already been added into your
            website. You can always go back through the setup process or edit
            the code manually below.
          </p>
        </div>
        <table class="fbmcc-settings">
          <tr valign="top">
            <th scope="row">Enabled</th>
            <td class="fbmcc-table-container">
              <div>
                <label class="fbmcc-switch">
                  <input
                    id="fbmcc-enabled"
                    value="1"
                    name="fbmcc_enabled"
                    type="checkbox"
                    <?php checked( '1', get_option( 'fbmcc_enabled' ) ); ?>
                  >
                  <span class="fbmcc-slider round"></span>
                </label>
              </div>
            </td>
          </tr>
          <tr valign="top">
            <th scope="row">Code Snippet</th>
          </tr>
        </table>
        <div class="fbmcc-codeContainer">
          <button id="fbmcc-editButton"
            class="fbmcc-editButton"
            type="button"
            onclick="fbmcc_editCode()"
          >
            Edit Code
          </button>
          <textarea
            id="fbmcc-codeArea"
            name="fbmcc_generatedCode"
            class="fbmcc-code-area"
            rows="17"
            cols="70"
            readonly="true"
          ><?php esc_html_e( stripslashes( get_option( 'fbmcc_generatedCode' ) ) ); ?>
          </textarea>
        </div>
        <?php submit_button(); ?>
      </div>
    </form>
    <div class="fbmcc-card card">
      <div class="intro">
        <p class="fbmcc-instructions"> Having a problem with Messenger customer chat?
          Report the issue on the <a
            href='https://developers.facebook.com/support/bugs/'
            target='_blank'>
            Facebook Platform Bug Reports</a> page. If you get stuck or have questions,
            you can ask for help in the
            <a
            href='https://wordpress.org/support/plugin/facebook-messenger-customer-chat'
            target='_blank'>
            Messenger Customer Chat plugin forum</a>.
        </p>
      </div>
    </div>
  </div>
<?php }
?>
