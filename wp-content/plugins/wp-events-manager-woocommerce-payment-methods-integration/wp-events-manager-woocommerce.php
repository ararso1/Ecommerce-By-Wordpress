<?php
/**
 * Plugin Name: WP Events Manager - WooCommerce Payment Methods Integration
 * Description: Support paying for a booking with the payment methods provided by Woocommerce
 * Author: ThimPress
 * Version: 2.0.7.3
 * Author URI: http://thimpress.com/
 * Requires at least: 6.3
 * WC tested up to: 8.4
 * Text Domain: wp-events-manager-woo
 * Domain Path: /languages/
 * Require_WPEMS_Version: 2.0
 */
use Automattic\WooCommerce\Utilities\FeaturesUtil;

defined( 'ABSPATH' ) || exit;

define( 'WPEMS_BASENAME', plugin_basename( __FILE__ ) );

/*
 * Class WPEMS_Woo
 */
class WPEMS_Woo {

	/**
	 * Hold the instance of WPEMS_Woo
	 *
	 * @var null
	 */
	protected static $_instance = null;

	/**
	 * Check woo payment activated
	 *
	 * @var bool
	 */
	protected static $_wc_loaded = false;

	/**
	 * Notice for error
	 *
	 * @var
	 */
	protected static $_notice;

	/**
	 * Addon info
	 *
	 * @var array
	 */
	public static $addon_info = array();

	/**
	 * WPEMS_Woo constructor
	 */
	private function __construct() {
		$can_load = true;

		// Set version addon for WPEMS check .
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		self::$addon_info = get_file_data(
			__FILE__,
			array(
				'Name'                  => 'Plugin Name',
				'Require_WPEMS_Version' => 'Require_WPEMS_Version',
				'Version'               => 'Version',
			)
		);

		define( 'WPEMS_WOO_VER', self::$addon_info['Version'] );
		define( 'WPEMS_WOO_REQUIRE_VER', self::$addon_info['Require_WPEMS_Version'] );
		define( 'WPEMS_WOO_BASENAME', plugin_basename( __FILE__ ) );

		// Check WPEMS activated .
		if ( ! is_plugin_active( 'wp-events-manager/wp-events-manager.php' ) ) {
			$can_load = false;
		}
		/*elseif ( version_compare( WPEMS_WOO_REQUIRE_VER, get_option( 'wpems_version', '2.0' ), '>=' ) ) {
			$can_load = false;
		}*/

		if ( ! $can_load ) {
			add_action( 'admin_notices', array( $this, 'show_note_errors_require_wpems' ) );
			deactivate_plugins( WPEMS_WOO_BASENAME );

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}

			return;
		}

		// Check Woo activated .
		if ( ! $this->check_woo_activated() ) {
			return;
		}

		// define constants
		$this->define_constants();

		$this->init_hook();
	}

	public function init_hook() {
		add_action(
			'init',
			function () {
				$this->load_text_domain();

				require_once WPEMS_WOO_INC . '/class-wpems-wc-settings.php';
				require_once WPEMS_WOO_INC . '/class-wpems-wc-product.php';
				require_once WPEMS_WOO_INC . '/class-wpems-wc-checkout.php';
				require_once WPEMS_WOO_INC . '/class-wpems-wc-payment.php';
				require_once WPEMS_WOO_INC . '/class-wpems-wc-product-order-item.php';
			}
		);
		// add event as woocommerce product
		add_filter( 'woocommerce_product_class', array( $this, 'event_product_class' ), 10, 4 );
		// add event product to cart
		add_action( 'tp_event_register_event_action', array( $this, 'add_event_to_woo_cart' ), 1 );
		// update booking event status
		add_action( 'woocommerce_order_status_changed', array( $this, 'woocommerce_order_status_changed' ), 10, 3 );

		add_action( 'woocommerce_thankyou', array( $this, 'woocommerce_order_status_changed' ), 10, 1 );

		add_filter( 'woocommerce_get_order_item_classname', array( $this, 'get_classname_wpems_wc_order' ), 10, 3 );

		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'order_item_line' ), 10, 4 );

		if ( get_option( 'thimpress_events_woo_payment_enable' ) == 'yes' ) {

			// disable paypal when activate woo
			add_filter( 'tp_event_enable_paypal_payment', array( $this, 'disable_paypal_checkout' ), 10, 1 );

			add_filter( 'tp_event_get_currency', array( $this, 'wpemswc_get_currency' ), 50 );
		}

		add_filter( 'thimpress_event_l18n', array( $this, 'wpems_woo_l18n' ), 1 );

		add_filter( 'woocommerce_cart_item_class', array( $this, 'woo_cart_item_cart' ), 10, 3 );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action(
			'before_woocommerce_init',
			function () {
				if ( class_exists( FeaturesUtil::class ) ) {
					FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__ );
				}
			}
		);
	}

	/**
	 * Add css class to event product in woocomemrce cart page
	 *
	 * @param $class
	 * @param $cart_item
	 * @param $cart_item_key
	 *
	 * @return string
	 */
	public function woo_cart_item_cart( $class, $cart_item, $cart_item_key ) {

		$class = array( $class );

		$product_id = $cart_item['product_id'];

		if ( $product_id && get_post_type( $product_id ) == 'tp_event' ) {
			$class[] = 'wpems-event-product';
		}

		return implode( ' ', $class );
	}

	/**
	 * Site scripts
	 */
	public function enqueue_scripts() {
		if ( is_cart() ) {
			wp_enqueue_style( 'wpems-woo-style', WPEMS_WOO_ASSETS_URI . 'css/site.css', array(), WPEMS_WOO_VER );
		}
	}

	// filter woo currency
	public function wpemswc_get_currency( $currency ) {
		return get_woocommerce_currency();
	}

	/**
	 * Disable paypal checkout
	 *
	 * @param $enable
	 *
	 * @return bool
	 */
	public function disable_paypal_checkout( $enable ) {
		return false;
	}

	/**
	 * Update l18n
	 *
	 * @param $args
	 *
	 * @return array
	 */
	public function wpems_woo_l18n( $args ) {
		$l18n = array(
			'add_to_cart'  => __( ' has been added to your cart.', 'wp-events-manager-woo' ),
			'woo_cart_url' => sprintf( '<a href="%s" class="button wc-forward">%s</a>', esc_url( wc_get_page_permalink( 'cart' ) ), esc_html__( 'View Cart', 'wp-events-manager-woo' ) ),
		);

		return array_merge( $args, $l18n );
	}

	/**
	 * Add event to woo cart
	 *
	 * @param $args
	 *
	 * @throws Exception
	 */
	public function add_event_to_woo_cart( $args ) {
		WC()->cart->add_to_cart( $args['event_id'], $args['qty'] );
	}

	/**
	 * Add event product class to woocommerce
	 *
	 * @param $classname
	 * @param $product_type
	 * @param $post_type
	 * @param $product_id
	 *
	 * @return string
	 */
	public function event_product_class( $classname, $product_type, $post_type, $product_id ) {
		if ( get_post_type( $product_id ) == 'tp_event' ) {
			$classname = 'WPEMS_WC_Product';
		}

		return $classname;
	}

	/**
	 * Change event booking status when change woocommerce order status
	 *
	 * @param $order_id
	 * @param $old_status
	 * @param $new_status
	 *
	 * @throws Exception
	 */
	public function woocommerce_order_status_changed( $order_id ) {
		$wc_order = wc_get_order( $order_id );
		// $event_booking_id = get_post_meta( $order_id, '_tp_event_event_order' );
		if ( ! $wc_order ) {
			return;
		}
		$new_status = $wc_order->get_status();
		if ( ! in_array( $new_status, array( 'completed', 'pending', 'processing', 'cancelled' ) ) ) {
			$new_status = 'pending';
		}
		$wc_order_meta_items = $wc_order->get_meta( '_tp_event_event_order', false );
		if ( ! empty( $wc_order_meta_items ) ) {
			foreach ( $wc_order_meta_items as $item ) {
				$booking_data = $item->get_data();
				if ( ! empty( $booking_data['value'] ) ) {
					WPEMS_Booking::instance( (int) $booking_data['value'] )->update_status( 'ea-' . $new_status );
				}
			}
		}
	}

	/**
	 * Define Plugins Constants
	 */
	public function define_constants() {
		define( 'WPEMS_WOO_PATH', plugin_dir_path( __FILE__ ) );
		define( 'WPEMS_WOO_URI', plugin_dir_url( __FILE__ ) );
		define( 'WPEMS_WOO_INC', WPEMS_WOO_PATH . 'inc/' );
		define( 'WPEMS_WOO_ASSETS_URI', WPEMS_WOO_URI . 'assets/' );
	}

	/**
	 * Load text domain
	 */
	public function load_text_domain() {
		// Get mo file
		$text_domain = 'wp-events-manager-woo';
		$locale      = apply_filters( 'plugin_locale', get_locale(), $text_domain );
		$mo_file     = $text_domain . '-' . $locale . '.mo';
		// Check mo file global
		$mo_global = WP_LANG_DIR . '/plugins/' . $mo_file;
		// Load translate file
		if ( file_exists( $mo_global ) ) {
			load_textdomain( $text_domain, $mo_global );
		} else {
			load_textdomain( $text_domain, WPEMS_WOO_PATH . '/languages/' . $mo_file );
		}
	}

	/**
	 * Get classname WC_Order_Item_LP_Course
	 *
	 * @throws Exception
	 */
	public function get_classname_wpems_wc_order( $classname, $item_type, $id ) {
		if ( in_array( $item_type, array( 'line_item', 'product' ) ) ) {
			$event_id = wc_get_order_item_meta( $id, '_tp_event_id' );
			if ( $event_id && 'tp_event' === get_post_type( $event_id ) ) {
				$classname = 'WC_Order_Item_WPEMS_Product';
			}
		}

		return $classname;
	}

	/**
	 * Add item line meta data contains our course_id from product_id in cart.
	 * Since WC 3.x order item line product_id always is 0 if it is not a REAL product.
	 * Need to track course_id for creating LP order in WC hook after this action.
	 *
	 * @param $item
	 * @param $cart_item_key
	 * @param $values
	 * @param $order
	 */
	public function order_item_line( $item, $cart_item_key, $values, $order ) {
		if ( 'tp_event' === get_post_type( $values['product_id'] ) ) {
			$item->add_meta_data( '_tp_event_id', $values['product_id'], true );
		}
	}

	/**
	 * WPEMS_Woo instance
	 *
	 * @return null|WPEMS_Woo
	 */
	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Check plugin Woo activated.
	 */
	public function check_woo_activated(): bool {
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			add_action( 'admin_notices', array( $this, 'show_note_errors_install_plugin_woo' ) );

			deactivate_plugins( WPEMS_BASENAME );

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}

			return false;
		}

		return true;
	}

	/**
	 * Check activated plugin WP Events Manager
	 *
	 * @return void
	 */
	public function show_note_errors_require_wpems() {
		?>
		<div class="notice notice-error">
			<p>
				<?php
				printf(
					esc_html__( 'Please active %1$s before active %2$s', 'wp-events-manager-woo' ),
					'<strong>LP version ' . WPEMS_WOO_REQUIRE_VER . ' or later</strong>',
					'<strong>' . self::$addon_info['Name'] . '</strong>'
				);
				?>
		</div>
		<?php
	}

	/**
	 * Check activated plugin WooCommerce
	 *
	 * @since 2.0.7
	 * @version 1.0.0
	 * @return void
	 */
	public function show_note_errors_install_plugin_woo() {
		?>
		<div class="notice notice-error">
			<p>
				<?php
				printf(
					esc_html__( 'Please active plugin %1$s before active plugin %2$s', 'wp-events-manager-woo' ),
					sprintf(
						'<strong><a href="%s" target="_blank">%s</a></strong>',
						admin_url( 'plugin-install.php?tab=plugin-information&plugin=woocommerce' ),
						'WooCommerce'
					),
					'<strong>LearnPress - Woo payment</strong>'
				);
				?>
			</p>
		</div>
		<?php
	}
}

WPEMS_Woo::instance();
