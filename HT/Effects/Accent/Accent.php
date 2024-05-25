<?php
/**
 * Effects - Accent
 *
 * @package hey-tony
 */
namespace HT\Effects\Accent;

/**
 * Class - render text with styled underline
 */
class Accent {
	/**
	 * Output text with accent
	 *
	 * @return string
	 */
	public static function render( $args ) {
		$args = array_merge(
			[
				'text'   => '',
				'accent' => '',
			],
			$args
		);

		[
			'text'   => $text,
			'accent' => $accent,
		] = $args;

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
	 * Shortcode to output text with accent
	 *
	 * @param array $atts
	 * @param string $content
	 * @return string
	 */
	public static function shortcode( $atts, $content ) {
		$atts = shortcode_atts(
			[
				'text'   => '',
				'accent' => '',
			],
			$atts,
			'ht-accent'
		);

		return self::render( $atts );
	}
}
