<?php
/**
 * Objects - Overlap
 *
 * @package hey-tony
 */
namespace HT\Objects\Overlap;

/* Imports */

use HT\HT as HT;

/**
 * Class - render overlap image text layout
 */
class Overlap {
	/**
	 * Output overlap layout item
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
				'pretitle_a11y' => '',
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
			'pretitle_a11y' => $pretitle_a11y,
			'heading_level' => $heading_level,
		] = $args;

		/* Title required */

		if ( ! $title ) {
			return '';
		}

		if ( $title ) {
			$title = (
				"<a href='$link' class='o-overlap__link l-before e-accent-r e-accent-r-m' data-theme='primary-base'>" .
					"<span class='l-zi-1'>$title</span>" .
				'</a>'
			);
		}

		/* Pretitle */

		$pretitle_output = '';

		if ( $pretitle ) {
			if ( $pretitle_a11y ) {
				$pretitle_a11y = "<span class='a-hide-vis'>$pretitle_a11y</span>";
			}

			if ( $pretitle_link ) {
				$pretitle = "<a class='o-overlap__pre l-relative l-zi-2' href='$pretitle_link'>$pretitle_a11y$pretitle</a>";
			} else {
				$pretitle = "<p class='l-m-0 l-relative l-zi-2'>$pretitle_a11y$pretitle</p>";
			}

			$pretitle_output = (
				'<div class="p-s t-wt-bold l-pb-2xs e-underline-r l-flex">' .
					$pretitle .
				'</div>'
			);
		}

		/* Excerpt */

		if ( $excerpt ) {
			$excerpt = (
				'<div class="p-m t-foreground-base l-relative l-zi-2">' .
					"<p class='l-m-0'>$excerpt</p>" .
				'</div>'
			);
		}

		/* Featured image */

		$image = '<div class="l-absolute l-top-0 l-left-0 l-wd-full l-ht-full e-scale__item"></div>';

		if ( $media_id ) {
			$image = HT::get_image( $media_id, '1536x1536' );

			if ( $image ) {
				$src    = esc_url( $image['url'] );
				$srcset = esc_attr( $image['srcset'] );
				$sizes  = esc_attr( $image['sizes'] );
				$width  = esc_attr( $image['width'] );
				$height = esc_attr( $image['height'] );
				$alt    = esc_attr( $image['alt'] );

				$image = "<img class='l-absolute l-top-0 l-left-0 l-wd-full l-ht-full e-scale__item' src='$src' alt='$alt' srcset='$srcset' sizes='$sizes' width='$width' height='$height' loading='lazy'>";
			}
		}

		/* Output */

		return (
			'<li class="o-overlap">' .
				'<div class="l-flex l-col l-relative e-scale" data-scale="slow">' .
					'<div class="o-overlap__fg l-order-2">' .
						'<div class="o-overlap__text l-wd-full l-pt-m l-pt-l-m l-relative l-before l-zi-1">' .
							$pretitle_output .
							'<div class="h2-l l-pb-2xs t-foreground-base t-inherit">' .
								"<$heading_level class='o-overlap__heading l-m-0'>$title</$heading_level>" .
							'</div>' .
							$excerpt .
						'</div>' .
					'</div>' .
					"<div class='o-overlap__bg l-wd-full l-relative l-zi--1'>" .
						'<div class="o-overlap__ar l-relative l-overflow-hidden l-ar-3-2">' .
							$image .
						'</div>' .
					'</div>' .
				'</div>' .
			'</li>'
		);
	}

	/**
	 * Output overlap layout
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

		$class = 'l-flex l-col l-gm-l l-gm-4xl-m' . ( $class ? " $class" : '' );

		/* Output */

		return (
			'<div>' .
				"<ul class='$class t-ls-none' role='list'>" .
					$content .
				'</ul>' .
			'</div>'
		);
	}
}
