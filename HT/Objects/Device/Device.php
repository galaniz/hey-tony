<?php
/**
 * Objects - Device
 *
 * @package hey-tony
 */
namespace HT\Objects\Device;

/* Imports */

use HT\HT as HT;

/**
 * Class - render mobile or desktop device outlines
 */
class Device {
	/**
	 * Output device
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

			$img = "<img class='" . ( 'mobile' === $type ? ' l-absolute l-top-0 l-left-0 l-wd-full l-ht-full e-scale__item' : ' l-wd-full' ) . "' src='$src' alt='$alt' srcset='$srcset' sizes='$sizes' width='$wid' height='$height' loading='lazy'>";
		}

		/* Output */

		if ( 'mobile' === $type ) {
			return (
				'<div class="o-device l-wd-full l-relative l-overflow-hidden l-ar-9-16 l-ml-auto l-mr-auto t-bg-background-dark" data-type="mobile">' .
					'<div class="l-absolute l-top-0 l-left-0 l-right-0 l-bottom-0 l-overflow-hidden">' .
						$img .
					'</div>' .
				'</div>'
			);
		} else {
			return (
				'<div class="o-device" data-type="desktop">' .
					'<div class="l-relative l-overflow-hidden l-ar-4-100 t-bg-background-dark">' .
						'<div class="l-absolute">' .
							'<div class="l-relative l-overflow-hidden l-ar-1-1 t-bg-foreground-base b-radius-full"></div>' .
						'</div>' .
						'<div class="l-absolute">' .
							'<div class="l-relative l-overflow-hidden l-ar-1-1 t-bg-foreground-base b-radius-full"></div>' .
						'</div>' .
						'<div class="l-absolute">' .
							'<div class="l-relative l-overflow-hidden l-ar-1-1 t-bg-foreground-base b-radius-full"></div>' .
						'</div>' .
					'</div>' .
					'<div class="l-overflow-hidden" style="border-radius: 0% 0% ' . $left . '% ' . $left . '% / 0% 0% ' . $bottom . '% ' . $bottom . '%;">' .
						$img .
					'</div>' .
				'</div>'
			);
		}
	}

	/**
	 * Shortcode to output device
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
}
