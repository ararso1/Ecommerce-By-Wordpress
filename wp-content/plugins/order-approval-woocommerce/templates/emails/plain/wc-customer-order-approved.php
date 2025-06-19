<?php
/**
 * Admin new order email (plain text)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo "= " .esc_html($email_heading) . " =\n\n";
/* translators: %d: Order number */
echo sprintf(esc_html__( 'New order #%d has been approved. The order details:', 'order-approval-woocommerce' ), esc_html($order->get_id()) ) . "\n\n";
/* translators: %s: payment url */
echo sprintf(esc_html__('Please pay the order by clicking here %s ','order-approval-woocommerce'),esc_url($order->get_checkout_payment_url())); 
echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

/**
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Emails::order_schema_markup() Adds Schema.org markup.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

/**
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html(apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ));