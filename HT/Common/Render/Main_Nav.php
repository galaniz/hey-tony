<?php
/**
 * Render main navigation
 *
 * @package hey-tony
 */

namespace HT\Common\Render;

/**
 * Imports
 */

use Formation\Pub\Nav_Walker;
use Formation\Utils;

/**
 * Class
 */

class Main_Nav {

		/**
		 * Output main navigation.
		 *
		 * @return string
		 */

		public static function render() {
				/* Main navigation */

				$counter = 1;

				$nav_walker = new Nav_Walker(
						[
							'li_class'      => 'c-nav__item h1-s',
							'before_output' => function( &$obj, &$output, $depth, $args ) use ( &$counter ) {
								if ( 0 === $depth ) {

										$delay        = ( 150 * $counter ) . 'ms';
										$obj->li_attr = " style='--ht-nav-item-delay:$delay'";

										$counter++;
								} else {
										$obj->li_attr = '';
								}
							},
						]
				);

				$main_nav = wp_nav_menu(
						[
							'menu'       => 'main-navigation',
							'container'  => '',
							'items_wrap' => '%3$s',
							'echo'       => false,
							'walker'     => $nav_walker,
						]
				);

				/* Logo */

				$logo_info        = Avada()->settings->get( 'logo' );
				$sticky_logo_info = Avada()->settings->get( 'sticky_header_logo' );
				$logo             = '';

				if ( $logo_info ) {
						$image       = Utils::get_image( $logo_info['id'], 'medium' );
						$sticky_logo = '';

						if ( $sticky_logo_info ) {
								$sticky_logo = '<img class="u-p-a u-l-0 u-h-100" src="' . $sticky_logo_info['url'] . '" alt="">';
						}

						if ( $image ) {
								$src    = esc_url( $image['url'] );
								$srcset = esc_attr( $image['srcset'] );
								$sizes  = esc_attr( $image['sizes'] );

								$logo = (
									'<div>' .
										'<a class="u-d-b" href="' . esc_url( home_url( '/' ) ) . '">' .
											'<span class="u-v-h">' . get_bloginfo( 'name' ) . ' home</span>' .
											'<div class="o-logo l-flex u-p-r" data-align="center">' .
												"<img class='u-d-b' src='$src' alt='' srcset='$srcset' sizes='$sizes'>" .
												$sticky_logo .
											'</div>' .
										'</a>' .
									'</div>'
								);
						}
				}

				return (
					'<nav>' .
						'<div class="c-nav__bg c-nav__full c-nav__toggle u-p-a"></div>' .
						'<div class="c-nav__swoop c-nav__full c-nav__toggle u-p-a">' .
							'<div class="u-h-100 u-p-r">' .
								'<svg class="u-h-100 u-p-a u-r-0" preserveAspectRatio="none" viewBox="0 0 280 900" fill="none" xmlns="http://www.w3.org/2000/svg">' .
									'<path d="M0 0C19.3197 415.134 87.235 707.445 280 900H0.00011464L0 0Z" fill="#202020"></path>' .
								'</svg>' .
							'</div>' .
						'</div>' .
						'<div class="l-flex u-p-r u-z-1" data-gap="r" data-align="center" data-justify="def">' .
							$logo .
							'<div class="l-flex">' .
								'<button class="c-nav__button" type="button" aria-expanded="false" aria-controls="js-nav-overflow">' .
									'<div class="c-nav-icon" data-num="1">' .
										'<div class="c-nav-icon__top"></div>' .
										'<div class="c-nav-icon__middle"></div>' .
										'<div class="c-nav-icon__bottom"></div>' .
									'</div>' .
									'<div class="u-v-h">Menu</div>' .
								'</button>' .
							'</div>' .
						'</div>' .
						'<div class="c-nav__overflow c-nav__toggle c-nav__full l-flex u-p-a" id="js-nav-overflow">' .
							'<div class="fusion-builder-row fusion-row l-mt-auto l-mb-auto">' .
								"<ul class='l-mb-m-all'>$main_nav</ul>" .
							'</div>' .
						'</div>' .
					'</nav>'
				);
		}

		/**
		 * Shortcode to output main navigation.
		 *
		 * @param array $atts
		 * @param string $content
		 * @return string
		 */

		public static function shortcode( $atts, $content ) {
				return self::render();
		}

} // End Main_Nav
