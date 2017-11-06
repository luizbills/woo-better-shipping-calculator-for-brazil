/* global wc_cart_params */
// based on https://github.com/woocommerce/woocommerce/blob/3.0.4/assets/js/frontend/cart.js

window.jQuery( function( $ ) {

	// wc_cart_params is required to continue, ensure the object exists
	if ( typeof wc_cart_params === 'undefined' ) {
		return false;
	}

	// Utility functions for the file.

	function log () {
		if (wc_cart_params.script_debug) console.log.apply(console, arguments);
	}

	/**
	 * Gets a url for a given AJAX endpoint.
	 *
	 * @param {String} endpoint The AJAX Endpoint
	 * @return {String} The URL to use for the request
	 */
	var get_url = function( endpoint ) {
		return wc_cart_params.wc_ajax_url.toString().replace(
			'%%endpoint%%',
			endpoint
		);
	};

	/**
	 * Check if a node is blocked for processing.
	 *
	 * @param {JQuery Object} $node
	 * @return {bool} True if the DOM Element is UI Blocked, false if not.
	 */
	var is_blocked = function( $node ) {
		return $node.is( '.processing' ) || $node.parents( '.processing' ).length;
	};

	/**
	 * Block a node visually for processing.
	 *
	 * @param {JQuery Object} $node
	 */
	var block = function( $node ) {
		if ( ! is_blocked( $node ) ) {
			$node.addClass( 'processing' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			} );
		}
	};

	/**
	 * Unblock a node after processing is complete.
	 *
	 * @param {JQuery Object} $node
	 */
	var unblock = function( $node ) {
		$node.removeClass( 'processing' ).unblock();
	};

	/**
	 * Update the .woocommerce div with a string of html.
	 *
	 * @param {String} html_str The HTML string with which to replace the div.
	 * @param {bool} preserve_notices Should notices be kept? False by default.
	 */
	var update_wc_div = function( html_str, preserve_notices ) {
		var $html       = $.parseHTML( html_str );
		var $new_form   = $( '.woocommerce-cart-form', $html );
		var $new_totals = $( '.cart_totals', $html );
		var $notices    = $( '.woocommerce-error, .woocommerce-message, .woocommerce-info', $html );

		// No form, cannot do this.
		if ( $( '.woocommerce-cart-form' ).length === 0 ) {
			window.location.href = window.location.href;
			return;
		}

		// Remove errors
		if ( ! preserve_notices ) {
			$( '.woocommerce-error, .woocommerce-message, .woocommerce-info' ).remove();
		}

		if ( $new_form.length === 0 ) {
			// If the checkout is also displayed on this page, trigger reload instead.
			if ( $( '.woocommerce-checkout' ).length ) {
				window.location.href = window.location.href;
				return;
			}

			// No items to display now! Replace all cart content.
			var $cart_html = $( '.cart-empty', $html ).closest( '.woocommerce' );
			$( '.woocommerce-cart-form__contents' ).closest( '.woocommerce' ).replaceWith( $cart_html );

			// Display errors
			if ( $notices.length > 0 ) {
				show_notice( $notices, $( '.cart-empty' ).closest( '.woocommerce' ) );
			}
		} else {
			// If the checkout is also displayed on this page, trigger update event.
			if ( $( '.woocommerce-checkout' ).length ) {
				$( document.body ).trigger( 'update_checkout' );
			}

			$( '.woocommerce-cart-form' ).replaceWith( $new_form );
			$( '.woocommerce-cart-form' ).find( 'input[name="update_cart"]' ).prop( 'disabled', true );

			if ( $notices.length > 0 ) {
				show_notice( $notices );
			}

			update_cart_totals_div( $new_totals );
		}

		$( document.body ).trigger( 'updated_wc_div' );
	};

	/**
	 * Update the .cart_totals div with a string of html.
	 *
	 * @param {String} html_str The HTML string with which to replace the div.
	 */
	var update_cart_totals_div = function( html_str ) {
		$( '.cart_totals' ).replaceWith( html_str );
		$( document.body ).trigger( 'updated_cart_totals' );
	};

	/**
	 * Clear previous notices and shows new one above form.
	 *
	 * @param {Object} The Notice HTML Element in string or object form.
	 */
	var show_notice = function( html_element, $target ) {
		if ( ! $target ) {
			$target = $( '.woocommerce-cart-form' );
		}
		$target.before( html_element );
	};


	/**
	 * Object to handle AJAX calls for cart shipping changes.
	 */
	var cart_shipping = {

		/**
		 * Initialize event handlers and UI state.
		 */
		init: function( cart ) {
			this.cart                       = cart;
			this.toggle_shipping            = this.toggle_shipping.bind( this );
			this.shipping_method_selected   = this.shipping_method_selected.bind( this );
			this.shipping_calculator_submit = this.shipping_calculator_submit.bind( this );
			this.maybe_hide_state_field     = this.maybe_hide_state_field.bind( this );

			$( document ).on(
				'click',
				'.shipping-calculator-button',
				this.toggle_shipping
			);
			$( document ).on(
				'change',
				'select.shipping_method, input[name^=shipping_method]',
				this.shipping_method_selected
			);
			$( document ).on(
				'submit',
				'form.woocommerce-shipping-calculator',
				this.shipping_calculator_submit
			);

			if ( wc_cart_params.hide_country_field !== 'on' && wc_cart_params.hide_state_field === 'on' ) {
				$( document.body ).on(
					'country_to_state_changing',
					this.maybe_hide_state_field
				);
			}

			$( '.shipping-calculator-form' ).hide();
		},

		/**
		 * Toggle Shipping Calculator panel
		 */
		toggle_shipping: function() {
			var calculator_fields = $( '.shipping-calculator-form' );

			if ( wc_cart_params.hide_country_field === 'on' ) {
				calculator_fields.find('#calc_shipping_country_field').hide();
				calculator_fields.find('#calc_shipping_state_field').hide();
			} else if ( wc_cart_params.hide_state_field === 'on' ) {
				this.maybe_hide_state_field( null, $( 'select#calc_shipping_country' ).find( 'option:selected' ).val() );
			}

			calculator_fields.slideToggle( 'slow' );
			$( document.body ).trigger( 'country_to_state_changed' );

			return false;
		},

		maybe_hide_state_field: function( ev, country ) {
			if ( country === 'BR' ) {
				$( '.shipping-calculator-form' ).find('#calc_shipping_state_field').hide();
			} else {
				$( '.shipping-calculator-form' ).find('#calc_shipping_state_field').show();
			}

			return true;
		},

		/**
		 * Handles when a shipping method is selected.
		 *
		 * @param {Object} evt The JQuery event.
		 */
		shipping_method_selected: function( evt ) {
			var target = evt.currentTarget;

			var shipping_methods = {};

			$( 'select.shipping_method, input[name^=shipping_method][type=radio]:checked, input[name^=shipping_method][type=hidden]' ).each( function() {
				shipping_methods[ $( target ).data( 'index' ) ] = $( target ).val();
			} );

			block( $( 'div.cart_totals' ) );

			var data = {
				security: wc_cart_params.update_shipping_method_nonce,
				shipping_method: shipping_methods
			};

			$.ajax( {
				type:     'post',
				url:      get_url( 'update_shipping_method' ),
				data:     data,
				dataType: 'html',
				success:  function( response ) {
					update_cart_totals_div( response );
				},
				complete: function() {
					unblock( $( 'div.cart_totals' ) );
					$( document.body ).trigger( 'updated_shipping_method' );
				}
			} );
		},

		/**
		 * Handles a shipping calculator form submit.
		 *
		 * @param {Object} evt The JQuery event.
		 */
		shipping_calculator_submit: function( evt ) {
			evt.preventDefault();

			var $form = $( evt.currentTarget );
			var $country_select = $form.find( 'select#calc_shipping_country' );
			var country_is_BR = wc_cart_params.hide_country_field === 'on' || $country_select.find( 'option:selected' ).val() === 'BR'

			if ( country_is_BR ) {
				$country_select.val( 'BR' ); // make sure BR is selected

				if ( wc_cart_params.hide_state_field === 'on' ) {
					// automatically discover the brazilian state
					var postcode = $form.find('#calc_shipping_postcode_field input').val();

					$form.find('select#calc_shipping_state').val( get_state_by_postcode( postcode ) );

					log( 'postcode:', postcode );
				}
			}

			block( $( 'div.cart_totals' ) );
			block( $form );

			// Provide the submit button value because wc-form-handler expects it.
			$( '<input />' ).attr( 'type', 'hidden' )
							.attr( 'name', 'calc_shipping' )
							.attr( 'value', 'x' )
							.appendTo( $form );

			// Make call to actual form post URL.
			$.ajax( {
				type:     $form.attr( 'method' ),
				url:      $form.attr( 'action' ),
				data:     $form.serialize(),
				dataType: 'html',
				success:  function( response ) {
					update_wc_div( response );
				},
				complete: function() {
					unblock( $form );
					unblock( $( 'div.cart_totals' ) );
				}
			} );
		}
	};

	/**
	 * Object to handle cart UI.
	 */
	var cart = {
		/**
		 * Initialize cart UI events.
		 */
		init: function() {
			this.update_cart_totals    = this.update_cart_totals.bind( this );
			this.input_keypress        = this.input_keypress.bind( this );
			this.cart_submit           = this.cart_submit.bind( this );
			this.submit_click          = this.submit_click.bind( this );
			this.apply_coupon          = this.apply_coupon.bind( this );
			this.remove_coupon_clicked = this.remove_coupon_clicked.bind( this );
			this.quantity_update       = this.quantity_update.bind( this );
			this.item_remove_clicked   = this.item_remove_clicked.bind( this );
			this.item_restore_clicked  = this.item_restore_clicked.bind( this );
			this.update_cart           = this.update_cart.bind( this );

			$( document ).on(
				'wc_update_cart',
				function() { cart.update_cart.apply( cart, [].slice.call( arguments, 1 ) ); } );
			$( document ).on(
				'click',
				'.woocommerce-cart-form input[type=submit]',
				this.submit_click );
			$( document ).on(
				'keypress',
				'.woocommerce-cart-form input[type=number]',
				this.input_keypress );
			$( document ).on(
				'submit',
				'.woocommerce-cart-form',
				this.cart_submit );
			$( document ).on(
				'click',
				'a.woocommerce-remove-coupon',
				this.remove_coupon_clicked );
			$( document ).on(
				'click',
				'.woocommerce-cart-form .product-remove > a',
				this.item_remove_clicked );
			$( document ).on(
				'click',
				'.woocommerce-cart .restore-item',
				this.item_restore_clicked );
			$( document ).on(
				'change input',
				'.woocommerce-cart-form .cart_item :input',
				this.input_changed );

			$( '.woocommerce-cart-form input[name="update_cart"]' ).prop( 'disabled', true );
		},

		/**
		 * After an input is changed, enable the update cart button.
		 */
		input_changed: function() {
			$( '.woocommerce-cart-form input[name="update_cart"]' ).prop( 'disabled', false );
		},

		/**
		 * Update entire cart via ajax.
		 */
		update_cart: function( preserve_notices ) {
			var $form = $( '.woocommerce-cart-form' );

			block( $form );
			block( $( 'div.cart_totals' ) );

			// Make call to actual form post URL.
			$.ajax( {
				type:     $form.attr( 'method' ),
				url:      $form.attr( 'action' ),
				data:     $form.serialize(),
				dataType: 'html',
				success:  function( response ) {
					update_wc_div( response, preserve_notices );
				},
				complete: function() {
					unblock( $form );
					unblock( $( 'div.cart_totals' ) );
				}
			} );
		},

		/**
		 * Update the cart after something has changed.
		 */
		update_cart_totals: function() {
			block( $( 'div.cart_totals' ) );

			$.ajax( {
				url:      get_url( 'get_cart_totals' ),
				dataType: 'html',
				success:  function( response ) {
					update_cart_totals_div( response );
				},
				complete: function() {
					unblock( $( 'div.cart_totals' ) );
				}
			} );
		},

		/**
		 * Handle the <ENTER> key for quantity fields.
		 *
		 * @param {Object} evt The JQuery event
		 *
		 * For IE, if you hit enter on a quantity field, it makes the
		 * document.activeElement the first submit button it finds.
		 * For us, that is the Apply Coupon button. This is required
		 * to catch the event before that happens.
		 */
		input_keypress: function( evt ) {

			// Catch the enter key and don't let it submit the form.
			if ( 13 === evt.keyCode ) {
				evt.preventDefault();
				this.cart_submit( evt );
			}
		},

		/**
		 * Handle cart form submit and route to correct logic.
		 *
		 * @param {Object} evt The JQuery event
		 */
		cart_submit: function( evt ) {
			var $submit = $( document.activeElement );
			var $clicked = $( 'input[type=submit][clicked=true]' );
			var $form = $( evt.currentTarget );

			// For submit events, currentTarget is form.
			// For keypress events, currentTarget is input.
			if ( ! $form.is( 'form' ) ) {
				$form = $( evt.currentTarget ).parents( 'form' );
			}

			if ( 0 === $form.find( '.woocommerce-cart-form__contents' ).length ) {
				return;
			}

			if ( is_blocked( $form ) ) {
				return false;
			}

			if ( $clicked.is( 'input[name="update_cart"]' ) || $submit.is( 'input.qty' ) ) {
				evt.preventDefault();
				this.quantity_update( $form );

			} else if ( $clicked.is( 'input[name="apply_coupon"]' ) || $submit.is( '#coupon_code' ) ) {
				evt.preventDefault();
				this.apply_coupon( $form );
			}
		},

		/**
		 * Special handling to identify which submit button was clicked.
		 *
		 * @param {Object} evt The JQuery event
		 */
		submit_click: function( evt ) {
			$( 'input[type=submit]', $( evt.target ).parents( 'form' ) ).removeAttr( 'clicked' );
			$( evt.target ).attr( 'clicked', 'true' );
		},

		/**
		 * Apply Coupon code
		 *
		 * @param {JQuery Object} $form The cart form.
		 */
		apply_coupon: function( $form ) {
			block( $form );

			var cart = this;
			var $text_field = $( '#coupon_code' );
			var coupon_code = $text_field.val();

			var data = {
				security: wc_cart_params.apply_coupon_nonce,
				coupon_code: coupon_code
			};

			$.ajax( {
				type:     'POST',
				url:      get_url( 'apply_coupon' ),
				data:     data,
				dataType: 'html',
				success: function( response ) {
					$( '.woocommerce-error, .woocommerce-message, .woocommerce-info' ).remove();
					show_notice( response );
					$( document.body ).trigger( 'applied_coupon', [ coupon_code ] );
				},
				complete: function() {
					unblock( $form );
					$text_field.val( '' );
					cart.update_cart( true );
				}
			} );
		},

		/**
		 * Handle when a remove coupon link is clicked.
		 *
		 * @param {Object} evt The JQuery event
		 */
		remove_coupon_clicked: function( evt ) {
			evt.preventDefault();

			var cart     = this;
			var $wrapper = $( evt.currentTarget ).closest( '.cart_totals' );
			var coupon   = $( evt.currentTarget ).attr( 'data-coupon' );

			block( $wrapper );

			var data = {
				security: wc_cart_params.remove_coupon_nonce,
				coupon: coupon
			};

			$.ajax( {
				type:    'POST',
				url:      get_url( 'remove_coupon' ),
				data:     data,
				dataType: 'html',
				success: function( response ) {
					$( '.woocommerce-error, .woocommerce-message, .woocommerce-info' ).remove();
					show_notice( response );
					$( document.body ).trigger( 'removed_coupon', [ coupon ] );
					unblock( $wrapper );
				},
				complete: function() {
					cart.update_cart( true );
				}
			} );
		},

		/**
		 * Handle a cart Quantity Update
		 *
		 * @param {JQuery Object} $form The cart form.
		 */
		quantity_update: function( $form ) {
			block( $form );
			block( $( 'div.cart_totals' ) );

			// Provide the submit button value because wc-form-handler expects it.
			$( '<input />' ).attr( 'type', 'hidden' )
							.attr( 'name', 'update_cart' )
							.attr( 'value', 'Update Cart' )
							.appendTo( $form );

			// Make call to actual form post URL.
			$.ajax( {
				type:     $form.attr( 'method' ),
				url:      $form.attr( 'action' ),
				data:     $form.serialize(),
				dataType: 'html',
				success:  function( response ) {
					update_wc_div( response );
				},
				complete: function() {
					unblock( $form );
					unblock( $( 'div.cart_totals' ) );
				}
			} );
		},

		/**
		 * Handle when a remove item link is clicked.
		 *
		 * @param {Object} evt The JQuery event
		 */
		item_remove_clicked: function( evt ) {
			evt.preventDefault();

			var $a = $( evt.currentTarget );
			var $form = $a.parents( 'form' );

			block( $form );
			block( $( 'div.cart_totals' ) );

			$.ajax( {
				type:     'GET',
				url:      $a.attr( 'href' ),
				dataType: 'html',
				success: function( response ) {
					update_wc_div( response );
				},
				complete: function() {
					unblock( $form );
					unblock( $( 'div.cart_totals' ) );
				}
			} );
		},

		/**
		 * Handle when a restore item link is clicked.
		 *
		 * @param {Object} evt The JQuery event
		 */
		item_restore_clicked: function( evt ) {
			evt.preventDefault();
 
			var $a = $( evt.currentTarget );
			var $form = $( 'form.woocommerce-cart-form' );
 
			block( $form );
			block( $( 'div.cart_totals' ) );
 
			$.ajax( {
				type:     'GET',
				url:      $a.attr( 'href' ),
				dataType: 'html',
				success:  function( response ) {
					update_wc_div( response );
				},
				complete: function() {
					unblock( $form );
					unblock( $( 'div.cart_totals' ) );
				}
			} );
		}
	};

	function between( x, min, max ) {
		// just compare numbers
		return (+x) >= (+min) && (+x) <= (+max);
	}

	function get_state_by_postcode ( postcode ) {
		var states = get_brazilian_states_range();

		postcode = postcode.toString().replace(/-/g, '');

		var res = states.filter( function ( state ) {
			return between( postcode, state.min, state.max );
		} );

		log( 'Dados do estado encontrado:', res[0] ? res[0] : 'não foi encontrado nenhum estado para o CEP ' + postcode );

		return res.length > 0 ? res[0].name : '';
	}

	function get_brazilian_states_range () {
		return [
			{
				name: 'SP',
				min: '01000000',
				max: '19999999'
			},
			{
				name: 'RJ',
				min: '20000000',
				max: '28999999'
			},
			{
				name: 'ES',
				min: '29000000',
				max: '29999999'
			},
			{
				name: 'MG',
				min: '30000000',
				max: '39999999'
			},
			{
				name: 'BA',
				min: '40000000',
				max: '48999999'
			},
			{
				name: 'SE',
				min: '49000000',
				max: '49999999'
			},
			{
				name: 'PE',
				min: '50000000',
				max: '56999999'
			},
			{
				name: 'AL',
				min: '57000000',
				max: '57999999'
			},
			{
				name: 'PB',
				min: '58000000',
				max: '58999999'
			},
			{
				name: 'RN',
				min: '59000000',
				max: '59999999'
			},
			{
				name: 'CE',
				min: '60000000',
				max: '63999999'
			},
			{
				name: 'PI',
				min: '64000000',
				max: '64999999'
			},
			{
				name: 'MA',
				min: '65000000',
				max: '65999999'
			},
			{
				name: 'PA',
				min: '66000000',
				max: '68899999'
			},
			{
				name: 'AP',
				min: '68900000',
				max: '68999999'
			},
			{
				name: 'AM',
				min: '69000000',
				max: '69899999'
			},
			{
				name: 'RR',
				min: '69300000',
				max: '69389999'
			},
			{
				name: 'AC',
				min: '69900000',
				max: '69920000'
			},
			{
				name: 'DF',
				min: '70000000',
				max: '73699999'
			},
			{
				name: 'GO',
				min: '72800000',
				max: '76799999'
			},
			{
				name: 'TO',
				min: '77000000',
				max: '77995000'
			},
			{
				name: 'MT',
				min: '78000000',
				max: '78899999'
			},
			{
				name: 'RO',
				min: '78900000',
				max: '78999999'
			},
			{
				name: 'MS',
				min: '79000000',
				max: '79999999'
			},
			{
				name: 'PR',
				min: '80000000',
				max: '87999999'
			},
			{
				name: 'SC',
				min: '88000000',
				max: '89999999'
			},
			{
				name: 'RS',
				min: '90000000',
				max: '99999999'
			}
		];
	}

	cart_shipping.init( cart );
	cart.init();
} );
