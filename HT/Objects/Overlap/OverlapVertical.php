<?php
/**
 * Objects - Overlap Vertical
 *
 * @package hey-tony
 */
namespace HT\Objects\Overlap;

/* Imports */

use HT\HT as HT;

/**
 * Class - render vertcical overlap image text card.
 */
class OverlapVertical {
	/**
	 * Output card.
	 *
	 * @param array $args {
	 *  @type string $title
	 *  @type string $link
	 *  @type string $excerpt
	 *  @type string $media_id
	 *  @type string $pretitle
	 *  @type string $pretitle_link
	 *  @type string $pretitle_a11y
	 *  @type string $heading_level
	 *  @type int    $index
	 *  @type int    $width
	 *  @type string $theme
	 *  @type bool   $small
	 *  @type string $li_class
	 * }
	 * @return string
	 */
	public static function render_item( $args = [] ) {
		/* Defaults */

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
				'index'         => 0,
				'width'         => 33,
				'theme'         => 'background-base',
				'small'         => false,
				'li_class'      => '',
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
			'index'         => $index,
			'width'         => $width,
			'theme'         => $theme,
			'small'         => $small,
			'li_class'      => $li_class,
		] = $args;

		$first_two = 0 === $index || 1 === $index;

		/* Container class */

		if ( $li_class ) {
			$li_class = " $li_class";
		}

		/* Width */

		if ( $first_two && 33 === $width ) {
			$width = 50;
		}

		$width_opts = [
			100 => 'full',
			50  => '1-2',
			33  => '1-3',
		];

		$width_str = isset( $width_opts[ $width ] ) ? $width_opts[ $width ] : '1-3';

		/* Title required */

		if ( ! $title ) {
			return '';
		}

		if ( $link ) {
			$title = "<a href='$link' class='l-before'><span class='l-relative'>$title</span></a>";
		}

		$title_class = 't-foreground-base e-underline--thick t-inherit';

		if ( $first_two ) {
			$title_class .= ' h3-l l-mb-3xs';
		} elseif ( $small ) {
			$title_class .= ' h5';
		} else {
			$title_class .= ' h4';
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
				$pretitle = "<p class='l-m-0 l-relative l-zi-2'>$pretitle_a11y$pretitle</p>";
			}

			$pretitle_output = (
				'<div class="o-overlap-v__p e-underline-r t-' . ( $first_two ? 's' : 'xs' ) . ' t-wt-bold l-pb-2xs l-flex">' .
					$pretitle .
				'</div>'
			);
		} else {
			$title_class .= ' o-overlap-v__p';
		}

		/* Excerpt */

		if ( $excerpt ) {
			$excerpt = (
				'<div class="t-s t-foreground-base l-relative l-zi-2">' .
					"<p class='l-m-0'>$excerpt</p>" .
				'</div>'
			);

			$title_class .= ' l-mb-4xs';
		}

		/* Featured image */

		$image = '<div class="l-absolute l-top-0 l-left-0 l-wd-full l-ht-full e-scale__item"></div>';

		if ( $media_id ) {
			$image = HT::get_image( $media_id, $first_two ? 'large' : 'medium_large' );

			if ( $image ) {
				$src    = esc_url( $image['url'] );
				$srcset = esc_attr( $image['srcset'] );
				$sizes  = esc_attr( $image['sizes'] );
				$wid    = esc_attr( $image['width'] );
				$height = esc_attr( $image['height'] );
				$alt    = esc_attr( $image['alt'] );

				$image = "<img class='l-absolute l-top-0 l-left-0 l-wd-full l-ht-full e-scale__item' src='$src' alt='$alt' srcset='$srcset' sizes='$sizes' width='$wid' height='$height' loading='lazy'>";
			}
		}

		/* Output */

		return (
			( $width ? "<li class='l-wd-$width_str$li_class'>" : '' ) .
				( ! $small ? '<div class="l-pb-2xs l-pb-s-m">' : '' ) .
					'<div class="o-overlap-v l-flex l-col l-relative e-scale e-underline--primary" data-theme="' . $theme . '">' .
						'<div class="o-overlap-v__fg l-order-2">' .
							$pretitle_output .
							"<div class='$title_class'>" .
								"<$heading_level class='l-m-0 e-underline-r'>$title</$heading_level>" .
							'</div>' .
							$excerpt .
						'</div>' .
						'<div class="l-relative l-zi--1">' .
							'<div class="o-overlap-v__bg l-relative l-overflow-hidden l-ar-3-2">' .
								$image .
							'</div>' .
						'</div>' .
					'</div>' .
				( ! $small ? '</div>' : '' ) .
			( $width ? '</li>' : '' )
		);
	}

	/**
	 * Output cards.
	 *
	 * @param array $args {
	 *  @type string $content
	 *  @type string $class
	 *  @type bool   $flex
	 * }
	 * @return string
	 */
	public static function render( $args = [] ) {
		/* Defaults */

		$args = array_merge(
			[
				'content' => '',
				'class'   => '',
				'flex'    => true,
			],
			$args
		);

		[
			'content' => $content,
			'class'   => $class,
			'flex'    => $flex,
		] = $args;

		/* Content required */

		if ( ! $content ) {
			return '';
		}

		$class =
			( $flex ? 'l-flex l-col l-row-s l-wrap-s l-gm-s' : 't-cols' ) .
			( $class ? " $class" : '' );

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
