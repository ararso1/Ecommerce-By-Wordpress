<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sg_Order_Approval_Woocommerce_Pro
 * @subpackage Sg_Order_Approval_Woocommerce_Pro/admin
 * @author     Sevengits <sevengits@gmail.com>
 */
class Sg_Dokan_Order_Approval_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/dokan-order-approval-pro-admin.css', array(), $this->version, 'all');

		if (!wp_style_is('sgits-admin-settings-sidebar-css', 'enqueued'))
			wp_enqueue_style('sgits-admin-settings-sidebar', plugin_dir_url(__FILE__) . 'css/settings-sidebar.css', array(), $this->version, 'all');

		if (!wp_style_is('sgits-admin-common-css', 'enqueued'))
			wp_enqueue_style('sgits-admin-common', plugin_dir_url(__FILE__) . 'css/common.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
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

		// wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/sg-dokan-order-approval-admin.js', array('jquery'), $this->version, false);
	}

	/**
	 *  @since 1.0.0  
	 * 
	 */

	function email_waiting_new_order_vendor_notifications($order_id, $order)
	{
		WC()->mailer()->get_emails()['SG_Vendor_PreOrder']->trigger($order_id);
	}

	function sgda_custom_update_order_meta($order_id)
	{
		if (!$order_id)
			return;
		// Allow code execution only once 
		if (!get_post_meta($order_id, '_thankyou_action_done', true)) {
			if (get_post_meta($order_id, '_payment_method', true) == 'woa_gateway') {
				$sgda_total_orders = get_option('sgda_total_orders', 0);
				update_option('sgda_total_orders', $sgda_total_orders + 1);
			}
		}
	}

	function sgda_approval_actions_display($order)
	{
		if ($order->get_status() !== 'cancelled') {
			$actions = array(
				"approve" =>
				array(
					'label' => "Approve",
					'url' => wp_nonce_url(admin_url('admin-ajax.php?action=woocommerce_mark_order_status&status=pending&order_id=' . $order->get_id()), 'woocommerce-mark-order-status'),
					'classList' => "success",
					'condition' => ($order->get_status() === 'waiting')
				),
				"reject" => array(
					'label' => "Reject",
					'url' => wp_nonce_url(admin_url('admin-ajax.php?action=woocommerce_mark_order_status&status=cancelled&order_id=' . $order->get_id()), 'woocommerce-mark-order-status'),
					'classList' => "danger",
					'condition' => ($order->get_status() !== 'cancelled')
				)
			);
?>
			<div class="dokan-panel dokan-panel-default">
				<div class="dokan-panel-heading"><strong>Quick action</strong></div>
				<div class="dokan-panel-body general-details">

					<ul class="list-unstyled list-inline sgits-action-container">
						<?php
						foreach ($actions as $status => $action) :
							if ($action['condition'] === true) {
						?>
								<li class="list-item <?php echo $status; ?>">
									<a class="button <?php echo $action['classList']; ?>" href="<?php echo $action['url']; ?>"><?php _e($action['label'], 'dokan-order-approval'); ?></a>
								</li>

						<?php
							}
						endforeach;
						?>
					</ul>
				</div>
			</div>
			<style>
				.sgits-action-container {
					margin: 0px;
				}

				.dokan-dashboard .dokan-dashboard-content ul.sgits-action-container li {
					margin-right: 10px;
					display: inline-block;
				}

				.sgits-action-container li a {
					color: red;
					border: 1px solid red;
					border-radius: 4px;
					padding: 4px 8px;
				}

				.sgits-action-container li a {
					border: 1px solid red;
					border-radius: 4px;
					padding: 4px 8px;
				}

				.sgits-action-container li.reject a {
					color: red;
					border-color: red;
				}

				.sgits-action-container li.approve a {
					color: green;
					border-color: green;
				}
			</style>
		<?php
		}
	}

	/**
	 * 
	 * For array of data convert array of links and merge with exists array of links
	 * 
	 * $position = "start | end" 
	 */
	public function sgda_merge_links($old_list, $new_list, $position = "end")
	{
		$settings = array();
		foreach ($new_list as $name => $item) {
			$target = (array_key_exists("target", $item)) ? $item['target'] : '';
			$classList = (array_key_exists("classList", $item)) ? $item['classList'] : '';
			$settings[$name] = sprintf('<a href="%s" target="' . $target . '" class="' . $classList . '">%s</a>', esc_url($item['link'], $this->plugin_name), esc_html__($item['name'], $this->plugin_name));
		}
		if ($position !== "start") {
			// push into $links array at the end
			return array_merge($old_list, $settings);
		} else {
			return array_merge($settings, $old_list);
		}
	}

	public function sgda_links_below_title_begin($links)
	{
		// if plugin is installed $links listed below the plugin title in plugins page. add custom links at the begin of list
		if (!defined('SGDOA_DISABLED')) {

			$new_links = array(
				'settings' => array(
					"name" => __('Settings', 'dokan-order-approval'),
					"classList" => "",
					"link" => admin_url('admin.php?page=wc-settings&tab=advanced&section=sg_order_tab#sg_oawoo_dokan_settings-description')
				)
			);
			$links = $this->sgda_merge_links($links, $new_links, "start");
		}
		return $links;
	}

	public function sgda_links_below_title_end($links)
	{
		// if plugin is installed $links listed below the plugin title in plugins page. add custom links at the end of list
		$new_links = array(
			'buy-pro' => array(
				"name" => __('Buy Premium', 'dokan-order-approval'),
				"classList" => "pro-purchase get-pro-link",
				"target" => '_blank',
				"link" => 'https://sevengits.com/plugin/dokan-order-approval/?utm_source=Wordpress&utm_medium=plugins-link&utm_campaign=Free-plugin'
			)
		);

		$links = $this->sgda_merge_links($links, $new_links, "end");

		return $links;
	}

	/**
	 * add more links like docs,support and premium version link in plugin page.
	 */
	public function sgda_plugin_description_below_end($links, $file)
	{
		if (strpos($file, 'dokan-order-approval.php') !== false) {
			$new_links = array(
				'pro' => array(
					"name" => __('Buy Premium', 'dokan-order-approval'),
					"classList" => "pro-purchase get-pro-link",
					"target" => '_blank',
					"link" => "https://sevengits.com/plugin/dokan-order-approval/?utm_source=dashboard&utm_medium=plugins-link&utm_campaign=Free-plugin"
				),
				'docs' => array(
					"name" => __('Docs', 'dokan-order-approval'),
					"target" => '_blank',
					"link" => "https://sevengits.com/docs/dokan-order-approval/?utm_source=dashboard&utm_medium=plugins-link&utm_campaign=Free-plugin"
				),
				'support' => array(
					"name" => __('Free Support', 'dokan-order-approval'),
					"target" => '_blank',
					"link" => 'https://wordpress.org/support/plugin/dokan-order-approval/'
				),

			);
			# This function is defined in pre-defined-functions/merge-arrays.php

			$links = $this->sgda_merge_links($links, $new_links, "end");
		}

		return $links;
	}

	/**
	 * plugin settings
	 *
	 * @param [array] $settings
	 * @return void
	 */
	function sgits_doa_settings($settings)
	{
		$doa_settings = array(
			array(
				'type' => 'title',
				'id'   => 'sg_oawoo_dokan_settings',
				'desc' => __('Order approval for dokan', 'dokan-order-approval'),
			),
			array(
				'type' => 'sectionend',
				'name' => 'end_section',
				'id' => 'ppw_woo'
			)
		);
		$settings = array_merge($settings, $doa_settings);
		return $settings;
	}

	function sgits_doa_grouped_fields($option)
	{

		$opt1 = $option['fields'][0];
		$opt2 = $option['fields'][1];
		$default_value = get_option($option['id'], array("duration" => $opt1['default'], "unit" => $opt2['default']));
		if (is_array($default_value)) {
			$field_values['duration'] = array_values($default_value)[0];
			$field_values['unit'] = array_values($default_value)[1];
		}
		?>
		<tr style="display: flex;">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr($opt1['id']); ?>"><?php echo esc_html($option['name']); ?></label>
				<?php
				$tooltip_html = wc_help_tip($option['desc']);
				if ($option['desc_tip']) {
					echo $tooltip_html;
				}
				?>
			</th>
			<td class="forminp">
				<input name="<?php echo esc_attr($option['id']); ?>['duration']" id="<?php echo esc_attr($opt1['id']); ?>" type="<?php echo $opt1['type']; ?>" style="width: 80px;" value="<?php _e($field_values['duration'], 'dokan-order-approval'); ?>" placeholder="<?php echo esc_attr($opt1['placeholder']); ?>" step="1" min="0" />
				<select name="<?php echo esc_attr($option['id']); ?>['unit']" style="width: auto;">
					<?php
					foreach ($opt2['options'] as $value => $label) {
						echo '<option value="' . esc_attr($value) . '"' . selected($field_values['unit'], $value, false) . '>' . esc_html($label) . '</option>';
					}
					?>
				</select>
				<span class="desc">
					<?php
					echo (!$option['desc_tip']) ? $option['desc'] : '';
					?>
				</span>
			</td>
		</tr>
<?php
	}

	function sgits_doa_cancel_unhandled_orders()
	{
		// Get all unpaid orders older than 24 hours

		$settings = get_option('sg_doa_orders_auto_cancel', array(
			"duration" => get_option('sg_doa_orders_auto_cancel_duration', "0"),
			"unit" => get_option("sg_doa_orders_auto_cancel_duration_mode", "daily")
		));
		if (count($settings) > 0) {
			$hour = array_values($settings)[0];
			$mode = array_values($settings)[1];
			if ($hour > 0) {

				if ($mode === 'daily') {
					$hour = $hour * 24;
				}
				$args = array(
					'status' => array("waiting"),
					'date_modified' => '<' . (time() - ($hour * 60 * 60))
				);
				$unpaid_orders = wc_get_orders($args);
				# Cancel the orders
				foreach ($unpaid_orders as $order) {
					$order->update_status('cancelled', __('Order cancelled after 24 hours of being unpaid.', 'woocommerce'));
				}
			}
		}
	}
	function sgits_doa_cancel_unpaid_orders_action()
	{
		$settings = get_option('sg_doa_orders_auto_cancel', array("duration" => "0", "unit" => "daily"));
		if (count($settings) > 0) {
			$mode = array_values($settings)[1];
			$mode = get_option('sg_doa_orders_auto_cancel_duration_mode', 'daily');
			if (!wp_next_scheduled('sgits_doa_cancel_unpaid_orders_action_hook')) {
				wp_schedule_event(time(), $mode, 'sgits_doa_cancel_unpaid_orders_action_hook');
			}
		}
	}
}
