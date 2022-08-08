<?php

namespace WC_Better_Shipping_Calculator_for_Brazil\Admin;

use WC_Better_Shipping_Calculator_for_Brazil\Helpers as h;

final class Notices {
	protected static $fields = null;

	public function __start () {
		add_action( 'admin_notices', [ $this, 'donation_notice' ] );
	}

	public function donation_notice () {
		if ( ! current_user_can( 'administrator' ) ) return;

		$cookie = h::prefix( 'donation_notice_closed' );
		if ( 'yes' === h::get( $_COOKIE[ $cookie ] ) ) return;

		global $pagenow;
		$in_plugins = 'plugins.php' === $pagenow;
		if ( $in_plugins ) {
			$class = 'notice notice-info is-dismissible';

			echo h::get_template( 'notice-donation', [
				'class' => $class,
				'cookie' => $cookie,
				'cookie_max_age' => 3 * MONTH_IN_SECONDS
			] );
		}
	}
}