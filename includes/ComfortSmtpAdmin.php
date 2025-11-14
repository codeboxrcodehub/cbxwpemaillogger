<?php

namespace Comfort\Crm\Smtp;

//phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

use Comfort\Crm\Smtp\Helpers\ComfortSmtpHelpers;
use Comfort\Crm\Smtp\Models\SmtpLog;

class ComfortSmtpAdmin {

    private $settings;

    private $version;

    public function __construct() {
        $this->settings = new ComfortSmtpSettings();

        $this->version = time();
    }

    /**
     * Create admin menu for this plugin .
     *
     * @since 1.0.0
     */
    public function create_menus() {

        //review listing page
        add_menu_page( esc_html__( 'Email log listing', 'cbxwpemaillogger' ),
                esc_html__( 'Email SMTP', 'cbxwpemaillogger' ),
                'comfortsmtp_log_manage',
                'comfortsmtp_log',
                [ $this, 'display_comfortsmtp_listing_page' ],
                COMFORTSMTP_ROOT_URL . 'assets/images/icon_20.png',
                '6' );


        //add settings for this plugin
        add_submenu_page( 'comfortsmtp_log',
                esc_html__( 'Global Setting', 'cbxwpemaillogger' ),
                esc_html__( 'Global Setting', 'cbxwpemaillogger' ),
                'comfortsmtp_settings_manage',
                'comfortsmtp_settings',
                [ $this, 'display_settings_submenu_page' ] );

        //add settings for this plugin
        add_submenu_page( 'comfortsmtp_log',
                esc_html__( 'Email Testing', 'cbxwpemaillogger' ),
                esc_html__( 'Email Testing', 'cbxwpemaillogger' ),
                'comfortsmtp_settings_manage',
                'comfortsmtp_emailtesting',
                [ $this, 'display_plugin_admin_email_testing' ] );

        // Tools submenu add
        add_submenu_page(
                'comfortsmtp_log',
                esc_html__( 'Tools', 'cbxwpemaillogger' ),
                esc_html__( 'Tools', 'cbxwpemaillogger' ),
                'comfortsmtp_settings_manage',
                'comfortsmtp_tools',
                [ $this, 'display_tools_submenu_page' ]
        );

        add_submenu_page( 'comfortsmtp_log',
                esc_html__( 'Helps & Updates', 'cbxwpemaillogger' ),
                esc_html__( 'Helps & Updates', 'cbxwpemaillogger' ),
                'comfortsmtp_settings_manage',
                'comfortsmtp_support',
                [ $this, 'comfortsmtp_helps_updates_display' ] );


        global $submenu;
        if ( isset( $submenu['comfortsmtp_log'][0][0] ) ) {
            $submenu['comfortsmtp_log'][0][0] = esc_html__( 'Email Logs', 'cbxwpemaillogger' );
        }
    } //end method create_admin_menu

    /**
     * Initialized settings api
     * Author - @tareq_hasan
     */
    public function settings_init() {
        //set the settings
        $this->settings->set_sections( $this->get_settings_sections() );
        $this->settings->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings->admin_init();

    } //end method settings_init

    /**
     * Set global setting sections
     *
     * @return mixed|null
     */
    public function get_settings_sections() {
        return ComfortSmtpHelpers::get_settings_sections();
    } //end method get_settings_sections

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    public function get_settings_fields() {
        return ComfortSmtpHelpers::get_settings_fields();
    } //end method get_settings_fields

    /**
     * Register the stylesheets for the admin area.
     *
     * @since 1.0.0
     */
    public function enqueue_styles() {
        $version = $this->version;
        //phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

        $css_url_part     = COMFORTSMTP_ROOT_URL . 'assets/css/';
        $js_url_part      = COMFORTSMTP_ROOT_URL . 'assets/js/';
        $vendors_url_part = COMFORTSMTP_ROOT_URL . 'assets/vendors/';

        $css_path_part     = COMFORTSMTP_ROOT_PATH . 'assets/css/';
        $js_path_part      = COMFORTSMTP_ROOT_PATH . 'assets/js/';
        $vendors_path_part = COMFORTSMTP_ROOT_PATH . 'assets/vendors/';

        wp_register_style( 'comfortsmtp-admin', $css_url_part . 'comfortsmtp-admin.css', [], $version, 'all' );
        wp_register_style( 'comfortsmtp-builder', $css_url_part . 'comfortsmtp-builder.css', [], $version, 'all' );

        wp_register_style( 'select2', $vendors_url_part . 'select2/select2.min.css', [], $version );
        wp_register_style( 'pickr', $vendors_url_part . 'pickr/classic.min.css', [], $version );
        wp_register_style( 'awesome-notifications', $vendors_url_part . 'awesome-notifications/style.css', [],
                $version );

        wp_register_style( 'comfortsmtp-settings', $css_url_part . 'comfortsmtp-settings.css', [
                'select2',
                'pickr',
                'awesome-notifications'
        ], $version, 'all' );

        wp_enqueue_style( 'select2' );
        wp_enqueue_style( 'pickr' );
        wp_enqueue_style( 'awesome-notifications' );

        //non vue js pages
        if ( $page == 'comfortsmtp_settings' ) {
            wp_enqueue_style( 'comfortsmtp-settings' );
            wp_enqueue_style( 'comfortsmtp-admin' );
        }

        //vue js pages
        if ( in_array( $page, [ 'comfortsmtp_log', 'comfortsmtp_emailtesting' ] ) ) {
            wp_enqueue_style( 'comfortsmtp-admin' );
            wp_enqueue_style( 'comfortsmtp-builder' );
        }

        if ( $page == 'comfortsmtp_support' ) {
            wp_enqueue_style( 'comfortsmtp-admin' );
        }

        if ( $page == 'comfortsmtp_tools' ) {
            wp_enqueue_style( 'comfortsmtp-admin' );
            //wp_enqueue_style( 'comfortsmtp-settings' );
        }

        // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
        wp_register_style( 'comfortsmtp-menuhandle', false );
        wp_enqueue_style( 'comfortsmtp-menuhandle' );
        wp_add_inline_style( 'comfortsmtp-menuhandle',
                '#adminmenu .toplevel_page_comfortsmtp_dashboard .wp-menu-image img{  max-width: 20px !important; height: auto !important; position: relative !important; top: -2px; }' );

    } //end method enqueue_styles

    /**
     * Register the JavaScript for the admin area.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts() {
        $current_user = wp_get_current_user();
        $blog_id      = is_multisite() ? get_current_blog_id() : null;
        $version      = $this->version;

        //phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

        $js_url_part_vanila = COMFORTSMTP_ROOT_URL . 'assets/js/vanila/';
        $js_url_part_build  = COMFORTSMTP_ROOT_URL . 'assets/js/build/';
        $vendors_url_part   = COMFORTSMTP_ROOT_URL . 'assets/vendors/';


        //vue js pages

        if ( $page == 'comfortsmtp_log' ) {
            $js_translations = ComfortSmtpHelpers::comfortsmtp_log_builder_js_translation( $current_user, $blog_id );

            if ( defined( 'COMFORTSMTP_DEV_MODE' ) && COMFORTSMTP_DEV_MODE == true ) {
                //for development version
                wp_register_script( 'comfortsmtp_form_vue_dev',
                        'http://localhost:8880/assets/vuejs/apps/admin/comfortsmtplogs.js', [], $version, true );
                wp_localize_script( 'comfortsmtp_form_vue_dev', 'comfortsmtp_vue_var', $js_translations );
                wp_enqueue_script( 'comfortsmtp_form_vue_dev' );
            } else {
                // for production
                wp_register_script( 'comfortsmtp_form_vue_main', $js_url_part_build . 'comfortsmtplogs.js', [],
                        $version, true );
                wp_localize_script( 'comfortsmtp_form_vue_main', 'comfortsmtp_vue_var', $js_translations );
                wp_enqueue_script( 'comfortsmtp_form_vue_main' );
            }
        }

        //vue js pages

        if ( $page == 'comfortsmtp_emailtesting' ) {
            $js_translations = ComfortSmtpHelpers::comfortsmtp_test_email_js_translation( $current_user, $blog_id );

            if ( defined( 'COMFORTSMTP_DEV_MODE' ) && COMFORTSMTP_DEV_MODE == true ) {
                //for development version
                wp_register_script( 'comfortsmtp_form_vue_dev',
                        'http://localhost:8880/assets/vuejs/apps/admin/comfortsmtptest.js', [], $version, true );
                wp_localize_script( 'comfortsmtp_form_vue_dev', 'comfortsmtp_vue_var', $js_translations );
                wp_enqueue_script( 'comfortsmtp_form_vue_dev' );
            } else {
                // for production
                wp_register_script( 'comfortsmtp_form_vue_main', $js_url_part_build . 'comfortsmtptest.js', [],
                        $version, true );
                wp_localize_script( 'comfortsmtp_form_vue_main', 'comfortsmtp_vue_var', $js_translations );
                wp_enqueue_script( 'comfortsmtp_form_vue_main' );
            }
        }

        //non vue js
        if ( $page == 'comfortsmtp_settings' ) {
            wp_register_script( 'select2', $vendors_url_part . 'select2/select2.min.js', [ 'jquery' ], $version,
                    true );
            wp_register_script( 'pickr', $vendors_url_part . 'pickr/pickr.min.js', [], $version, true );
            wp_register_script( 'awesome-notifications', $vendors_url_part . 'awesome-notifications/script.js', [],
                    $version, true );


            wp_register_script( 'comfortsmtp-settings', $js_url_part_vanila . 'comfortsmtp-settings.js', [
                    'jquery',
                    'select2',
                    'pickr',
                    'awesome-notifications'
            ], $version, true );

            $translation_placeholder = apply_filters(
                    'comfortsmtp_setting_js_vars',
                    [
                            'ajaxurl'                  => admin_url( 'admin-ajax.php' ),
                            'ajax_fail'                => esc_html__( 'Request failed, please reload the page.',
                                    'cbxwpemaillogger' ),
                            'nonce'                    => wp_create_nonce( 'comfortsmtpnonce' ),
                            'is_user_logged_in'        => is_user_logged_in() ? 1 : 0,
                            'please_select'            => esc_html__( 'Please Select', 'cbxwpemaillogger' ),
                            'search'                   => esc_html__( 'Search...', 'cbxwpemaillogger' ),
                            'upload_title'             => esc_html__( 'Window Title', 'cbxwpemaillogger' ),
                            'search_placeholder'       => esc_html__( 'Search here', 'cbxwpemaillogger' ),
                            'teeny_setting'            => [
                                    'teeny'         => true,
                                    'media_buttons' => true,
                                    'editor_class'  => '',
                                    'textarea_rows' => 5,
                                    'quicktags'     => false,
                                    'menubar'       => false,
                            ],
                            'copycmds'                 => [
                                    'copy'       => esc_html__( 'Copy', 'cbxwpemaillogger' ),
                                    'copied'     => esc_html__( 'Copied', 'cbxwpemaillogger' ),
                                    'copy_tip'   => esc_html__( 'Click to copy', 'cbxwpemaillogger' ),
                                    'copied_tip' => esc_html__( 'Copied to clipboard', 'cbxwpemaillogger' ),
                            ],
                            'confirm_msg'              => esc_html__( 'Are you sure to remove this step?', 'cbxwpemaillogger' ),
                            'confirm_msg_all'          => esc_html__( 'Are you sure to remove all steps?', 'cbxwpemaillogger' ),
                            'confirm_yes'              => esc_html__( 'Yes', 'cbxwpemaillogger' ),
                            'confirm_no'               => esc_html__( 'No', 'cbxwpemaillogger' ),
                            'are_you_sure_global'      => esc_html__( 'Are you sure?', 'cbxwpemaillogger' ),
                            'are_you_sure_delete_desc' => esc_html__( 'Once you delete, it\'s gone forever. You can not revert it back.',
                                    'cbxwpemaillogger' ),
                            'pickr_i18n'               => [
                                // Strings visible in the UI
                                    'ui:dialog'       => esc_html__( 'color picker dialog', 'cbxwpemaillogger' ),
                                    'btn:toggle'      => esc_html__( 'toggle color picker dialog', 'cbxwpemaillogger' ),
                                    'btn:swatch'      => esc_html__( 'color swatch', 'cbxwpemaillogger' ),
                                    'btn:last-color'  => esc_html__( 'use previous color', 'cbxwpemaillogger' ),
                                    'btn:save'        => esc_html__( 'Save', 'cbxwpemaillogger' ),
                                    'btn:cancel'      => esc_html__( 'Cancel', 'cbxwpemaillogger' ),
                                    'btn:clear'       => esc_html__( 'Clear', 'cbxwpemaillogger' ),

                                // Strings used for aria-labels
                                    'aria:btn:save'   => esc_html__( 'save and close', 'cbxwpemaillogger' ),
                                    'aria:btn:cancel' => esc_html__( 'cancel and close', 'cbxwpemaillogger' ),
                                    'aria:btn:clear'  => esc_html__( 'clear and close', 'cbxwpemaillogger' ),
                                    'aria:input'      => esc_html__( 'color input field', 'cbxwpemaillogger' ),
                                    'aria:palette'    => esc_html__( 'color selection area', 'cbxwpemaillogger' ),
                                    'aria:hue'        => esc_html__( 'hue selection slider', 'cbxwpemaillogger' ),
                                    'aria:opacity'    => esc_html__( 'selection slider', 'cbxwpemaillogger' ),
                            ],
                            'awn_options'              => [
                                    'tip'           => esc_html__( 'Tip', 'cbxwpemaillogger' ),
                                    'info'          => esc_html__( 'Info', 'cbxwpemaillogger' ),
                                    'success'       => esc_html__( 'Success', 'cbxwpemaillogger' ),
                                    'warning'       => esc_html__( 'Attention', 'cbxwpemaillogger' ),
                                    'alert'         => esc_html__( 'Error', 'cbxwpemaillogger' ),
                                    'async'         => esc_html__( 'Loading', 'cbxwpemaillogger' ),
                                    'confirm'       => esc_html__( 'Confirmation', 'cbxwpemaillogger' ),
                                    'confirmOk'     => esc_html__( 'OK', 'cbxwpemaillogger' ),
                                    'confirmCancel' => esc_html__( 'Cancel', 'cbxwpemaillogger' )
                            ],
                            'global_setting_link_html' => '<a href="' . admin_url( 'admin.php?page=comfortsmtp_settings' ) . '"  class="button outline primary pull-right">' . esc_html__( 'Global Settings',
                                            'cbxwpemaillogger' ) . '</a>',
                            'lang'                     => get_user_locale(),
                    ]
            );

            wp_localize_script( 'comfortsmtp-settings', 'comfortsmtp_setting',
                    apply_filters( 'comfortsmtp_setting_js_vars', $translation_placeholder ) );


            wp_enqueue_script( 'jquery' );
            wp_enqueue_media();

            wp_enqueue_script( 'select2' );
            wp_enqueue_script( 'pickr' );
            wp_enqueue_script( 'awesome-notifications' );

            wp_enqueue_script( 'comfortsmtp-settings' );
        }

        if ( $page == 'comfortsmtp_tools' ) {
            $js_translations = ComfortSmtpHelpers::comfortsmtp_tools_js_translation( $current_user, $blog_id );

            $js_translations['migration_files']      = ComfortSmtpHelpers::migration_files();
            $js_translations['migration_files_left'] = ComfortSmtpHelpers::migration_files_left();

            if ( defined( 'COMFORTSMTP_DEV_MODE' ) && COMFORTSMTP_DEV_MODE == true ) {
                //for development version
                wp_register_script( 'comfortsmtp_tools_vue_dev',
                        'http://localhost:8880/assets/vuejs/apps/admin/comfortsmtptools.js', [], $version, true );
                wp_localize_script( 'comfortsmtp_tools_vue_dev', 'comfortsmtp_vue_var', $js_translations );
                wp_enqueue_script( 'comfortsmtp_tools_vue_dev' );
            } else {
                // for production
                wp_register_script( 'comfortsmtp_tools_vue_main', $js_url_part_build . 'comfortsmtptools.js', [],
                        $version, true );
                wp_localize_script( 'comfortsmtp_tools_vue_main', 'comfortsmtp_vue_var', $js_translations );
                wp_enqueue_script( 'comfortsmtp_tools_vue_main' );
            }
        }

    } //end method enqueue_scripts

    /**
     * Display form form builder page
     */
    public function display_comfortsmtp_listing_page() {
        echo comfortsmtp_get_template_html( 'admin/email-logs.php' );//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    } //end method display_comfortsmtp_listing_page

    /**
     * Display the form settings page
     *
     * @since 1.0.0
     */
    public function display_settings_submenu_page() {
        $plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . COMFORTSMTP_PLUGIN_NAME . '.php' );
        $plugin_data     = get_plugin_data( plugin_dir_path( __DIR__ ) . '/../' . $plugin_basename );

        //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo comfortsmtp_get_template_html( 'admin/settings.php', [
                'plugin_data' => $plugin_data,
                'ref'         => $this,
                'settings'    => $this->settings
        ] );
    } //end method display_settings_submenu_page

    /**
     * Render Help & Support page
     *
     * @since 1.0.0
     */
    public function comfortsmtp_helps_updates_display() {
        echo comfortsmtp_get_template_html( 'admin/support.php' );//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    } //end method comfortsmtp_helps_updates_display

    /**
     * Show applicatin tools admin dashboard
     *
     * @return void
     */
    public function display_tools_submenu_page() {
        echo comfortsmtp_get_template_html( 'admin/tools.php' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }//end method display_tools_submenu_page

    /**
     * Load setting html
     *
     * @return void
     */
    public function settings_reset_load() {
        //security check
        check_ajax_referer( 'comfortsmtpnonce', 'security' );

        $msg            = [];
        $msg['html']    = '';
        $msg['message'] = esc_html__( 'Form reset setting html loaded successfully', 'cbxwpemaillogger' );
        $msg['success'] = 1;

        if ( ! current_user_can( 'manage_options' ) ) {
            $msg['message'] = esc_html__( 'Sorry, you don\'t have enough permission', 'cbxwpemaillogger' );
            $msg['success'] = 0;
            wp_send_json( $msg );
        }

        $msg['html'] = ComfortSmtpHelpers::setting_reset_html_table();

        wp_send_json( $msg );
    } //end method settings_reset_load

    /**
     * Full plugin reset and redirect
     */
    public function plugin_options_reset() {
        //security check
        check_ajax_referer( 'comfortsmtpnonce', 'security' );
        $url = admin_url( 'admin.php?page=comfortsmtp_settings' );

        $msg            = [];
        $msg['message'] = esc_html__( 'Form setting options reset successfully', 'cbxwpemaillogger' );
        $msg['success'] = 1;
        $msg['url']     = $url;

        if ( ! current_user_can( 'manage_options' ) ) {
            $msg['message'] = esc_html__( 'Sorry, you don\'t have enough permission', 'cbxwpemaillogger' );
            $msg['success'] = 0;
            wp_send_json( $msg );
        }

        do_action( 'comfortsmtp_plugin_reset_before' );

        $plugin_resets = wp_unslash( $_POST );

        //delete options
        $reset_options = isset( $plugin_resets['reset_options'] ) ? $plugin_resets['reset_options'] : [];
        $option_values = ( is_array( $reset_options ) && sizeof( $reset_options ) > 0 ) ? array_values( $reset_options ) : array_values( ComfortSmtpHelpers::getAllOptionNamesValues() );

        foreach ( $option_values as $key => $option ) {
            delete_option( $option );
        }

        do_action( 'comfortsmtp_plugin_option_delete' );
        do_action( 'comfortsmtp_plugin_reset_after' );
        do_action( 'comfortsmtp_plugin_reset' );

        wp_send_json( $msg );
    } //end plugin_reset

    /**
     * Dropdown menu focus out or click outside event - to close the dropdown
     *
     * @param $ref
     *
     * @return void
     * @since 1.0.0
     */
    public function dropdown_menu_focusout_js( $ref = '' ) {
        ?>
        <script type="text/javascript">
            document.addEventListener('click', function (e) {
                var details = [...document.querySelectorAll('details')];
                if (details.some(f => f.contains(e.target)).length != 0) {
                    details.forEach(f => f.removeAttribute('open'));
                }
            });
        </script>
        <?php
    }//end method dropdown_menu_focusout_js

    /**
     * Display email testing page
     */
    public function display_plugin_admin_email_testing() {
        echo comfortsmtp_get_template_html( 'admin/emailtesting.php' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }//end display_plugin_admin_email_testing

    /**
     * Insert email log into database
     */
    public function insert_log( $atts ) {
        $setting          = $this->settings;
        $email_log_enable = intval( $setting->get_option( 'email_log_enable', 'comfortsmtp_log', 1 ) );

        if ( $email_log_enable == 0 ) {
            return $atts;
        }


        $to = $atts['to'];
        if ( ! is_array( $to ) ) {
            if ( is_null( $to ) ) {
                $to = '';
            }
            $to = explode( ',', $to );
        }

        $subject = isset( $atts['subject'] ) ? wp_unslash( sanitize_text_field( $atts['subject'] ) ) : '';
        $body    = isset( $atts['message'] ) ? wp_unslash( $atts['message'] ) : ( isset( $atts['html'] ) ? wp_unslash( $atts['html'] ) : '' );

        $headers     = isset( $atts['headers'] ) ? $atts['headers'] : [];
        $attachments = isset( $atts['attachments'] ) ? $atts['attachments'] : [];

        if ( ! is_array( $attachments ) ) {
            $attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
        }


        if ( ! is_array( $headers ) ) {
            // Explode the headers out, so this function can take both
            // string headers and an array of headers.
            $headers = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
        }


        $attachments_store = [];

        if ( is_array( $attachments ) && sizeof( $attachments ) > 0 ) {
            foreach ( $attachments as $attachment ) {
                $file_name           = basename( $attachment );
                $attachments_store[] = $file_name;

            }
        }

        $email_data = [];

        $email_data['atts']        = $atts; //keep the blueprint
        $email_data['body']        = $body;
        $email_data['headers']     = $headers;           //raw header data
        $email_data['attachments'] = $attachments_store; //raw attachment info data

        //parse header information
        $headers_arr = [];
        $cc          = $bcc = $reply_to = [];

        $email_source = '';


        if ( is_array( $headers ) && sizeof( $headers ) > 0 ) {
            foreach ( (array) $headers as $header ) {
                if ( strpos( $header, ':' ) === false ) {
                    if ( false !== stripos( $header, 'boundary=' ) ) {
                        $parts    = preg_split( '/boundary=/i', trim( $header ) );
                        $boundary = trim( str_replace( [ "'", '"' ], '', $parts[1] ) );
                    }
                    continue;
                }
                // Explode them out
                [ $name, $content ] = explode( ':', trim( $header ), 2 );

                // Cleanup crew
                $name    = trim( $name );
                $content = trim( $content );

                $email_source = apply_filters( 'comfortsmtp_src_tracking', $email_source, $name, $content );

                switch ( strtolower( $name ) ) {
                    case 'x-wpcf7-content-type':
                        $email_source = 'contact-form-7';

                        break;
                    // Mainly for legacy -- process a From: header if it's there
                    case 'from':
                        $bracket_pos = strpos( $content, '<' );
                        if ( $bracket_pos !== false ) {
                            // Text before the bracketed email is the "From" name.
                            if ( $bracket_pos > 0 ) {
                                $from_name = substr( $content, 0, $bracket_pos - 1 );
                                $from_name = str_replace( '"', '', $from_name );
                                $from_name = trim( $from_name );
                            }

                            $from_email = substr( $content, $bracket_pos + 1 );
                            $from_email = str_replace( '>', '', $from_email );
                            $from_email = trim( $from_email );

                            // Avoid setting an empty $from_email.
                        } elseif ( '' !== trim( $content ) ) {
                            $from_email = trim( $content );
                        }
                        break;
                    case 'content-type':
                        if ( strpos( $content, ';' ) !== false ) {
                            [ $type, $charset_content ] = explode( ';', $content );
                            $content_type = trim( $type );
                            if ( false !== stripos( $charset_content, 'charset=' ) ) {
                                $charset = trim( str_replace( [ 'charset=', '"' ], '', $charset_content ) );
                            } elseif ( false !== stripos( $charset_content, 'boundary=' ) ) {
                                $boundary = trim( str_replace( [
                                        'BOUNDARY=',
                                        'boundary=',
                                        '"'
                                ], '', $charset_content ) );
                                $charset  = '';
                            }

                            // Avoid setting an empty $content_type.
                        } elseif ( '' !== trim( $content ) ) {
                            $content_type = trim( $content );
                        }
                        break;
                    case 'cc':
                        $cc = array_merge( (array) $cc, explode( ',', $content ) );
                        break;
                    case 'bcc':
                        $bcc = array_merge( (array) $bcc, explode( ',', $content ) );
                        break;
                    case 'reply-to':
                        $reply_to = array_merge( (array) $reply_to, explode( ',', $content ) );
                        break;
                    default:
                        // Add it to our grand headers array
                        $headers[ trim( $name ) ] = trim( $content );
                        break;
                }
            }
        }

        //$email_data['headers_arr']  = $headers_arr;

        // From email and name
        // If we don't have a name from the input headers
        if ( ! isset( $from_name ) ) {
            $from_name = 'WordPress';
        }

        /* If we don't have an email from the input headers default to wordpress@$sitename
         * Some hosts will block outgoing mail from this address if it doesn't exist but
         * there's no easy alternative. Defaulting to admin_email might appear to be another
         * option but some hosts may refuse to relay mail from an unknown domain. See
         * https://core.trac.wordpress.org/ticket/5007.
         */

        if ( ! isset( $from_email ) ) {
            // Get the site domain and get rid of www.
            $sitename = isset( $_SERVER['SERVER_NAME'] ) ? strtolower( sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) ) : '';
            if ( substr( $sitename, 0, 4 ) == 'www.' ) {
                $sitename = substr( $sitename, 4 );
            }

            $from_email = 'wordpress@' . $sitename;
        }

        /**
         * Filters the email address to send from.
         *
         * @param  string  $from_email  Email address to send from.
         *
         * @since 2.2.0
         *
         */
        $from_email = apply_filters( 'wp_mail_from', $from_email );

        /**
         * Filters the name to associate with the "from" email address.
         *
         * @param  string  $from_name  Name associated with the "from" email address.
         *
         * @since 2.3.0
         *
         */
        $from_name = apply_filters( 'wp_mail_from_name', $from_name );


        $address_headers = compact( 'to', 'cc', 'bcc', 'reply_to' );

        foreach ( $address_headers as $address_header => $addresses ) {
            if ( empty( $addresses ) ) {
                continue;
            }

            foreach ( (array) $addresses as $address ) {

                // Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
                $recipient_name = '';

                if ( preg_match( '/(.*)<(.+)>/', $address, $matches ) ) {
                    if ( count( $matches ) == 3 ) {
                        $recipient_name = $matches[1];
                        $address        = $matches[2];
                    }
                }

                switch ( $address_header ) {
                    case 'to':
                        $headers_arr['email_to'][] = [ 'recipient_name' => $recipient_name, 'address' => $address ];
                        break;
                    case 'cc':

                        $headers_arr['email_cc'][] = [ 'recipient_name' => $recipient_name, 'address' => $address ];
                        break;
                    case 'bcc':

                        $headers_arr['email_bcc'][] = [ 'recipient_name' => $recipient_name, 'address' => $address ];
                        break;
                    case 'reply_to':
                        $headers_arr['email_reply_to'][] = [
                                'recipient_name' => $recipient_name,
                                'address'        => $address
                        ];
                        break;
                }

            }
        }


        $headers_arr['email_from'] = [ 'from_name' => $from_name, 'from_email' => $from_email ];
        $email_data['headers_arr'] = $headers_arr;


        $data = [
                'date_created'  => gmdate( 'Y-m-d H:i:s' ),
                'subject'       => sanitize_text_field( $subject ),
                'email_data'    => maybe_serialize( $email_data ),
                'ip_address'    => ComfortSmtpHelpers::get_ipaddress(),
                'src_tracked'   => sanitize_text_field( wp_unslash( $email_source ) ),
                'error_message' => ''//fix for tables created using non migration methods
        ];

        $email_smtp_enable = absint( $setting->get_option( 'email_smtp_enable', 'comfortsmtp_email', 0 ) );

        if ( $email_smtp_enable ) {
            $mailer        = esc_attr( sanitize_text_field( $setting->get_option( 'mailer', 'comfortsmtp_email',
                    'default' ) ) );
            $custom_mailer = esc_attr( sanitize_text_field( $setting->get_option( 'custom_mailer', 'comfortsmtp_smtps',
                    'custom_smtp' ) ) );

            $data['mailer'] = $mailer;
            if ( $mailer == 'custom' && $custom_mailer == 'email_api' ) {

                $mail_api = $setting->get_field( 'mail_api', 'comfortsmtp_smtps', '' );

                $data['mailer_api'] = $mail_api;
            }
        }

        $data = apply_filters( 'comfortsmtp_log_entry_data', $data );

        $log_insert_status = SmtpLog::query()->create( $data );

        if ( $log_insert_status ) {
            $log_id = $log_insert_status->id;


            //we will set a new email header
            $enable_store_attachment = intval( $setting->get_option( 'enable_store_attachment', 'comfortsmtp_log',
                    0 ) );
            if ( $enable_store_attachment && is_array( $attachments ) && sizeof( $attachments ) > 0 ) {
                $this->store_email_attachments( $log_id, $attachments );
            }

            $headers_t = isset( $atts['headers'] ) ? $atts['headers'] : [];

            if ( empty( $headers_t ) ) {
                $headers_t = [];
            } else {
                if ( ! is_array( $headers_t ) ) {
                    // Explode the headers out, so this function can take both
                    // string headers and an array of headers.
                    $headers_t = explode( "\n", str_replace( "\r\n", "\n", $headers_t ) );
                }
            }

            $headers_t[] = "x-cbxwpemaillogger-id: $log_id";


            $atts['headers'] = $headers_t;
        }


        return $atts;
    }//end insert_log

    public function phpmailer_init_extend( $phpmailer ) {
        global $wpdb;

        $table_comfortsmtp = esc_sql( $wpdb->prefix . 'cbxwpemaillogger_log' );
        $content_type      = $phpmailer->ContentType;

        $custom_headers = $phpmailer->getCustomHeaders();

        if ( is_array( $custom_headers ) && sizeof( $custom_headers ) > 0 ) {
            foreach ( $custom_headers as $custom_header ) {
                if ( is_array( $custom_header ) && isset( $custom_header[0] ) && esc_attr( $custom_header[0] ) == 'x-cbxwpemaillogger-id' ) {
                    $insert_id = isset( $custom_header[1] ) ? absint( $custom_header[1] ) : 0;
                    if ( $insert_id > 0 ) {
                        //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                        $log_update_status = $wpdb->update(
                                $table_comfortsmtp,
                                [ 'email_type' => esc_attr( $content_type ) ],
                                [ 'id' => $insert_id ],
                                [ '%s' ],
                                [ '%d' ]
                        );
                    }
                    break;
                }
            }
        }//end email type update

        $setting = $this->settings;

        $email_smtp_enable = absint( $setting->get_option( 'email_smtp_enable', 'comfortsmtp_email', 0 ) );


        if ( $email_smtp_enable ) {
            $smtp_email_return_path = sanitize_email( $setting->get_option( 'smtp_email_returnpath',
                    'comfortsmtp_email', '' ) );
            $mailer                 = esc_attr( sanitize_text_field( $setting->get_option( 'mailer',
                    'comfortsmtp_email', 'default' ) ) );


            if ( $smtp_email_return_path != '' ) {
                $phpmailer->AddCustomHeader( 'Return-Path: ' . $smtp_email_return_path );
                $phpmailer->Sender = $smtp_email_return_path;
            }

            if ( $mailer == 'custom' ) {
                //if custom emailer then we need to choose which emailer we can use

                $custom_mailer = esc_attr( sanitize_text_field( $setting->get_option( 'custom_mailer',
                        'comfortsmtp_smtps', 'custom_smtp' ) ) );

                if ( $custom_mailer == 'custom_smtp' ) {

                    $custom_mailer      = $setting->get_field( 'custom_mailer', 'comfortsmtp_smtps', '' );
                    $smtp_email_servers = $setting->get_field( 'smtp_email_servers', 'comfortsmtp_smtps', '' );
                    $smtp_email_server  = $setting->get_field( 'smtp_email_server', 'comfortsmtp_smtps', '' );

                    $smtp_email_servers_list = ComfortSmtpHelpers::getSMTPHostServers( true );

                    if ( is_array( $smtp_email_servers_list ) && sizeof( $smtp_email_servers_list ) > 0 && isset( $smtp_email_servers_list[ $smtp_email_server ] ) ) {

                        $smtp_config       = ComfortSmtpHelpers::getSMTPHostServer( $smtp_email_server );
                        $phpmailer->Mailer = 'smtp';

                        $host = isset( $smtp_config['smtp_email_host'] ) ? sanitize_text_field( $smtp_config['smtp_email_host'] ) : 'localhost';
                        $port = isset( $smtp_config['smtp_email_port'] ) ? intval( $smtp_config['smtp_email_port'] ) : 25;

                        $secure = isset( $smtp_config['smtp_email_secure'] ) ? esc_attr( sanitize_text_field( $smtp_config['smtp_email_secure'] ) ) : 'none';
                        if ( $secure == 'none' ) {
                            $secure = '';
                        }

                        $auth = isset( $smtp_config['smtp_email_auth'] ) ? intval( $smtp_config['smtp_email_auth'] ) : 0;

                        $username = isset( $smtp_config['smtp_email_username'] ) ? sanitize_text_field( $smtp_config['smtp_email_username'] ) : '';
                        $password = isset( $smtp_config['smtp_email_password'] ) ? sanitize_text_field( $smtp_config['smtp_email_password'] ) : '';

                        $phpmailer->Host       = $host;
                        $phpmailer->Port       = $port;
                        $phpmailer->SMTPSecure = $secure;
                        $phpmailer->SMTPAuth   = ( $auth ) ? true : false;

                        if ( $phpmailer->SMTPAuth ) {
                            $phpmailer->Username = $username;
                            $phpmailer->Password = $password;
                        }
                    }
                }
            }
        }
    }//end phpmailer_init_extend

    /**
     * Store email attachment files
     *
     * @param  int  $log_id
     * @param  array  $attachments
     */
    public function store_email_attachments( $log_id = 0, $attachments = [] ) {
        $log_id = intval( $log_id );
        if ( $log_id > 0 && is_array( $attachments ) && sizeof( $attachments ) > 0 ) {
            $dir_info = ComfortSmtpHelpers::checkUploadDir( $log_id );

            global $wp_filesystem;
            require_once( ABSPATH . '/wp-admin/includes/file.php' );
            WP_Filesystem();

            $log_folder_dir = $dir_info['comfortsmtp_base_dir'] . $log_id;
            if ( ! $wp_filesystem->exists( $log_folder_dir ) ) {
                $created = wp_mkdir_p( $log_folder_dir );
                if ( $created ) {
                    $folder_exists = 1;
                } else {
                    $folder_exists = 0;
                }
            }

            foreach ( $attachments as $attachment ) {
                $file_name = basename( $attachment );
                $wp_filesystem->copy( $attachment, $log_folder_dir . '/' . $file_name, true );
            }
        }
    }//end store_email_attachments

    /**
     * Override from email name
     *
     * @param $original_email_address
     *
     * @return string
     */
    public function wp_mail_from_name_custom( $original_email_name ) {
        $setting = $this->settings;

        $email_smtp_enable = intval( $setting->get_option( 'email_smtp_enable', 'comfortsmtp_email', 0 ) );

        $smtp_from_name = sanitize_text_field( $setting->get_option( 'smtp_from_name', 'comfortsmtp_email',
                sanitize_text_field( get_option( 'blogname' ) ) ) );

        if ( $email_smtp_enable && $smtp_from_name != '' ) {
            $original_email_name = $smtp_from_name;
        }

        return $original_email_name;
    }//end wp_mail_from_name_custom

    /**
     * Override from email address
     *
     * @param $original_email_address
     *
     * @return string
     */
    public function wp_mail_from_custom( $original_email_address ) {
        $setting = $this->settings;

        $email_smtp_enable = intval( $setting->get_option( 'email_smtp_enable', 'comfortsmtp_email', 0 ) );
        $smtp_from_email   = sanitize_email( $setting->get_option( 'smtp_from_email', 'comfortsmtp_email',
                sanitize_email( get_option( 'admin_email' ) ) ) );


        if ( $email_smtp_enable && $smtp_from_email != '' ) {
            $original_email_address = $smtp_from_email;
        }

        return $original_email_address;
    }//end wp_mail_from_custom

    /**
     * Email sent fail hook callback
     *
     * @param        $wp_error
     * @param  string  $email
     */
    public function email_sent_failed( $wp_error, $email = OBJECT ) {

        $setting          = $this->settings;
        $email_log_enable = intval( $setting->get_option( 'email_log_enable', 'comfortsmtp_log', 1 ) );

        if ( $email_log_enable == 0 ) {
            return;
        }

        if ( ! ( $wp_error instanceof \WP_Error ) ) {
            return;
        }

        $mail_error_data    = $wp_error->get_error_data( 'wp_mail_failed' );
        $mail_error_message = sanitize_text_field( wp_unslash( $wp_error->get_error_message() ) );


        $headers = isset( $mail_error_data['headers'] ) ? $mail_error_data['headers'] : [];

        if ( isset( $headers['x-cbxwpemaillogger-id'] ) && intval( $headers['x-cbxwpemaillogger-id'] ) > 0 ) {

            $log_id = intval( $headers['x-cbxwpemaillogger-id'] );

            SmtpLog::where( 'id', intval( $log_id ) )->update( [
                    'status'        => 0,
                    'error_message' => $mail_error_message
            ] );
        }
    }//end email_sent_failed

    /**
     * Delete attachment folder after log delete
     *
     * @param  int  $id
     */
    public function delete_attachments_after_log_delete( $id = 0 ) {
        $id = absint( $id );
        if ( $id > 0 ) {
            //delete attachment folder
            $delete_status = ComfortSmtpHelpers::deleteLogFolder( $id );
        }

        return $delete_status;

    }//end method delete_attachments_after_log_delete

    /**
     * Delete old from scheduled event
     */
    public function delete_old_log() {

        $settings = new ComfortSmtpSettings();

        $delete_old_log = $settings->get_option( 'delete_old_log', 'comfortsmtp_log', 'no' );

        if ( $delete_old_log == 'yes' ) {

            $log_old_days = absint( $settings->get_option( 'log_old_days', 'comfortsmtp_log', 30 ) );

            if ( $log_old_days > 0 ) {

                ComfortSmtpHelpers::delete_old_log( $log_old_days );
            }
        }

    }//end delete_old_log

    /**
     * If we need to do something in upgrader process is completed
     *
     */
    public function plugin_upgrader_process_complete() {
        $saved_version = get_option( 'comfortsmtp_version' );

        if ( $saved_version === false || version_compare( $saved_version, COMFORTSMTP_PLUGIN_VERSION, '<' ) ) {
            ComfortSmtpHelpers::load_orm();

            // Run the upgrade routine
            ComfortSmtpHelpers::migration_and_defaults();

            // Update the saved version
            update_option( 'comfortsmtp_version', COMFORTSMTP_PLUGIN_VERSION );
            set_transient( 'comfortsmtp_upgraded_notice', 1 );
        }
    }//end plugin_upgrader_process_complete

    /**
     * Show a notice to anyone who has just installed the plugin for the first time
     * This notice shouldn't display to anyone who has just updated this plugin
     */
    public function plugin_activate_upgrade_notices() {
        $activation_notice_shown = false;


        $kiss_html_arr = [
                'strong' => [],
                'a'      => [
                        'href'  => [],
                        'class' => []
                ]
        ];

        // Check the transient to see if we've just activated the plugin
        if ( get_transient( 'comfortsmtp_activated_notice' ) ) {
            echo '<div class="notice notice-success is-dismissible" style="border-color: #6648fe !important;">';

            echo '<p>';

            //phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
            /*echo '<img alt="icon" style="float: left; display: inline-block; margin-right: 20px;" src="' . esc_url( plugins_url( 'assets/images/icon_c_48.png',
                    dirname( __FILE__ ) ) ) . '" />';*/

            /* translators: 1: plugin version 2. codeboxr website url  */
            echo sprintf( wp_kses( __( 'Thanks for installing/deactivating <strong>CBX Email SMTP & Logger</strong> V%1$s - <a href="%2$s" target="_blank">Codeboxr Team</a>',
                    'cbxwpemaillogger' ), $kiss_html_arr ), esc_attr( COMFORTSMTP_PLUGIN_VERSION ), 'https://codeboxr.com' );

            echo '</p>';

            /* translators: 1: Settings url 2. plugin url  */
            echo '<p>' . sprintf( wp_kses( __( 'Check Plugin <a href="%1$s">Setting</a> and <a href="%2$s" target="_blank"><span class="dashicons dashicons-external"></span> Documentation</a>',
                            'cbxwpemaillogger' ), $kiss_html_arr ), esc_attr( admin_url( 'admin.php?page=comfortsmtp_settings' ) ),
                            'https://codeboxr.com/product/cbx-email-logger-for-wordpress/' ) . '</p>';
            echo '</div>';


            // Delete the transient so we don't keep displaying the activation message
            delete_transient( 'comfortsmtp_activated_notice' );

            $this->pro_addon_compatibility_campaign();

            $activation_notice_shown = true;
        }

        // Check the transient to see if we've just activated the plugin
        if ( get_transient( 'comfortsmtp_upgraded_notice' ) ) {
            if ( ! $activation_notice_shown ) {
                echo '<div class="notice notice-success is-dismissible" style="border-color: #6648fe !important;">';

                echo '<p>';

                //phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
                /*echo '<img alt="icon" style="float: left; display: inline-block;  margin-right: 20px;" src="' . esc_url( plugins_url( 'assets/images/icon_c_48.png',
                        dirname( __FILE__ ) ) ) . '"/>';*/

                /* translators: 1: plugin version 2. team url  */
                echo sprintf( wp_kses( __( 'Thanks for upgrading <strong>CBX Email SMTP & Logger</strong> V%1$s - <a href="%2$s" target="_blank">Codeboxr Team</a>',
                        'cbxwpemaillogger' ), $kiss_html_arr ), esc_attr( COMFORTSMTP_PLUGIN_VERSION ), 'https://codeboxr.com' );

                echo '</p>';


                echo '<p>';

                /* translators: 1: Settings url 2. plugin url  */
                echo sprintf( wp_kses( __( 'Check Plugin <a href="%1$s">Setting</a> and <a href="%2$s" target="_blank"><span class="dashicons dashicons-external"></span> Documentation</a>',
                        'cbxwpemaillogger' ), $kiss_html_arr ), esc_attr( admin_url( 'admin.php?page=comfortsmtp_settings' ) ),
                        'https://codeboxr.com/product/cbx-email-logger-for-wordpress/' );

                echo '</p>';

                echo '</div>';


                $this->pro_addon_compatibility_campaign();
            }


            // Delete the transient so we don't keep displaying the activation message
            delete_transient( 'comfortsmtp_upgraded_notice' );
        }
    }//end plugin_activate_upgrade_notices

    /**
     * Check plugin compatibility and pro addon install campaign
     */
    public function pro_addon_compatibility_campaign() {
        /*if ( ! function_exists( 'is_plugin_active' ) ) {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }*/

        //if the pro addon is active or installed
        if ( ! defined( 'COMFORTSMTPPROADDON_PLUGIN_NAME' ) ) {
            /* translators: %s: plugin url */
            echo '<div style="border-left-color:#6648fe;" class="notice notice-success is-dismissible"><p>' . wp_kses( sprintf( __( '<a target="_blank" href="%s">CBX Email SMTP & Logger Pro Addon</a> has extended features - give it a try.',
                            'cbxwpemaillogger' ), 'https://codeboxr.com/product/cbx-email-logger-for-wordpress/' ), [
                            'a' => [
                                    'href' => [],
                                    'span' => []
                            ]
                    ] ) . '</p></div>';
        }
    }//end pro_addon_compatibility_campaign

    /**
     * Show action links on the plugin screen.
     *
     * @param  mixed  $links  Plugin Action links.
     *
     * @return  array
     */
    public static function plugin_action_links( $links ) {
        $action_links = [
                'settings' => '<a style="color: #6648fe !important; font-weight: bold;" href="' . admin_url( 'admin.php?page=comfortsmtp_settings' ) . '" aria-label="' . esc_attr__( 'View settings',
                                'cbxwpemaillogger' ) . '">' . esc_html__( 'Settings', 'cbxwpemaillogger' ) . '</a>',
        ];

        return array_merge( $action_links, $links );
    }//end plugin_action_links

    /**
     * Filters the array of row meta for each/specific plugin in the Plugins list table.
     * Appends additional links below each/specific plugin on the plugins page.
     *
     * @access  public
     *
     * @param  array  $links_array  An array of the plugin's metadata
     * @param  string  $plugin_file_name  Path to the plugin file
     * @param  array  $plugin_data  An array of plugin data
     * @param  string  $status  Status of the plugin
     *
     * @return  array       $links_array
     */
    public function plugin_row_meta( $links_array, $plugin_file_name, $plugin_data, $status ) {
        if ( strpos( $plugin_file_name, COMFORTSMTP_BASE_NAME ) !== false ) {
            /*if ( ! function_exists( 'is_plugin_active' ) ) {
                include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            }*/

            $links_array[] = '<a target="_blank" style="color:#6044ea !important; font-weight: bold;" href="https://wordpress.org/support/plugin/cbxwpemaillogger/" aria-label="' . esc_attr__( 'Free Support',
                            'cbxwpemaillogger' ) . '">' . esc_html__( 'Free Support', 'cbxwpemaillogger' ) . '</a>';

            $links_array[] = '<a target="_blank" style="color:#6044ea !important; font-weight: bold;" href="https://wordpress.org/plugins/cbxwpemaillogger/#reviews" aria-label="' . esc_attr__( 'Reviews',
                            'cbxwpemaillogger' ) . '">' . esc_html__( 'Reviews', 'cbxwpemaillogger' ) . '</a>';


            $links_array[] = '<a target="_blank" style="color:#6044ea !important; font-weight: bold;" href="https://codeboxr.com/product/cbx-email-logger-for-wordpress/" aria-label="' . esc_attr__( 'Documentation',
                            'cbxwpemaillogger' ) . '">' . esc_html__( 'Documentation', 'cbxwpemaillogger' ) . '</a>';

            $links_array[] = '<a target="_blank" style="color:#6044ea !important; font-weight: bold;" href="https://codeboxr.com/product/cbx-email-logger-for-wordpress/" aria-label="' . esc_attr__( 'Try Pro Addon',
                            'cbxwpemaillogger' ) . '">' . esc_html__( 'Try Pro Addon', 'cbxwpemaillogger' ) . '</a>';


        }

        return $links_array;
    }//end plugin_row_meta

    /**
     * Add field to repeat fields
     */
    public function add_new_repeat_field() {
        check_ajax_referer( 'comfortsmtpnonce', 'security' );


        $delete_svg = '<i class="cbx-icon">' . comfortsmtp_esc_svg( comfortsmtp_load_svg( 'icon_delete' ) ) . '</i>';
        $edit_svg   = '<i class="cbx-icon">' . comfortsmtp_esc_svg( comfortsmtp_load_svg( 'icon_edit' ) ) . '</i>';
        $sort_svg   = '<i class="cbx-icon">' . comfortsmtp_esc_svg( comfortsmtp_load_svg( 'icon_move' ) ) . '</i>';

        $msg            = [];
        $msg['message'] = esc_html__( 'New field added successfully', 'cbxwpemaillogger' );
        $msg['success'] = 1;

        if ( ! current_user_can( 'manage_options' ) ) {
            $msg['message'] = esc_html__( 'Sorry, you don\'t have enough permission', 'cbxwpemaillogger' );
            $msg['success'] = 0;
            wp_send_json( $msg );
        }


        $section_name = isset( $_REQUEST['section_name'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['section_name'] ) ) : '';
        $option_name  = isset( $_REQUEST['option_name'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['option_name'] ) ) : '';
        $field_name   = isset( $_REQUEST['field_name'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['field_name'] ) ) : '';
        $index        = isset( $_REQUEST['index'] ) ? absint( $_REQUEST['index'] ) : 0;

        //$index = 1;
        $html = '';

        if ( $section_name != '' && $option_name != '' ) {
            $all_fields     = $this->get_settings_fields();
            $section_fields = isset( $all_fields[ $section_name ] ) ? $all_fields[ $section_name ] : [];


            $args            = [];
            $args['name']    = $field_name;
            $args['id']      = $option_name;
            $args['section'] = $section_name;

            if ( isset( $section_fields[ $option_name ] ) ) {
                $option_field = $section_fields[ $option_name ];
                $fields       = $option_field['fields'];


                if ( is_array( $fields ) & sizeof( $fields ) > 0 ) {

                    //foreach ( $value as $val ) {
                    /*if ( ! is_array( $val ) ) {
                        $val = array();
                    }*/

                    $html .= '<div class="form-table-fields-parent-item">';
                    $html .= '<h5><p class="form-table-fields-parent-item-heading">' . $field_name . ' #' . ( $index + 1 ) . '</p>';
                    $html .= '<span class="form-table-fields-parent-item-icon form-table-fields-parent-item-sort icon icon-only">' . $sort_svg . '</span>';
                    $html .= '<span class="form-table-fields-parent-item-icon form-table-fields-parent-item-control icon icon-only">' . $edit_svg . '</span>';
                    $html .= '<span class="form-table-fields-parent-item-icon form-table-fields-parent-item-delete icon icon-only">' . $delete_svg . '</span>';

                    $html .= '</h5>';
                    $html .= '<div class="form-table-fields-parent-item-wrap">';

                    $html .= '<table class="form-table-fields-items">';
                    foreach ( $fields as $field ) {
                        $args_t = $args;

                        $args_t['section']           = isset( $args['section'] ) ? $args['section'] . '[' . $args['id'] . '][' . $index . ']' : '';
                        $args_t['desc']              = isset( $field['desc'] ) ? $field['desc'] : '';
                        $args_t['name']              = isset( $field['name'] ) ? $field['name'] : '';
                        $args_t['label']             = isset( $field['label'] ) ? $field['label'] : '';
                        $args_t['class']             = isset( $field['class'] ) ? $field['class'] : $args_t['name'];
                        $args_t['id']                = $args_t['name'];
                        $args_t['size']              = isset( $field['size'] ) ? $field['size'] : null;
                        $args_t['min']               = isset( $field['min'] ) ? $field['min'] : '';
                        $args_t['max']               = isset( $field['max'] ) ? $field['max'] : '';
                        $args_t['step']              = isset( $field['step'] ) ? $field['step'] : '';
                        $args_t['options']           = isset( $field['options'] ) ? $field['options'] : '';
                        $args_t['default']           = isset( $field['default'] ) ? $field['default'] : '';
                        $args_t['sanitize_callback'] = isset( $field['sanitize_callback'] ) ? $field['sanitize_callback'] : '';
                        $args_t['placeholder']       = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
                        $args_t['type']              = isset( $field['type'] ) ? $field['type'] : 'text';
                        $args_t['optgroup']          = isset( $field['optgroup'] ) ? intval( $field['optgroup'] ) : 0;
                        $args_t['sortable']          = isset( $field['sortable'] ) ? intval( $field['sortable'] ) : 0;
                        $callback                    = isset( $field['callback'] ) ? $field['callback'] : [
                                $this->settings,
                                'callback_' . $args_t['type']
                        ];


                        $val_t = $args_t['default'];

                        $html    .= '<tr class="form-table-fields-item"><td>';
                        $html_id = "{$args_t['section']}_{$args_t['id']}";
                        $html_id = ComfortSmtpHelpers::settings_clean_label_for( $html_id );
                        $html    .= sprintf( '<label class="main-label" for="%1$s">%2$s</label>', $html_id,
                                $args_t['label'] );
                        $html    .= '</td></tr>';

                        $html .= '<tr class="form-table-fields-item"><td>';
                        ob_start();
                        call_user_func( $callback, $args_t, $val_t );
                        $html .= ob_get_contents();
                        ob_end_clean();
                        $html .= '</td></tr>';
                    }
                    $html .= '</table>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $index ++;
                    //}

                }
            }
        }//if valid section name, option name

        $msg['html']  = $html;
        $msg['index'] = $index;

        wp_send_json( $msg );
    }//end add_new_repeat_field
}//end class ComfortSmtpAdmin

//phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound