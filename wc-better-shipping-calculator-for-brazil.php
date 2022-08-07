<?php
/*
 * Plugin Name: Calculadora de frete melhorada para lojas brasileiras
 * Version: 3.0.0
 * Plugin URI: https://github.com/luizbills/wc-better-shipping-calculator-for-brazil
 * Description: Shipping calculator without Country and State fields. Works only in brazilian stores.
 * Author: Luiz Bills
 * Author URI: http://github.com/luizbills
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wc-better-shipping-calculator-for-brazil
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-wc-better-shipping-calculator-for-brazil-plugin.php' );

/**
 * Returns the main instance of WC_Better_Shipping_Calculator_for_Brazil to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object WC_Better_Shipping_Calculator_for_Brazil
 */
function WC_Better_Shipping_Calculator_for_Brazil () {
	$instance = WC_Better_Shipping_Calculator_for_Brazil_Plugin::instance( __FILE__, '2.1' );
	return $instance;
}

WC_Better_Shipping_Calculator_for_Brazil();
