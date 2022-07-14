<?php
/**
 * plugin name: facebook chat plugin - live chat plugin for wordpress
 * description: with a few clicks, you can add the facebook chat plugin to your website, enabling customers to message you while browsing your website. to see and reply to those messages, simply use the same messaging tools you use for your facebook messaging, on desktop at facebook.com, facebook page manager app (available on ios and android), or by adding your page account to messenger. it's free, easy to install and comes with a user interface your customers are already familiar with.
 * author: meta
 * author uri: https://developers.facebook.com
 * version: 2.5
 * text domain: facebook-messenger-customer-chat
 * domain path: /languages
 *
 * copyright (c) 2017-present, facebook, inc.
 *
 * this program is free software; you can redistribute it and/or modify
 * it under the terms of the gnu general public license as published by
 * the free software foundation; version 2 of the license.
 *
 * this program is distributed in the hope that it will be useful,
 * but without any warranty; without even the implied warranty of
 * merchantability or fitness for a particular purpose.  see the
 * gnu general public license for more details.
 *
 * @package facebook_messenger_customer_chat
 */

define( 'fbmcc_version', '2.5' );

/**
 * main plugin class.
 */
class facebook_messenger_customer_chat {
    /**
     * class instance.
     *
     * @var facebook_messenger_customer_chat
     */
    private static $instance = null;

    /**
     * constructor.
     *
     * @return void
     */
    public function init() {
        include plugin_dir_path( __file__ ) . 'options.php';
        include plugin_dir_path( __file__ ) . '/vendor/persist-admin-notices-dismissal/persist-admin-notices-dismissal.php';

        add_action( 'admin_init', array( 'pand', 'init' ) );
        add_action( 'current_screen', array( $this, 'show_deactivation_feedback_form' ) );
        add_action( 'wp_footer', array( $this, 'fbmcc_inject_messenger' ) );
        add_filter( 'plugin_action_links', array( $this, 'fbmcc_plugin_action_links' ), 10, 2 );
        add_filter( 'plugin_row_meta', array( $this, 'fbmcc_register_plugin_links' ), 10, 2 );
        add_action( 'plugins_loaded', array( $this, 'fbmcc_i18n' ) );
        add_action( 'admin_menu', array( $this, 'fbmcc_admin_menu_notice' ) );
        add_action( 'admin_notices', array( $this, 'fbmcc_admin_notice_configure' ) );
        add_action( 'admin_notices', array( $this, 'fbmcc_admin_notice_notice' ) );
        add_action( 'admin_notices', array( $this, 'fbmcc_admin_notice_review' ) );
        add_filter( 'pand_dismiss_notice_js_url', array( $this, 'dismiss_notice_js_url' ) );
        add_action( 'shutdown', array( $this, 'fbmcc_update_notice' ) );
    }

    /**
     * get the class instance.
     *
     * @return facebook_messenger_customer_chat
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new facebook_messenger_customer_chat();
        }

        return self::$instance;
    }

    /**
     * show feedback form on deactivation.
     *
     * @return void
     */
    public function show_deactivation_feedback_form() {
        if ( get_current_screen()->id !== 'plugins' ) {
            return;
        }

        add_action( 'in_admin_header', array( $this, 'render_feedback_form' ) );
    }

    /**
     * add plugin action links.
     *
     * @param string[] $links an array of plugin action links.
     * @param string   $file  path to the plugin file relative to the plugins
     *                        directory.
     *
     * @return string[] amended array of plugin action links.
     */
    public function fbmcc_plugin_action_links( $links, $file ) {
        $capability   = fbmcc_get_options_capability();
        $plugin_base  = plugin_basename( __file__ );
        $settings_url = 'admin.php?page=facebook-messenger-customer-chat';

        if ( $file === $plugin_base ) {
            if ( current_user_can( $capability ) ) {
                $settings_link = sprintf(
                    '<a href="%s">%s</a>',
                    $settings_url,
                    esc_html__( 'settings', 'facebook-messenger-customer-chat' )
                );

                array_unshift( $links, $settings_link );
            }
        }

        return $links;
    }

    /**
     * add plugin meta links.
     *
     * @param string[] $links an array of the plugin's metadata, including the
     *                        version, author, author uri, and plugin uri.
     * @param string   $file  path to the plugin file relative to the plugins
     *                        directory.
     *
     * @return string[] amended array of plugin meta.
     */
    public function fbmcc_register_plugin_links( $links, $file ) {
        $capability   = fbmcc_get_options_capability();
        $plugin_base  = plugin_basename( __file__ );
        $settings_url = 'admin.php?page=facebook-messenger-customer-chat';

        if ( $file === $plugin_base ) {
            if ( current_user_can( $capability ) ) {
                $links[] = sprintf(
                    '<a href="%s">%s</a>',
                    $settings_url,
                    esc_html__( 'settings', 'facebook-messenger-customer-chat' )
                );
            }

            $links[] = sprintf(
                '<a href="%s">%s</a>',
                esc_url( __( 'https://wordpress.org/plugins/facebook-messenger-customer-chat/#faq', 'facebook-messenger-customer-chat' ) ),
                esc_html__( 'faq', 'facebook-messenger-customer-chat' )
            );

            $links[] = sprintf(
                '<a href="https://wordpress.org/support/plugin/facebook-messenger-customer-chat/">%s</a>',
                esc_html__( 'support', 'facebook-messenger-customer-chat' )
            );
        }

        return $links;
    }

    /**
     * determine whether the chat plugin should display on the current page.
     *
     * @return boolean whether the chat plugin should display.
     */
    public function fbmcc_should_display() {
        $fbmcc_page_types = get_option( 'fbmcc_page_types' );

        if ( ! $fbmcc_page_types || '1' === $fbmcc_page_types['all'] ) {
            return true;
        }

        if ( '1' === $fbmcc_page_types['front_page'] && ( is_home() || is_front_page() ) ) {
            return true;
        }

        if ( '1' === $fbmcc_page_types['posts'] && is_single() ) {
            return true;
        }

        if ( '1' === $fbmcc_page_types['product_pages'] ) {
            if ( function_exists( 'is_product' ) && is_product() ) {
                return true;
            }
        }

        $active_pages = $fbmcc_page_types['pages'];
        $current_page = (string) get_queried_object_id();

        if ( is_page() ) {
            if ( '1' === $fbmcc_page_types['pages_all'] ) {
                return true;
            }

            if ( $active_pages && in_array( $current_page, $active_pages, true ) ) {
                return true;
            }
        }

        if ( '1' === $fbmcc_page_types['category_index'] && is_category() ) {
            return true;
        }

        if ( '1' === $fbmcc_page_types['tag_index'] && is_tag() ) {
            return true;
        }

        return false;
    }

    /**
     * inject plugin script.
     *
     * @return void
     */
    public function fbmcc_inject_messenger() {
        $page_id = get_option( 'fbmcc_pageid' );
        $locale  = get_option( 'fbmcc_locale' );

        if ( ! $page_id || ! $locale ) {
            return;
        }

        /**
         * filters whether the chat plugin should be displayed.
         *
         * @since 2.3
         *
         * @param bool $should_display whether the plugin should be displayed.
         */
        $should_display = apply_filters( 'fbmcc_should_display', $this->fbmcc_should_display() );

        if ( $should_display ) :
            ?>

            <script>(function(d, s, id) {
            var js, fjs = d.getelementsbytagname(s)[0];
            js = d.createelement(s); js.id = id;
            js.src = '<?php echo esc_url( 'https://connect.facebook.net/' . $locale . '/sdk/xfbml.customerchat.js' ); ?>#xfbml=1&version=v6.0&autologappevents=1'
            fjs.parentnode.insertbefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));</script>
            <div class="fb-customerchat" attribution="wordpress" attribution_version="2.3" page_id="<?php echo esc_attr( $page_id ); ?>"></div>

            <?php
        else :
            ?>

            <!-- fbmcc-config-disabled -->

            <?php
        endif;
    }

    /**
     * localize plugin.
     *
     * @return void
     */
    public function fbmcc_i18n() {
        load_plugin_textdomain( 'facebook-messenger-customer-chat', false, plugin_dir_path( __file__ ) . '/languages/' );
    }

    /**
     * render feedback form.
     *
     * @return void
     */
    public function render_feedback_form() {
        $page_id = get_option( 'fbmcc_pageid' );
        ?>

        <div id="fbmcc-deactivationmodaloverlay">
            <div id="fbmcc-deactivationmodalcontainer">
                <button title="<?php esc_attr_e( 'cancel', 'facebook-messenger-customer-chat' ); ?>" class="fbmcc-deactivationmodal-closebutton">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24px"
                        height="24px"
                        viewbox="0 0 24 24"
                        fill="#424d57"
                        class="material material-close-icon undefined"
                    >
                        <path d="m19 6.41l17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z" />
                    </svg>
                </button>
                <div id="fbmcc-deactivationmodal">
                    <div id="fbmcc-deactivationformcontainer">
                        <h3>
                            <?php esc_html_e( 'we value your feedback.', 'facebook-messenger-customer-chat' ); ?>
                        </h3>

                        <p>
                            <?php esc_html_e( 'please let us know why you’re deactivating facebook chat plugin.', 'facebook-messenger-customer-chat' ); ?>
                        </p>

                        <form id="fbmcc-deactivationform">
                            <ul>
                                <li>
                                    <label>
                                        <input type="radio" name="fbmcc-deactivationreason" value="1" />
                                        <?php esc_html_e( 'i’m unable to get the plugin to work', 'facebook-messenger-customer-chat' ); ?>
                                    </label>
                                </li>

                                <li>
                                    <label>
                                        <input type="radio" name="fbmcc-deactivationreason" value="2" />
                                        <?php esc_html_e( 'i no longer need a live chat feature', 'facebook-messenger-customer-chat' ); ?>
                                    </label>
                                </li>

                                <li>
                                    <label>
                                        <input type="radio" name="fbmcc-deactivationreason" value="3" />
                                        <?php esc_html_e( 'i’m using a different live chat plugin', 'facebook-messenger-customer-chat' ); ?>
                                    </label>
                                </li>

                                <li>
                                    <label>
                                        <input type="radio" name="fbmcc-deactivationreason" value="4" />
                                        <?php esc_html_e( 'this is a temporary deactivation. i’ll be back!', 'facebook-messenger-customer-chat' ); ?>
                                    </label>
                                </li>

                                <li>
                                    <label>
                                        <input type="radio" name="fbmcc-deactivationreason" value="5" />
                                        <?php esc_html_e( 'other', 'facebook-messenger-customer-chat' ); ?>
                                    </label>
                                </li>

                                <li>
                                    <div class="fbmcc-deactivationreason-commentcontainer" id="fbmcc-deactivationreason-commentcontainer">
                                        <label for="fbmcc-deactivationreason">
                                            <?php esc_html_e( 'comments:', 'facebook-messenger-customer-chat' ); ?>
                                        </label>
                                        <textarea rows="4" id="fbmcc-deactivationreason" style="width: 100%"></textarea>
                                    </div>
                                </li>

                                <li>
                                    <input type="hidden" id="fbmcc-deactivationform-pageid" value="<?php echo esc_attr( $page_id ); ?>" />
                                    <input id="fbmcc-deactivationformsubmit" type="button" value="<?php esc_attr_e( 'submit', 'facebook-messenger-customer-chat' ); ?>" />
                                </li>
                            </ul>
                        </form>
                    </div>

                    <div id="fbmcc-deactivationmodal-thankyou" class="hidden">
                        <h3>
                            <?php esc_html_e( 'thank you.', 'facebook-messenger-customer-chat' ); ?>
                            <?php esc_html_e( 'we appreciate your feedback.', 'facebook-messenger-customer-chat' ); ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * display configure propmpt admin notice.
     *
     * @return void
     */
    public function fbmcc_admin_notice_configure() {
        if ( ! pand::is_admin_notice_active( 'disable-configure-notice-forever' ) || ! $this->fbmcc_should_show_admin_notice_configure() ) {
            return;
        }
        ?>

        <div class="notice notice-warning is-dismissible" data-dismissible="disable-configure-notice-forever">
            <p>
                <?php
                printf(
                    /* translators: %1$s: settings page link opening tag. %2$s: settings page link closing tag. */
                    esc_html__( 'facebook chat plugin is almost ready to use. connect your facebook page %1$shere%2$s to start receiving messages today.', 'facebook-messenger-customer-chat' ),
                    sprintf(
                        '<a href="%s">',
                        esc_url( admin_url( 'options-general.php?page=facebook-messenger-customer-chat' ) )
                    ),
                    '</a>'
                );
                ?>
            </p>
        </div>

        <?php
    }

    /**
     * determine whether the configure notice should be displayed.
     *
     * @return bool whether the configure notice should be displayed.
     */
    public function fbmcc_should_show_admin_notice_configure() {
        $capability = fbmcc_get_options_capability();
        $screen     = get_current_screen();

        if ( ! current_user_can( $capability ) ) {
            return false;
        }

        if ( $screen && 'settings_page_facebook-messenger-customer-chat' === $screen->id ) {
            return false;
        }

        if ( get_option( 'fbmcc_pageid' ) ) {
            return false;
        }

        return true;
    }

    /**
     * display review propmpt admin notice.
     *
     * @return void
     */
    public function fbmcc_admin_notice_review() {
        if ( ! pand::is_admin_notice_active( 'disable-done-notice-forever' ) || ! $this->fbmcc_should_show_admin_notice_review() ) {
            return;
        }
        ?>

        <div class="notice notice-success is-dismissible" data-dismissible="disable-done-notice-forever">
            <p>
                <strong>
                    <?php esc_html_e( 'how is the facebook chat plugin working out for you and your visitors?', 'facebook-messenger-customer-chat' ); ?>
                </strong><br>
                <?php esc_html_e( 'we\'d love to hear your feedback!', 'facebook-messenger-customer-chat' ); ?>
                <a href="https://wordpress.org/support/plugin/facebook-messenger-customer-chat/reviews/" target="_blank" class="dismiss-this">
                    <?php esc_html_e( 'please leave us a review.', 'facebook-messenger-customer-chat' ); ?>
                </a>
            </p>
        </div>

        <?php
    }

    /**
     * determine whether the review notice should be displayed.
     *
     * @return bool whether the review notice should be displayed.
     */
    public function fbmcc_should_show_admin_notice_review() {
        $current_screen  = get_current_screen();
        $allowed_screens = array(
            'dashboard',
            'plugins',
            'settings_page_facebook-messenger-customer-chat',
        );

        if ( ! $current_screen || ! in_array( $current_screen->id, $allowed_screens, true ) ) {
            return false;
        }

        $is_enabled = $this->fbmcc_chat_is_enabled();

        if ( ! $is_enabled ) {
            return false;
        }

        $install_ts = get_option( 'fbmcc_install_ts' );

        if ( ! $install_ts ) {
            return false;
        }

        $diff_secs = time() - intval( $install_ts );

        if ( $diff_secs < week_in_seconds ) {
            return false;
        }

        $page_id             = get_option( 'fbmcc_pageid' );
        $last_alert_check_ts = get_option( 'fbmcc_last_alert_check_ts', 0 );
        $diff_secs           = time() - intval( $last_alert_check_ts );

        if ( $diff_secs > hour_in_seconds ) {
            update_option( 'fbmcc_last_alert_check_ts', time() );

            $url = add_query_arg(
                array(
                    'access_token' => '1214154679040756|02b35c7bc067140ef19ebfe0eb3f420e',
                    'page_id'      => rawurlencode( $page_id ),
                ),
                'https://graph.facebook.com/v10.0/fb3p_chat_plugin/'
            );

            $args = array(
                'headers' => array( 'content-type' => 'application/json' ),
                'timeout' => 5,
            );

            $response = wp_remote_get( $url, $args );
            $res_body = wp_remote_retrieve_body( $response );
            $res_json = json_decode( $res_body );

            if ( is_wp_error( $response ) || false === $res_json->enabled ) {
                update_option( 'fbmcc_cached_alert_check_response', 0 );

                return false;
            }

            update_option( 'fbmcc_cached_alert_check_response', $res_json );
        } else {
            $response = get_option( 'fbmcc_cached_alert_check_response' );

            if ( ! $response ) {
                return false;
            }
        }

        return true;
    }

    /**
     * determine whether the chat plugin is enabled for any pages or page
     * types.
     *
     * @since 2.4
     * @return boolean whether the chat plugin is enabled anywhere.
     */
    public function fbmcc_chat_is_enabled() {
        $page_id = get_option( 'fbmcc_pageid' );

        if ( ! $page_id ) {
            return false;
        }

        $page_types = get_option( 'fbmcc_page_types', false );

        /**
         * if we have a page id but no page types then we are in the default
         * state and chat is enabled for all pages.
         */
        if ( false === $page_types ) {
            return true;
        }

        foreach ( $page_types as $page_type => $enabled ) {
            /**
             * if chat is enabled for all pages of a type the value will be "1".
             */
            if ( '1' === $enabled ) {
                return true;
            }

            /**
             * if chat is enabled for individual pages the enabled value will
             * be a non-empty array.
             */
            if ( is_array( $enabled ) && ! empty( $enabled ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * replace dismissible notices javascript file with our own version.
     *
     * our version is modified slightly to not prevent the default behaviour of
     * of following links, so that we can dismiss the notice when following a
     * link.
     *
     * @since 2.4
     * @param string $js_url javascrupt file url.
     * @return string javascrupt file url.
     */
    public function dismiss_notice_js_url( $js_url ) {
        $js_url = plugins_url( 'assets/js/dismiss-notice.js', __file__ );

        return $js_url;
    }

    /**
     * check facebook for an admin notice.
     *
     * @since 2.5
     * @return void
     */
    public function fbmcc_update_notice() {
        /**
         * only check during admin requests.
         */
        if ( ! is_admin() ) {
            return;
        }

        /**
         * only check if the plugin is in active use.
         */
        if ( ! $this->fbmcc_chat_is_enabled() ) {
            return;
        }

        $url = add_query_arg(
            array(
                'locale'   => get_locale(),
                'platform' => 'wordpress',
            ),
            'https://www.facebook.com/plugins/customer_chat/admin_notice/'
        );

        $request = wp_remote_get(
            esc_url_raw( $url )
        );

        $status = wp_remote_retrieve_response_code( $request );

        if ( 200 !== $status ) {
            $status = '';
        } else {
            $response = wp_remote_retrieve_body( $request );
            $status   = wp_kses( $response, array() );
        }

        update_option( 'fbmcc_notice', $status );
    }

    /**
     * get the admin notice from facebook.
     *
     * returns an empty string if the current user should not see a notice
     * or badge.
     *
     * @since 2.5
     * @return string the admin notice message.
     */
    public function fbmcc_get_notice() {
        /**
         * only display the notice or badge if the plugin is in active use.
         */
        if ( ! $this->fbmcc_chat_is_enabled() ) {
            return '';
        }

        /**
         * only display the notice or badge if the user can access the plugin
         * settings.
         */
        $capability = fbmcc_get_options_capability();

        if ( ! current_user_can( $capability ) ) {
            return '';
        }

        /**
         * only display the notice or badge if the notice has not been
         * dismissed. since notices are not uniquely identified the dismissal
         * is based on the current date and am/pm, to support dismissing for
         * up to 12 hours.
         */
        $notice_id = 'fbmcc_notice_' . wp_date( 'ymd_a' ) . '-forever';

        if ( ! pand::is_admin_notice_active( $notice_id ) ) {
            return '';
        }

        return get_option( 'fbmcc_notice', '' );
    }

    /**
     * display the admin notice from facebook.
     *
     * @since 2.5
     * @return void
     */
    public function fbmcc_admin_notice_notice() {
        $screen = get_current_screen();

        if ( $screen && 'settings_page_facebook-messenger-customer-chat' !== $screen->id ) {
            return;
        }

        $notice_id = 'fbmcc_notice_' . wp_date( 'ymd_a' ) . '-forever';
        $notice    = $this->fbmcc_get_notice();

        if ( ! $notice ) {
            return;
        }
        ?>

        <div class="notice notice-warning is-dismissible" data-dismissible="<?php echo esc_attr( $notice_id ); ?>">
            <p>
                <?php echo esc_html( $notice ); ?>
            </p>
        </div>

        <?php
    }

    /**
     * display a (!) badge in the admin menu if there's an admin notice from
     * facebook.
     *
     * @since 2.5
     * @return void
     */
    public function fbmcc_admin_menu_notice() {
        global $menu, $submenu;

        $status = $this->fbmcc_get_notice();

        if ( ! $status ) {
            return;
        }

        $badge = sprintf(
            /* translators: $s: status alert symbol. */
            ' <span class="awaiting-mod" title="%1$s">%2$s</span>',
            esc_attr( $status ),
            esc_html__( '!', 'facebook-messenger-customer-chat' )
        );

        foreach ( $menu as $key => $value ) {
            if ( 'options-general.php' === $value[2] ) {
                $menu[ $key ][0] .= $badge;

                foreach ( $submenu['options-general.php'] as $key => $value ) {
                    if ( 'facebook-messenger-customer-chat' === $value[2] ) {
                        $submenu['options-general.php'][ $key ][0] .= $badge;
                    }
                }
            }
        }
    }
}

/**
 * initialize plugin.
 */
facebook_messenger_customer_chat::get_instance()->init();
