<?php

namespace WC_Better_Shipping_Calculator_for_Brazil;

use WC_Better_Shipping_Calculator_for_Brazil\Helpers as h;
use WC_Better_Shipping_Calculator_for_Brazil\Brazilian_States;

final class Shipping_Calculator {
	protected static $instance = null;

	public static function get_instance () {
		return self::$instance;
	}

	public function __start () {
		self::$instance = $this;

		// hide shipping calculator country, state and city fields
		add_filter( 'woocommerce_shipping_calculator_enable_country', '__return_false', 20 );
		add_filter( 'woocommerce_shipping_calculator_enable_state', '__return_false', 20 );
		add_filter( 'woocommerce_shipping_calculator_enable_city', '__return_false', 20 );

		// detect state from postcode
		add_filter( 'woocommerce_cart_calculate_shipping_address', [ $this, 'prepare_address' ], 20 );

		// frontend scripts
		add_action( 'woocommerce_before_shipping_calculator', [ $this, 'add_extra_css' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'add_extra_js' ] );
	}

	public function prepare_address ( $address ) {
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
			display: block;
			content: "<?php echo esc_html( $postcode_label ); ?>";
		}
		<?php endif; ?>

		.shipping-calculator-button {
			display: none!important;
			visibility: hidden!important;
		}

		.shipping-calculator-form {
			display: block!important;
		}
		</style>
		<?php
	}

	public function add_extra_js () {
		if ( ! is_cart() ) return;

		$suffix = h::get_defined( 'SCRIPT_DEBUG' ) ? '' : '.min';

		wp_enqueue_script(
			h::prefix( 'frontend' ),
			h::plugin_url( "/assets/js/frontend{$suffix}.js" ),
			[ 'jquery', 'wc-cart' ],
			h::config_get( 'VERSION' ),
			true
		);

		wp_localize_script(
			h::prefix( 'frontend' ),
			h::prefix( 'params' ),
			apply_filters(
				h::prefix( 'frontend_params' ),
				[
					'postcode_placeholder' => esc_attr__( 'Type your postcode', 'wc-better-shipping-calculator-for-brazil' ),
					'postcode_input_type' => 'tel',
					'selectors' => [
						'postcode' => '#calc_shipping_postcode',
					],
				]
			),
		);
	}
}
