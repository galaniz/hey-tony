<?php
/**
 * Effects - Accent
 *
 * @package hey-tony
 */
namespace HT\Effects\Accent;

/**
 * Class - render text with styled underline.
 */
class Accent {
	/**
	 * Default shortcode and render attributes.
	 *
	 * @var array {
	 *  @type string $text
	 *  @type string $accent
	 * }
	 */
	public static $default_atts = [
		'text'   => '',
		'accent' => '',
	];

	/**
	 * Set shortcode action.
	 *
	 * @return void
	 */
	public function __construct() {
		add_shortcode( 'ht-accent', [$this, 'shortcode'] );
	}

	/**
	 * Output text with accent.
	 *
	 * @param array $atts - $default_atts
	 * @return string
	 */
	public static function render( $atts ) {
		/* Defaults */

		$atts = array_merge( self::$default_atts, $atts );

		[
			'text'   => $text,
			'accent' => $accent,
		] = $atts;

		/* Accent required */

		if ( ! $accent ) {
			return $text;
		}

		/* Check if accent is the last part of text */

		$text_arr = explode( $accent, $text );
		$last     = false;

		if ( ! $text_arr[1] ) {
			$last = true;
		}

		/* Replace accent text */

		$accent_output = "<span>$accent</span>";

		if ( $last ) {
			$accent_output = (
				'<div class="l-inline-block e-accent__d">' .
					"<span class='e-accent__f'>$accent</span>" .
				'</div>'
			);
		}

		$text = str_replace( $accent, $accent_output, $text );

		/* Output */

		return (
			'<div class="l-inline" role="text">' .
				$text .
			'</div>'
		);
	}

	/**
	 * Shortcode to output text with accent.
	 *
	 * @param array $atts - $default_atts
	 * @return string
	 */
	public static function shortcode( $atts ) {
		$atts = shortcode_atts( self::$default_atts, $atts, 'ht-accent' );

		return self::render( $atts );
	}
}
