<?php
/**
 * Class  WC_Customer_Order_Approved
 */
class WC_Customer_Order_Approved extends WC_Email {

	/**
	 * Create an instance of the class.
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
    // Email slug we can use to filter other data.
		$this->id          = 'wc_customer_order_approved';
		$this->title       = __( 'Order approved notification', 'order-approval-woocommerce' );
		$this->description = __( 'An email sent to the customer when an order is approved.', 'order-approval-woocommerce' );
    // For admin area to let the user know we are sending this email to customers.
		$this->customer_email = true;
		$this->heading     = __( 'Your order is approved.', 'order-approval-woocommerce' );
		// translators: placeholder is {blogname}, a variable that will be substituted when email is sent out
		$this->subject     = sprintf( _x( '[%s] Order  Approved. ', 'default email subject for new emails sent to the customer', 'order-approval-woocommerce' ), '{blogname}' );
    
    // Template paths.
		$this->template_html  = 'emails/wc-customer-order-approved.php';
		$this->template_plain = 'emails/plain/wc-customer-order-approved.php';
		
		if(file_exists(get_stylesheet_directory().'/woocommerce/'.$this->template_html) ){
			$this->template_base  = get_stylesheet_directory().'/woocommerce/';
		}elseif(file_exists(get_stylesheet_directory().'/woocommerce/'.$this->template_plain)){
			$this->template_base  = get_stylesheet_directory().'/woocommerce/';
		}
		else{
			$this->template_base  = SG_PLUGIN_PATH_ORDER . 'templates/';
		} 
   

		parent::__construct();
	}
	/**
	 * Determine if the email should actually be sent and setup email merge variables
	 *
	 * @since 1.0.0
	 * @param int $order_id
	 */
	public function trigger( $order_id ) {

		// bail if no order ID is present
		if ( ! $order_id )
			return;
		
		// setup order object
		$this->object = new WC_Order( $order_id );


		// replace variables in the subject/headings
		$this->find[] = '{order_date}';
		$this->replace[] = date_i18n( wc_date_format(), strtotime( $this->object->get_date_created() ) );

		$this->find[] = '{order_number}';
		$this->replace[] = $this->object->get_order_number();
		
		if ( version_compare( '3.0.0', WC()->version, '>' ) ) {
			$order_email = $this->object->billing_email;
		} else {
			$order_email = $this->object->get_billing_email();
		}

		$this->recipient = $order_email;


		if ( ! $this->is_enabled() || ! $this->get_recipient() )
			return;

		// woohoo, send the email!
		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

		
	}
	/**
	 * get_content_html function.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_content_html() {
		ob_start();
		wc_get_template($this->template_html, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
			'email'			=> $this,
			'additional_content' => $this->get_additional_content(),
			'sent_to_admin' => false,
			'plain_text'    => false,
		) ,$this->template_base,$this->template_base);
		return ob_get_clean();
	}


	/**
	 * get_content_plain function.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();
		wc_get_template( $this->template_plain, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
			'email'			=> $this,
			'sent_to_admin' => false,
			'plain_text'    => true,
		),$this->template_base,$this->template_base );
		return ob_get_clean();
	}
}
?>