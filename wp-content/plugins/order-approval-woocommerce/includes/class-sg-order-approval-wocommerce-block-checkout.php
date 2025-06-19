<?php

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

final class Woa_Order_Blocks extends AbstractPaymentMethodType {

	private $gateway;
	protected $name = 'woa_gateway';

	public function initialize() {
		$this->settings = get_option( 'woocommerce_woa_gateway_settings', [] );
		
		foreach ( WC_Payment_Gateways::instance()->payment_gateways() as $gateway ) {
			if ( $gateway instanceof Woa_Gateway ) {
				$this->gateway = $gateway;
				break;
			}
		}
		
	}

	public function is_active() {
		return $this->gateway->is_available();
	}

	public function get_payment_method_script_handles() {

		wp_register_script(
			'wc-woa-gateway-blocks-integration',
            plugin_dir_url(__FILE__) . 'block/woa-checkout.js',
			[
				'wc-blocks-registry',
				'wc-settings',
				'wp-element',
				'wp-html-entities',
				'wp-i18n',
			],
			SG_ORDER_APPROVAL_WOOCOMMERCE_VERSION,
			true
		);
		if( function_exists( 'wp_set_script_translations' ) ) {			
			wp_set_script_translations( 'wc-woa-gateway-blocks-integration', 'order-approval-woocommerce', WOA_DIR_PATH. 'languages/' );
			
		}
		return [ 'wc-woa-gateway-blocks-integration' ];
	}

	public function get_payment_method_data() {
		return [
			'title' => $this->gateway->title,
			'description' => $this->gateway->description,
		];
	}

}