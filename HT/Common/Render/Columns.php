<?php
/**
 * Render two column layout
 *
 * @package hey-tony
 */

namespace HT\Common\Render;

/**
 * Class
 */

class Columns {

		/**
		 * Output column.
		 *
		 * @return string
		 */

		public static function render_column( $args = [] ) {
				$args = array_merge(
						[
							'content' => '',
							'border'  => true,
						],
						$args
				);

				[
					'content' => $content,
					'border'  => $border,
				] = $args;

				/* Content required */

				if ( ! $content ) {
						return '';
				}

				/* Output */

				return (
					'<div class="l-flex u-p-r' . ( $border ? ' o-border' : '' ) . '">' .
						'<div class="l-flex">' .
							$content .
						'</div>' .
					'</div>'
				);
		}

		/**
		 * Output columns.
		 *
		 * @return string
		 */

		public static function render( $args = [] ) {
				$args = array_merge(
						[
							'content' => '',
							'border'  => true,
							'width'   => 50,
						],
						$args
				);

				[
					'content' => $content,
					'border'  => $border,
					'width'   => $width,
				] = $args;

				/* Content required */

				if ( ! $content ) {
						return '';
				}

				$gap_l = $border ? 'xl' : 'l';

				/* Output */

				return (
					'<div>' .
						"<div class='l-flex l-w-all u-o-h' data-width='$width-pc' data-gap='l' data-gap-l='$gap_l' data-row='m' data-wrap data-col>" .
							$content .
						'</div>' .
					'</div>'
				);
		}

} // End Columns
