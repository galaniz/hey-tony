<?php
/**
 * Render swoop shape
 *
 * @package hey-tony
 */

namespace HT\Common\Render;

/**
 * Imports
 */

use HT\HT as HT;

/**
 * Class
 */

class Swoop {

		/**
		 * Output swoop.
		 *
		 * @return string
		 */

		public static function render( $args = [] ) {
				$args = array_merge(
						[
							'flip'           => false,
							'position'       => 'bottom',
							'featured_image' => false,
							'nav'            => false,
						],
						$args
				);

				/* Destructure */

				[
					'flip'           => $flip,
					'position'       => $position,
					'featured_image' => $featured_image,
					'nav'            => $nav,
				] = $args;

				$flip           = filter_var( $flip, FILTER_VALIDATE_BOOLEAN );
				$nav            = filter_var( $nav, FILTER_VALIDATE_BOOLEAN );
				$featured_image = filter_var( $featured_image, FILTER_VALIDATE_BOOLEAN );

				/* Svg */

				$swoop = (
					'<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" viewBox="0 0 50 9.7222">' .
						'<path d="M50,0C26.937.67082,10.6975,3.029,0,9.7222V0Z" fill="currentColor" />' .
					'</svg>'
				);

				/* Svg + featured image */

				if ( $featured_image && ( ! is_post_type_archive() && ! is_archive() && ! is_home() ) ) {
						$media_id = get_post_thumbnail_id( get_the_ID() );
						$image    = '';

						if ( $media_id ) {
								$image = HT::get_image( $media_id, '1536x1536' );

								if ( $image ) {
										$src    = esc_url( $image['url'] );
										$srcset = esc_attr( $image['srcset'] );
										$sizes  = esc_attr( $image['sizes'] );
										$width  = esc_attr( $image['width'] );
										$height = esc_attr( $image['height'] );

										$image = "<img class='l-w-100-pc' src='$src' alt='' srcset='$srcset' sizes='$sizes' width='$width' height='$height'>";
								}
						}

						if ( $image ) {
								$swoop = (
									'<svg class="c-hero__swoop u-p-a u-t-0 u-l-0" aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">' .
										'<defs>' .
											'<clipPath id="htsw" clipPathUnits="objectBoundingBox" transform="scale(0.02, 0.10285737796)">' .
												'<path d="M50,0C26.937.67082,10.6975,3.029,0,9.7222V0Z" />' .
											'</clipPath>' .
										'</defs>' .
										'<switch>' .
											'<foreignObject x="0" y="0" width="100%" height="100%" clip-path="url(#htsw)">' .
												$image .
											'</foreignObject>' .
										'</switch>' .
									'</svg>' .
									$swoop
								);
						}
				}

				/* Output */

				$classes = ['u-p-a', 'u-svg-100-pc'];
				$attr    = [];

				if ( $nav ) {
						$classes[] = 'l-h-100-pc u-r-0';
				} else {
						$classes[] = 'o-swoop l-breakout';
						$classes[] = 'top' === $position ? 'u-t-0' : 'u-b-0';
				}

				if ( $flip ) {
						$attr[] = 'data-flip';
				}

				$classes = implode( ' ', $classes );
				$attr    = implode( ' ', $attr );
				$attr    = $attr ? " $attr" : '';

				$swoop = "<div class='$classes'$attr>$swoop</div>";

				return $swoop;
		}

		/**
		 * Shortcode to output swoop.
		 *
		 * @param array $atts
		 * @param string $content
		 * @return string
		 */

		public static function shortcode( $atts, $content ) {
				$atts = shortcode_atts(
						[
							'flip'           => false,
							'position'       => 'bottom',
							'featured_image' => false,
						],
						$atts,
						'ht-swoop'
				);

				return self::render( $atts );
		}

} // End Swoop
