<?php
/**
 * Components - Hero Image
 *
 * @package hey-tony
 */
namespace HT\Components\Hero;

/* Imports */

use HT\HT as HT;

/**
 * Class - render hero background image
 */
class HeroImage {
	/**
	 * Output hero background image
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
			$image = HT::get_image( $media_id, '1536x1536' );

			if ( $image ) {
				$src    = esc_url( $image['url'] );
				$srcset = esc_attr( $image['srcset'] );
				$sizes  = esc_attr( $image['sizes'] );
				$width  = esc_attr( $image['width'] );
				$height = esc_attr( $image['height'] );
				$alt    = esc_attr( $image['alt'] );

				$image = "<img class='c-hero__media l-wd-full' src='$src' alt='$alt' srcset='$srcset' sizes='$sizes' width='$width' height='$height'>";
			}
		} else {
			return '';
		}

		return (
			'<span class="l-breakout l-ht-full l-block l-absolute l-top-0 l-overflow-hidden">' .
				$image .
			'</span>'
		);
	}

	/**
	 * Shortcode to output hero background image
	 *
	 * @param array $atts
	 * @param string $content
	 * @return string
	 */
	public static function shortcode( $atts, $content ) {
		return self::render();
	}
}
