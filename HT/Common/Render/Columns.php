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

		public static function render_column( $content = '', $border = true ) {
				if ( ! $content ) {
						return '';
				}

				return (
					'<div class="l-flex u-p-r l-w-50-pc' . ( $border ? ' o-border' : '' ) . '">' .
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

		public static function render( $content = '', $border = true ) {
				if ( ! $content ) {
						return '';
				}

				return (
					'<div>' .
						'<div class="l-flex u-o-h" data-gap="l" data-gap-l="' . ( $border ? 'xl' : 'l' ) . '" data-row="m" data-wrap data-col>' .
							$content .
						'</div>' .
					'</div>'
				);
		}

} // End Columns
