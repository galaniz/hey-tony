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

use HT\Utils;

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
							'title'         => '',
							'link'          => '',
							'excerpt'       => '',
							'media_id'      => 0,
							'pretitle'      => '',
							'pretitle_link' => '',
							'heading_level' => 'h2',
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
					'heading_level' => $heading_level,
				] = $args;

				/* Title required */

				if ( ! $title ) {
						return '';
				}

				if ( $title ) {
						$title = (
							"<a href='$link' class='o-overlap__a u-tlrb-b o-accent-r o-accent-r-m' data-theme='primary-base'>" .
								"<span class='u-zi-1'>$title</span>" .
							'</a>'
						);
				}

				/* Pretitle */

				$pretitle_output = '';

				if ( $pretitle ) {
						if ( $pretitle_link ) {
								$pretitle = "<a class='o-overlap__r u-p-r u-zi-2' href='$pretitle_link'>$pretitle</a>";
						} else {
								$pretitle = "<p class='l-m-0 u-p-r u-zi-2'>$pretitle</p>";
						}

						$pretitle_output = (
							'<div class="p-s u-fw-b l-pb-xxxs o-underline-r l-flex">' .
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
						$image = Utils::get_image( $media_id, '1536x1536' );

						if ( $image ) {
								$src    = esc_url( $image['url'] );
								$srcset = esc_attr( $image['srcset'] );
								$sizes  = esc_attr( $image['sizes'] );
								$width  = esc_attr( $image['width'] );
								$height = esc_attr( $image['height'] );
								$alt    = esc_attr( $image['alt'] );

								$image = "<img class='o-aspect-ratio__media' src='$src' alt='$alt' srcset='$srcset' sizes='$sizes' width='$width' height='$height' loading='lazy'>";
						}
				}

				/* Output */

				return (
					'<li class="o-overlap">' .
						'<div class="l-flex u-p-r" data-col data-hover>' .
							'<div class="o-overlap__fg u-or-2">' .
								'<div class="l-mw-r l-pt-s l-pt-r-l u-p-r u-tlrb-b u-zi-1">' .
									$pretitle_output .
									'<div class="h2-l l-pb-xxxs t-foreground-base u-c-i">' .
										"<$heading_level class='o-overlap__h l-m-0'>$title</$heading_level>" .
									'</div>' .
									$excerpt .
								'</div>' .
							'</div>' .
							"<div class='o-overlap__bg l-mw-l u-p-r u-zi--1'>" .
								'<div class="o-aspect-ratio" data-type="overlap" data-hover="scale" data-scale="slow">' .
									$image .
								'</div>' .
							'</div>' .
						'</div>' .
					'</li>'
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

				$class = 'l-flex' . ( $class ? " $class" : '' );

				/* Output */

				return (
					'<div>' .
						"<ul class='$class' data-gap='l' data-gap-l='xl' data-col>" .
							$content .
						'</ul>' .
					'</div>'
				);
		}

} // End Overlap
