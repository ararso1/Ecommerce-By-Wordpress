<?php

/**
 * Payment Gateway for Order Approval.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Sg_Order_Approval_Woocommerce
 * @subpackage Sg_Order_Approval_Woocommerce/includes
 * @author     Sevengits <sevengits@gmail.com>
 */
if (!class_exists('WC_Payment_Gateway') || defined('OAWOO_DISABLED')) {
	return;
}
class Woa_Gateway extends WC_Payment_Gateway {

	/**
		 * Constructor for the gateway.
		 */
		public function __construct() {
	  
			$this->id                 = 'woa_gateway';
			$this->icon               = apply_filters('woocommerce_offline_icon', '');
			$this->has_fields         = false;
			$this->method_title       = __( 'Woocommerce Order Approval', 'order-approval-woocommerce' );
			$this->method_description = __( 'Allow store owner to approve  woocommerce order  before payment.', 'order-approval-woocommerce' );
		  
			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();
		  
			// 
			$this->title        = $this->get_option( 'title' );
			$this->description  = $this->get_option( 'description' );
			$this->instructions = $this->get_option( 'instructions');
		  
			// Actions
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
		  
			// Customer Emails
			add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
		}
	
	
		/**
		 * Initialize Gateway Settings Form Fields
		 */
		public function init_form_fields() {
	  
			$this->form_fields = apply_filters( 'wc_offline_form_fields', array(
		  
				'enabled' => array(
					'title'   => __( 'Enable/Disable', 'order-approval-woocommerce' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable Woocommerce Order Approval Payment', 'order-approval-woocommerce' ),
					'default' => 'yes'
				),
				
				'title' => array(
					'title'       => __( 'Title', 'order-approval-woocommerce' ),
					'type'        => 'text',
					'description' => __( 'This controls the title for the payment method the customer sees during checkout.', 'order-approval-woocommerce' ),
					'default'     => __( 'Pre Order', 'order-approval-woocommerce' ),
					'desc_tip'    => true,
				),
				
				'description' => array(
					'title'       => __( 'Description', 'order-approval-woocommerce' ),
					'type'        => 'textarea',
					'description' => __( 'Payment method description that the customer will see on your checkout.', 'order-approval-woocommerce' ),
					'default'     => __( 'Please remit payment after order approval.', 'order-approval-woocommerce' ),
					'desc_tip'    => true,
				),
				
				'instructions' => array(
					'title'       => __( 'Instructions', 'order-approval-woocommerce' ),
					'type'        => 'textarea',
					'description' => __( 'Instructions that will be added to the thank you page and emails.', 'order-approval-woocommerce' ),
					'default'     => '',
					'desc_tip'    => true,
				),
			) );
		}
	
	
		/**
		 * Output for the order received page.
		 */
		public function thankyou_page($order_id) {
			if ( $this->instructions ) {
				echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ));
			}
		}
	
	
		/**
		 * Add content to the WC emails.
		
		 */
		public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
		
			if ( $this->instructions && ! $sent_to_admin && $this->id === $order->get_payment_method() && $order->has_status( 'waiting' ) ) {
				echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) ). PHP_EOL;
			}
		}
	
	
		/**
		 * Process the payment and return the result
		
		 */
		public function process_payment( $order_id ) {
	
			$order = wc_get_order( $order_id );
			
			// Mark as waiting (we're awaiting the payment)
			$order->update_status( 'wc-waiting', __( 'waiting admin approval', 'order-approval-woocommerce' ) );
			
			if (get_option('sg_oa_woo_manage_inventory', 'order_received') === 'order_received') {
				# Reduce stock levels
				wc_reduce_stock_levels($order_id);
			}
			
			// Remove cart
			WC()->cart->empty_cart();
			
			// Return thankyou redirect
			return array(
				'result' 	=> 'success',
				'redirect'	=> $this->get_return_url( $order )
			);
		}

}
