<?php
/**
 * Render mobile and desktop device outline
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

class Device {

		/**
		 * Output device.
		 *
		 * @return string
		 */

		public static function render( $args ) {
				$args = array_merge(
						[
							'type'   => 'mobile',
							'img_id' => 0,
						],
						$args
				);

				[
					'type'   => $type,
					'img_id' => $img_id,
				] = $args;

				/* Image required */

				if ( ! $img_id ) {
						return '';
				}

				/* Image */

				$img   = '';
				$image = HT::get_image( $img_id, 'medium_large' );

				$style = '';

				if ( $image ) {
						$src    = esc_url( $image['url'] );
						$srcset = esc_attr( $image['srcset'] );
						$sizes  = esc_attr( $image['sizes'] );
						$wid    = esc_attr( $image['width'] );
						$height = esc_attr( $image['height'] );
						$alt    = esc_attr( $image['alt'] );

						$aspect_ratio = $height / $wid;

						$base = 4;

						$left   = $aspect_ratio * $base;
						$bottom = $base;

						if ( $left > $bottom ) {
								$left   = $base / 2;
								$bottom = ( $wid / $height ) * ( $base / 2 );
						}

						$img = "<img class='" . ( 'mobile' === $type ? ' o-aspect-ratio__media' : ' l-w-100-pc' ) . "' src='$src' alt='$alt' srcset='$srcset' sizes='$sizes' width='$wid' height='$height' loading='lazy'>";
				}

				/* Output */

				if ( 'mobile' === $type ) {
						return (
							'<div class="o-device o-aspect-ratio l-mw-s l-m-auto t-bg-background-dark" data-p="177" data-type="mobile">' .
								'<div class="u-p-a u-t-0 u-l-0 u-r-0 u-b-0 u-o-h">' .
									$img .
								'</div>' .
							'</div>'
						);
				} else {
						return (
							'<div class="o-device" data-type="desktop">' .
								'<div class="o-aspect-ratio t-bg-background-dark" data-p="4.2">' .
									'<div class="u-p-a">' .
										'<div class="o-aspect-ratio t-bg-foreground-base u-br-100-pc"></div>' .
									'</div>' .
									'<div class="u-p-a">' .
										'<div class="o-aspect-ratio t-bg-foreground-base u-br-100-pc"></div>' .
									'</div>' .
									'<div class="u-p-a">' .
										'<div class="o-aspect-ratio t-bg-foreground-base u-br-100-pc"></div>' .
									'</div>' .
								'</div>' .
								'<div class="u-o-h" style="border-radius: 0% 0% ' . $left . '% ' . $left . '% / 0% 0% ' . $bottom . '% ' . $bottom . '%;">' .
									$img .
								'</div>' .
							'</div>'
						);
				}
		}

		/**
		 * Shortcode to output device.
		 *
		 * @param array $atts
		 * @param string $content
		 * @return string
		 */

		public static function shortcode( $atts, $content ) {
				$atts = shortcode_atts(
						[
							'type'   => 'mobile',
							'img_id' => 0,
						],
						$atts,
						'ht-device'
				);

				return self::render( $atts );
		}

} // End Device
