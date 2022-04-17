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
							'title'         => '',
							'link'          => '',
							'excerpt'       => '',
							'media_id'      => 0,
							'pretitle'      => '',
							'pretitle_link' => '',
							'index'         => 0,
							'width'         => 33,
							'theme'         => 'background-base',
							'small'         => false,
						],
						$args
				);

				[
					'title'         => $title,
					'link'          => $link,
					'excerpt'       => $excerpt,
					'media_id'      => $media_id,
					'pretitle'      => $pretitle,
					'pretitle_link' => $pretitle_link,
					'index'         => $index,
					'width'         => $width,
					'theme'         => $theme,
					'small'         => $small,
				] = $args;

				$first_two = 0 === $index || 1 === $index;

				if ( $first_two ) {
						$width = 50;
				}

				/* Title required */

				if ( ! $title ) {
						return '';
				}

				$title_id    = uniqid();
				$title_h     = 'h2';
				$title_class = 'o-overlap-v__h t-foreground-base u-c-i';

				if ( $first_two ) {
						$title_class .= ' h3-l l-mb-s';
				} elseif ( $small ) {
						$title_class .= ' h5';
						$title_h      = 'h3';
				} else {
						$title_class .= ' h4';
				}

				/* Pretitle */

				$pretitle_output = '';

				if ( $pretitle ) {
						if ( $pretitle_link ) {
								$pretitle = "<a class='l-m-0 u-p-r u-zi-2' href='$pretitle_link'>$pretitle</a>";
						} else {
								$pretitle = "<p class='l-m-0 u-p-r u-zi-2'>$pretitle</p>";
						}

						$pretitle_output = (
							'<div class="o-underline-r p-' . ( $first_two ? 's' : 'xs' ) . ' u-fw-b l-pb-xxxs l-flex">' .
								$pretitle .
							'</div>'
						);
				}

				/* Excerpt */

				if ( $excerpt ) {
						$excerpt = (
							'<div class="p-m t-foreground-base u-p-r u-zi-2">' .
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

				/* Link */

				$link_output = '';

				if ( $link ) {
						$link_output = (
							"<a class='o-underline-rs u-oo-s u-p-a u-b-0 u-t-0 u-l-0 u-r-0 u-zi-1 u-d-b' href='$link' aria-labelledby='$title_id'></a>"
						);
				}

				/* Output */

				return (
					"<li class='l-w-$width-pc'>" .
						( ! $small ? '<div class="l-pb-xxxs l-pb-xs-l">' : '' ) .
							'<div class="o-overlap-v l-flex u-p-r" data-theme="' . $theme . '" data-col>' .
								$link_output .
								'<div class="o-overlap-v__fg u-or-2">' .
									$pretitle_output .
									"<div class='$title_class'>" .
										"<$title_h id='$title_id' class='l-m-0'><span>$title</span></$title_h>" .
									'</div>' .
									( $first_two && $excerpt ? $excerpt : '' ) .
								'</div>' .
								'<div class="u-p-r u-zi--1">' .
									'<div class="o-overlap-v__bg o-aspect-ratio" data-p="66" data-hover="scale">' .
										$image .
									'</div>' .
								'</div>' .
							'</div>' .
						( ! $small ? '</div>' : '' ) .
					'</li>'
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
						"<ul class='$class' data-gap='r' data-row='s' data-wrap-s data-col>" .
							$content .
						'</ul>' .
					'</div>'
				);
		}

} // End Cards
