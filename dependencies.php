<?php

use WC_Better_Shipping_Calculator_for_Brazil\Helpers as h;

defined( 'WPINC' ) || exit( 1 );

return [
	// Check the PHP version
	'php' => [
		'check' => function () {
			$req_version = h::config_get( 'REQUIRED_PHP_VERSION', false );
			$serv_version = \preg_replace( '/[^0-9\.]/', '', PHP_VERSION );
			return $req_version && $serv_version ? \version_compare( $serv_version, $req_version, '>=' ) : true;
		},
		'message' => function () {
			$req_version = h::config_get( 'REQUIRED_PHP_VERSION', false );
			return sprintf(
				/* translators: %s is replaced with <strong>PHP</strong> */
				__( "Update your %s version to $req_version or later.", 'wc-better-shipping-calculator-for-brazil' ),
				'<strong>PHP</strong>'
			);
		},
	],

	// Check if WooCommerce is activated
	'woocommerce' => [
		'check' => function () {
			return \function_exists( 'WC' );
		},
		'message' => sprintf(
			// translators: %s is replaced with a required plugin name
			__( 'Install and activate the %s plugin.', 'wc-better-shipping-calculator-for-brazil' ),
			'<strong>WooCommerce</strong>'
		),
	],
];
