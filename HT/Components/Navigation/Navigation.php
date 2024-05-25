<?php
/**
 * Components - Navigation
 *
 * @package hey-tony
 */
namespace HT\Components\Navigation;

/* Imports */

use HT\HT as HT;
use HT\Objects\Swoop\Swoop;
use Formation\Pub\Nav_Walker;

/**
 * Class - render main navigation
 */
class Navigation {
	/**
	 * Output main navigation
	 *
	 * @return string
	 */
	public static function render() {
		/* Main navigation */

		$counter = 1;

		$nav_walker = new Nav_Walker(
			[
				'li_class'      => 'c-nav__item',
				'before_output' => function( &$obj, &$output, $depth, $args ) use ( &$counter ) {
					if ( 0 === $depth ) {
						$delay        = ( 50 * $counter ) . 'ms';
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
				'menu'           => 'main-navigation',
				'theme_location' => 'main_navigation',
				'container'      => '',
				'items_wrap'     => '%3$s',
				'echo'           => false,
				'walker'         => $nav_walker,
			]
		);

		/* Logo */

		$logo_info        = Avada()->settings->get( 'logo' );
		$sticky_logo_info = Avada()->settings->get( 'sticky_header_logo' );
		$logo             = '';

		if ( $logo_info ) {
			$image       = HT::get_image( $logo_info['id'], 'medium' );
			$sticky_logo = '';

			if ( $sticky_logo_info ) {
				$sticky_logo = '<img class="l-absolute l-left-0 l-ht-full" src="' . $sticky_logo_info['url'] . '" alt="">';
			}

			if ( $image ) {
				$src    = esc_url( $image['url'] );
				$srcset = esc_attr( $image['srcset'] );
				$sizes  = esc_attr( $image['sizes'] );
				$width  = esc_attr( $image['width'] );
				$height = esc_attr( $image['height'] );

				$logo = (
					'<div>' .
						'<a class="outline-snug l-block" href="' . esc_url( home_url( '/' ) ) . '">' .
							'<span class="a-hide-vis">' . get_bloginfo( 'name' ) . ' home</span>' .
							'<div class="o-logo l-flex l-align-center l-relative" data-size="l">' .
								"<img class='l-block' src='$src' alt='' srcset='$srcset' sizes='$sizes' width='$width' height='$height'>" .
								$sticky_logo .
							'</div>' .
						'</a>' .
					'</div>'
				);
			}
		}

		/* Output */

		return (
			'<nav aria-label="Main" data-nav-overflow="false" data-nav-overflow-all="false" data-nav-open="false">' .
				'<div class="c-nav__bg c-nav__toggle l-breakout l-ht-full-vh l-absolute"></div>' .
				'<div class="c-nav__swoop c-nav__toggle l-breakout l-ht-full-vh l-absolute">' .
					'<div class="l-ht-full l-relative">' .
						Swoop::render( ['nav' => true] ) .
					'</div>' .
				'</div>' .
				'<div class="l-flex l-align-center l-justify-between l-gm-s l-relative l-zi-1' . ( HT::$nav_light ? ' t-text-light' : '' ) . '">' .
					$logo .
					'<div class="l-flex">' .
						'<button class="c-nav__button" type="button" aria-expanded="false" aria-controls="js-nav-overflow">' .
							'<span class="c-nav-icon l-block" data-num="1">' .
								'<span class="c-nav-icon__top l-block"></span>' .
								'<span class="c-nav-icon__middle l-block"></span>' .
								'<span class="c-nav-icon__bottom l-block"></span>' .
							'</span>' .
							'<span class="a-hide-vis">Menu</span>' .
						'</button>' .
					'</div>' .
				'</div>' .
				'<div class="c-nav__overflow c-nav__toggle t-text-light l-flex l-breakout l-ht-full-vh l-absolute" id="js-nav-overflow">' .
					'<div class="fusion-builder-row fusion-row l-mt-auto l-mb-auto">' .
						"<ul class='l-mb-xs-all t-ls-none e-accent-r e-accent-r-l l-inline-block-a' role='list'>$main_nav</ul>" .
					'</div>' .
				'</div>' .
			'</nav>'
		);
	}

	/**
	 * Shortcode to output main navigation
	 *
	 * @param array $atts
	 * @param string $content
	 * @return string
	 */
	public static function shortcode( $atts, $content ) {
		return self::render();
	}
}
