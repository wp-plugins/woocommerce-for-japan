<?php
/**
 * Plugin Name: WooCommerce For Japan
 * Plugin URI: http://wordpress.org/plugins/woocommerce-for-japan/
 * Description: Woocommerce toolkit for Japanese use.
 * Version: 1.0.6
 * Author: Artisan Workshop
 * Author URI: http://wc.artws.info/
 * Requires at least: 3.8
 * Tested up to: 4.0
 *
 * Text Domain: woocommerce-4jp
 * Domain Path: /i18n/languages/
 *
 * @package WooCommerce-4jp
 * @category Core
 * @author Artisan Workshop
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_4JP_Admin_Screen {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'wc4jp_admin_menu' ) ,60 );
		add_action( 'admin_init', array( $this, 'wc4jp_setting_init') );
	}
	/**
	 * Admin Menu
	 */
	public function wc4jp_admin_menu() {
		$page = add_submenu_page( 'woocommerce', __( 'For Japanese', 'woocommerce-4jp' ), __( 'For Japanese', 'woocommerce-4jp' ), 'manage_woocommerce', 'wc4jp-output', array( $this, 'wc4jp_output' ) );
	}

	/**
	 * Admin Screen output
	 */
	public function wc4jp_output() {
		$tab = ! empty( $_GET['tab'] ) && $_GET['tab'] == 'info' ? 'info' : 'setting';
		include( 'views/html-admin-screen.php' );
	}

	/**
	 * Admin page for Setting
	 */
	public function admin_setting_page() {
		include( 'views/html-admin-setting-screen.php' );
	}

	/**
	 * Admin page for infomation
	 */
	public function admin_info_page() {
		include( 'views/html-admin-info-screen.php' );
	}
	
	function wc4jp_setting_init(){
		if( isset( $_POST['wc4jp-setting'] ) ){
			if( check_admin_referer( 'my-nonce-key', 'wc4jp-setting')){
				//yomigana
				if(isset($_POST['yomigana']) && $_POST['yomigana']){
					update_option( 'wc4jp-yomigana', $_POST['yomigana']);
				}else{
					update_option( 'wc4jp-yomigana', '');
				}
				//company-name
				if(isset($_POST['company-name']) && $_POST['company-name']){
					update_option( 'wc4jp-company-name', $_POST['company-name']);
				}else{
					update_option( 'wc4jp-company-name', '');
				}
				//bankjp payment method
					$woocommerce_bankjp_settings = get_option('woocommerce_bankjp_settings');
				if(isset($_POST['bankjp']) && $_POST['bankjp']){
					update_option( 'wc4jp-bankjp', $_POST['bankjp']);
					if(isset($woocommerce_bankjp_settings)){
						$woocommerce_bankjp_settings['enabled'] = 'yes';
						update_option( 'woocommerce_bankjp_settings', $woocommerce_bankjp_settings);
					}
				}else{
					update_option( 'wc4jp-bankjp', '');
					if(isset($woocommerce_bankjp_settings)){
						$woocommerce_bankjp_settings['enabled'] = 'no';
						update_option( 'woocommerce_bankjp_settings', $woocommerce_bankjp_settings);
					}
				}
				//PostOffice bankjp payment method
					$woocommerce_postofficebankjp_settings = get_option('woocommerce_postofficebankjp_settings');
				if(isset($_POST['postofficebank']) && $_POST['postofficebank']){
					update_option( 'wc4jp-postofficebank', $_POST['postofficebank']);
					if(isset($woocommerce_postofficebankjp_settings)){
						$woocommerce_postofficebankjp_settings['enabled'] = 'yes';
						update_option( 'woocommerce_postofficebankjp_settings', $woocommerce_postofficebankjp_settings);
					}
				}else{
					update_option( 'wc4jp-postofficebank', '');
					if(isset($woocommerce_postofficebankjp_settings)){
						$woocommerce_postofficebankjp_settings['enabled'] = 'no';
						update_option( 'woocommerce_postofficebankjp_settings', $woocommerce_postofficebankjp_settings);
					}
				}
				//At Store payment method
				if(isset($_POST['atstore']) && $_POST['atstore']){
					update_option( 'wc4jp-atstore', $_POST['atstore']);
				}else{
					update_option( 'wc4jp-atstore', '');
				}
			}
		}
	}
}

new WC_4JP_Admin_Screen();