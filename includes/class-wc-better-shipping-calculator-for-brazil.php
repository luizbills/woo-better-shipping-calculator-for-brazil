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

		// Load frontend JS & CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );
	} // End __construct ()

	/**
	 * Load frontend Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function enqueue_scripts () {
		if ( is_cart() ) {

			// remove the default cart.js of the WooCommerce
			wp_deregister_script( 'wc-cart' );

			// support WC 2.6.x and 3.x
			$version_suffix = version_compare( WC()->version, '3.0.0', '<') ? '-' . substr( WC()->version, 0, 3) : '';

			// add our custom cart.js
			wp_register_script( $this->_token . '-cart', esc_url( $this->assets_url ) . 'js/cart' . $version_suffix . $this->script_suffix . '.js', array( 'jquery', 'wc-country-select', 'wc-address-i18n' ), $this->_version );
			wp_enqueue_script( $this->_token . '-cart' );

			wp_localize_script( $this->_token . '-cart', 'wc_cart_params', array(
				// used by woocommerce
				'ajax_url'                     => WC()->ajax_url(),
				'wc_ajax_url'                  => WC_AJAX::get_endpoint( "%%endpoint%%" ),
				'update_shipping_method_nonce' => wp_create_nonce( "update-shipping-method" ),
				'apply_coupon_nonce'           => wp_create_nonce( "apply-coupon" ),
				'remove_coupon_nonce'          => wp_create_nonce( "remove-coupon" ),

				// used by this plugin
				'WC_VERSION' => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? WC()->version : '',
				'script_debug' => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG,
				'hide_country_field' => get_option( 'wcbuefb_hide_country_field' ),
				'hide_state_field' => get_option( 'wcbuefb_hide_state_field' )
			) );
		}
	} // End enqueue_scripts ()

	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'wc-better-user-experience-for-brazil', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
		$domain = 'wc-better-user-experience-for-brazil';

		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );

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
		$plugin_name = '<b>' . __( 'WooCommerce Better User Experience for Brazil', 'wc-better-user-experience-for-brazil' ) . '</b>';
		$woocommerce_link = '<a href="https://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a>';
		$class = 'notice notice-error';

		echo '<div class="' . $class . '"><p>' . sprintf( '%s depends on %s to work!', $plugin_name, $woocommerce_link ) . '</p></div>';
	}

	/**
	 * Main WC_Better_User_Experience_for_Brazil Instance
	 *
	 * Ensures only one instance of WC_Better_User_Experience_for_Brazil is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WC_Better_User_Experience_for_Brazil()
	 * @return Main WC_Better_User_Experience_for_Brazil instance
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