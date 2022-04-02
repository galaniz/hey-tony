<?php
/**
 * Render swoop shape
 *
 * @package hey-tony
 */

namespace HT\Common\Render;

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

				if ( $featured_image && ( ! is_post_type_archive() && ! is_home() ) ) {
						$featured_image_url = get_the_post_thumbnail_url( get_the_ID(), '1536x1536' );

						if ( $featured_image_url ) {
								$swoop = (
									'<svg class="c-hero__swoop u-p-a u-t-0 u-l-0" aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">' .
										'<defs>' .
											'<clipPath id="htsw" clipPathUnits="objectBoundingBox" transform="scale(0.02, 0.10285737796)">' .
												'<path d="M50,0C26.937.67082,10.6975,3.029,0,9.7222V0Z" />' .
											'</clipPath>' .
										'</defs>' .
										'<switch>' .
											'<foreignObject x="0" y="0" width="100%" height="100%" clip-path="url(#htsw)">' .
												"<img class='u-w-100' src='$featured_image_url' alt=''>" .
											'</foreignObject>' .
										'</switch>' .
									'</svg>' .
									$swoop
								);
						}
				}

				/* Output */

				$classes = ['u-p-a', 'u-svg-100'];
				$attr    = [];

				if ( $nav ) {
						$classes[] = 'u-h-100 u-r-0';
				} else {
						$classes[] = 'o-swoop l-breakout';
						$classes[] = 'top' === $position ? 'u-t-0' : 'u-b-0';

						$attr[] = 'data-size="' . ( is_front_page() ? 'r' : 's' ) . '"';
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
		 * Shortcode to output main footer.
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
