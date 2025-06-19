<?php
/**
 * Order approved email to Customer.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

	<p><?php 
	/* Translators: %d: Order number */
	echo esc_html( sprintf( __( 'Your order #%d has been approved.', 'order-approval-woocommerce' ), $order->get_order_number() ) );?></p>
<?php 
 /**
 * adding payment link options
 * @since 2.0.4
 */
$pay_now_url = esc_url( $order->get_checkout_payment_url() );

?>
<h2 class="email-upsell-title"><?php  printf( esc_html__( 'Pay for order', 'order-approval-woocommerce' ) ); ?> </h2>
<p class="email-upsell-p"><?php printf(esc_html__('Please pay the order by clicking here ','order-approval-woocommerce')); ?>
<a href='<?php echo esc_url($pay_now_url);?>'> <?php  printf( esc_html__( 'Pay now', 'order-approval-woocommerce' ) ); ?></a></p>
<?php 
/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
	echo "<p>Order Details:</p>";
}


/**
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Emails::order_schema_markup() Adds Schema.org markup.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );