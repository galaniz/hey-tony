<?php
/**
 * Render main footer
 *
 * @package hey-tony
 */

namespace HT\Common\Render;

/**
 * Imports
 */

use HT\HT as HT;
use HT\Common\Render\Swoop;
use Formation\Pub\Nav_Walker;

/**
 * Class
 */

class Main_Footer {

	/**
	 * Output main footer.
	 *
	 * @return string
	 */

	public static function render() {

		/* Widgets output */

		$widgets = wp_get_sidebars_widgets();
		$allowed = ['avada-footer-widget-1', 'avada-footer-widget-2', 'avada-footer-widget-3', 'ht-footer-widget-4'];

		$widgets_ordered = $allowed;

		foreach ( $widgets as $k => $v ) {
			$a = in_array( $k, $allowed, true );

			if ( $a ) {
				$widgets_ordered[ array_search( $k, $widgets_ordered, true ) ] = [
					'key'   => $k,
					'value' => $v,
				];
			}
		}

		$widgets_output = [];

		foreach ( $widgets_ordered as $i => $wo ) {
			$k = $wo['key'];
			$v = $wo['value'];

			if ( ! $v ) {
				continue;
			}

			$ww     = '';
			$social = false;
			$first  = 0 === $i;

			foreach ( $v as $w ) {
				$social = strpos( $w, 'social' ) !== false ? true : false;
				$wid    = wp_render_widget( $w, $k );

				if ( $social ) {
					$wid = HT::filter_social( $wid );
					$wid = "<div class='l-pt-xxxs'>$wid</div>";
				} else {
					$contact = strpos( $wid, 'mailto:' ) !== false;
					$class   = ( $contact ? 'o-accent' : 'o-underline u-c-i' ) . ' u-d-ib-a';

					if ( $first ) {
						$class .= ' l-pb-xxxs';
					}

					$wid = "<div class='$class'>$wid</div>";
				}

				$ww .= $wid;
			}

			$widgets_output[] = "<div class='" . ( $first ? 'l-mb-r-all l-flex-grow l-w-100-pc' : 'l-mb-s-all' ) . "'>$ww</div>";
		}

		/* Logo */

		$logo_info = Avada()->settings->get( 'logo' );
		$logo      = '';

		if ( $logo_info ) {
			$image = HT::get_image( $logo_info['id'], 'medium' );

			if ( $image ) {
				$src    = esc_url( $image['url'] );
				$srcset = esc_attr( $image['srcset'] );
				$sizes  = esc_attr( $image['sizes'] );
				$width  = esc_attr( $image['width'] );
				$height = esc_attr( $image['height'] );

				$logo = (
					'<a class="u-d-b" href="' . esc_url( home_url( '/' ) ) . '">' .
						'<span class="u-v-h">' . get_bloginfo( 'name' ) . ' home</span>' .
						'<div class="o-logo" data-align="center">' .
							"<img class='u-d-b' src='$src' alt='' srcset='$srcset' sizes='$sizes' width='$width' height='$height'>" .
						'</div>' .
					'</a>'
				);
			}
		}

		/* Navigation */

		$footer_nav = wp_nav_menu(
			[
				'menu'           => 'footer_navigation',
				'theme_location' => 'footer_navigation',
				'container'      => '',
				'items_wrap'     => '%3$s',
				'echo'           => false,
				'depth'          => 0,
			]
		);

		if ( $footer_nav ) {
			$footer_nav = "<div><ul class='l-flex o-underline u-c-i' data-gap='s'>$footer_nav</ul></div>";
		} else {
			$footer_nav = '';
		}

		/* Output */

		return (
			'<div class="u-p-r">' .
				Swoop::render( ['flip' => true, 'position' => 'top'] ) .
				'<div class="l-pt-r l-pb-s l-pt-l-l l-pb-r-l t-text-light">' .
					'<div class="l-flex l-pb-s">' .
						$logo .
					'</div>' .
					'<div class="l-pb-s">' .
						'<div class="l-flex" data-gap="r" data-justify="def" data-row="m" data-wrap-m data-col>' .
							implode( '', $widgets_output ) .
						'</div>' .
					'</div>' .
					'<div class="l-flex p-xs" data-gap="s" data-gap-l="r" data-align="center" data-justify="def" data-wrap>' .
						'<div>' .
							'<p>&copy; ' . get_bloginfo( 'name' ) . ' ' . gmdate( 'Y' ) . '</p>' .
						'</div>' .
						$footer_nav .
					'</div>' .
				'</div>' .
			'</div>'
		);
	}

	/**
	 * Shortcode to output main footer.
	 *
	 * @param array $atts
	 * @param string $content
	 * @return string
	 */

	public static function shortcode( $atts, $content ) {
		return self::render();
	}

} // End Main_Footer
