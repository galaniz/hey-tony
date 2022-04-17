<?php
/**
 * Utility methods
 *
 * @package hey-tony
 */

namespace HT;

/**
 * Imports
 */

use Formation\Utils as FrmUtils;

/**
 * Class
 */

class Utils {

		/**
		 * Namespace for FrmUtils.
		 *
		 * @var string $namespace
		 */

		public static $namespace = 'ht';

		/**
		 * For FrmUtils.
		 *
		 * @var array $posts_per_page
		 */

		public static $posts_per_page = [];

		/**
		 * Formation utilities.
		 */

		use FrmUtils;

		/**
		 * Filter Avada social icons output.
		 *
		 * @param string $html
		 * @return string
		 */

		public static function filter_social( $html ) {
				$html = str_replace(
						['class="fusion-social-networks-wrapper"', 'style=', '<a class="', 'a>'],
						['class="fusion-social-networks-wrapper l-flex u-oo-s" data-gap="s"', 'data-style=', '<div><a class="l-m-0 ', 'a></div>'],
						$html
				);

				return $html;
		}

} // End Utils
