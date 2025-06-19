<?php

namespace sevengits;

if (!is_admin())
	return;

global $pagenow;

if ($pagenow != "plugins.php")
	return;

if (defined('SGITS_DEACTIVATE_FEEDBACK_FORM_INCLUDED'))
	return;
define('SGITS_DEACTIVATE_FEEDBACK_FORM_INCLUDED', true);

add_action('admin_enqueue_scripts', function () {

	// Enqueue scripts
	if (!wp_script_is('sgits-remodal-js', 'enqueued'))
		wp_enqueue_script('sgits-remodal-js', plugin_dir_url(__FILE__) . 'remodal.min.js',array(),SG_ORDER_APPROVAL_WOOCOMMERCE_VERSION,false);

	if (!wp_style_is('sgits-remodal-css', 'enqueued'))
		wp_enqueue_style('sgits-remodal-css', plugin_dir_url(__FILE__) . 'remodal.css',array(),SG_ORDER_APPROVAL_WOOCOMMERCE_VERSION,false);

	if (!wp_style_is('remodal-default-theme', 'enqueued'))
		wp_enqueue_style('remodal-default-theme', plugin_dir_url(__FILE__) . 'remodal-default-theme.css',array(),SG_ORDER_APPROVAL_WOOCOMMERCE_VERSION,false);

	if (!wp_script_is('sgits-deactivate-feedback-form-js', 'enqueued'))
		wp_enqueue_script('sgits-deactivate-feedback-form-js', plugin_dir_url(__FILE__) . 'deactivate-feedback-form.js',array(),SG_ORDER_APPROVAL_WOOCOMMERCE_VERSION,false);

	if (!wp_script_is('sgits-deactivate-feedback-form-css', 'enqueued'))
		wp_enqueue_style('sgits-deactivate-feedback-form-css', plugin_dir_url(__FILE__) . 'deactivate-feedback-form.css',array(),SG_ORDER_APPROVAL_WOOCOMMERCE_VERSION,false);

	// Localized strings
	wp_localize_script('sgits-deactivate-feedback-form-js', 'sgits_deactivate_feedback_form_strings', array(
		'quick_feedback'			=> __('Quick Feedback', 'order-approval-woocommerce'),
		'foreword'					=> __('If you would be kind enough, please tell us why you\'re deactivating?', 'order-approval-woocommerce'),
		'better_plugins_name'		=> __('Please tell us which plugin?', 'order-approval-woocommerce'),
		'please_tell_us'			=> __('Please tell us the reason so we can improve the plugin', 'order-approval-woocommerce'),
		'do_not_attach_email'		=> __('Do not send my e-mail address with this feedback', 'order-approval-woocommerce'),

		'brief_description'			=> __('Please give us any feedback that could help us improve', 'order-approval-woocommerce'),

		'cancel'					=> __('Cancel', 'order-approval-woocommerce'),
		'skip_and_deactivate'		=> __('Skip &amp; Deactivate', 'order-approval-woocommerce'),
		'submit_and_deactivate'		=> __('Submit &amp; Deactivate', 'order-approval-woocommerce'),
		'please_wait'				=> __('Please wait', 'order-approval-woocommerce'),
		'thank_you'					=> __('Thank you!', 'order-approval-woocommerce')
	));

	// Plugins
	$plugins = apply_filters('sgits_deactivate_feedback_form_plugins', array());

	// Reasons
	$defaultReasons = array(
		'suddenly-stopped-working'	=> __('The plugin suddenly stopped working', 'order-approval-woocommerce'),
		'plugin-broke-site'			=> __('The plugin broke my site', 'order-approval-woocommerce'),
		'no-longer-needed'			=> __('I don\'t need this plugin any more', 'order-approval-woocommerce'),
		'found-better-plugin'		=> __('I found a better plugin', 'order-approval-woocommerce'),
		'temporary-deactivation'	=> __('It\'s a temporary deactivation, I\'m troubleshooting', 'order-approval-woocommerce'),
		'other'						=> __('Other', 'order-approval-woocommerce')
	);

	foreach ($plugins as $plugin) {
		$plugin->reasons = apply_filters('sgits_deactivate_feedback_form_reasons', $defaultReasons, $plugin);
	}

	// Send plugin data
	wp_localize_script('sgits-deactivate-feedback-form-js', 'sgits_deactivate_feedback_form_plugins', $plugins);
});

/**
 * Hook for adding plugins, pass an array of objects in the following format:
 *  'slug'		=> 'plugin-slug'
 *  'version'	=> 'plugin-version'
 * @return array The plugins in the format described above
 */
add_filter('sgits_deactivate_feedback_form_plugins', function ($plugins) {
	return $plugins;
});
