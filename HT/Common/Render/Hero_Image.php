<?php
/**
 * Render hero background image
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

class Hero_Image {

		/**
		 * Output hero background image.
		 *
		 * @return string
		 */

		public static function render() {
				if ( is_post_type_archive() || is_archive() || is_home() ) {
						return '';
				}

				$media_id = get_post_thumbnail_id( get_the_ID() );
				$image    = '';

				if ( $media_id ) {
						$image = Utils::get_image( $media_id, '1536x1536' );

						if ( $image ) {
								$src    = esc_url( $image['url'] );
								$srcset = esc_attr( $image['srcset'] );
								$sizes  = esc_attr( $image['sizes'] );
								$alt    = esc_attr( $image['alt'] );

								$image = "<img class='c-hero__media l-w-100-pc' src='$src' alt='$alt' srcset='$srcset' sizes='$sizes'>";
						}
				} else {
						return '';
				}

				return (
					'<span class="l-breakout l-h-100-pc u-d-b u-p-a u-t-0 u-o-h">' .
						$image .
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
