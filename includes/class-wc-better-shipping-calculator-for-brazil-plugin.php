<?php

if ( ! defined( 'ABSPATH' ) ) exit;

final class WC_Better_Shipping_Calculator_for_Brazil_Plugin {

	/**
	 * The single instance of WC_Better_Shipping_Calculator_for_Brazil_Plugin.
	 * @var 	object
	 * @access  protected
	 * @since 	1.0.0
	 */
	protected static $_instance = null;

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
	 * The prefix.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_prefix;

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
	protected function __construct ( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->domain = 'wc-better-user-experience-for-brazil';
		$this->_token = 'wc_better_shipping_calculator_for_brazil';
		$this->_prefix = $this->_token. '_';

		// Load plugin environment variables
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );

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

		// remove city field
		if ( apply_filters( $this->_prefix . 'hide_city', true ) ) {
			add_filter( 'woocommerce_shipping_calculator_enable_city', '__return_false' );
		}

		// Load frontend JS & CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		// show donation button
		add_action( 'admin_notices', array( $this, 'add_donation_notice' ) );

		// add links to plugin meta
		add_filter( 'plugin_row_meta', array( $this, 'plugin_meta' ), 10, 2 );

		// add shipping calculator css
		add_action( 'woocommerce_before_shipping_calculator', array( $this, 'add_css' ) );
	}

	/**
	 * Load frontend Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function enqueue_scripts () {
		$condition = apply_filters( $this->_prefix . 'enqueue_cart_script', is_cart() );
		if ( $condition ) {
			wp_enqueue_script(
				$this->_token . '-cart',
				esc_url( $this->assets_url ) . 'js/cart' . $this->script_suffix . '.js',
				[ 'jquery', 'wc-country-select', 'wc-address-i18n', 'wc-cart' ],
				$this->_version
			);

			wp_localize_script( $this->_token . '-cart', $this->_prefix . 'params', $this->get_cart_script_data() );
		}
	} // End enqueue_scripts ()

	/**
	 * Load plugin textdomain
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
		load_plugin_textdomain( $this->domain, false, dirname( plugin_basename( $this->file ) ) . '/languages' );
	} // End load_plugin_textdomain ()

	/**
	 * Shows an error if there is any missing plugin dependency
	 * @access  public
	 * @since   1.0.0
	 */
	public function missing_dependencies_admin_notice () {
		$plugin_data = \get_plugin_data( $this->file );
		$woocommerce_link = '<a href="https://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a>';
		$class = 'notice notice-error';

		echo '<div class="' . esc_attr( $class ) . '"><p>' . sprintf( esc_html__( '%s depends on %s to work!', 'wc-better-shipping-calculator-for-brazil' ), '<b>' . esc_html( $plugin_data['Name'] ) . '</b>', $woocommerce_link ) . '</p></div>';
	}

	/**
	 * Shows a notice about donations in plugins page
	 * @access  public
	 * @since   2.0.2
	 */
	public function add_donation_notice () {
		global $pagenow;
		if ( ! in_array( $pagenow, [ 'plugins.php', 'update-core.php' ] ) ) return;

		$prefix = $this->_prefix . '';
		if ( isset( $_GET[ $prefix . 'dismiss_donation_notice' ] ) ) {
			update_option(
				$prefix . 'donation_notice_dismissed',
				time()
			);
		}

		$notice_dismissed = (int) get_option( $prefix . 'donation_notice_dismissed' );
		$duration = 4 * MONTH_IN_SECONDS;
		if ( $notice_dismissed && time() <= ( $duration + $notice_dismissed ) ) {
			return;
		}

		$plugin_data = \get_plugin_data( $this->file );
		$plugin_name = esc_html( $plugin_data['Name'] );
		$donation_url = $this->get_donation_url();

		?>
		<div id="<?php echo $prefix ?>donation_notice" class="notice notice-info is-dismissible">
			<p>
				<?php echo sprintf(
					esc_html__( 'Thanks for using the %s plugin! Consider making a donation to help keep this plugin always up to date.', 'wc-better-shipping-calculator-for-brazil' ),
					"<strong>$plugin_name</strong>"
				); ?>
			</p>
			<p>
				<a href="<?php echo esc_url( $donation_url ); ?>" class="button button-primary">
					<?php echo esc_html__( 'Donate', 'wc-better-shipping-calculator-for-brazil' ); ?>
				</a>
			</p>
		</div>
		<script>
			window.jQuery(function ($) {
				const dismiss_selector = '#<?php echo $prefix ?>donation_notice .notice-dismiss';
				$(document).on('click', dismiss_selector, function (evt) {
					$.ajax({
						url: window.location.origin
							+ window.location.pathname
							+ '?<?php echo $prefix ?>dismiss_donation_notice',
						method: 'GET'
					});
				})
			})
		</script>
		<?php
	}

	/**
	 * Add more links to plugin meta.
	 * @access  public
	 * @since   2.1.0
	 */
	public function plugin_meta ( $plugin_meta, $plugin_file ) {
		if ( plugin_basename( $this->file ) === $plugin_file ) {
			$donation_url = esc_url( $this->get_donation_url() );
			$forum_url = 'https://wordpress.org/support/plugin/woo-better-shipping-calculator-for-brazil/';

			$plugin_meta[] = "<a href=\"$forum_url\" target='blank' rel='noopener'>" . esc_html__( 'Community support', 'wc-better-shipping-calculator-for-brazil' ) . "</a>";
			$plugin_meta[] = "<a href=\"$donation_url\" target='blank' rel='noopener' style='color:#087f5b;font-weight:700;'>" . esc_html__( 'Donate', 'wc-better-shipping-calculator-for-brazil' ) . "</a>";
		}
		return $plugin_meta;
	}

	/**
	 * Add CSS to always show the shipping calculator
	 * @access  public
	 * @since   2.1.0
	 */
	public function add_css () {
		if ( is_cart() ) {
			$data = $this->get_cart_script_data();
			$postcode_label = apply_filters( $this->_prefix . 'postcode_label', 'Calcule o frete:' );
			?>
			<style>
				<?php if ( $data['hide_country_field'] ) : ?>
				#calc_shipping_country_field,
				#calc_shipping_state_field,
				<?php endif ?>
				.shipping-calculator-button {
					display: none!important
				}

				.shipping-calculator-form {
					padding-top: 0!important;
					display: block!important
				}

				<?php if ( $postcode_label ) : ?>
				#calc_shipping_postcode_field::before {
					content: "<?php echo esc_html( $postcode_label ); ?>";
					font-weight: bold;
				}
				<?php endif ?>
			</style>
			<?php
		}
	}

	/**
	 * Check for plugin dependencies
	 * @access  protected
	 * @since   1.0.0
	 */
	protected function has_dependecies () {
		return class_exists( 'WooCommerce' );
	}

	/**
	 * Return the URL for donations
	 * @access  protected
	 * @since   2.1.0
	 * @return  string
	 */
	protected function get_donation_url () {
		return 'https://luizpb.com/donate/';
	}

	/**
	 * Return data used in assets/js/cart.js
	 * @access  protected
	 * @since   2.1.0
	 * @return  array
	 */
	protected function get_cart_script_data () {
		return [
			'script_debug' => defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG,
			'add_postcode_mask' => apply_filters( $this->_prefix . 'add_postcode_mask', true ),
			'hide_country_field' => apply_filters( $this->_prefix . 'hide_country', true ),
			'clear_city' => apply_filters( $this->_prefix . 'clear_city_field', true ),

			'selectors' => apply_filters( $this->_prefix . 'field_selectors', [
				'calculator' => apply_filters( $this->_prefix . 'calculator_selectors', '.woocommerce-shipping-calculator' ),
				'country' => apply_filters( $this->_prefix . 'country_selectors', '.woocommerce-shipping-calculator #calc_shipping_country' ),
				'state' => apply_filters( $this->_prefix . 'state_selectors', '.woocommerce-shipping-calculator #calc_shipping_state' ),
				'city' => apply_filters( $this->_prefix . 'city_selectors', '.woocommerce-shipping-calculator #calc_shipping_city' ),
				'postcode' => apply_filters( $this->_prefix . 'postcode_selectors', '.woocommerce-shipping-calculator #calc_shipping_postcode' ),
			] ),
		];
	}

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {
		$this->log_version_number();
	} // End install ()

	/**
	 * Save the plugin version number.
	 *
	 * @access  protected
	 * @since   1.0.0
	 * @return  void
	 */
	protected function log_version_number () {
		update_option( $this->_prefix . 'version', $this->_version );
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
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __wakeup ()
}
