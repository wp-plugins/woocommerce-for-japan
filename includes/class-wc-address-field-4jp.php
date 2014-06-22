<?php
/**
 * Plugin Name: WooCommerce For Japan
 * Plugin URI: http://wordpress.org/plugins/woocommerce4jp/
 * Description: Woocommerce toolkit for Japanese use.
 * Version: 0.9.0
 * Author: Artisan Workshop
 * Author URI: http://profiles.wordpress.org/shoheitanaka
 * Requires at least: 3.8
 * Tested up to: 3.9
 *
 * Text Domain: woocommerce-4jp
 * Domain Path: /i18n/languages/
 *
 * @package WooCommerce-4jp
 * @category Core
 * @author Artisan Workshop
 */
    /**
     * New checkout billing fields
     *
     * @param  array $fields Default fields.
     *
     * @return array         New fields.
     */
class AddressField4jp{
	
	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		if(defined('ICL_SITEPRESS_VERSION')){
			global $sitepress;
            $current_language = $sitepress->get_current_language();
		}
		if(!$current_language or $current_language == 'ja'){
        // MyPage Edit And Checkout fields.
		add_filter( 'woocommerce_default_address_fields',array( &$this,  'address_fields'));
		add_filter( 'woocommerce_billing_fields',array( &$this,  'billing_address_fields'));
		add_filter( 'woocommerce_shipping_fields',array( &$this,  'shipping_address_fields'));
		}
	}
    public function address_fields( $fields ) {
		$fields = array(
			'country'            => array(
				'type'     => 'country',
				'label'    => __( 'Country', 'woocommerce-4jp' ),
				'required' => true,
				'class'    => array( 'form-row-wide', 'address-field', 'update_totals_on_change' ),
			),
			'last_name'          => array(
				'label'    => __( 'Last Name', 'woocommerce-4jp' ),
				'required' => true,
				'class'    => array( 'form-row-first' ),
			),
			'first_name'         => array(
				'label'    => __( 'First Name', 'woocommerce-4jp' ),
				'required' => true,
				'class'    => array( 'form-row-last' ),
				'clear'    => true
			),
			'company'            => array(
				'label' => __( 'Company Name', 'woocommerce-4jp' ),
				'class' => array( 'form-row-wide' ),
			),
			'postcode'           => array(
				'label'       => __( 'Postcode / Zip', 'woocommerce-4jp' ),
				'placeholder' => __( 'Postcode / Zip', 'woocommerce-4jp' ),
				'required'    => true,
				'class'       => array( 'form-row-first', 'address-field' ),
				'validate'    => array( 'postcode' )
			),
			'state'              => array(
				'type'        => 'state',
				'label'       => __( 'Province', 'woocommerce-4jp' ),
				'placeholder' => __( 'Province', 'woocommerce-4jp' ),
				'required'    => true,
				'class'       => array( 'form-row-last', 'address-field' ),
				'clear'       => true,
				'validate'    => array( 'state' )
			),
			'city'               => array(
				'label'       => __( 'Town / City', 'woocommerce-4jp' ),
				'placeholder' => __( 'Town / City', 'woocommerce-4jp' ),
				'required'    => true,
				'class'       => array( 'form-row-wide', 'address-field' )
			),
			'address_1'          => array(
				'label'       => __( 'Address', 'woocommerce-4jp' ),
				'placeholder' => _x( 'Street address', 'placeholder', 'woocommerce-4jp' ),
				'required'    => true,
				'class'       => array( 'form-row-wide', 'address-field' )
			),
			'address_2'          => array(
				'placeholder' => _x( 'Apartment, suite, unit etc. (optional)', 'placeholder', 'woocommerce-4jp' ),
				'class'       => array( 'form-row-wide', 'address-field' ),
				'required'    => false
			),
		);
		return $fields;
	}
		// Billing/Shipping Specific
    public function billing_address_fields( $fields ) {
		$address_fields = $fields;

		$address_fields['billing_email'] = array(
				'label' 		=> __( 'Email Address', 'woocommerce-4jp' ),
				'required' 		=> true,
				'class' 		=> array( 'form-row-first' ),
				'validate'		=> array( 'email' ),
			);
			$address_fields['billing_phone'] = array(
				'label' 		=> __( 'Billing Phone', 'woocommerce-4jp' ),
				'required' 		=> true,
				'class' 		=> array( 'form-row-last' ),
				'clear'			=> true,
				'validate'		=> array( 'phone' ),
			);
		return $address_fields;
	}
    public function shipping_address_fields( $fields ) {
		$address_fields = $fields;

		$address_fields['shipping_phone'] = array(
			'label' 		=> __( 'Shipping Phone', 'woocommerce-4jp' ),
			'required' 		=> true,
			'class' 		=> array( 'form-row-wide' ),
			'clear'			=> true,
			'validate'		=> array( 'phone' ),
		);
	return $address_fields;
	}
}
        $AddressField4jp = new AddressField4jp();
