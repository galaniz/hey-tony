<?php
/**
 * Objects - Swoop
 *
 * @package hey-tony
 */
namespace HT\Objects\Swoop;

/* Imports */

use HT\HT as HT;
use HT\Components\Hero\Hero;

/**
 * Class - render swoop shape.
 */
class Swoop {
	/**
	 * Default shortcode and render attributes.
	 *
	 * @var array {
	 *  @type bool   $flip
	 *  @type string $position
	 *  @type string $location
	 * }
	 */
	public static $default_atts = [
		'flip'     => false,
		'position' => 'bottom',
		'location' => 'hero',
	];

	/**
	 * Set shortcode action and classes filter.
	 *
	 * @return void
	 */
	public function __construct() {
		add_shortcode( 'ht-swoop', [$this, 'shortcode'] );
		add_filter( 'body_class', [$this, 'filter_body_class'], 10, 1 );
	}

	/**
	 * Add classes to body.
	 *
	 * @param string[] $classes
	 * @return string[]
	 */
	public function filter_body_class( $classes ) {
		$n          = HT::$namespace;
		$size       = 'm';
		$is_single  = is_singular( 'post' ) || is_singular( 'work' ) || is_singular( 'testimonial' );
		$is_archive =
			( is_archive() && ! is_post_type_archive() ) || is_post_type_archive( 'service' ) || is_search();

		if ( is_front_page() ) {
			$size = 'l';
		}

		if ( $is_single || $is_archive ) {
			$size = 's';
		}

		$classes[] = "$n-swoop-$size";

		return $classes;
	}

	/**
	 * Output swoop.
	 *
	 * @param array $atts - $default_atts
	 * @return string
	 */
	public static function render( $atts ) {
		/* Defaults */

		$atts = array_merge( self::$default_atts, $atts );

		[
			'flip'     => $flip,
			'position' => $position,
			'location' => $location,
		] = $atts;

		$flip = filter_var( $flip, FILTER_VALIDATE_BOOLEAN );

		/* Attributes */

		$classes = ['l-absolute'];
		$attr    = [];

		if ( 'nav' === $location ) {
			$classes[] = 'l-ht-full l-right-0';
		} else {
			$classes[] = 'o-swoop l-breakout';
			$classes[] = 'top' === $position ? 'l-top-0' : 'l-bottom-0';
		}

		if ( $flip ) {
			$attr[] = 'data-flip';
		}

		$classes = implode( ' ', $classes );
		$attr    = implode( ' ', $attr );
		$attr    = $attr ? " $attr" : '';

		/* Svg */

		$output = (
			"<svg class='$classes'$attr aria-hidden='true' focusable='false' role='img' xmlns='http://www.w3.org/2000/svg' preserveAspectRatio='none' viewBox='0 0 50 9.7222'>" .
				'<path d="M50,0C26.937.67082,10.6975,3.029,0,9.7222V0Z" fill="' . ( 'hero' === $location ? 'var(--ht-hero-bg)' : 'currentcolor' ) . '" />' .
			'</svg>'
		);

		/* Hero image */

		if ( ! empty( Hero::$swoop_image ) && 'hero' === $location ) {
			$output = (
				$output .
				"<svg class='c-hero__swoop $classes'$attr aria-hidden='true' focusable='false' role='img' xmlns='http://www.w3.org/2000/svg' preserveAspectRatio='none'>" .
					'<defs>' .
						'<clipPath id="htsw" clipPathUnits="objectBoundingBox" transform="scale(0.02, 0.10285737796)">' .
							'<path d="M50,0C26.937.67082,10.6975,3.029,0,9.7222V0Z" />' .
						'</clipPath>' .
					'</defs>' .
					'<switch>' .
						'<foreignObject x="0" y="0" width="100%" height="100%" clip-path="url(#htsw)">' .
							Hero::$swoop_image .
						'</foreignObject>' .
					'</switch>' .
				'</svg>'
			);
		}

		/* Output */

		return $output;
	}

	/**
	 * Shortcode to output swoop.
	 *
	 * @param array $atts - $default_atts
	 * @return string
	 */
	public static function shortcode( $atts ) {
		$atts = shortcode_atts( self::$default_atts, $atts, 'ht-swoop' );

		return self::render( $atts );
	}
}
