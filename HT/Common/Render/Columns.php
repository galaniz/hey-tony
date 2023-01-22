<?php
/**
 * Render two column layout
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

class Columns {

	/**
	 * Output content.
	 *
	 * @return string
	 */

	public static function render_content( $args = [] ) {
		$args = array_merge(
			[
				'title'         => '',
				'link'          => '',
				'excerpt'       => '',
				'media_id'      => 0,
				'pretitle'      => '',
				'pretitle_link' => '',
				'pretitle_a11y' => '',
				'heading_level' => 'h2',
				'heading_large' => true,
				'flush'         => true,
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
			'pretitle_a11y' => $pretitle_a11y,
			'heading_level' => $heading_level,
			'heading_large' => $heading_large,
			'flush'         => $flush,
		] = $args;

		/* Title required */

		if ( ! $title || ! $link ) {
			return '';
		}

		/* Pretitle */

		$pretitle_output = '';

		if ( $pretitle ) {
			if ( $pretitle_a11y ) {
				$pretitle_a11y = "<span class='u-v-h'>$pretitle_a11y</span>";
			}

			if ( $pretitle_link ) {
				$pretitle = "<a class='u-p-r u-zi-2' href='$pretitle_link'>$pretitle_a11y$pretitle</a>";
			} else {
				$pretitle = "<p class='u-p-r u-zi-2'>$pretitle_a11y$pretitle</p>";
			}

			$pretitle_output = (
				'<div class="o-underline-r p-s u-fw-b l-pb-xxxs l-flex">' .
					$pretitle .
				'</div>'
			);
		}

		/* Excerpt */

		if ( $excerpt ) {
			$excerpt = (
				'<div class="p-m l-pt-xxxs">' .
					"<p class='l-m-0'>$excerpt</p>" .
				'</div>'
			);
		}

		/* Featured image */

		$image_output = '';

		if ( $media_id && $flush ) {
			$image = HT::get_image( $media_id, 'medium_large' );

			if ( $image ) {
				$src    = esc_url( $image['url'] );
				$srcset = esc_attr( $image['srcset'] );
				$sizes  = esc_attr( $image['sizes'] );
				$wid    = esc_attr( $image['width'] );
				$height = esc_attr( $image['height'] );
				$alt    = esc_attr( $image['alt'] );

				$image_output = (
					'<div class="o-flush__media o-aspect-ratio u-zi--1 u-p-a u-l-0 u-t-0 u-r-0 u-b-0" data-p="0" data-hover="scale" aria-hidden="true">' .
						"<img class='o-aspect-ratio__media' src='$src' alt='$alt' srcset='$srcset' sizes='$sizes' width='$wid' height='$height' loading='lazy'>" .
					'</div>'
				);
			}
		}

		/* Link */

		$link_classes = 'o-underline-r u-ul-w u-ul-c';

		if ( $flush ) {
			$link_classes .= ' o-flush__link u-tlrb-b';
		}

		/* Output */

		return (
			'<div class="l-flex-grow u-p-r u-zi-1" data-hover>' .
				$image_output .
				$pretitle_output .
				'<div class="h3' . ( $heading_large ? '-l' : '' ) . '">' .
					"<$heading_level class='l-m-0 t-foreground-base u-c-i'>" .
						"<a href='$link' class='$link_classes'>" .
							"<span class='u-p-r'>$title</span>" .
						'</a>' .
					"</$heading_level>" .
				'</div>' .
				$excerpt .
			'</div>'
		);
	}

	/**
	 * Output column.
	 *
	 * @return string
	 */

	public static function render_column( $args = [] ) {
		$args = array_merge(
			[
				'content' => '',
				'border'  => true,
				'width'   => 50,
				'fill'    => false,
				'class'   => '',
			],
			$args
		);

		[
			'content' => $content,
			'border'  => $border,
			'width'   => $width,
			'fill'    => $fill,
			'class'   => $class,
		] = $args;

		/* Content required */

		if ( ! $content ) {
			return '';
		}

		/* Output */

		$classes = "l-w-$width-pc l-flex";

		if ( $class ) {
			$classes .= " $class";
		}

		$inner_class = 'l-flex u-p-r';

		if ( $border ) {
			$inner_class .= ' o-border';
		}

		if ( $fill ) {
			$inner_class .= ' l-flex-grow';
		}

		return (
			"<li class='$classes'>" .
				"<div class='$inner_class'>" .
					$content .
				'</div>' .
			'</li>'
		);
	}

	/**
	 * Output columns.
	 *
	 * @return string
	 */

	public static function render( $args = [] ) {
		$args = array_merge(
			[
				'content'  => '',
				'border'   => true,
				'class'    => '',
				'overflow' => 'hidden',
			],
			$args
		);

		[
			'content'  => $content,
			'border'   => $border,
			'class'    => $class,
			'overflow' => $overflow,
		] = $args;

		/* Content required */

		if ( ! $content ) {
			return '';
		}

		$gap_l = $border ? 'xl' : 'l';
		$class = 'l-flex' . ( $class ? " $class" : '' );

		/* Output */

		return (
			'<div' . ( 'hidden' === $overflow ? ' class="u-o-h"' : '' ) . '>' .
				"<ul class='$class' data-gap='l' data-gap-l='$gap_l' data-row='m' data-wrap-m data-col>" .
					$content .
				'</ul>' .
			'</div>'
		);
	}

} // End Columns
