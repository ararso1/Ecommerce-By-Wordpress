<?php

namespace sevengits;

if (!is_admin())
	return;

global $pagenow;

if ($pagenow != "plugins.php")
	return;

if (defined('SGDOA_DEACTIVATE_FEEDBACK_FORM_INCLUDED'))
	return;
define('SGDOA_DEACTIVATE_FEEDBACK_FORM_INCLUDED', true);

add_action('admin_enqueue_scripts', function () {

	// Enqueue scripts
	if (!wp_script_is('sgits-oad-remodal-js', 'enqueued'))
		wp_enqueue_script('sgits-oad-remodal-js', plugin_dir_url(__FILE__) . 'remodal.min.js');

	if (!wp_style_is('sgits-oad-remodal-css', 'enqueued'))
		wp_enqueue_style('sgits-oad-remodal-css', plugin_dir_url(__FILE__) . 'remodal.css');

	if (!wp_style_is('remodal-default-theme', 'enqueued'))
		wp_enqueue_style('remodal-default-theme', plugin_dir_url(__FILE__) . 'remodal-default-theme.css');

	if (!wp_script_is('sgits-oad-deactivate-feedback-form-js', 'enqueued'))
		wp_enqueue_script('sgits-oad-deactivate-feedback-form-js', plugin_dir_url(__FILE__) . 'deactivate-feedback-form.js');

	if (!wp_script_is('sgits-oad-deactivate-feedback-form-css', 'enqueued'))
		wp_enqueue_style('sgits-oad-deactivate-feedback-form-css', plugin_dir_url(__FILE__) . 'deactivate-feedback-form.css');

	// Localized strings
	wp_localize_script('sgits-oad-deactivate-feedback-form-js', 'sgdoa_deactivate_feedback_form_strings', array(
		'quick_feedback'			=> __('Quick Feedback', 'dokan-order-approval'),
		'foreword'					=> __('If you would be kind enough, please tell us why you\'re deactivating?', 'dokan-order-approval'),
		'better_plugins_name'		=> __('Please tell us which plugin?', 'dokan-order-approval'),
		'please_tell_us'			=> __('Please tell us the reason so we can improve the plugin', 'dokan-order-approval'),
		'do_not_attach_email'		=> __('Do not send my e-mail address with this feedback', 'dokan-order-approval'),

		'brief_description'			=> __('Please give us any feedback that could help us improve', 'dokan-order-approval'),

		'cancel'					=> __('Cancel', 'dokan-order-approval'),
		'skip_and_deactivate'		=> __('Skip &amp; Deactivate', 'dokan-order-approval'),
		'submit_and_deactivate'		=> __('Submit &amp; Deactivate', 'dokan-order-approval'),
		'please_wait'				=> __('Please wait', 'dokan-order-approval'),
		'thank_you'					=> __('Thank you!', 'dokan-order-approval')
	));

	// Plugins
	$plugins = apply_filters('sgdoa_deactivate_feedback_form_plugins', array());

	// Reasons
	$defaultReasons = array(
		'suddenly-stopped-working'	=> __('The plugin suddenly stopped working', 'dokan-order-approval'),
		'plugin-broke-site'			=> __('The plugin broke my site', 'dokan-order-approval'),
		'no-longer-needed'			=> __('I don\'t need this plugin any more', 'dokan-order-approval'),
		'found-better-plugin'		=> __('I found a better plugin', 'dokan-order-approval'),
		'temporary-deactivation'	=> __('It\'s a temporary deactivation, I\'m troubleshooting', 'dokan-order-approval'),
		'other'						=> __('Other', 'dokan-order-approval')
	);

	foreach ($plugins as $plugin) {
		$plugin->reasons = apply_filters('sgdoa_deactivate_feedback_form_reasons', $defaultReasons, $plugin);
	}

	// Send plugin data
	wp_localize_script('sgits-oad-deactivate-feedback-form-js', 'sgdoa_deactivate_feedback_form_plugins', $plugins);
});

/**
 * Hook for adding plugins, pass an array of objects in the following format:
 *  'slug'		=> 'plugin-slug'
 *  'version'	=> 'plugin-version'
 * @return array The plugins in the format described above
 */
add_filter('sgdoa_deactivate_feedback_form_plugins', function ($plugins) {
	return $plugins;
});
