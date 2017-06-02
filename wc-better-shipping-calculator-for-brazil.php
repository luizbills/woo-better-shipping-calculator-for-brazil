<?php
/*
 * Plugin Name: WooCommerce Better Shipping Calculator for Brazil
 * Version: 1.0.1
 * Plugin URI: https://github.com/luizbills/wc-better-shipping-calculator-for-brazil
 * Description: Shipping calculator without Country and State fields. Works only in brazilian stores.
 * Author: Luiz Paulo Bills
 * Author URI: http://www.luizp.com/
 * Requires at least: 4.0
 * Tested up to: 4.7
 *
 * WC requires at least: 2.6
 * WC tested up to: 3.0
 *
 * Text Domain: wc-better-shipping-calculator-for-brazil
 * Domain Path: /lang/
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-wc-better-shipping-calculator-for-brazil.php' );

/**
 * Returns the main instance of WC_Better_Shipping_Calculator_for_Brazil to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object WC_Better_Shipping_Calculator_for_Brazil
 */
function WC_Better_Shipping_Calculator_for_Brazil () {
	$instance = WC_Better_Shipping_Calculator_for_Brazil_Plugin::instance( __FILE__, '1.0.0' );

	return $instance;
}

WC_Better_Shipping_Calculator_for_Brazil();
