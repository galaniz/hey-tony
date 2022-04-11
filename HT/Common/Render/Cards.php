<?php
/**
 * Render card layout
 *
 * @package hey-tony
 */

namespace HT\Common\Render;

/**
 * Imports
 */

use HT\Utils;

/**
 * Class
 */

class Cards {

		/**
		 * Output card.
		 *
		 * @return string
		 */

		public static function render_card( $args = [] ) {
				$args = array_merge(
						[
							'title'    => '',
							'link'     => '',
							'excerpt'  => '',
							'media_id' => 0,
							'pretitle' => '',
							'index'    => 0,
							'width'    => 33,
						],
						$args
				);

				[
					'title'    => $title,
					'link'     => $link,
					'excerpt'  => $excerpt,
					'media_id' => $media_id,
					'pretitle' => $pretitle,
					'index'    => $index,
					'width'    => $width,
				] = $args;

				$first_two = 0 === $index || 1 === $index;

				if ( $first_two ) {
						$width = 50;
				}

				/* Title required */

				if ( ! $title ) {
						return '';
				}

				/* Pretitle */

				$pretitle_output = '';

				if ( $pretitle ) {
						$pretitle_output = (
							'<div class="p-' . ( $first_two ? 's' : 'xs' ) . ' u-fw-b l-pb-xxxs">' .
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
						$image = Utils::get_image( $media_id, $first_two ? 'large' : 'medium_large' );

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
					"<div class='l-w-$width-pc'>" .
						'<div class="l-pb-xxxs l-pb-xs-l">' .
							( $link ? "<a class='l-overlap-v o-underline-r u-d-b' href='$link'>" : '' ) .
								'<div class="o-aspect-ratio" data-p="66" data-hover="scale">' .
									$image .
								'</div>' .
								'<div class="u-p-r">' .
									$pretitle_output .
									'<div class="' . ( $first_two ? 'h3-l l-mb-s' : 'h4' ) . ' t-foreground-base u-c-i">' .
										"<h2 class='l-m-0'><span>$title</span></h2>" .
									'</div>' .
									( $first_two && $excerpt ? $excerpt : '' ) .
								'</div>' .
							( $link ? '</a>' : '' ) .
						'</div>' .
					'</div>'
				);
		}

		/**
		 * Output cards.
		 *
		 * @return string
		 */

		public static function render( $args = [] ) {
				$args = array_merge(
						[
							'content' => '',
							'class'   => '',
						],
						$args
				);

				[
					'content' => $content,
					'class'   => $class,
				] = $args;

				/* Content required */

				if ( ! $content ) {
						return '';
				}

				$class = 'l-flex l-w-all' . ( $class ? " $class" : '' );

				/* Output */

				return (
					'<div>' .
						"<div class='$class' data-gap='r' data-row='s' data-wrap='s' data-col>" .
							$content .
						'</div>' .
					'</div>'
				);
		}

} // End Cards
