<?php
namespace ChiliDevs\ContactForm7;

use ChiliDevs\ContactForm7\SettingsAPI;

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Admin class
 *
 * @since 1.0.0
 */
class Admin {

    /**
     * Holde Settings API class
     *
     * @since 1.0.0
     */
    private $settings_api;

    /**
     * Load automatically when class initiate
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->settings_api = new SettingsAPI();

        add_action( 'admin_init', [$this, 'admin_init'] );
        add_action( 'admin_menu', [ $this, 'load_menu' ], 12 );
        add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
        add_action( 'chili_settings_form_bottom_cf7_sms_settings', [ $this, 'settings_gateway_fields' ] );
    }

    /**
     * Enqueue admin scripts
     *
     * Allows plugin assets to be loaded.
     *
     * @since 1.0.0
     */
    public function admin_enqueue_scripts() {
        wp_enqueue_style( 'admin-cf7-sms-scripts', CF7_SMS_ASSETS . '/css/admin.css', false, date( 'Ymd' ) );
        wp_enqueue_script( 'admin-cf7-sms-scripts', CF7_SMS_ASSETS . '/js/admin.js', array( 'jquery' ), false, true );

        wp_localize_script( 'admin-cf7-sms-scripts', 'wcmessagemedia', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' )
        ) );
    }

    /**
     * Initialize the settings.
     *
     * @return void
     */
    public function admin_init() {
        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    /**
     * Load SMS setting menu under cf7 main menu
     *
     * @since 1.0.0
     */
    public function load_menu() {
        add_submenu_page( 'wpcf7',
            __( 'SMS Settings', 'cf7-sms' ),
            __( 'SMS Settings', 'cf7-sms' )
                . wpcf7_admin_menu_change_notice( 'wpcf7-sms-settings' ),
            'wpcf7_edit_contact_forms',
            'wpcf7-sms-settings',
            [ $this, 'cf7_sms_settings_page' ]
        );
    }

    /**
     * Plugin settings sections.
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_settings_sections() {
        $sections = [
            [
                'id'    => 'cf7_sms_settings',
                'title'    => '',
                'name' => __( 'SMS Settings', 'cf7-sms' ),
                'icon' => 'dashicons-admin-tools'
            ],
        ];

        return apply_filters( 'cf7_sms_get_settings_sections', $sections );
    }

    /**
     * Returns all the settings fields.
     *
     * @since 1.0.0
     *
     * @return array settings fields
     */
    public function get_settings_fields() {
        $settings_fields = [
            'cf7_sms_settings' => [
                [
                    'name' => 'sms_gateway',
                    'label' => __( 'Select Gateway', 'cf7-sms' ),
                    'desc' => __( 'Select your sms gateway', 'cf7-sms' ),
                    'type' => 'select',
                    'default' => '-1',
                    'options' => $this->get_sms_gateway()
                ]
            ]
        ];

        return apply_filters( 'cf7_sms_get_settings_fields', $settings_fields );
    }

    /**
     * Render setting content page
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function cf7_sms_settings_page() {
        ?>
            <div class="wrap">
                <h1><?php esc_html_e( 'SMS Settings', 'cf7-sms' ); ?> </h1>
                <hr>
                <?php
                    $this->settings_api->show_navigation();
                    $this->settings_api->show_forms();
                ?>
            </div>
        <?php
    }

    /**
     * Get sms Gateway settings
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_sms_gateway() {
        $gateway = array(
            ''          => __( '--select--', 'cf7-sms' ),
            'nexmo'     => __( 'Vonage(Nexmo)', 'cf7-sms' ),
            'clicksend' => __( 'ClickSend', 'cf7-sms' ),
        );

        return apply_filters( 'cf7_sms_gateway', $gateway );
    }

    /**
     * Render settings gateway extra fields
     *
     * @since 1.0.0
     *
     * @return void|HTML
     */
    public function settings_gateway_fields() {
        $nexmo_api        = cf7_sms_get_option( 'nexmo_api', 'cf7_sms_settings', '' );
        $nexmo_api_secret = cf7_sms_get_option( 'nexmo_api_secret', 'cf7_sms_settings', '' );
        $nexmo_from_name  = cf7_sms_get_option( 'nexmo_from_name', 'cf7_sms_settings', '' );

        $nexmo_helper     = sprintf( __( 'Enter your Vonage(Nexmo) details. Please visit <a href="%s" target="_blank">%s</a> and get your api keys and options', 'cf7-sms' ), 'https://dashboard.nexmo.com/login', 'Nexmo' );

        $clicksend_username = cf7_sms_get_option( 'clicksend_username', 'cf7_sms_settings', '' );
        $clicksend_api      = cf7_sms_get_option( 'clicksend_api', 'cf7_sms_settings', '' );
        $clicksend_helper   = sprintf( __( 'Enter ClickSend details. Please visit <a href="%s" target="_blank">%s</a> and get your username and api keys', 'cf7-sms' ), 'https://dashboard.clicksend.com/signup', 'Clicksend' );
        ?>

        <?php do_action( 'cf7_gateway_settings_options_before' ); ?>

        <div class="nexmo_wrapper hide_class">
            <hr>
            <p style="margin-top:15px; margin-bottom:0px; font-style: italic; font-size: 14px;">
                <strong><?php echo wp_kses_post( $nexmo_helper ); ?></strong>
           </p>
            <table class="form-table">
                <tr valign="top">
                    <th scrope="row"><?php esc_html_e( 'Vonage(Nexmo) API', 'cf7-sms' ) ?></th>
                    <td>
                        <input type="text" class="regular-text" name="cf7_sms_settings[nexmo_api]" id="cf7_sms_settings[nexmo_api]" value="<?php echo esc_attr( $nexmo_api ); ?>">
                        <p class="description"><?php esc_html_e( 'Enter Vonage(Nexmo) API key', 'cf7-sms' ); ?></p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scrope="row"><?php esc_html_e( 'Vonage(Nexmo) API Secret', 'cf7-sms' ) ?></th>
                    <td>
                        <input type="text" class="regular-text" name="cf7_sms_settings[nexmo_api_secret]" id="cf7_sms_settings[nexmo_api_secret]" value="<?php echo esc_attr( $nexmo_api_secret ); ?>">
                        <p class="description"><?php esc_html_e( 'Enter Vonage(Nexmo) API secret', 'cf7-sms' ); ?></p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scrope="row"><?php esc_html_e( 'Vonage(Nexmo) From Name', 'cf7-sms' ) ?></th>
                    <td>
                        <input type="text" class="regular-text" name="cf7_sms_settings[nexmo_from_name]" id="cf7_sms_settings[nexmo_from_name]" value="<?php echo esc_attr( $nexmo_from_name ); ?>">
                        <p class="description"><?php esc_html_e( 'From which name the message will be sent to the users ( Default : VONAGE )', 'cf7-sms' ); ?></p>
                    </td>
                </tr>

            </table>

            <?php do_action( 'cf7_gateway_fields_after' ); ?>
        </div>
        <!-- starting clicksend div -->


        <div class="clicksend_wrapper hide_class">
            <hr>
            <p style="margin-top:15px; margin-bottom:0px; font-style: italic; font-size: 14px;">
                <strong><?php echo wp_kses_post( $clicksend_helper ); ?></strong>
           </p>
            <table class="form-table">
                <tr valign="top">
                    <th scrope="row"><?php esc_html_e( 'ClickSend Username', 'cf7-sms' ) ?></th>
                    <td>
                        <input type="text" class="regular-text" name="cf7_sms_settings[clicksend_username]" id="cf7_sms_settings[clicksend_username]" value="<?php echo esc_attr( $clicksend_username ); ?>">
                        <p class="description"><?php esc_html_e( 'Enter ClickSend Username', 'cf7-sms' ); ?></p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scrope="row"><?php esc_html_e( 'ClickSend API key', 'cf7-sms' ) ?></th>
                    <td>
                        <input type="text" class="regular-text" name="cf7_sms_settings[clicksend_api]" id="cf7_sms_settings[clicksend_api]" value="<?php echo esc_attr( $clicksend_api); ?>">
                        <p class="description"><?php esc_html_e( 'Enter ClickSend API', 'cf7-sms' ); ?></p>
                    </td>
                </tr>

            </table>

        </div>
        <?php

        do_action( 'cf7_gateway_settings_options_after' );
    }

}
