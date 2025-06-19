<?php
/**
 * Class WC_Admin_Order_New
 */
class WC_Admin_Order_New  extends WC_Email {

	/**
	 * Create an instance of the class.
	 *
	 * @access public
	 * @return void
	 */
	function __construct() { 
    // Email slug we can use to filter other data.
		$this->id          = 'wc_admin_order_new';
		$this->title       = __( 'Admin Pre order notification ', 'order-approval-woocommerce' );
		$this->description = __( 'An email sent to the admin when an order is created.', 'order-approval-woocommerce' );
    // default the email recipient to the admin's email address

        $this->recipient = $this->get_option( 'recipient' );

        // if none was entered, just use the WP admin email as a fallback
        if ( ! $this->recipient )
            $this->recipient = get_option( 'admin_email' );
		$this->heading     = __( 'New Order ', 'order-approval-woocommerce' );
		// translators: placeholder is {blogname}, a variable that will be substituted when email is sent out
		$this->subject     = sprintf( _x( '[%1$s] : New Order #[%2$s]', 'default email subject for new emails sent to admin', 'order-approval-woocommerce' ), '{blogname}','{order_number}' );
    
    // Template paths.
		$this->template_html  = 'emails/wc-admin-order-new.php';
		$this->template_plain = 'emails/plain/wc-admin-order-new.php';
		
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
			'sent_to_admin' => true,
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
			'sent_to_admin' => true,
			'plain_text'    => true,
		),$this->template_base,$this->template_base);
		return ob_get_clean();
	}
/**
     * Initialize Settings Form Fields
     *
     * @since 2.0.1
     */
    public function init_form_fields() {

        $this->form_fields = array(
            'enabled'    => array(
                'title'   => 'Enable/Disable',
                'type'    => 'checkbox',
                'label'   => 'Enable this email notification',
                'default' => 'yes'
            ),
            'recipient'  => array(
                'title'       => 'Recipient(s)',
                'type'        => 'text',
                'description' => sprintf( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', esc_attr( get_option( 'admin_email' ) ) ),
                'placeholder' => '',
                'default'     => ''
            ),
            'subject'    => array(
                'title'       => 'Subject',
                'type'        => 'text',
                'description' => sprintf( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', $this->subject ),
                'placeholder' => '',
                'default'     => ''
            ),
            'heading'    => array(
                'title'       => 'Email Heading',
                'type'        => 'text',
                'description' => sprintf( /* translators: %s is the default email heading. */
					__( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.','order-approval-woocommerce' ), $this->heading ),
                'placeholder' => '',
                'default'     => ''
            ),
            'email_type' => array(
                'title'       => 'Email type',
                'type'        => 'select',
                'description' => 'Choose which format of email to send.',
                'default'     => 'html',
                'class'       => 'email_type',
                'options'     => array(
                    'plain'     => __( 'Plain text', 'order-approval-woocommerce' ),
                    'html'      => __( 'HTML', 'order-approval-woocommerce' ),
                    'multipart' => __( 'Multipart', 'order-approval-woocommerce' ),
                )
            )
        );
    }
}
?>