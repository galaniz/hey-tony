<?php
/**
 * Objects - Testimonial
 *
 * @package hey-tony
 */
namespace HT\Objects\Testimonial;

/* Imports */

use HT\HT as HT;

/**
 * Class - render swoop shape
 */
class Testimonial {
	/**
	 * Output testimonial
	 *
	 * @return string
	 */
	public static function render( $args = [] ) {
		$args = array_merge(
			[
				'text'     => '',
				'title'    => '',
				'subtitle' => '',
				'media_id' => 0,
				'center'   => true,
				'large'    => true,
			],
			$args
		);

		[
			'text'     => $text,
			'title'    => $title,
			'subtitle' => $subtitle,
			'media_id' => $media_id,
			'center'   => $center,
			'large'    => $large,
		] = $args;

		$media_id = (int) $media_id;
		$center   = filter_var( $center, FILTER_VALIDATE_BOOLEAN );
		$large    = filter_var( $large, FILTER_VALIDATE_BOOLEAN );

		/* Text and title required */

		if ( ! $text || ! $title ) {
			return '';
		}

		$text = wpautop( $text );

		/* Featured image */

		$image = '';

		if ( $media_id ) {
			$image = HT::get_image( $media_id, 'thumbnail' );

			if ( $image ) {
				$src    = esc_url( $image['url'] );
				$alt    = esc_attr( $image['alt'] );
				$width  = esc_attr( $image['width'] );
				$height = esc_attr( $image['height'] );

				$image = (
					'<div class="l-shrink-0">' .
						'<div class="l-wd-l">' .
							'<div class="l-relative l-overflow-hidden l-ar-1-1 b-radius-full">' .
								"<img class='l-absolute l-top-0 l-left-0 l-wd-full l-ht-full e-scale__item' src='$src' alt='$alt' width='$width' height='$height' loading='lazy'>" .
							'</div>' .
						'</div>' .
					'</div>'
				);
			} else {
				$image = '';
			}
		}

		/* Output */

		/* phpcs:ignore */
		$quote_mark = file_get_contents( get_stylesheet_directory() . '/assets/svg/quote-mark.svg' ); // Ignore: local path

		return (
			'<figure class="l-flex l-col ' . ( $center ? 'l-align-center' : 'l-align-start-m' ) . '">' .
				"<div class='l-pb-xs l-pb-s-m t-primary-base t-ht-tight'>$quote_mark</div>" .
				"<blockquote class='l-pb-xs l-pb-xs-l l-m-0 " . ( $center ? 't-align-center' : 't-align-left-m' ) . ' p' . ( $large ? '-l' : '' ) . "'>$text</blockquote>" .
				'<figcaption class="l-mt-auto">' .
					'<div class="l-flex l-align-center l-gm-2xs">' .
						$image .
						'<div class="p-s' . ( ! $image && $center ? ' t-align-center' : '' ) . '">' .
							"<p class='t-wt-bold t-ht-snug " . ( $subtitle ? 'l-mb-5xs' : 'l-m-0' ) . "'>$title</p>" .
							( $subtitle ? "<p class='t-ht-snug l-m-0'>$subtitle</p>" : '' ) .
						'</div>' .
					'</div>' .
				'</figcaption>' .
			'</figure>'
		);
	}
}
