<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link  https://sevengits.com/
 * @since 1.0.0
 *
 * @package    Sg_Order_Approval_Woocommerce_Pro
 * @subpackage Sg_Order_Approval_Woocommerce_Pro/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Sg_Order_Approval_Woocommerce_Pro
 * @subpackage Sg_Order_Approval_Woocommerce_Pro/public
 * @author     Sevengits <sevengits@gmail.com>
 */
class Sg_Dokan_Order_Approval_Public
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
         * defined in Sg_Dokan_Order_Approval_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Sg_Dokan_Order_Approval_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/sg-dokan-order-approval-public.css', array(), $this->version, 'all');
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
         * defined in Sg_Dokan_Order_Approval_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Sg_Dokan_Order_Approval_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/sg-dokan-order-approval-public.js', array('jquery'), $this->version, false);
    }

    /**
     * @since 1.0.0  add action button in admin 
     * @return actions
     */
    function oawoo_add_custom_order_status_actions_button($actions, $order)
    {

        if (is_user_logged_in()) {

            $user = wp_get_current_user();

            $roles = (array) $user->roles;
            error_log(print_r($roles, true));
            if ($order->has_status(array('waiting'))) {

                $actions['wc_approved'] = array(
                    'url'       => wp_nonce_url(admin_url('admin-ajax.php?action=woocommerce_mark_order_status&status=pending&order_id=' . $order->get_id()), 'woocommerce-mark-order-status'),
                    'name'      => __('Approve', 'dokan-order-approval'),
                    'action'    => 'wc_approved',
                    'icon'   => '<i class="far fa-check-circle">&nbsp;</i>'
                );
                // Set the action button
                $actions['wc_reject'] = array(
                    'url'       => wp_nonce_url(admin_url('admin-ajax.php?action=woocommerce_mark_order_status&status=cancelled&order_id=' . $order->get_id()), 'woocommerce-mark-order-status'),
                    'name'      => __('Reject', 'dokan-order-approval'),
                    'action'    => 'wc_reject',
                    'icon'   => '<i class="far fa-times-circle">&nbsp;</i>'
                );
            }
        }

        return $actions;
    }

    /**
     * 
     * @since 1.0.0  add action button in admin 
	 
     */


    function oawoo_add_custom_order_status_actions_button_css()
    {
        global $post;
        if (!$post) {
            return;
        }
        if (has_shortcode($post->post_content, 'dokan-dashboard')) {
            echo '<style>a[data-original-title="Approve"]::before { font-family: "Font Awesome 5 Free" !important; content: "\f058" !important; color:GREEN }</style>';
            echo '<style>a[data-original-title="Reject"]::before { font-family: "Font Awesome 5 Free" !important; content: "\f057" !important; color:RED}</style>';
        }
    }

    function oawoo_get_order_status_class($text, $status )
    {
        switch ( $status ) {
            case 'waiting':
            case 'wc-waiting':
                $text = 'info';
                break;       
        }       
        
        return $text;
    }

    function oawoo_get_order_status_translated($text, $status)
    {
        switch ( $status ) {
            case 'waiting':
            case 'wc-waiting':
                $text = __('Waiting', 'dokan-order-approval-pro');
                break;       
        }    
        return $text;
    }
}
