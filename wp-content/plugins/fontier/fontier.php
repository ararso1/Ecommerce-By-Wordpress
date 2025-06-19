<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://teconce.com/about/
 * @since             1.0.4
 * @package           Fontier
 *
 * @wordpress-plugin
 * Plugin Name:       Fontier
 * Plugin URI:        https://teconce.com
 * Description:       Fontier is a auto font preview genration plugin for EDD & Woocommerce
 * Version:           1.4
 * Author:            Teconce
 * Author URI:        https://teconce.com/about/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fontier
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'FONTIER_VERSION', '1.4' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-fontier-activator.php
 */
function activate_fontier() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fontier-activator.php';
	Fontier_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-fontier-deactivator.php
 */
function deactivate_fontier() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fontier-deactivator.php';
	Fontier_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_fontier' );
register_deactivation_hook( __FILE__, 'deactivate_fontier' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-fontier.php';
require plugin_dir_path( __FILE__ ) . 'admin/vendors/codestar-framework/codestar-framework.php';
require plugin_dir_path( __FILE__ ) . 'public/class-fontier-fes.php';
require plugin_dir_path( __FILE__ ) . 'public/class-fontier-dokan.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_fontier() {

	$plugin = new Fontier();
	$plugin->run();

}
run_fontier();
