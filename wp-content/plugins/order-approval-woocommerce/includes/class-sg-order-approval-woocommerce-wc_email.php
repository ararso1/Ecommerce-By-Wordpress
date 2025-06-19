<?php
/**
 * Email class 
 */
class Sg_Order_Approval_WC_Email {

	/**
	 * Custom_WC_Email constructor.
	 */
	public function __construct() {
    // Filtering the emails and adding our own email.
		add_filter( 'woocommerce_email_classes', array( $this, 'register_email' ), 90, 1 );
    
	}

	/**
	 * @param array $emails
	 *
	 * @return array
	 */
	public function register_email( $emails ) {
		
		require_once 'class-sg-order-approval-woocommerce-wc-customer-order.php';
		require_once 'class-sg-order-approval-woocommerce-wc-admin-order.php';
		require_once 'class-sg-order-approval-woocommerce-wc-customer-order-approved.php';
		require_once 'class-sg-order-approval-woocommerce-wc-customer-order-rejected.php';
		$emails['WC_Customer_Order_New'] = new WC_Customer_Order_New();
		$emails['WC_Admin_Order_New'] = new WC_Admin_Order_New();
		$emails['WC_Customer_Order_Approved'] = new WC_Customer_Order_Approved();
		$emails['WC_Customer_Order_Rejected'] = new WC_Customer_Order_Rejected();

		return $emails;
	}


}
new Sg_Order_Approval_WC_Email();