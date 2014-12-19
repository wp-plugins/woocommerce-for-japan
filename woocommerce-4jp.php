<?php
/**
 * Plugin Name: WooCommerce For Japan
 * Plugin URI: http://wordpress.org/plugins/woocommerce-for-japan/
 * Description: Woocommerce toolkit for Japanese use.
 * Version: 1.0.5
 * Author: Artisan Workshop
 * Author URI: http://wc.artws.info/
 * Requires at least: 3.8
 * Tested up to: 4.1
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
		$this->init();
	}
	/**
	 * Include required core files used in admin and on the frontend.
	 */
	private function includes() {
		// Payment Gateway For Bank
		if(get_option('wc4jp-bankjp')) include_once( plugin_dir_path( __FILE__ ).'/includes/gateways/bank-jp/class-wc-gateway-bank-jp.php' );	// Bank in Japan
		// Payment Gateway For Post Office Bank
		if(get_option('wc4jp-postofficebank')) include_once( plugin_dir_path( __FILE__ ).'/includes/gateways/postofficebank/class-wc-gateway-postofficebank-jp.php' );	// Post Office Bank in Japan
		// Payment Gateway For Post Office Bank
		if(get_option('wc4jp-atstore')) include_once( plugin_dir_path( __FILE__ ).'/includes/gateways/atstore/class-wc-gateway-atstore-jp.php' );	// Post Office Bank in Japan
		// Address field
		include_once( plugin_dir_path( __FILE__ ).'/includes/class-wc-address-field-4jp.php' );
		// Admin Setting Screen 
		include_once( plugin_dir_path( __FILE__ ).'/includes/class-wc-admin-screen-4jp.php' );
		// ADD COD Fee 
		include_once( plugin_dir_path( __FILE__ ).'/includes/class-wc-cod-fee-4jp.php' );
		new WooCommerce_Cod_Fee();
	}
	/**
	 * Init WooCommerce when WordPress Initialises.
	 */
	public function init() {
		// Set up localisation
		$this->load_plugin_textdomain();
		// Address Fields Class load
		new AddressField4jp();}

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

function wc4jp_fallback_notice() {
	?>
    <div class="error">
        <ul>
            <li><?php echo __( 'WooCommerce for Japanese is enabled but not effective. It requires WooCommerce in order to work.', 'woocommerce-4jp' );?></li>
        </ul>
    </div>
    <?php
}
function WooCommerce4jp_plugin() {
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        new WooCommerce4jp();
    } else {
        add_action( 'admin_notices', 'wc4jp_fallback_notice' );
    }
}
