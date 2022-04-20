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
use HT\Utils;
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
				$allowed = ['avada-footer-widget-1', 'avada-footer-widget-2', 'avada-footer-widget-3'];

				$widgets = array_filter(
						$widgets,
						function( $k ) use ( $allowed ) {
								return in_array( $k, $allowed, true );
						},
						ARRAY_FILTER_USE_KEY
				);

				$widgets_output = [];

				foreach ( $widgets as $k => $v ) {
						$ww     = '';
						$social = false;

						foreach ( $v as $w ) {
								$social = strpos( $w, 'social' ) !== false ? true : false;
								$wid    = wp_render_widget( $w, $k );

								if ( $social ) {
										$wid = Utils::filter_social( $wid );
										$wid = "<div class='l-pt-xxxs'>$wid</div>";
								} else {
										$wid = "<div class='o-accent u-d-ib-a'>$wid</div>";
								}

								$ww .= $wid;
						}

						$widgets_output[] = "<div class='l-mb-s-all'>$ww</div>";
				}

				/* Logo */

				$logo_info = Avada()->settings->get( 'logo' );
				$logo      = '';

				if ( $logo_info ) {
						$image = Utils::get_image( $logo_info['id'], 'medium' );

						if ( $image ) {
								$src    = esc_url( $image['url'] );
								$srcset = esc_attr( $image['srcset'] );
								$sizes  = esc_attr( $image['sizes'] );

								$logo = (
									'<a class="u-d-b" href="' . esc_url( home_url( '/' ) ) . '">' .
										'<span class="u-v-h">' . get_bloginfo( 'name' ) . ' home</span>' .
										'<div class="o-logo" data-align="center">' .
											"<img class='u-d-b' src='$src' alt='' srcset='$srcset' sizes='$sizes'>" .
										'</div>' .
									'</a>'
								);
						}
				}

				/* Navigation */

				$footer_nav = wp_nav_menu(
						[
							'menu'       => 'footer_navigation',
							'container'  => '',
							'items_wrap' => '%3$s',
							'echo'       => false,
							'depth'      => 0,
						]
				);

				if ( $footer_nav ) {
						$footer_nav = "<div><ul class='l-flex o-underline' data-gap='s'>$footer_nav</ul></div>";
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
