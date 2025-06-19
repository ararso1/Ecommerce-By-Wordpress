<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://sevengits.com/
 * @since      1.0.0
 *
 * @package    Sg_Order_Approval_Woocommerce
 * @subpackage Sg_Order_Approval_Woocommerce/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Sg_Order_Approval_Woocommerce
 * @subpackage Sg_Order_Approval_Woocommerce/includes
 * @author     Sevengits <sevengits@gmail.com>
 */
class Sg_Order_Approval_Woocommerce_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'order-approval-woocommerce',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
