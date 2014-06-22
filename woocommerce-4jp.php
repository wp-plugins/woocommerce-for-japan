<?php
/**
 * Plugin Name: WooCommerce For Japan
 * Plugin URI: http://wordpress.org/plugins/woocommerce-for-japan/
 * Description: Woocommerce toolkit for Japanese use.
 * Version: 0.9.0
 * Author: Artisan Workshop
 * Author URI: http://profiles.wordpress.org/shoheitanaka
 * Requires at least: 3.8
 * Tested up to: 3.9
 *
 * Text Domain: woocommerce-4jp
 * Domain Path: /i18n/
 *
 * @package WooCommerce-4jp
 * @category Core
 * @author Artisan Workshop
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WooCommerce4jp' ) ) :

/**
 * Load plugin functions.
 */
add_action( 'plugins_loaded', 'WooCommerce4jp_plugin', 0 );

class WooCommerce4jp{

	/**
	 * WooCommerce Constructor.
	 * @access public
	 * @return WooCommerce
	 */
	public function __construct() {
		// Include required files
		$this->includes();
//		add_filter( 'woocommerce_default_address_fields',array( &$this,  'address_fields'));
		$this->init();
	}
	/**
	 * Include required core files used in admin and on the frontend.
	 */
	private function includes() {
		// Payment Gateway
		include_once( 'includes/gateways/bank-jp/class-wc-gateway-bank-jp.php' );	// Bank in Japan
		// Address field
		include_once( 'includes/class-wc-address-field-4jp.php' );
	}
	/**
	 * Init WooCommerce when WordPress Initialises.
	 */
	public function init() {
		// Before init action
//		do_action( 'before_woocommerce_init' );
		// Set up localisation
		$this->load_plugin_textdomain();
		// Init action
//		do_action( 'woocommerce_init' );
	}

	/*
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-4jp' );
		// Global + Frontend Locale
		load_plugin_textdomain( 'woocommerce-4jp', false, plugin_basename( dirname( __FILE__ ) ) . "/i18n" );
	}

}

endif;
function WooCommerce4jp_plugin() {
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        $wc4jp = new WooCommerce4jp();
    } else {
        add_action( 'admin_notices', 'wc4jp_fallback_notice' );
    }
}
