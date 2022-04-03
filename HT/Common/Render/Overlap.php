<?php
/**
 * Render overlap image text layout
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

class Overlap {

		/**
		 * Output overlap layout item.
		 *
		 * @return string
		 */

		public static function render_item( $args = [] ) {
				$args = array_merge(
						[
							'title'    => '',
							'link'     => '',
							'excerpt'  => '',
							'media_id' => 0,
							'pretitle' => '',
						],
						$args
				);

				[
					'title'    => $title,
					'link'     => $link,
					'excerpt'  => $excerpt,
					'media_id' => $media_id,
					'pretitle' => $pretitle,
				] = $args;

				/* Title required */

				if ( ! $title ) {
						return '';
				}

				/* Pretitle */

				$pretitle_output = '';

				if ( $pretitle ) {
						$pretitle_output = (
							'<div class="p-s u-fw-b l-pb-xxxs">' .
								"<p class='l-m-0'>$pretitle</p>" .
							'</div>'
						);
				}

				/* Excerpt */

				if ( $excerpt ) {
						$excerpt = (
							'<div class="p-m t-foreground-base">' .
								"<p class='l-m-0'>$excerpt</p>" .
							'</div>'
						);
				}

				/* Featured image */

				$image = '<div class="o-aspect-ratio__media"></div>';

				if ( $media_id ) {
						$image = Utils::get_image( $media_id, '1536x1536' );

						if ( $image ) {
								$src    = esc_url( $image['url'] );
								$srcset = esc_attr( $image['srcset'] );
								$sizes  = esc_attr( $image['sizes'] );
								$alt    = esc_attr( $image['alt'] );

								$image = "<img class='o-aspect-ratio__media' src='$src' alt='$alt' srcset='$srcset' sizes='$sizes'>";
						}
				}

				/* Output */

				return (
					'<div class="l-overlap">' .
						( $link ? "<a class='o-accent-r o-accent-r-m' href='$link' data-theme='primary-base'>" : '' ) .
						"<div class='l-overlap__bg l-mw-l'>" .
							'<div class="o-aspect-ratio" data-type="overlap" data-hover="scale">' .
								$image .
							'</div>' .
						'</div>' .
						'<div class="l-overlap__fg l-mw-r l-pt-s l-pt-r-l u-p-r u-tlrb-b">' .
							'<div class="u-p-r">' .
								$pretitle_output .
								'<div class="h2-l l-pb-xxxs t-foreground-base u-c-i">' .
									"<h3 class='l-m-0'><span class='u-zi-1'>$title</span></h3>" .
								'</div>' .
								$excerpt .
							'</div>' .
						'</div>' .
						( $link ? '</a>' : '' ) .
					'</div>'
				);
		}

		/**
		 * Output overlap layout.
		 *
		 * @return string
		 */

		public static function render( $args = [] ) {
				$args = array_merge(
						[
							'content' => '',
						],
						$args
				);

				[
					'content' => $content,
				] = $args;

				/* Content required */

				if ( ! $content ) {
						return '';
				}

				/* Output */

				return (
					'<div>' .
						"<div class='l-flex' data-gap='l' data-gap-l='xl' data-col>" .
							$content .
						'</div>' .
					'</div>'
				);
		}

} // End Overlap
