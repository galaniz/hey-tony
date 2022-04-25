<?php
/**
 * Render overlapping stat
 *
 * @package hey-tony
 */

namespace HT\Common\Render;

/**
 * Imports
 */

use HT\Utils;

/**
 * Class
 */

class Stat {

		/**
		 * Output stat.
		 *
		 * @return string
		 */

		public static function render( $args ) {
				$args = array_merge(
						[
							'large_text' => '',
							'small_text' => '',
							'align'      => 'left',
							'wide'       => false,
						],
						$args
				);

				[
					'large_text' => $large_text,
					'small_text' => $small_text,
					'align'      => $align,
					'wide'       => $wide,
				] = $args;

				$classes = 'o-stat t-bg-background-base u-p-a u-b-0 u-d-ib';

				/* Output */

				return (
					"<div class='$classes' data-align='$align' data-wide='$wide' aria-hidden='true'>" .
						'<div class="h2-l o-accent o-accent-l l-mb-xxs u-p-r u-zi-1" data-theme="primary-base">' .
							'<p class="l-m-0">' .
								"<span class='u-d-ib'>$large_text</span>" .
							'</p>' .
						'</div>' .
						'<div class="p-m u-fw-b">' .
							"<p class='l-m-0 u-lh-130-pc'>$small_text</p>" .
						'</div>' .
					'</div>'
				);
		}

		/**
		 * Shortcode to output stat.
		 *
		 * @param array $atts
		 * @param string $content
		 * @return string
		 */

		public static function shortcode( $atts, $content ) {
				$atts = shortcode_atts(
						[
							'large_text' => '',
							'small_text' => '',
							'align'      => 'left',
							'wide'       => false,
						],
						$atts,
						'ht-stat'
				);

				return self::render( $atts );
		}

} // End Stat
