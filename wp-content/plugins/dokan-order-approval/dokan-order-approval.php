<?php

/**
 *
 * @link              https://sevengits.com/
 * @since             1.0.0
 * @package           Sg_Order_Approval_Woocommerce_Pro
 *
 * @wordpress-plugin
 * Plugin Name:       Dokan Order Approval	
 * Plugin URI:        https://sevengits.com/plugins/dokan-order-approval-pro/
 * Description:       Dokan Order Approval plugin allowing vendors to approve or reject all the orders placed by customers before payment processed.
 * Version:           2.0.6
 * Author:            Sevengits
 * Author URI:        https://sevengits.com/
 * WC requires at least: 3.7
 * WC tested up to: 	9.4
 * Requires Plugins: woocommerce,order-approval-woocommerce
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       dokan-order-approval
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

if (!function_exists('get_plugin_data')) {
	require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

if (!defined('SGDOA_VERSION')) {
	define('SGDOA_VERSION', '2.0.6');
}

if (!defined('SGDOA_BASE')) {
	define('SGDOA_BASE', plugin_basename(__FILE__));
}

if (!defined('SGDOA_PLUGIN_PATH')) {
	define('SGDOA_PLUGIN_PATH', plugin_dir_path(__FILE__));
}


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sg-dokan-order-approval-activator.php
 */
if (!function_exists('sgdoa_activate')) {
	function sgdoa_activate()
	{
		require_once SGDOA_PLUGIN_PATH . 'includes/class-sg-dokan-order-approval-activator.php';
		Sg_Dokan_Order_Approval_Activator::activate();
	}
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sg-dokan-order-approval-deactivator.php
 */
if (!function_exists('sgdoa_deactivate')) {
	function sgdoa_deactivate()
	{
		require_once SGDOA_PLUGIN_PATH . 'includes/class-sg-dokan-order-approval-deactivator.php';
		Sg_Dokan_Order_Approval_Deactivator::deactivate();
	}
}

register_activation_hook(__FILE__, 'sgdoa_activate');
register_deactivation_hook(__FILE__, 'sgdoa_deactivate');

require SGDOA_PLUGIN_PATH . 'plugin-deactivation-survey/deactivate-feedback-form.php';
add_filter('sgdoa_deactivate_feedback_form_plugins', 'sgitsda_deactivate_feedback');
function sgitsda_deactivate_feedback($plugins)
{
	$plugins[] = (object)array(
		'slug'		=> 'dokan-order-approval',
		'version'	=> SGDOA_VERSION
	);
	return $plugins;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require SGDOA_PLUGIN_PATH . 'includes/class-sg-dokan-order-approval.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

function sgdoa_run()
{

	$plugin = new Sg_Dokan_Order_Approval();
	$plugin->run();
}

sgdoa_run();

// cart checkout compatiblity
add_action('before_woocommerce_init', 'oadwoo_declare_cart_checkout_blocks_compatibility');
function oadwoo_declare_cart_checkout_blocks_compatibility()
{
	// Check if the required class exists
	if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);
	}
}

// Declaring extension HPOS compatibility
add_action('before_woocommerce_init', function () {
	if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
	}
});

?>