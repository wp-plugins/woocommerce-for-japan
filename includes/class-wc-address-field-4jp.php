<?php
/**
 * Plugin Name: WooCommerce For Japan
 * Plugin URI: http://wordpress.org/plugins/woocommerce-for-japan/
 * Description: Woocommerce toolkit for Japanese use.
 * Version: 1.0.0
 * Author: Artisan Workshop
 * Author URI: http://wc.artws.info/
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
        // MyPage Edit And Checkout fields.
		add_filter( 'woocommerce_default_address_fields',array( &$this,  'address_fields'));
		add_filter( 'woocommerce_billing_fields',array( &$this,  'billing_address_fields'));
		add_filter( 'woocommerce_shipping_fields',array( &$this,  'shipping_address_fields'));
		add_filter( 'woocommerce_formatted_address_replacements', array( &$this, 'address_replacements'),10,2);
		add_filter( 'woocommerce_localisation_address_formats', array( &$this, 'address_formats'));
		//My Account Display for address
		add_filter( 'woocommerce_my_account_my_address_formatted_address', array( &$this, 'formatted_address'),10,3);//template/myaccount/my-address.php
		//Check Out Display for address
		add_filter( 'woocommerce_order_formatted_billing_address', array( &$this, 'wc4jp_billing_address'),10,2);//includes/abstract/abstract-wc-order.php
		add_filter( 'woocommerce_order_formatted_shipping_address', array( &$this, 'wc4jp_shipping_address'),10,2);//includes/abstract/abstract-wc-order.php
		//Admin CSS file 
		add_action( 'admin_enqueue_scripts', 'load_custom_wc4jp_admin_style' ,20);

function load_custom_wc4jp_admin_style() {
        wp_register_style( 'custom_wc4jp_admin_css', plugins_url() . '/woocommerce-for-japan/includes/views/css/admin-wc4jp.css', false, '1.0.0' );
        wp_enqueue_style( 'custom_wc4jp_admin_css' );
}
	// Admin Edit Address
		add_filter( 'woocommerce_admin_billing_fields',array( &$this,  'admin_billing_address_fields'));
		add_filter( 'woocommerce_admin_shipping_fields',array( &$this,  'admin_shipping_address_fields'));
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
			'yomigana_last_name'          => array(
				'label'    => __( 'Last Name (Yomigana)', 'woocommerce-4jp' ),
				'required' => true,
				'class'    => array( 'form-row-first' ),
			),
			'yomigana_first_name'         => array(
				'label'    => __( 'First Name (Yomigana)', 'woocommerce-4jp' ),
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
				'label'       => __( 'Prefecture', 'woocommerce-4jp' ),
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
		if(!get_option( 'wc4jp-company-name'))unset($fields['company']);
		if(!get_option( 'wc4jp-yomigana'))unset($fields['yomigana_last_name'],$fields['yomigana_first_name']);
		return $fields;
	}
		// Billing/Shipping Specific
    public function billing_address_fields( $fields ) {
		$address_fields = $fields;
		$address_fields['billing_state'] = array(
			'type'        => 'state',
			'label'       => __( 'Prefecture', 'woocommerce-4jp' ),
			'required'    => true,
			'class'       => array( 'form-row-last', 'address-field' ),
			'clear'       => true,
			'validate'    => array( 'state' )
		);
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

		$address_fields['shipping_state'] = array(
				'type'        => 'state',
				'label'       => __( 'Prefecture', 'woocommerce-4jp' ),
				'required'    => true,
				'class'       => array( 'form-row-last', 'address-field' ),
				'clear'       => true,
				'validate'    => array( 'state' )
			);
		$address_fields['shipping_phone'] = array(
			'label' 		=> __( 'Shipping Phone', 'woocommerce-4jp' ),
			'required' 		=> true,
			'class' 		=> array( 'form-row-wide' ),
			'clear'			=> true,
			'validate'		=> array( 'phone' ),
		);
	return $address_fields;
	}

    public function address_replacements( $fields, $args ) {
		$fields['{name}'] = $args['last_name'] . ' ' . $args['first_name'];
		$fields['{name_upper}'] = strtoupper( $args['last_name'] . ' ' . $args['first_name'] );
		if(get_option( 'wc4jp-yomigana')){
			$fields['{yomigana_last_name}'] = $args['yomigana_last_name'];
			$fields['{yomigana_first_name}'] = $args['yomigana_first_name'];
		}

		return $fields;
	}
	public function address_formats( $fields ) {
		
		if(get_option( 'wc4jp-company-name') and get_option( 'wc4jp-yomigana')){
			$fields['JP'] = "〒{postcode}\n{state}{city}{address_1}\n{address_2}\n{company}\n{yomigana_last_name} {yomigana_first_name}\n{last_name} {first_name}\n {country}";
		}
		if(!get_option( 'wc4jp-company-name') and get_option( 'wc4jp-yomigana')){
			$fields['JP'] = "〒{postcode}\n{state}{city}{address_1}\n{address_2}\n{yomigana_last_name} {yomigana_first_name}\n{last_name} {first_name}\n {country}";
		}
		if(!get_option( 'wc4jp-company-name') and !get_option( 'wc4jp-yomigana')){
			$fields['JP'] = "〒{postcode}\n{state}{city}{address_1}\n{address_2}\n{last_name} {first_name}\n {country}";
		}
		return $fields;
	}
	public function formatted_address( $fields, $customer_id, $name) {
		$fields['yomigana_first_name']  = get_user_meta( $customer_id, $name . '_yomigana_first_name', true );
		$fields['yomigana_last_name']  = get_user_meta( $customer_id, $name . '_yomigana_last_name', true );

		return $fields;
	}
	public function wc4jp_billing_address( $fields, $args) {
		$fields['yomigana_first_name'] = $args->billing_yomigana_first_name;
		$fields['yomigana_last_name'] = $args->billing_yomigana_last_name;

		return $fields;
	}
	public function wc4jp_shipping_address( $fields, $args) {
		$fields['yomigana_first_name'] = $args->shipping_yomigana_first_name;
		$fields['yomigana_last_name'] = $args->shipping_yomigana_last_name;

		return $fields;
	}

    public function admin_billing_address_fields( $fields ) {
		$fields=array(
			'country' => array(
				'label' => __( 'Country', 'woocommerce' ),
				'show'	=> false,
				'type'	=> 'select',
				'options' => array( '' => __( 'Select a country&hellip;', 'woocommerce' ) ) + WC()->countries->get_allowed_countries()
				),
			'state' => array(
				'label' => __( 'State/County', 'woocommerce' ),
				'show'	=> false,
				'type'	=> 'select',
				'options' => array( '' => __( 'Select a state&hellip;', 'woocommerce' ) )
				),
			'postcode' => array(
				'label' => __( 'Postcode', 'woocommerce' ),
				'show'	=> false,
				),
			'city' => array(
				'label' => __( 'City', 'woocommerce' ),
				'show'	=> false
				),
			'address_1' => array(
				'label' => __( 'Address 1', 'woocommerce' ),
				'show'	=> false
				),
			'address_2' => array(
				'label' => __( 'Address 2', 'woocommerce' ),
				'show'	=> false
				),
			'company' => array(
				'label' => __( 'Company', 'woocommerce' ),
				'show'	=> false
				),
			'last_name' => array(
				'label' => __( 'Last Name', 'woocommerce-4jp' ),
				'show'	=> false
				),
			'first_name' => array(
				'label' => __( 'First Name', 'woocommerce-4jp' ),
				'show'	=> false
				),
			'yomigana_last_name' => array(
				'label' => __( 'Last Name Yomigana', 'woocommerce-4jp' ),
				'show'	=> false
				),
			'yomigana_first_name' => array(
				'label' => __( 'First Name Yomigana', 'woocommerce-4jp' ),
				'show'	=> false
				),
			'email' => array(
				'label' => __( 'Email Address', 'woocommerce' ),
				'show'	=> false
				),
			'phone' => array(
				'label' => __( 'Phone', 'woocommerce' ),
				'show'	=> false
				),
		);

		$states = WC()->countries->get_allowed_country_states();
		$fields['state']['options'] = array_merge($fields['state']['options'],$states['JP']);

		if(!get_option( 'wc4jp-company-name'))unset($fields['company']);
		if(!get_option( 'wc4jp-yomigana'))unset($fields['yomigana_last_name'],$fields['yomigana_first_name']);

		return $fields;
	}
    public function admin_shipping_address_fields( $fields ) {
		$fields=array(
			'country' => array(
				'label' => __( 'Country', 'woocommerce' ),
				'show'	=> false,
				'type'	=> 'select',
				'options' => array( '' => __( 'Select a country&hellip;', 'woocommerce' ) ) + WC()->countries->get_shipping_countries()
				),
			'state' => array(
				'label' => __( 'State/County', 'woocommerce' ),
				'show'	=> false,
				'type'	=> 'select',
				'options' => array( '' => __( 'Select a state&hellip;', 'woocommerce' ) )
				),
			'postcode' => array(
				'label' => __( 'Postcode', 'woocommerce' ),
				'show'	=> false
				),
			'city' => array(
				'label' => __( 'City', 'woocommerce' ),
				'show'	=> false
				),
			'address_1' => array(
				'label' => __( 'Address 1', 'woocommerce' ),
				'show'	=> false
				),
			'address_2' => array(
				'label' => __( 'Address 2', 'woocommerce' ),
				'show'	=> false
				),
			'company' => array(
				'label' => __( 'Company', 'woocommerce' ),
				'show'	=> false
				),
			'last_name' => array(
				'label' => __( 'Last Name', 'woocommerce-4jp' ),
				'show'	=> false
				),
			'first_name' => array(
				'label' => __( 'First Name', 'woocommerce-4jp' ),
				'show'	=> false
				),
			'yomigana_last_name' => array(
				'label' => __( 'Last Name Yomigana', 'woocommerce-4jp' ),
				'show'	=> false
				),
			'yomigana_first_name' => array(
				'label' => __( 'First Name Yomigana', 'woocommerce-4jp' ),
				'show'	=> false
				),
			'phone' => array(
				'label' => __( 'Phone', 'woocommerce' ),
				'show'	=> false
				),
		);
		$states = WC()->countries->get_allowed_country_states();
		$fields['state']['options'] = array_merge($fields['state']['options'],$states['JP']);

		if(!get_option( 'wc4jp-company-name'))unset($fields['company']);
		if(!get_option( 'wc4jp-yomigana'))unset($fields['yomigana_last_name'],$fields['yomigana_first_name']);

		return $fields;
	}
}
