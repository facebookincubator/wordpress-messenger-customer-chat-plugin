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

add_action( 'admin_enqueue_scripts', 'fbmcc_add_styles' );
add_action( 'admin_enqueue_scripts', 'fmcc_localize_ajax' );
add_action( 'admin_menu', function() {

  add_menu_page(
    'Plugin settings',
    'Facebook Chat',
    'manage_options',
    'facebook-messenger-customer-chat',
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

add_action( 'wp_ajax_fbmcc_update_options', 'fbmcc_update_options');
add_action( 'current_screen', array( $this, 'show_deactivation_feedback_form' ) );
add_action( 'plugins_loaded', array( $this, 'fbmcc_i18n' ) );

function fbmcc_update_options() {

  if ( current_user_can( 'manage_options' ) ) {
    check_ajax_referer( 'update_fmcc_code' );

    if ($_POST['pageTypes']) { update_option( 'fbmcc_page_types', $_POST['pageTypes']); }
    if ($_POST['pageID']) { update_option( 'fbmcc_pageID', fbmcc_sanitize_page_id($_POST['pageID'])); }
    if ($_POST['locale']) { update_option( 'fbmcc_locale', fbmcc_sanitize_locale($_POST['locale'])); }
  }
  wp_die();
}

function fbmcc_sanitize_page_id($input) {
  if ( preg_match('/^\d+$/', $input) ) {
    return $input;
  } else {
    return '';
  }
}

function fbmcc_sanitize_locale($input) {
  if ( preg_match('/^[A-Za-z_]{4,5}$/', $input) ){
    return $input;
  } else {
    return '';
  }
}

function fbmcc_add_styles() {
  wp_enqueue_style(
    'fbmcc-admin-styles',
    plugins_url( '/settings.css', __FILE__ ),
    false,
    '2.1',
    'all'
  );
}

function fmcc_localize_ajax() {

  wp_register_script( 'code_script',
    plugin_dir_url( __FILE__ ) . 'script.js', '', '2.1' );

  if ( current_user_can( 'manage_options' ) ) {
    $ajax_object = array(
      'nonce' => wp_create_nonce( 'update_fmcc_code' )
    );
    wp_localize_script( 'code_script', 'ajax_object', $ajax_object );
  }

  wp_enqueue_script( 'code_script' );

}

function fbmcc_i18n() {
  load_plugin_textdomain( 'facebook-messenger-customer-chat', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

function fbmcc_integration_settings() {
  $pages_arr = get_pages();
  ?>
  <div class="wrap">
    <h2><?=esc_html__( 'Facebook Chat Plugin Settings', 'facebook-messenger-customer-chat' )?></h2>
    <div class="fbmcc-card card">
      <div class="intro">
        <div>
          <h2><?=esc_html__( 'Getting Started?', 'facebook-messenger-customer-chat' )?></h2>
          <p class="fbmcc-instructions"><?=esc_html__( 'Let people start a conversation on '.
            'your website and continue in Messenger. It\'s easy to set up. Chats '.
            'started on your website can be continued in the customers\' '.
            'Messenger app, so you never lose connections with your customers. '.
            'Even those without a Facebook Messenger account can chat with you '.
            'in guest mode, so you can reach more customers than ever.',
            'facebook-messenger-customer-chat' )?>
          </p>
        </div>
        <div class="fbmcc-buttonContainer">
          <button
            class="fbmcc-setupButton"
            type="button"
            onclick="fbmcc_setupCustomerChat()"
          >
            <?php
            if( get_option( 'fbmcc_pageID' ) == "" ) {
              _e( 'Setup Chat Plugin', 'facebook-messenger-customer-chat' );
            } else {
              _e( 'Edit Chat Plugin', 'facebook-messenger-customer-chat' );
            }
            ?>
          </button>
        </div>
      </div>
    </div>
    <div
      id="fbmcc-page-params"
      class="fbmcc-card card
      <?php if( get_option( 'fbmcc_pageID' ) == "" ) {
        'hidden';
      } ?>">
      <div>
        <h2><?=esc_html__( 'Setup status', 'facebook-messenger-customer-chat' )?></h2>
        <p class="fbmcc-instructions"><?=esc_html__( 'The plugin code has already been added '.
          'into your website. You can always go back through the setup process '.
          'to customize the plugin.', 'facebook-messenger-customer-chat' )?>
        </p>
        <h2><?=esc_html__( 'Advanced Configuration', 'facebook-messenger-customer-chat' )?></h2>
        <?php
          $fbmcc_page_types = get_option( 'fbmcc_page_types' );
          $active_pages = $fbmcc_page_types['pages'];
          if (!$active_pages) { $active_pages = Array(); }
        ?>
        <ul>
          <li>
            <table>
              <tr>
                <td><?=esc_html__( 'Deploy Chat plugin on:', 'facebook-messenger-customer-chat' )?></td>
                <td>
                  <select id="fbmcc-deploymentSelector" class="fbmcc-displaySetting">
                    <option value="1"
                    <?php if ((!$fbmcc_page_types) || ($fbmcc_page_types['all'] == "1")) { echo "selected"; } ?>>
                      <?=esc_html__( 'All pages', 'facebook-messenger-customer-chat' )?>
                    </option>
                    <option value="2"
                    <?php if ($fbmcc_page_types['all'] == "0") { echo "selected"; } ?>>
                      <?=esc_html__( 'Custom pages', 'facebook-messenger-customer-chat' )?>
                    </option>
                  </select>
                </td>
              </tr>
              <tr>
                <td></td>
                <td>
                  <div class="fbmcc-deploymentMenu
                  <?php if ((!$fbmcc_page_types) || ($fbmcc_page_types['all'] == "1")) { echo "hidden"; } ?>">
                    <ul>
                      <li>
                        <input type="checkbox" id="cbShowFrontPage" class="fbmcc-displaySetting"
                          <?php echo
                            ( isset($fbmcc_page_types['front_page'])
                              && ($fbmcc_page_types['front_page'] == "1") )
                            ? 'checked' : '';?>/>
                              Homepage
                      </li>
                      <li>
                        <input type="checkbox" id="cbShowPosts" class="fbmcc-displaySetting fbmcc-menuParentItem" />
                        <a
                          href="javascript:;"
                          id="fbmcc-postsSubmenuLink"
                          class="fbmcc-menuParentLink"
                          title="Click for granular options">
                          Posts
                          <img
                            src="<?=plugin_dir_url( __FILE__ ) . 'images/chevron-right.png';?>"
                            class="fbmcc-chevron" alt="&gt;" /></a>
                        <ul class="fbmcc-submenu hidden">
                          <li>
                            <input type="checkbox" id="cbShowSinglePostView" class="fbmcc-displaySetting fbmcc-submenuOption"
                            <?php echo
                              ( isset($fbmcc_page_types['posts'])
                                && ($fbmcc_page_types['posts'] == "1") )
                              ? 'checked' : '';?>/> Single post view
                          </li>
                          <li>
                            <input type="checkbox" id="cbShowCategoryIndex" class="fbmcc-displaySetting fbmcc-submenuOption"
                            <?php echo
                              ( isset($fbmcc_page_types['category_index'])
                                && ($fbmcc_page_types['category_index'] == "1") )
                              ? 'checked' : '';?>/> Category view
                          </li>
                          <li>
                            <input type="checkbox" id="cbShowTagsIndex" class="fbmcc-displaySetting fbmcc-submenuOption"
                              <?php echo
                                ( isset($fbmcc_page_types['tag_index'])
                                  && ($fbmcc_page_types['tag_index'] == "1") )
                                ? 'checked' : '';?>/> Tags view
                          </li>
                        </ul>
                      </li>
                      <li>
                        <input type="checkbox" id="cbShowPages" class="fbmcc-displaySetting fbmcc-menuParentItem"
                          <?php echo
                            ( isset($fbmcc_page_types['pages_all'])
                              && ($fbmcc_page_types['pages_all'] == "1") )
                            ? 'checked' : '';?> />
                            <?php
                              if (empty($pages_arr)) {
                            ?>Pages<?php
                              } else {
                            ?><a href="javascript:;" id="fbmcc-pagesSubmenuLink" class="fbmcc-menuParentLink" title="Click for granular options">Pages <img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/chevron-right.png'; ?>" class="fbmcc-chevron" alt="&gt;" /></a><?php
                              }
                            ?>
                        <ul class="fbmcc-submenu hidden">
                          <?php
                            foreach($pages_arr as $page) {
                          ?>
                          <li>
                            <input type="checkbox" id="pageid_<?php echo $page->ID; ?>" class="fbmcc-displaySetting fbmcc-submenuOption fbmcc-activePageOption"<?php
                              if( ($fbmcc_page_types['pages_all'] == "1") || (in_array( $page->ID, $active_pages )) ) {
                                echo 'checked';
                              }
                            ?>/> <?php echo $page->post_title; ?>
                          </li>
                          <?php
                            }
                          ?>
                        </ul>
                      </li>
                      <?php
                        if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
                      ?>
                      <li>
                        <input type="checkbox" id="cbShowProductPages" class="fbmcc-displaySetting"
                          <?php echo
                            ( isset($fbmcc_page_types['product_pages'])
                              && ($fbmcc_page_types['product_pages'] == "1") )
                              ? 'checked' : '';?>/> WooCommerce
                              <?=esc_html__( 'Product pages', 'facebook-messenger-customer-chat' )?>
                      </li>
                      <?php
                        }
                      ?>
                    </ul>
                  </div>
                  <div id="fbmcc-saveStatus">
                    <div class="hidden" id="fbmcc-saveStatus-error">
                      <?=esc_html__( 'Error saving settings.', 'facebook-messenger-customer-chat' )?></div>
                    <div class="hidden" id="fbmcc-saveStatus-saved">
                      <?=esc_html__( 'Settings saved.', 'facebook-messenger-customer-chat' )?></div>
                    <div class="hidden" id="fbmcc-saveStatus-saving">
                      <?=esc_html__( 'Saving settings...', 'facebook-messenger-customer-chat' )?></div>
                  </div>
                </td>
              </tr>
            </table>
          </li>
        </ul>
      </div>
    </div>
    <div class="fbmcc-card card">
      <div class="intro">
        <p class="fbmcc-instructions">
          <a href='https://developers.facebook.com/terms/'>
            <?=esc_html__( 'Use of this plugin is subject to Facebook\'s Platform Terms',
              'facebook-messenger-customer-chat' )?></a><br><br>
            <?=esc_html__( 'Having a problem setting up or using the Chat Plugin?',
              'facebook-messenger-customer-chat' )?><br>
        <ul>
          <li><a href='https://www.facebook.com/business/help/789975831794468'>
            <?=esc_html__( 'Please consult our Troubleshooting Guide.', 'facebook-messenger-customer-chat' )?></a>
          </li>
          <li><a href='https://wordpress.org/support/plugin/facebook-messenger-customer-chat/'>
            <?=esc_html__( 'If the troubleshooting steps in the guide do not solve your problem, please post '.
            'in the plugin support forum.', 'facebook-messenger-customer-chat' )?></a>
          </li>
        </ul>
        </p>
      </div>
    </div>
  </div>
<?php }

?>
