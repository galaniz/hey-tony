<?php
/**
 * Render text with accent underline
 *
 * @package hey-tony
 */

namespace HT\Common\Render;

/**
 * Class
 */

class Accent {

		/**
		 * Output text with accent.
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
								'<div class="u-d-ib o-accent__d">' .
									"<span class='o-accent__f'>$accent</span>" .
								'</div>'
						);
				}

				$text = str_replace( $accent, $accent_output, $text );

				/* Output */

				return (
					'<div class="u-d-i" role="text">' .
						$text .
					'</div>'
				);
		}

		/**
		 * Shortcode to output text with accent.
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

} // End Accent
