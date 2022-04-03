<?php
/**
 * Render hero background image
 *
 * @package hey-tony
 */

namespace HT\Common\Render;

/**
 * Class
 */

class Hero_Image {

		/**
		 * Output hero background image.
		 *
		 * @return string
		 */

		public static function render() {
				if ( is_post_type_archive() || is_home() ) {
						return '';
				}

				$featured_image_url = get_the_post_thumbnail_url( get_the_ID(), '1536x1536' );

				if ( ! $featured_image_url ) {
						return '';
				}

				return (
					'<span class="l-breakout l-h-100-pc u-d-b u-p-a u-t-0 u-o-h">' .
						"<img class='c-hero__media l-w-100-pc' src='$featured_image_url' alt=''>" .
					'</span>'
				);
		}

		/**
		 * Shortcode to output main footer.
		 *
		 * @param array $atts
		 * @param string $content
		 * @return string
		 */

		public static function shortcode( $atts, $content ) {
				return self::render();
		}

} // End Hero_Image
