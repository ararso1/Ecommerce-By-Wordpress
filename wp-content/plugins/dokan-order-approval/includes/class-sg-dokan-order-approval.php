<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Sg_Order_Approval_Woocommerce_Pro
 * @subpackage Sg_Order_Approval_Woocommerce_Pro/includes
 * @author     Sevengits <sevengits@gmail.com>
 */
class Sg_Dokan_Order_Approval
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Sg_Dokan_Order_Approval_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('SGDOA_VERSION')) {
			$this->version = SGDOA_VERSION;
		} else {
			$this->version = '1.0.0';
		}

		$this->plugin_name = 'dokan-order-approval';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Sg_Dokan_Order_Approval_Loader. Orchestrates the hooks of the plugin.
	 * - Sg_Dokan_Order_Approval_i18n. Defines internationalization functionality.
	 * - Sg_Dokan_Order_Approval_Admin. Defines all hooks for the admin area.
	 * - Sg_Dokan_Order_Approval_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sg-dokan-order-approval-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sg-dokan-order-approval-i18n.php';

		/**
		 * The class responsible for defining email template
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-sg-dokan-order-approval-wc_email.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-sg-dokan-order-approval-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-sg-dokan-order-approval-public.php';


		$this->loader = new Sg_Dokan_Order_Approval_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Sg_Dokan_Order_Approval_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Sg_Dokan_Order_Approval_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{
		$plugin_admin = new Sg_Dokan_Order_Approval_Admin($this->get_plugin_name(), $this->get_version());
		if (!defined('SGDOA_DISABLED')) {

			# for enqueue js and css files
			$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
			$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

			$this->loader->add_action('woocommerce_order_status_waiting', $plugin_admin, 'email_waiting_new_order_vendor_notifications', 10, 2);

			$this->loader->add_action('dokan_order_detail_after_order_general_details', $plugin_admin, 'sgda_approval_actions_display');

			# add setting page
			$this->loader->add_filter('sg_oawoo_additional_settings', $plugin_admin, 'sgits_doa_settings');

			# custom field type for inline fields in settings
			$this->loader->add_action('woocommerce_admin_field_sgits_relative_select', $plugin_admin, 'sgits_doa_grouped_fields', 100);



			# Show only for main order
			$this->loader->add_action('woocommerce_thankyou', $plugin_admin, 'sgda_custom_update_order_meta', 20, 1);

			# below the plugin title in plugins page. add custom links at the begin of list
			$this->loader->add_filter('plugin_action_links_' . SGDOA_BASE, $plugin_admin, 'sgda_links_below_title_begin');

			# below the plugin title in plugins page. add custom links at the end of list
			$this->loader->add_filter('plugin_action_links_' . SGDOA_BASE, $plugin_admin, 'sgda_links_below_title_end');

			# below the plugin description in plugins page. add custom links at the end of list
			$this->loader->add_filter('plugin_row_meta', $plugin_admin, 'sgda_plugin_description_below_end', 10, 2);

			# cron job for cancel unhandled orders
			$this->loader->add_action('wp', $plugin_admin, 'sgits_doa_cancel_unpaid_orders_action');
			$this->loader->add_action('sgits_doa_cancel_unpaid_orders_action_hook', $plugin_admin, 'sgits_doa_cancel_unhandled_orders');
		}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		$plugin_public = new Sg_Dokan_Order_Approval_Public($this->get_plugin_name(), $this->get_version());
		if (!defined('SGDOA_DISABLED')) {

			$this->loader->add_action('wp_head', $plugin_public, 'oawoo_add_custom_order_status_actions_button_css');
			$this->loader->add_filter('dokan_my_account_my_sub_orders_actions', $plugin_public, 'oawoo_add_custom_order_status_actions_button', 100, 2);
			$this->loader->add_action('dokan_get_order_status_class', $plugin_public, 'oawoo_get_order_status_class',10,2);
			$this->loader->add_action('dokan_get_order_status_translated', $plugin_public, 'oawoo_get_order_status_translated',10,2);
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Sg_Dokan_Order_Approval_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
