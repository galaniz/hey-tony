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
 * Class - render mobile or desktop device outlines.
 */
class Device {
	/**
	 * Shortcode handle.
	 *
	 * @var string
	 */
	public static $handle = 'ht-device';

	/**
	 * Default shortcode and render attributes.
	 *
	 * @var array {
	 *  @type string $type
	 *  @type int $img_id
	 * }
	 */
	public static $default_atts = [
		'type'   => 'mobile',
		'img_id' => 0,
	];

	/**
	 * Set shortcode action and styles.
	 *
	 * @return void
	 */
	public function __construct() {
		add_shortcode( self::$handle, [$this, 'shortcode'] );
	}

	/**
	 * Output device.
	 *
	 * @param array $atts - $default_atts
	 * @return string
	 */
	public static function render( $atts ) {
		/* Defaults */

		$atts = array_merge( self::$default_atts, $atts );

		[
			'type'   => $type,
			'img_id' => $img_id,
		] = $atts;

		/* Image required */

		if ( ! $img_id ) {
			return '';
		}

		/* Image */

		$image        = HT::get_image( $img_id, 'medium_large' );
		$image_output = '';
		$left         = 0;
		$bottom       = 0;

		if ( $image ) {
			$src    = esc_url( $image['url'] );
			$srcset = esc_attr( $image['srcset'] );
			$sizes  = esc_attr( $image['sizes'] );
			$wid    = esc_attr( $image['width'] );
			$height = esc_attr( $image['height'] );
			$alt    = esc_attr( $image['alt'] );

			$aspect_ratio = $height / $wid;
			$base         = 4;
			$left         = $aspect_ratio * $base;
			$bottom       = $base;

			if ( $left > $bottom ) {
				$left   = $base / 2;
				$bottom = ( $wid / $height ) * ( $base / 2 );
			}

			$classes = 'l-wd-full';

			if ( 'mobile' === $type ) {
				$classes = 'l-absolute l-top-0 l-left-0 l-wd-full l-ht-full e-scale__item';
			}

			$image_output = "<img class='$classes' src='$src' alt='$alt' srcset='$srcset' sizes='$sizes' width='$wid' height='$height' loading='lazy'>";
		}

		/* Output */

		if ( 'mobile' === $type ) {
			return (
				'<div class="o-device l-wd-full l-relative l-overflow-hidden l-ar-9-16 l-ml-auto l-mr-auto t-bg-background-dark" data-type="mobile">' .
					'<div class="o-device__main l-absolute l-top-0 l-left-0 l-right-0 l-bottom-0 l-overflow-hidden">' .
						$image_output .
					'</div>' .
				'</div>'
			);
		}

		return (
			'<div class="o-device" data-type="desktop">' .
				'<div class="o-device__top l-relative l-overflow-hidden l-ar-4-100 t-bg-background-dark">' .
					'<div class="o-device__dot l-absolute">' .
						'<div class="l-relative l-overflow-hidden l-ar-1-1 t-bg-foreground-base b-radius-full"></div>' .
					'</div>' .
					'<div class="o-device__dot l-absolute">' .
						'<div class="l-relative l-overflow-hidden l-ar-1-1 t-bg-foreground-base b-radius-full"></div>' .
					'</div>' .
					'<div class="o-device__dot l-absolute">' .
						'<div class="l-relative l-overflow-hidden l-ar-1-1 t-bg-foreground-base b-radius-full"></div>' .
					'</div>' .
				'</div>' .
				'<div class="o-device__main l-overflow-hidden" style="border-radius: 0% 0% ' . $left . '% ' . $left . '% / 0% 0% ' . $bottom . '% ' . $bottom . '%;">' .
					$image_output .
				'</div>' .
			'</div>'
		);
	}

	/**
	 * Shortcode to output device.
	 *
	 * @param array $atts - $default_atts
	 * @return string
	 */
	public function shortcode( $atts ) {
		$atts = shortcode_atts( self::$default_atts, $atts, self::$handle );

		return self::render( $atts );
	}
}
