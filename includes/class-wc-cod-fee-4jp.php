<?php
/**
 * Thank you for Pinch Of Code
 * I used the code made  by Pinch Of Code
 *
 * WooCommerce Payment Fees
 * Copyright (C) 2014 Pinch Of Code. All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Contact the author at info@pinchofcode.com
 */

/**
 * Main plugin class
 */

if( !class_exists( 'WooCommerce_Cod_Fee' ) ) :
class WooCommerce_Cod_Fee {
    /**
     * @var string
     */
    public $version;

    /**
     * @var string
     */
    public $suffix;

    /**
     * @var string
     */
    private $plugin_url;

    /**
     * @var string
     */
    private $plugin_path;

    /**
     * @var object
     */
    public $current_gateway;

    /**
     * @var double
     */
    public $current_extra_charge_amount;

    /**
     * Constructor
     *
     * @param string $id Order id
     */
    public function __construct() {
        global $pagenow;

        $this->version                              = '1.0.2';
        $this->suffix                               = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        $this->plugin_url                           = $this->plugin_url();
        $this->plugin_path                          = $this->plugin_path();
        $this->current_gateway                      = null;
        $this->current_extra_charge_amount          = 0;
        $this->current_extra_charge_max_cart_value  = 0;

        //Hooks & Filters
        add_action( 'woocommerce_cart_calculate_fees',          array( $this, 'calculate_order_totals' ) );
        add_action( 'wp_enqueue_scripts' ,                      array( $this, 'enqueue_scripts_frontend' ) );

        if( is_admin() ) {
			if(isset($_GET['section']) and $_GET['section']=='wc_gateway_cod'){
            add_action( 'admin_head',                           array( $this, 'manage_form_fields' ) );
			}
        }
    }

    public function enqueue_scripts_frontend() {
        $min = !defined( 'SCRIPT_DEBUG' ) || !SCRIPT_DEBUG  ? '.min' : '';

        if( !is_checkout() ) { return; }

        wp_enqueue_script( 'wc-pf-checkout', $this->plugin_url . '/assets/js/checkout' . $min . '.js', array( 'jquery' ), $this->version, true );
    }

    /**
     * Manage gateways form fields
     *
     * @return string
     */
    public function manage_form_fields() {
        $current_tab        = !isset( $_GET['tab'] )     || empty( $_GET['tab'] )     ? '' : sanitize_text_field( urldecode( $_GET['tab'] ) );
        $current_section    = !isset( $_GET['section'] ) || empty( $_GET['section'] ) ? '' : sanitize_text_field( urldecode( $_GET['section'] ) );
        $current_gateway    = 'cod';
        $charge_amount      = 0;
        $max_cart_value     = 0;

        if( $current_tab == 'checkout' && !empty( $current_section ) ) {
            $html = $this->manage_form_fields_for_basic_gateways( $current_gateway );
            $html = str_replace( array( "\r", "\n" ) , '', trim( $html ) );
            $html = str_replace( "'", '"', $html );

            wc_enqueue_js( "$( '.form-table:last' ).after( '" . $html . "' );" );
        }
    }

    /**
     * Prints fees form on the gateways settings page for all the gateways NOT USING credit/debit cards.
     *
     * @param  string $current_gateway
     * @return string
     */
    public function manage_form_fields_for_basic_gateways( $current_gateway ) {
        if( isset( $_REQUEST['save'] ) ) {
            update_option( $this->get_option_id( $current_gateway, 'name' ), $_REQUEST[ $this->get_option_id( $current_gateway, 'name' ) ] );
            update_option( $this->get_option_id( $current_gateway, 'amount' ), $_REQUEST[ $this->get_option_id( $current_gateway, 'amount' ) ] );
            update_option( $this->get_option_id( $current_gateway, 'max_cart_value' ), $_REQUEST[ $this->get_option_id( $current_gateway, 'max_cart_value' ) ] );
            update_option( $this->get_option_id( $current_gateway, 'calc_taxes' ), $_REQUEST[ $this->get_option_id( $current_gateway, 'calc_taxes' ) ] );
        }

        $fee_name       = get_option( $this->get_option_id( $current_gateway, 'name' ) );
        $charge_amount  = get_option( $this->get_option_id( $current_gateway, 'amount' ) );
        $max_cart_value = get_option( $this->get_option_id( $current_gateway, 'max_cart_value' ) );
        $calc_taxes     = get_option( $this->get_option_id( $current_gateway, 'calc_taxes' ) );

        ob_start() ?>
        <h3><?php _e( 'Extra charge for COD method', 'woocommerce-4jp' ) ?></h3>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row" class="titledesc"><label for="woocommerce_cod_extra_charge_name"><?php _e( 'Fee name', 'woocommerce-4jp' ) ?></label></th>
                    <td class="forminp">
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e( 'Fee name', 'woocommerce-4jp' ) ?></span></legend>
                            <input class="input-text regular-input " type="text" name="woocommerce_cod_extra_charge_name" id="woocommerce_cod_extra_charge_name" value="<?php echo $fee_name ?>" placeholder="<?php _e( 'COD Payment method fee', 'woocommerce-4jp' ) ?>">
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc"><label for="woocommerce_cod_extra_charge_amount"><?php _e( 'Extra charge amount', 'woocommerce-4jp' ) ?></label></th>
                    <td class="forminp">
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e( 'Extra charge amount', 'woocommerce-4jp' ) ?></span></legend>
                            <input class="input-text regular-input " type="number" name="woocommerce_cod_extra_charge_amount" id="woocommerce_cod_extra_charge_amount" style="width:70px" value="<?php echo $charge_amount ?>" placeholder="0" min="0" step="1">
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc"><label for="woocommerce_cod_extra_charge_max_cart_value"><?php _e( 'Maximum cart value to which adding fee', 'woocommerce-4jp' ) ?></label></th>
                    <td class="forminp">
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e( 'Maximum cart value for adding fee', 'woocommerce-4jp' ) ?></span></legend>
                            <input class="input-text regular-input " type="number" name="woocommerce_cod_extra_charge_max_cart_value" id="woocommerce_cod_extra_charge_max_cart_value" style="width:70px" value="<?php echo $max_cart_value ?>" placeholder="0" min="0" step="1">
                            <p><?php _e( 'If you dont need this setting, please set empty, 0.', 'woocommerce-4jp' ) ?></p>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="titledesc"><?php _e( 'Includes taxes', 'woocommerce-4jp' ) ?></th>
                    <td class="forminp">
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e( 'Calculate taxes on this fee', 'woocommerce-4jp' ) ?></span></legend>
                            <select name="woocommerce_cod_extra_charge_calc_taxes" id="woocommerce_cod_extra_charge_calc_taxes" style="" class="select">
                                <option value="no-tax" <?php selected( 'no-tax', $calc_taxes ) ?>><?php _e( 'Do not calculate taxes', 'woocommerce-4jp' ) ?></option>
                                <option value="tax-incl" <?php selected( 'tax-incl', $calc_taxes ) ?>><?php _e( 'The fee is taxes included', 'woocommerce-4jp' ) ?></option>
                                <option value="tax-excl" <?php selected( 'tax-excl', $calc_taxes ) ?>><?php _e( 'The fee is taxes excluded', 'woocommerce-4jp' ) ?></option>
                            </select>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php

        return ob_get_clean();
    }

    /**
     * Add extra charge to cart totals
     *
     * @param double $totals
     * return double
     */
    public function calculate_order_totals( $cart ) {
        if( !defined( 'WOOCOMMERCE_CHECKOUT' ) ) { return; }

        $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
        $current_gateway    = WC()->session->chosen_payment_method;
        $subtotal           = $cart->cart_contents_total;

        if( !empty( $available_gateways ) ) {
            //Get the current gateway
            if ( isset( $current_gateway ) && isset( $available_gateways[ $current_gateway ] ) ) {
                $current_gateway = $available_gateways[ $current_gateway ];
            } elseif ( isset( $available_gateways[ get_option( 'woocommerce_default_gateway' ) ] ) ) {
                $current_gateway = $available_gateways[ get_option( 'woocommerce_default_gateway' ) ];
            } else {
                $current_gateway = current( $available_gateways );
            }
        }

        $this->current_gateway          = $current_gateway; //Note: this is an object
        $extra_charge_max_cart_value    = get_option( $this->get_option_id( $current_gateway->id, 'max_cart_value' ) );

        //Add charges to cart totals
        if( !empty( $current_gateway ) && ( empty( $extra_charge_max_cart_value ) || $extra_charge_max_cart_value >= $subtotal ) ) {

            $extra_charge_name              = get_option( $this->get_option_id( $current_gateway->id, 'name' ) );
            $extra_charge_amount            = get_option( $this->get_option_id( $current_gateway->id, 'amount' ) );
            $calc_taxes                     = get_option( $this->get_option_id( $current_gateway->id, 'calc_taxes' ) );

            $decimal_sep                    = wp_specialchars_decode( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ), ENT_QUOTES );
            $thousands_sep                  = wp_specialchars_decode( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ), ENT_QUOTES );

            $taxable    = false;
            $taxes      = 0;
            if( $calc_taxes != 'no-tax' ) {
                $taxable    = true;
                $tax        = new WC_Tax();
                $base_rate  = $tax->get_shop_base_rate();
                $taxrates   = array_shift( $base_rate );
                $taxrate    = floatval( $taxrates['rate'] ) / 100;
                if( $calc_taxes == 'tax-incl' ) {
                    $taxes                = $extra_charge_amount - ( $extra_charge_amount / ( 1 + $taxrate ) );
                    $extra_charge_amount -= $taxes;
                } else {
                    $taxes = $extra_charge_amount * $taxrate;
                }
            }

            $extra_charge_amount        = apply_filters( 'woocommerce_wc_pf_' . $current_gateway->id . '_amount' , $extra_charge_amount , $subtotal , $current_gateway );
            $do_apply                   = $extra_charge_amount != 0;
            $do_apply                   = apply_filters( 'woocommerce_wc_pf_apply' , $do_apply , $extra_charge_amount , $subtotal , $current_gateway );
            $do_apply                   = apply_filters( 'woocommerce_wc_pf_apply_for_' . $current_gateway->id , $do_apply , $extra_charge_amount , $subtotal , $current_gateway );

            if ( $do_apply ) {

                $already_exists = false;
                $fees           = $cart->get_fees();
                for( $i = 0; $i < count( $fees ); $i++ ) {
                    if( $fees[$i]->id == 'payment-method-fee' ) {
                        $already_exists = true;
                        $fee_id = $i;
                    }
                }

                if( !$already_exists ) {
                    $cart->add_fee( $extra_charge_name, $extra_charge_amount, $taxable );
                } else {
                    $fees[$fee_id]->amount = $extra_charge_amount;
                }
            }

            $this->current_extra_charge_amount = $extra_charge_amount;
        }
    }

    /**
     * Return formatted name of the option
     *
     * @param string $payment_gateway
     * @param string $option
     * @return string
     */
    public function get_option_id( $payment_gateway, $option ) {
        return 'woocommerce_' . $payment_gateway . '_extra_charge_' . $option;
    }

    /**
     * Get the plugin url.
     *
     * @return string
     */
    public function plugin_url() {
        if ( $this->plugin_url ) return $this->plugin_url;
        return $this->plugin_url = untrailingslashit( plugins_url( '/', dirname( __FILE__ ) ) );
    }


    /**
     * Get the plugin path.
     *
     * @return string
     */
    public function plugin_path() {
        if ( $this->plugin_path ) return $this->plugin_path;

        return $this->plugin_path = untrailingslashit( dirname( plugin_dir_path( __FILE__ ) ) );
    }
}
endif;
