<?php
/**
 * Objects - Media Text
 *
 * @package hey-tony
 */
namespace HT\Objects\MediaText;

/* Imports */

use HT\HT as HT;

/**
 * Class - render media text card.
 */
class MediaText {
	/**
	 * Output media text item.
	 *
	 * @param array $args {
	 *  @type string $title
	 *  @type string $content
	 *  @type string $media_id
	 *  @type string $heading_level
	 * }
	 * @return string
	 */
	public static function render_item( $args = [] ) {
		/* Defaults */

		$args = array_merge(
			[
				'title'         => '',
				'content'       => '',
				'media_id'      => 0,
				'heading_level' => 'h2',
			],
			$args
		);

		[
			'title'         => $title,
			'content'       => $content,
			'media_id'      => $media_id,
			'heading_level' => $heading_level,
		] = $args;

		/* Title, content and media required */

		if ( ! $title || ! $media_id || ! $content ) {
			return '';
		}

		/* Content */

		$content = wpautop(
			do_shortcode(
				str_replace(
					'[fusion_button',
					'[fusion_button margin_top="0.4rem"',
					$content
				)
			) 
		);

		/* Featured image */

		$image        = HT::get_image( $media_id, 'large' );
		$image_output = '';

		if ( $image ) {
			$src    = esc_url( $image['url'] );
			$srcset = esc_attr( $image['srcset'] );
			$sizes  = esc_attr( $image['sizes'] );
			$width  = esc_attr( $image['width'] );
			$height = esc_attr( $image['height'] );
			$alt    = esc_attr( $image['alt'] );

			$image_output = "<img src='$src' alt='$alt' srcset='$srcset' sizes='$sizes' width='$width' height='$height' loading='lazy' style='border-radius:var(--ht-2xs)'>";
		}

		/* Output */

		return (
			'<li>' .
				'<div class="l-flex l-align-center l-row-m l-wrap l-gm-xs l-gm-l-m">' .
					'<div class="l-wd-full l-wd-1-2">' .
						"<$heading_level class='l-mb-3xs'>$title</$heading_level>" .
						'<div class="ht-rich-text t-m" style="--margin:var(--ht-3xs)">' .
							$content .
						'</div>' .
					'</div>' .
					'<div class="l-wd-full l-wd-1-2" style="order:-1">' .
						$image_output .
					'</div>' .
				'</div>' .
			'</li>'
		);
	}

	/**
	 * Output media text list.
	 *
	 * @param array $args {
	 *  @type string $content
	 *  @type string $class
	 * }
	 * @return string
	 */
	public static function render( $args = [] ) {
		/* Defaults */

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

		$class = 'l-flex l-col l-gm-m l-gm-xl-m' . ( $class ? " $class" : '' );

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
