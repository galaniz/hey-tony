<?php
/**
 * Components - Footer
 *
 * @package hey-tony
 */
namespace HT\Components\Footer;

/* Imports */

use HT\HT as HT;
use HT\Objects\Swoop\Swoop;
use HT\Objects\LinkList\LinkList;
use Formation\Pub\Nav_Walker;

/**
 * Class - render main footer.
 */
class Footer {
	/**
	 * Set shortcode and setup actions.
	 *
	 * @return void
	 */
	public function __construct() {
		add_shortcode( 'ht-main-footer', [$this, 'shortcode'] );
		add_action( 'after_setup_theme', [$this, 'add_nav_menu'] );

		LinkList::$widgets[] = [
			'name' => 'Footer Widget 4',
			'id'   => 'footer-widget-4',
			'type' => 'footer',
		];
	}

	/**
	 * Add footer nav menu.
	 *
	 * @return void
	 */
	public function add_nav_menu() {
		register_nav_menus(
			[
				'footer_navigation' => 'Footer Navigation',
			]
		);
	}

	/**
	 * Filter social icons output.
	 *
	 * @param string $html
	 * @return string
	 */
	public static function filter_social( $html ) {
		$html = str_replace(
			[
				'class="fusion-social-networks-wrapper"',
				'style=',
				'<a class="',
				'a>',
			],
			[
				'class="fusion-social-networks-wrapper l-flex l-gm-3xs"',
				'data-style=',
				'<div><a class="l-m-0 ',
				'a></div>',
			],
			$html
		);

		return $html;
	}

	/**
	 * Output main footer.
	 *
	 * @return string
	 */
	public static function render() {
		/* Widgets output */

		$widgets = wp_get_sidebars_widgets();
		$allowed = [
			'avada-footer-widget-1',
			'avada-footer-widget-2',
			'avada-footer-widget-3',
			'ht-footer-widget-4',
		];

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
					$wid = self::filter_social( $wid );
					$wid = "<div class='l-pt-2xs'>$wid</div>";
				} else {
					$contact = strpos( $wid, 'mailto:' ) !== false;
					$class   = ( $contact ? 'e-accent' : 'e-underline t-inherit' ) . ' l-inline-block-a';

					if ( $first ) {
						$class .= ' l-pb-2xs';
					}

					$wid = "<div class='$class'>$wid</div>";
				}

				$ww .= $wid;
			}

			$widgets_output[] = "<div class='" . ( $first ? 'l-mb-2xs-all l-grow-1 l-wd-full' : 'l-mb-3xs-all' ) . "'>$ww</div>";
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
					'<a class="l-block" href="' . esc_url( home_url( '/' ) ) . '">' .
						'<span class="a-hide-vis">' . get_bloginfo( 'name' ) . ' home</span>' .
						'<div class="o-logo">' .
							"<img class='l-block' src='$src' alt='' srcset='$srcset' sizes='$sizes' width='$width' height='$height'>" .
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
			$footer_nav = (
				'<div>' .
					"<ul class='l-flex l-gm-2xs e-underline t-ls-none t-inherit' role='list'>$footer_nav</ul>" .
				'</div>'
			);
		} else {
			$footer_nav = '';
		}

		/* Output */

		return (
			'<div class="l-relative">' .
				Swoop::render( ['flip' => true, 'position' => 'top', 'location' => 'footer'] ) .
				'<div class="l-pt-l l-pb-m l-pt-3xl-m l-pb-l-m t-light">' .
					'<div class="l-flex l-pb-m">' .
						$logo .
					'</div>' .
					'<div class="l-pb-m">' .
						'<div class="l-flex l-col l-justify-between l-row-m l-wrap-m l-gm-s">' .
							implode( '', $widgets_output ) .
						'</div>' .
					'</div>' .
					'<div class="l-flex l-align-center l-justify-between l-wrap l-gm-2xs l-gm-s-m t-xs">' .
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
	 * @return string
	 */
	public function shortcode() {
		return self::render();
	}
}
