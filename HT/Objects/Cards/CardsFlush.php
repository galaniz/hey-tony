<?php
/**
 * Objects - Cards Flush
 *
 * @package hey-tony
 */
namespace HT\Objects\Cards;

/* Imports */

use HT\HT as HT;

/**
 * Class - render cards flush with each other
 */
class CardsFlush {
	/**
	 * Output content
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
				$pretitle_a11y = "<span class='a-hide-vis'>$pretitle_a11y</span>";
			}

			if ( $pretitle_link ) {
				$pretitle = "<a class='l-relative l-zi-2' href='$pretitle_link'>$pretitle_a11y$pretitle</a>";
			} else {
				$pretitle = "<p class='l-relative l-zi-2'>$pretitle_a11y$pretitle</p>";
			}

			$pretitle_output = (
				'<div class="e-underline-r p-s t-wt-bold l-pb-2xs l-flex">' .
					$pretitle .
				'</div>'
			);
		}

		/* Excerpt */

		if ( $excerpt ) {
			$excerpt = (
				'<div class="p-m l-pt-2xs">' .
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
					'<div class="o-flush__media l-relative l-overflow-hidden l-ar-1-1 l-zi--1 l-absolute l-top-0 l-right-0 l-bottom-0" aria-hidden="true">' .
						"<img class='l-absolute l-top-0 l-left-0 l-wd-full l-ht-full e-scale__item' src='$src' alt='$alt' srcset='$srcset' sizes='$sizes' width='$wid' height='$height' loading='lazy'>" .
					'</div>'
				);
			}
		}

		/* Link */

		$link_classes = 'e-underline-r e-underline--thick e-underline--primary';

		if ( $flush ) {
			$link_classes .= ' o-flush__link l-before';
		}

		/* Output */

		return (
			'<div class="l-grow-1 l-relative l-zi-1 e-fade e-scale">' .
				$image_output .
				$pretitle_output .
				'<div class="h3' . ( $heading_large ? '-l' : '' ) . '">' .
					"<$heading_level class='l-m-0 t-foreground-base t-inherit'>" .
						"<a href='$link' class='$link_classes'>" .
							"<span class='l-relative'>$title</span>" .
						'</a>' .
					"</$heading_level>" .
				'</div>' .
				$excerpt .
			'</div>'
		);
	}

	/**
	 * Output item
	 *
	 * @return string
	 */
	public static function render_item( $args = [] ) {
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

		/* Width */

		$width_opts = [
			100 => 'full',
			50  => '1-2',
			33  => '1-3',
		];

		$width_str = isset( $width_opts[ $width ] ) ? $width_opts[ $width ] : '1-2';

		/* Container classes */

		$classes = "l-wd-$width_str l-flex";

		if ( $class ) {
			$classes .= " $class";
		}

		/* Inner classes */

		$inner_class = 'l-flex l-relative';

		if ( $border ) {
			$inner_class .= ' o-border';
		}

		if ( $fill ) {
			$inner_class .= ' l-grow-1';
		}

		/* Output */

		return (
			"<li class='$classes'>" .
				"<div class='$inner_class'>" .
					$content .
				'</div>' .
			'</li>'
		);
	}

	/**
	 * Output cards
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

		$gap_large = $border ? ' l-gm-4xl-m' : '';
		$class     = "l-flex l-col l-row-m l-wrap-m l-gm-l$gap_large" . ( $class ? " $class" : '' );

		/* Output */

		return (
			'<div' . ( 'hidden' === $overflow ? ' class="l-overflow-hidden"' : '' ) . '>' .
				"<ul class='$class t-ls-none' role='list'>" .
					$content .
				'</ul>' .
			'</div>'
		);
	}
}
