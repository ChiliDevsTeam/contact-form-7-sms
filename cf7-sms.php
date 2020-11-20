<?php
/*
Plugin Name: Contact Form 7 SMS Integration
Plugin URI: https://chilidevs.com/downloads/contact-form-7-sms-integration/
Description: SMS Integration for Contact Form 7
Version: 1.0.0
Author: chilidevs
Author URI: http://chilidevs.com/
Text Domain: cf7-sms
Domain Path: /languages/
License: GPL2
*/

/**
 * Copyright (c) YEAR chilidevs (email: info@chilidevs.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Contact_Form_7_SMS class
 *
 * @class Contact_Form_7_SMS The class that holds the entire Contact_Form_7_SMS plugin
 */
class Contact_Form_7_SMS {

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     * Instance of self
     *
     * @var Contact_Form_7_SMS
     */
    private static $instance = null;

    /**
     * Constructor for the Contact_Form_7_SMS class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     *
     * @uses register_activation_hook()
     * @uses register_deactivation_hook()
     * @uses is_admin()
     * @uses add_action()
     */
    public function __construct() {
        // Define all constant
        $this->define_constant();

        add_action( 'plugins_loaded', [ $this, 'load_cf7_sms' ], 12 );
    }

    /**
     * Initializes the Contact_Form_7_SMS() class
     *
     * Checks for an existing Contact_Form_7_SMS() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
    * Defined constant
    *
    * @since 1.0.0
    *
    * @return void
    **/
    private function define_constant() {
        define( 'CF7_SMS_VERSION', $this->version );
        define( 'CF7_SMS_FILE', __FILE__ );
        define( 'CF7_SMS_PATH', dirname( CF7_SMS_FILE ) );
        define( 'CF7_SMS_ASSETS', plugins_url( '/assets', CF7_SMS_FILE ) );
    }

    /**
     * Installation notice
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function installation_notice() {
        ?>
        <div id="message" class="error notice is-dismissible">
            <p><?php echo sprintf( wp_kses_post( '<b>Contact Form 7 SMS Integration</b> requires <a href="%s">Contact Form 7</a> to be installed & activated! Go back your <a href="%s">Plugin page</a>', 'cf7-sms' ), 'https://wordpress.org/plugins/contact-form-7/', esc_url( admin_url( 'plugins.php' ) ) ) ?></p>
            <button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e( 'Dismiss this notice.', 'cf7-sms' ) ?></span></button>
        </div>
        <?php
    }

    /**
     * Load SMS plugin all files
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function load_cf7_sms() {
        if ( ! function_exists( 'wpcf7' ) ) {
            add_action( 'admin_notices', [ $this, 'installation_notice' ], 10 );
            return;
        }

        //includes file
        $this->includes();

        // Init main hooks
        $this->init_hooks();

        do_action( 'cf7_SMS_loaded', $this );
    }

    /**
    * Includes all files
    *
    * @since 1.0.0
    *
    * @return void
    **/
    public function includes() {
        require_once CF7_SMS_PATH . '/vendor/autoload.php';
        require_once CF7_SMS_PATH . '/includes/functions.php';

        if ( is_admin() ) {
            require_once CF7_SMS_PATH . '/includes/class-admin.php';
        }

        require_once CF7_SMS_PATH . '/includes/class-form-settings.php';
        require_once CF7_SMS_PATH . '/includes/class-gateway.php';
    }

    /**
    * Init all filters
    *
    * @since 1.0.0
    *
    * @return void
    **/
    public function init_hooks() {
        add_action( 'init', array( $this, 'localization_setup' ) );
        add_action( 'init', array( $this, 'init_classes' ) );
    }

    /**
    * Inistantiate all classes
    *
    * @since 1.0.0
    *
    * @return void
    **/
    public function init_classes() {
        if ( is_admin() ) {
            new CF7_SMS_Admin();
        }

        new CF7_SMS_Form_Settings();
    }

    /**
     * Initialize plugin for localization
     *
     * @since 1.0.0
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup() {
        load_plugin_textdomain( 'cf7-sms', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

} // Contact_Form_7_SMS

$cf7_sms = Contact_Form_7_SMS::init();
