<?php
/*
 * @Author : leehld
 * @Date   : 2/13/2017
 * @Last Modified by: leehld
 * @Last Modified time: 2/13/2017
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPEMS_Booking' ) ) {
	return;
}

class WPEMS_WC_Checkout extends WPEMS_Booking {

	function __construct( $id = null ) {
		parent::__construct( $id );

		/**
		 * woo add new order hook
		 */
		// add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'woo_add_order_classic' ) );
		add_action( 'woocommerce_store_api_checkout_order_processed', array( $this, 'woo_add_order_store_api' ) );
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'woo_add_order_classic' ), 10, 4 );
	}
	/**
	 * Use for classic checkout page
	 * @param  integer $order_id Woocommerce order id
	 */
	public function woo_add_order_classic( $order_id ) {
		$this->woo_add_order( $order_id );
	}
	/**
	 * Use for blocks checkout page
	 * @param  WC_Order $order woocommerce order
	 */
	public function woo_add_order_store_api( $order ) {
		$order_id = $order->get_id();
		$this->woo_add_order( $order_id );
	}
	/**
	 * woo_add_order WooCoommerce hook create new order
	 *
	 * @param  integer $order_id Woocommerce order id
	 */
	public function woo_add_order( $order_id ) {

		$cart_contents = wc()->cart->cart_contents;

		$create = false;
		$args   = array();
		foreach ( $cart_contents as $cart_key => $cart_content ) {
			if ( get_post_type( $cart_content['product_id'] ) === 'tp_event' ) {
				$create = true;
				$args   = array(
					'event_id'   => $cart_content['product_id'],
					'qty'        => $cart_content['quantity'],
					'price'      => $cart_content['line_total'],
					'payment_id' => 'woo_payment',
				);
				$booking = $this->create_booking( $args, 'woo_payment' );
				if ( $booking ) {
					update_post_meta( $booking, '_tp_event_woo_order', $order_id );
					$wc_order = wc_get_order( $order_id );
					$wc_order->add_meta_data( '_tp_event_event_order', $booking );
					$wc_order->save_meta_data();
					// deprecated because add_post_meta is old method
					// add_post_meta( $order_id, '_tp_event_event_order', $booking );
				}
				continue;
			}
		}
	}
}

new WPEMS_WC_Checkout();
