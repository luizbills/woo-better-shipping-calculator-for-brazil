<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WC_Better_Shipping_Calculator_for_Brazil_Plugin {

	/**
	 * The single instance of WC_Better_Shipping_Calculator_for_Brazil_Plugin.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The language domain.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $domain;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token = 'wc_better_shipping_calculator_for_brazil';
		$this->domain = 'wc-better-user-experience-for-brazil';

		// Load plugin environment variables
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/static/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/**
	 * `init` hook callback.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function init () {
		// Handle localisation
		$this->load_plugin_textdomain();

		if ( ! $this->has_dependecies() ) {
			add_action( 'admin_notices', array( $this, 'missing_dependencies_admin_notice' ) );
			return;
		}

		register_activation_hook( $this->file, array( $this, 'install' ) );

		// remove city field
		if ( apply_filters( $this->_token . '_hide_city', true ) ) {
			add_filter( 'woocommerce_shipping_calculator_enable_city', '__return_false' );
		}

		// Load frontend JS & CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		// show donation button
		add_action( 'admin_notices', array( $this, 'add_donation_notice' ) );
	}

	/**
	 * Load frontend Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function enqueue_scripts () {
		$condition = apply_filters( $this->_token . '_enqueue_cart_script', is_cart() );
		if ( $condition ) {

			wp_enqueue_script(
				$this->_token . '-cart',
				esc_url( $this->assets_url ) . 'js/cart' . $this->script_suffix . '.js',
				[ 'jquery', 'wc-country-select', 'wc-address-i18n' ],
				$this->_version
			);

			wp_localize_script( $this->_token . '-cart', $this->_token . '_params', [
				'script_debug' => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG,
				'hide_country_field' => apply_filters( $this->_token . '_hide_country', true ),

				'selectors' => apply_filters( $this->_token . '_field_selectors', [
					'country' => apply_filters( $this->_token . '_country_selectors', '.woocommerce-shipping-calculator #calc_shipping_country' ),
					'state' => apply_filters( $this->_token . '_state_selectors', '.woocommerce-shipping-calculator #calc_shipping_state' ),
					'postcode' => apply_filters( $this->_token . '_postcode_selectors', '.woocommerce-shipping-calculator #calc_shipping_postcode' ),
				] ),
			] );
		}
	} // End enqueue_scripts ()

	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation () {
		load_plugin_textdomain( $this->domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
		$domain =

		$locale = apply_filters( 'plugin_locale', get_locale(), $this->domain );

		load_textdomain( $this->domain, WP_LANG_DIR . '/' . $this->domain . '/' . $this->domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $this->domain, false, dirname( plugin_basename( $this->file ) ) . '/languages/' );

		add_action( 'init', array( $this, 'load_localisation' ), 0 );
	} // End load_plugin_textdomain ()


	/**
	 * Check for plugin dependencies
	 *
	 * @since   1.0.0
	 */
	private function has_dependecies () {
		return class_exists( 'WooCommerce' );
	}

	/**
	 * Shows an error if there is any missing plugin dependency
	 *
	 * @since 1.0.0
	 */
	public function missing_dependencies_admin_notice () {
		$plugin_name = '<b>' . __( 'WooCommerce Better Shipping Calculator for Brazil', 'wc-better-user-experience-for-brazil' ) . '</b>';
		$woocommerce_link = '<a href="https://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a>';
		$class = 'notice notice-error';

		echo '<div class="' . $class . '"><p>' . sprintf( '%s depends on %s to work!', $plugin_name, $woocommerce_link ) . '</p></div>';
	}

	/**
	 * Shows a notice about donations in plugins page
	 *
	 * @since 2.0.2
	 */
	public function add_donation_notice () {
		global $pagenow;
		$plugin_data = \get_plugin_data( $this->file );
		$plugin_name = $plugin_data['Name'];
		$prefix = $this->_token . '_';

		if ( ! in_array( $pagenow, [ 'plugins.php', 'update-core.php' ] ) ) return;

		if ( isset( $_GET[$prefix . 'dismiss_donation_notice'] ) ) {
			update_option(
				$prefix . 'donation_notice_dismissed',
				time()
			);
		}

		$notice_dismissed = (int) get_option( $prefix . 'donation_notice_dismissed' );
		$duration = 6 * MONTH_IN_SECONDS;
		if ( $notice_dismissed && time() <= ( $duration + $notice_dismissed ) ) {
			return;
		}

		?>
		<div id="<?= $prefix ?>donation_notice" class="notice notice-info is-dismissible">
			<p>
				<?= sprintf(
					esc_html__( 'Thanks for using the %s plugin! Consider making a donation to help keep this plugin always up to date.', 'wc-better-shipping-calculator-for-brazil' ),
					"<strong>$plugin_name</strong>"
				); ?>
			</p>
			<p>
				<a href="https://www.paypal.com/donate?hosted_button_id=29U8C2YV4BBQC&source=url" class="button button-primary">
					<?= esc_html__( 'Donate', 'wc-better-shipping-calculator-for-brazil' ); ?> 
				</a>
			</p>
		</div>
		<script>
			window.jQuery(function ($) {
				const dismiss_selector = '#<?= $prefix ?>donation_notice .notice-dismiss';
				$(document).on('click', dismiss_selector, function (evt) {
					$.ajax({
						url: window.location.origin
							+ window.location.pathname
							+ '?<?= $prefix ?>dismiss_donation_notice',
						method: 'GET'
					});
				})
			})
		</script>
		<?php
	}

	/**
	 * Main WC_Better_Shipping_Calculator_for_Brazil_Plugin Instance
	 *
	 * Ensures only one instance of WC_Better_Shipping_Calculator_for_Brazil_Plugin is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WC_Better_Shipping_Calculator_for_Brazil_Plugin()
	 * @return Main WC_Better_Shipping_Calculator_for_Brazil_Plugin instance
	 */
	public static function instance ( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {
		$this->_log_version_number();
	} // End install ()

	/**
	 * Log the plugin version number.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

}
