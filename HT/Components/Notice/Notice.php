<?php
/**
 * Components - Notice
 *
 * @package hey-tony
 */
namespace HT\Components\Notice;

/* Imports */

use HT\HT as HT;

/**
 * Class - render cookie notice.
 */
class Notice {
	/**
	 * Path in assets folder for css and js.
	 *
	 * @var string
	 */
	public static $asset_path = 'Components/Notice/Notice';

	/**
	 * Check if cookie set, set avada wrapper action, styles and scripts.
	 *
	 * @return void
	 */
	public function __construct() {
		if ( isset( $_COOKIE[ HT::$namespace . '_cookie_notice' ] ) ) {
			return;
		}

		add_action( 'avada_before_wrapper_container_close', [$this, 'render'] );

		HT::$scripts_styles['ht-cookie-notice'] = [
			'style'  => self::$asset_path,
			'script' => self::$asset_path,
		];
	}

	/**
	 * Output cookie notice.
	 *
	 * @return void
	 */
	public static function render() {
		$cookie_text = get_option(
			HT::$namespace . '_cookie_text',
			'We use cookies to give you the best experience. By continuing to use this site, you consent to the use of cookies.'
		);

		/* phpcs:disable */
		echo (
			'<aside class="c-notice l-wd-full l-px-container l-pb-2xs e-underline l-fixed l-bottom-0 l-right-0 l-none">' .
				'<div class="c-notice__main t-bg-background-base t-xs l-relative l-overflow-hidden l-before t-wt-bold">' .
					"<p class='l-relative'>$cookie_text</p>" .
					'<button class="c-notice__close t-foreground-base t-xs l-wd-xs l-ht-xs l-flex l-absolute l-top-0 l-right-0 outline-snug" type="button" aria-label="Close cookie notice">' .
						'<i aria-hidden="true" class="l-m-auto awb-icon-close"></i>' .
					'</button>' .
				'</div>' .
			'</aside>'
		);
		/* phpcs:enable */
	}
}
