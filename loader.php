<?php

namespace WC_Better_Shipping_Calculator_for_Brazil;

use WC_Better_Shipping_Calculator_for_Brazil\Helpers as h;

defined( 'WPINC' ) || exit( 1 );

// register_activation_hook( h::config_get( 'FILE' ), function () {
// 	h::log( 'plugin activated' );
// } );

return [
	[ Cart::class, 10 ], // 10 is priority
];
