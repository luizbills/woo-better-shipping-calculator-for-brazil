<?php

namespace WC_Better_Shipping_Calculator_for_Brazil\Admin;

use WC_Better_Shipping_Calculator_for_Brazil\Helpers as h;

final class Plugin_Meta {
	public function __start () {
		add_filter( 'plugin_row_meta', [ $this, 'plugin_meta' ], 10, 2 );
	}

	public function plugin_meta ( $plugin_meta, $plugin_file ) {
		if ( plugin_basename( h::config_get( 'FILE' ) ) === $plugin_file ) {
			$donation_url = esc_url( h::config_get( 'DONATION_URL' ) );
			$forum_url = h::config_get( 'PLUGIN_FORUM' );

			$plugin_meta[] = "<a href=\"$forum_url\" target='blank' rel='noopener'>" . esc_html__( 'Community support', 'wc-better-shipping-calculator-for-brazil' ) .  "</a>";

			$plugin_meta[] = "<a href=\"$donation_url\" target='blank' rel='noopener' style='color:#087f5b;font-weight:700;'>" . esc_html__( 'Donate', 'wc-better-shipping-calculator-for-brazil' ) .  "</a>";
		}
		return $plugin_meta;
	}
}
