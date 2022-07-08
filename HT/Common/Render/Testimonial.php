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

class Testimonial {

		/**
		 * Output testimonial.
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
									'<div class="l-flex-shrink-0">' .
										'<div class="l-w-r">' .
											'<div class="o-aspect-ratio" data-circle>' .
												"<img class='o-aspect-ratio__media' src='$src' alt='$alt' width='$width' height='$height' loading='lazy'>" .
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
				$quote_mark = file_get_contents( get_stylesheet_directory() . '/assets/public/svg/quote-mark.svg' ); // Ignore: local path

				return (
					'<figure class="l-flex" data-align="center"' . ( ! $center ? ' data-align-m="start"' : '' ) . ' data-col>' .
						"<div class='l-pb-xxs l-pb-xs-l t-primary-base u-lh-0'>$quote_mark</div>" .
						"<blockquote class='l-pb-xxs l-pb-xxs-l l-m-0 l-p-0 u-ta-c" . ( ! $center ? ' u-ta-l-m' : '' ) . ' u-brd-0 u-fs-n p' . ( $large ? '-l' : '' ) . "'>$text</blockquote>" .
						'<figcaption class="l-mt-auto">' .
							'<div class="l-flex" data-align="center" data-gap="s">' .
								$image .
								'<div class="p-s' . ( ! $image ? ' u-ta-c' : '' ) . '">' .
									"<p class='u-fw-b u-lh-130-pc " . ( $subtitle ? 'l-mb-xxs' : 'l-m-0' ) . "'>$title</p>" .
									( $subtitle ? "<p class='u-lh-130-pc l-m-0'>$subtitle</p>" : '' ) .
								'</div>' .
							'</div>' .
						'</figcaption>' .
					'</figure>'
				);
		}

} // End Testimonial
