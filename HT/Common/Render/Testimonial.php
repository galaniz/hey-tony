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

use Formation\Utils;

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
							'id'     => 0,
							'center' => true,
							'large'  => true,
						],
						$args
				);

				[
					'id'     => $id,
					'center' => $center,
					'large'  => $large,
				] = $args;

				$id       = (int) $id;
				$center   = filter_var( $center, FILTER_VALIDATE_BOOLEAN );
				$text     = '';
				$title    = '';
				$subtitle = '';
				$media_id = 0;

				/* Get data */

				if ( $id ) {
						$q = new \WP_Query(
								[
									'post_type' => 'testimonial',
									'p'         => $id,
								]
						);

						if ( $q->have_posts() ) {
								while ( $q->have_posts() ) {
										$q->the_post();

										$id       = get_the_ID();
										$text     = get_the_content();
										$title    = get_the_title();
										$subtitle = get_field( 'subtitle', $id );
										$media_id = get_post_thumbnail_id( $id );
								}

								wp_reset_postdata();
						}
				} else {
						$id       = get_the_ID();
						$text     = get_the_content();
						$title    = get_the_title();
						$subtitle = get_field( 'subtitle', $id );
						$media_id = get_post_thumbnail_id( $id );
				}

				/* Text and title required */

				if ( ! $text || ! $title ) {
						return '';
				}

				$text = wpautop( $text );

				/* Featured image */

				$image = '';

				if ( $media_id ) {
						$image = Utils::get_image( $media_id, 'thumbnail' );

						if ( $image ) {
								$src = esc_url( $image['url'] );
								$alt = esc_attr( $image['alt'] );

								$image = (
									'<div class="l-flex-shrink-0">' .
										'<div class="l-w-80">' .
											'<div class="o-aspect-ratio" data-circle>' .
												"<img class='o-aspect-ratio__media' src='$src' alt='$alt'>" .
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
					'<figure class="l-flex" data-align="center" data-align-m="start" data-col>' .
						"<div class='l-pb-xxs l-pb-xxs-l t-primary-base u-lh-0'>$quote_mark</div>" .
						"<blockquote class='l-pb-xxs l-pb-xxs-l l-m-0 l-p-0 u-ta-c u-ta-l-m u-b-0 u-fs-n p" . ( $large ? '-l' : '' ) . "'>$text</blockquote>" .
						'<figcaption class="l-mt-auto">' .
							'<div class="l-flex" data-align="center" data-gap="s">' .
								$image .
								'<div class="p-s">' .
									"<p class='u-lh-130 " . ( $subtitle ? 'l-mb-xxs' : 'l-m-0' ) . "'>$title</p>" .
									( $subtitle ? "<p class='u-fw-b u-lh-130 l-m-0'>$subtitle</p>" : '' ) .
								'</div>' .
							'</div>' .
						'</figcaption>' .
					'</figure>'
				);
		}

		/**
		 * Shortcode to output testimonial.
		 *
		 * @param array $atts
		 * @param string $content
		 * @return string
		 */

		public static function shortcode( $atts, $content ) {
				$atts = shortcode_atts(
						[
							'id'     => 0,
							'center' => true,
							'large'  => true,
						],
						$atts,
						'ht-testimonial'
				);

				return self::render( $atts );
		}

} // End Testimonial
