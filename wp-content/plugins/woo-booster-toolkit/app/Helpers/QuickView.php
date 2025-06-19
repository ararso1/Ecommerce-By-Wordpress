<?php

namespace WCBT\Helpers;

class QuickView
{
	/**
	 * @return array|false|\stdClass|string
	 */
	public static function get_quick_view_tooltip_text()
	{
		$enable = Settings::get_setting_detail('quick-view:fields:tooltip_enable');

		if ($enable === 'on') {
			return Settings::get_setting_detail('quick-view:fields:tooltip_text');
		}

		return '';
	}

	/**
	 * @return array|false|\stdClass|string
	 */
	public static function get_product_image_width()
	{
		$enable = Settings::get_setting_detail('quick-view:fields:enable');

		if ($enable === 'on') {
			return Settings::get_setting_detail('quick-view:fields:product_image_width');
		}

		return '';
	}

	/**
	 * @return array|false|\stdClass|string
	 */
	public static function get_product_image_height()
	{
		$enable = Settings::get_setting_detail('quick-view:fields:enable');

		if ($enable === 'on') {
			return Settings::get_setting_detail('quick-view:fields:product_image_height');
		}

		return '';
	}
}
