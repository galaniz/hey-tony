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

use Formation\Utils;

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
										$wid = "<div class='l-pt-xxs'>$wid</div>";
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
										'<div class="o-logo" data-align="center" data-invert>' .
											"<img class='u-d-b' src='$src' alt='' srcset='$srcset' sizes='$sizes'>" .
										'</div>' .
									'</a>'
								);
						}
				}

				return (
					'<div class="l-pt-r l-pb-s l-pt-l-l l-pb-r-l t-text-light">' .
						'<div class="l-flex l-pb-s">' .
							$logo .
						'</div>' .
						'<div class="l-pb-s">' .
							'<div class="l-flex" data-gap="r" data-justify="def" data-row="m" data-col data-wrap>' .
								implode( $widgets_output, '' ) .
							'</div>' .
						'</div>' .
						'<div class="l-flex" data-gap="s" data-justify="def" data-wrap>' .
							'<div class="p-xs">' .
								'<p>&copy; ' . get_bloginfo( 'name' ) . ' ' . gmdate( 'Y' ) . '</p>' .
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
