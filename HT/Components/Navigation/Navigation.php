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
use HT\Components\Hero\Hero;
use HT\Svg\Caret\CaretSvg;
use Formation\Pub\Nav_Walker;

/**
 * Class - render main navigation.
 */
class Navigation {
	/**
	 * Set shortcode action.
	 *
	 * @return void
	 */
	public function __construct() {
		add_shortcode( 'ht-main-nav', [$this, 'shortcode'] );
	}

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
				'ul_class'      => 'c-nav__list',
				'li_class'      => 'c-nav__item',
				'a_class'       => 'c-nav__link',
				'before_link_output' => function ( &$obj, &$output, $depth, $args ) {
					$output .= "<div class='c-nav__shift l-flex l-justify-between l-align-center' data-depth='$depth'>";
				},
				'before_output' => function( &$obj, &$output, $depth, $args, $item ) use ( &$counter ) {
					$li_attr  = '';
					$li_class = 'c-nav__item';

					if ( 0 === $depth ) {
						$delay   = ( 50 * $counter ) . 'ms';
						$li_attr = " style='--ht-nav-item-delay:$delay' ";

						$counter++;
					}

					if ( $obj->has_children ) {
						$li_class .= ' o-collapsible';
						$li_attr  .= ' data-nav ';
					}

					$obj->li_attr  = $li_attr;
					$obj->li_class = $li_class;
				},
				'after_output' => function ( &$obj, &$output, $depth, $args, $item ) {
					$ul_attr  = '';
					$ul_class = 'c-nav__list';

					if ( $obj->has_children ) {
						$id      = uniqid();
						$title   = $item->title;
						$output .= (
							"<button class='c-nav__toggle o-collapsible__toggle l-flex' type='button' aria-controls='$id' aria-expanded='false' aria-label='$title submenu'>" .
								CaretSvg::render( 'down', 'c-nav__caret e-trans l-flex' ) .
							'</button>'
						);

						$ul_class .= ' o-collapsible__main no-js-collapsible t-inherit t-ls-none e-trans e-underline-r outline-snug';
						$ul_attr   = " id='$id' role='list' data-collapsible-main ";
					}

					$output .= '</div>';

					$obj->ul_attr  = $ul_attr;
					$obj->ul_class = $ul_class;
				}
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

		/* Home */

		$home_link = esc_url( home_url( '/' ) );
		$home_name = get_bloginfo( 'name' ) . ' home';

		/* Logo */

		$logo_info        = Avada()->settings->get( 'logo' );
		$sticky_logo_info = Avada()->settings->get( 'sticky_header_logo' );
		$logo             = '';

		if ( $logo_info ) {
			$image       = HT::get_image( $logo_info['id'], 'medium' );
			$sticky_logo = '';

			if ( $sticky_logo_info ) {
				$sticky_logo = (
					'<img class="l-absolute l-left-0 l-ht-full e-trans" src="' . $sticky_logo_info['url'] . '" alt="">'
				);
			}

			if ( $image ) {
				$src    = esc_url( $image['url'] );
				$srcset = esc_attr( $image['srcset'] );
				$sizes  = esc_attr( $image['sizes'] );
				$width  = esc_attr( $image['width'] );
				$height = esc_attr( $image['height'] );

				$logo = (
					"<a class='c-nav__logo l-relative l-zi-1 l-block e-trans' href='$home_link'>" .
						"<span class='a-hide-vis'>$home_name</span>" .
						'<div class="o-logo l-flex l-align-center l-relative" data-size="l">' .
							"<img class='l-block e-trans' src='$src' alt='' srcset='$srcset' sizes='$sizes' width='$width' height='$height'>" .
							$sticky_logo .
						'</div>' .
					'</a>'
				);
			}
		}

		/* Classes */

		$classes = 'l-flex l-align-center l-justify-between l-relative l-zi-1';

		if ( 'background-dark' !== Hero::$background_color ) {
			$classes .= ' t-light';
		}

		/* Id */

		$id = 'n-' . uniqid();

		/* Output */

		return (
			'<nav aria-label="Main">' .
				"<div class='$classes'>" .
					$logo .
					"<ul class='c-nav__list c-nav__row l-flex l-align-center l-gm-s l-overflow-x-auto t-ls-none t-ws-nowrap e-accent-r' role='list'>$main_nav</ul>" .
					'<div class="c-nav__hide no-js-none">' .
						"<button class='c-nav__button c-nav__open l-flex l-relative l-zi-1' type='button' aria-haspopup='true' aria-controls='$id' aria-label='Open menu'>" .
							'<span class="c-nav-icon l-block" data-nav-icon="1">' .
								'<span class="c-nav-icon__top l-block"></span>' .
								'<span class="c-nav-icon__middle l-block"></span>' .
								'<span class="c-nav-icon__bottom l-block e-trans"></span>' .
							'</span>' .
						'</button>' .
					'</div>' .
					"<div class='c-nav__overflow t-light l-flex l-breakout l-ht-full-vh l-fixed' role='dialog' aria-modal='true' aria-label='Main menu' id='$id'>" .
						'<div class="c-nav__overlay l-wd-full l-ht-full l-absolute"></div>' .
						'<div class="c-nav__swoop l-wd-full l-ht-full l-absolute">' .
							'<div class="l-ht-full l-relative">' .
								Swoop::render( ['location' => 'nav'] ) .
							'</div>' .
						'</div>' .
						'<div class="c-nav__hide">' .
							"<a class='c-nav__home o-logo l-absolute l-ht-s outline-snug' data-size='l' href='$home_link' aria-label='$home_name'></a>" .
						'</div>' .
						'<div class="c-nav__scroll l-wd-full">' .
							'<div class="fusion-builder-row fusion-row l-mt-auto l-mb-auto">' .
								"<ul class='c-nav__column l-wd-full l-mb-xs-all t-ls-none e-accent-r' role='list'></ul>" .
							'</div>' .
						'</div>' .
						'<div class="c-nav__hide">' .
							'<button class="c-nav__button c-nav__close l-absolute l-wd-s l-ht-s outline-snug" type="button" aria-label="Close menu"></button>' .
						'</div>' .
					'</div>' .
				'</div>' .
			'</nav>'
		);
	}

	/**
	 * Shortcode to output main navigation.
	 *
	 * @return string
	 */
	public function shortcode() {
		return self::render();
	}
}
