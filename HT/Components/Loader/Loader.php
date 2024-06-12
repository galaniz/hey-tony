<?php
/**
 * Components - Loader
 *
 * @package hey-tony
 */
namespace HT\Components\Loader;

/* Imports */

use HT\HT as HT;

/**
 * Class - render loader for pagination.
 */
class Loader {
	/**
	 * Path in assets folder for css and js.
	 *
	 * @var string
	 */
	public static $asset_path = 'Components/Loader/Loader';

	/**
	 * Set avada container action and styles.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'avada_before_main_container', [$this, 'render'] );
	}

	/**
	 * Output loader.
	 *
	 * @return void
	 */
	public static function render() {
		if ( ! is_post_type_archive( 'work' ) && ! is_home() ) {
			return;
		}

		echo (
			'<aside class="c-loader l-flex l-align-center l-justify-center l-fixed l-top-0 l-bottom-0 l-left-0 l-right-0 outline-snug e-trans js-load-more-loader" tabindex="0" aria-label="Loading">' .
				'<div class="c-loader__icon l-wd-l t-primary-base b-radius-full b-all b-wd-thick">' .
					'<div class="l-relative l-overflow-hidden l-ar-1-1"></div>' .
				'</div>' .
			'</aside>'
		);
	}
}
