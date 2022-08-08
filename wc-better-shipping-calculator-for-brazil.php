<?php
/*
Plugin Name: Calculadora de frete melhorada para lojas brasileiras
Plugin URI: https://github.com/luizbills/wc-better-shipping-calculator-for-brazil
Description: Calculadora de frete do WooCommerce otimizada para lojas brasileiras: remove dos campos de país, estado e cidade. E alguns outros ajustes.
Version: 3.0.0
Author: Luiz Bills
Author URI: https://luizpb.com
Requires PHP: 7.3
Requires at least: 4.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wc-better-shipping-calculator-for-brazil
Domain Path: /languages
*/

// prevents your PHP files from being executed via direct browser access
defined( 'WPINC' ) || exit( 1 );

function WC_Better_Shipping_Calculator_for_Brazil () {
	include_once __DIR__ . '/main.php';
}
WC_Better_Shipping_Calculator_for_Brazil();
