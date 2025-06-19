<?php
/**
 * Email class 
 */
class Sgda_Order_Approval_WC_Email {

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
		require_once 'class-dokan-order-approval-vendor-order.php';
		$emails['SG_Vendor_PreOrder'] = new SG_Vendor_PreOrder();
		return $emails;
	}


}
new Sgda_Order_Approval_WC_Email();