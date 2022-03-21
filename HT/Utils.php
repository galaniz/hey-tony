<?php
/**
 * Utility methods
 *
 * @package hey-tony
 */

namespace HT;

/**
 * Trait
 */

trait Utils {

		/**
		 * Filter Avada social icons output.
		 */

		public static function filter_social( $html ) {
				$html = str_replace(
						['class="fusion-social-networks-wrapper"', 'style='],
						['class="fusion-social-networks-wrapper l-flex" data-gap="s"', 'data-style='],
						$html
				);

				return $html;
		}

} // End Utils
