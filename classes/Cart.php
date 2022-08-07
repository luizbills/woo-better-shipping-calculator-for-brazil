<?php

namespace WC_Better_Shipping_Calculator_for_Brazil;

use WC_Better_Shipping_Calculator_for_Brazil\Helpers as h;
use WC_Better_Shipping_Calculator_for_Brazil\Brazilian_States;

final class Cart {
	public function __start () {
		// hide shipping calculator country, state and city fields
		add_filter( 'woocommerce_shipping_calculator_enable_country', '__return_false', PHP_INT_MAX );
		add_filter( 'woocommerce_shipping_calculator_enable_state', '__return_false', PHP_INT_MAX );
		add_filter( 'woocommerce_shipping_calculator_enable_city', '__return_false', PHP_INT_MAX );

		add_action( 'woocommerce_before_shipping_calculator', [ $this, 'add_extra_css' ] );

		add_filter( 'woocommerce_cart_calculate_shipping_address', [ $this, 'detect_state_from_postcode' ], 20 );
	}

	public function detect_state_from_postcode ( $address ) {
		$country = h::get( $address['country'], 'BR' );
		if ( ! $country || 'BR' === $country ) {
			$postcode = h::get( $address['postcode'] );
			$state = Brazilian_States::get_state_from_postcode( $postcode );
			if ( $state ) {
				$address = [
					'country' => 'BR',
					'state' => $state,
					'city' => '',
					'postcode' => $postcode,
				];
			}
		}
		return $address;
	}

	public function add_extra_css () {
		// translate to "Calcule o frete:"
		$postcode_label = apply_filters(
			h::prefix( 'postcode_label' ),
			__( 'Calculate shipping:', 'wc-better-shipping-calculator-for-brazil' )
		);

		?>
		<style>
		<?php if ( $postcode_label ) : ?>
		#calc_shipping_postcode_field::before {
			content: "<?php echo esc_html( $postcode_label ); ?>";
			font-weight: bold;
		}
		<?php endif; ?>
		.shipping-calculator-button {
			display: none!important
		}
		.shipping-calculator-form {
			padding-top: 0!important;
			display: block!important
		}
		</style>
		<?php
	}
}
