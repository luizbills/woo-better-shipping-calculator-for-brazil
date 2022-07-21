<?php
/*
Always check for the constant WP_UNINSTALL_PLUGIN in uninstall.php before doing anything. This protects against direct access.
The constant will be defined by WordPress during the uninstall.php invocation.
The constant is NOT defined when uninstall is performed by `register_uninstall_hook`.
Reference: https://developer.wordpress.org/plugins/plugin-basics/uninstall-methods/#method-2-uninstall-php
*/
defined( 'WP_UNINSTALL_PLUGIN' ) || exit( 1 );

$prefix = 'wc_better_shipping_calculator_for_brazil_';
delete_option( $prefix . 'version' );
delete_option( $prefix . 'donation_notice_dismissed' );
