<?php

/**
 * Plugin Name:          Order Approval for Woocommerce
 * Plugin URI:           https://sevengits.com/plugin/order-approval-woocommerce-pro/
 * Description:          WooCommerce Order Approval plugin allowing shop owners to approve or reject all the orders placed by customers before payment processed.
 * Version:              2.1.12
 * Author:               Sevengits
 * Author URI:           https://sevengits.com/
 * Requires Plugins: woocommerce
 * License:              GPL-2.0+
 * License URI:          http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:          order-approval-woocommerce
 * Domain Path:          /languages
 * Requires at least: 3.7
 * WC Tested up to:      9.4
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

if (!function_exists('get_plugin_data')) {
	require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
if (!defined('SG_ORDER_APPROVAL_WOOCOMMERCE_VERSION')) {
	define('SG_ORDER_APPROVAL_WOOCOMMERCE_VERSION','2.1.12');
}
if (!defined('SG_BASE_ORDER')) {
	define('SG_BASE_ORDER', plugin_basename(__FILE__));
}
if (!defined('SG_PLUGIN_PATH_ORDER')) {
	define('SG_PLUGIN_PATH_ORDER', plugin_dir_path(__FILE__));
}

if (!defined('WOA_DIR_PATH')) {
	define('WOA_DIR_PATH', plugin_dir_path(__FILE__));
}



/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sg-order-approval-woocommerce-activator.php
 */
function oawoo_activate()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-sg-order-approval-woocommerce-activator.php';
	Sg_Order_Approval_Woocommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sg-order-approval-woocommerce-deactivator.php
 */
function oawoo_deactivate()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-sg-order-approval-woocommerce-deactivator.php';
	Sg_Order_Approval_Woocommerce_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'oawoo_activate');
register_deactivation_hook(__FILE__, 'oawoo_deactivate');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-sg-order-approval-woocommerce.php';
/**
 * Plugin Deactivation Survey
 */
require plugin_dir_path(__FILE__) . 'plugin-deactivation-survey/deactivate-feedback-form.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function oawoo_run()
{
	if (defined('OAWOO_DISABLED')) {
		return;
	}

	$plugin = new Sg_Order_Approval_Woocommerce();
	$plugin->run();
}
oawoo_run();

add_filter('sgits_deactivate_feedback_form_plugins', function ($plugins) {
	$plugins[] = (object)array(
		'slug'		=> 'order-approval-woocommerce',
		'version'	=> SG_ORDER_APPROVAL_WOOCOMMERCE_VERSION
	);
	return $plugins;
});

// Hook the custom function to the 'before_woocommerce_init' action
add_action('before_woocommerce_init', 'oawoo_declare_cart_checkout_blocks_compatibility');

/**
 * Custom function to declare compatibility with cart_checkout_blocks feature
 * @since 2.1.0
 */
function oawoo_declare_cart_checkout_blocks_compatibility()
{
	// Check if the required class exists
	if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);
	}
}


// Hook the custom function to the 'woocommerce_blocks_loaded' action
add_action('woocommerce_blocks_loaded', 'oawoo_register_order_approval_payment_method_type');

/**
 * Custom function to register a payment method type
 * @since 2.1.0
 */
function oawoo_register_order_approval_payment_method_type()
{
	// Check if the required class exists
	if (!class_exists('Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
		return;
	}

	// Include the custom Blocks Checkout class
	require_once plugin_dir_path(__FILE__) . 'includes/class-sg-order-approval-wocommerce-block-checkout.php';

	// Hook the registration function to the 'woocommerce_blocks_payment_method_type_registration' action
	add_action(
		'woocommerce_blocks_payment_method_type_registration',
		function (Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry) {
			// Register an instance of Woa_Order_Blocks
			$payment_method_registry->register(new Woa_Order_Blocks());
		}
	);
}

add_action('before_woocommerce_init', function () {
	if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
	}
});

?>