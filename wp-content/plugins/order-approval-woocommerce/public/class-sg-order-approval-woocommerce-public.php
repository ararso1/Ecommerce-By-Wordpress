<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link  https://sevengits.com/
 * @since 1.0.0
 *
 * @package    Sg_Order_Approval_Woocommerce
 * @subpackage Sg_Order_Approval_Woocommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Sg_Order_Approval_Woocommerce
 * @subpackage Sg_Order_Approval_Woocommerce/public
 * @author     Sevengits <sevengits@gmail.com>
 */
class Sg_Order_Approval_Woocommerce_Public
{

    /**
     * The ID of this plugin.
     *
     * @since  1.0.0
     * @access private
     * @var    string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since  1.0.0
     * @access private
     * @var    string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since 1.0.0
     * @param string $plugin_name The name of the plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since 1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Sg_Order_Approval_Woocommerce_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Sg_Order_Approval_Woocommerce_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Sg_Order_Approval_Woocommerce_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Sg_Order_Approval_Woocommerce_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
    }
    /**
     * @since 1.0.0 
     * 
     * Note : sg_enable_order_approval == enable means All order is need admin approval
     * sg_enable_order_approval == disable means enable plugin for per product
     */

    function oawoo_paymentgatways_disable_manager($available_gateways)
    {
        global $woocommerce;
        $allowed_gateways  = array();
        $sg_product_enable = false;


        if (is_admin()) {
            return $available_gateways;
        }

        if ((is_checkout()|| $this->is_checkout_block()) && isset($available_gateways['woa_gateway']) && !is_wc_endpoint_url('order-pay')) {

            $allowed_gateways['woa_gateway'] = $available_gateways['woa_gateway'];
	        return $allowed_gateways;
            
        }

        if (is_wc_endpoint_url('order-pay') && isset($available_gateways['woa_gateway'])) {

            unset($available_gateways['woa_gateway']);
            return $available_gateways;
        }

        return $available_gateways;
    }

    function oawoo_email_remove_payment_method($total_rows, $order, $tax_display)
    {
        if ((!is_wc_endpoint_url() || is_wc_endpoint_url('order-received')) && $order->get_payment_method() === 'woa_gateway') {
            unset($total_rows['payment_method']);
        }
        return $total_rows;
    }
    /**
     * Conditional function that check if Checkout page use Checkout Blocks
     * @since 2.1.5
     * @return boolean
     */
    
    function is_checkout_block() {
        return WC_Blocks_Utils::has_block_in_page( wc_get_page_id('checkout'), 'woocommerce/checkout' );
    }
}
?>