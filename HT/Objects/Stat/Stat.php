<?php
/**
 * Objects - Stat
 *
 * @package hey-tony
 */
namespace HT\Objects\Stat;

/* Imports */

use HT\HT as HT;

/**
 * Class - render overlapping stat.
 */
class Stat {
	/**
	 * Shortcode handle.
	 *
	 * @var string
	 */
	public static $handle = 'ht-stat';

	/**
	 * Default shortcode and render attributes.
	 *
	 * @var array {
	 *  @type string $large_text
	 *  @type string $small_text
	 *  @type string $align
	 *  @type bool   $wide
	 * }
	 */
	public static $default_atts = [
		'large_text' => '',
		'small_text' => '',
		'align'      => 'left',
		'wide'       => false,
	];

	/**
	 * Set shortcode action and styles.
	 *
	 * @return void
	 */
	public function __construct() {
		add_shortcode( self::$handle, [$this, 'shortcode'] );
	}

	/**
	 * Output stat.
	 *
	 * @param array $atts - $default_atts
	 * @return string
	 */
	public static function render( $atts ) {
		/* Defaults */

		$atts = array_merge( self::$default_atts, $atts );

		[
			'large_text' => $large_text,
			'small_text' => $small_text,
			'align'      => $align,
			'wide'       => $wide,
		] = $atts;

		/* Classes */

		$classes = "o-stat o-stat--$align";

		if ( $wide ) {
			$classes .= ' o-stat--wide';
		}

		/* Output */

		return (
			"<div class='$classes t-bg-background-base l-absolute l-bottom-0 l-inline-block' aria-hidden='true'>" .
				'<div class="h2-l e-accent e-accent-l l-mb-5xs l-relative l-zi-1" data-theme="primary-base">' .
					'<p class="l-m-0">' .
						"<span class='l-inline-block'>$large_text</span>" .
					'</p>' .
				'</div>' .
				'<div class="t-m t-wt-bold">' .
					"<p class='l-m-0 t-ht-snug'>$small_text</p>" .
				'</div>' .
			'</div>'
		);
	}

	/**
	 * Shortcode to output stat.
	 *
	 * @param array $atts - $default_atts
	 * @return string
	 */
	public static function shortcode( $atts ) {
		$atts = shortcode_atts( self::$default_atts, $atts, self::$handle );

		return self::render( $atts );
	}
}
