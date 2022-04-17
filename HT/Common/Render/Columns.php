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

				/* Output */

				$border_class = $border ? ' o-border' : '';

				return (
					"<li class='l-w-$width-pc l-flex u-p-r$border_class'>" .
						'<div class="l-flex">' .
							$content .
						'</div>' .
					'</li>'
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
							'class'   => '',
						],
						$args
				);

				[
					'content' => $content,
					'border'  => $border,
					'class'   => $class,
				] = $args;

				/* Content required */

				if ( ! $content ) {
						return '';
				}

				$gap_l = $border ? 'xl' : 'l';
				$class = 'l-flex l-w-all u-o-h' . ( $class ? " $class" : '' );

				/* Output */

				return (
					'<div>' .
						"<ul class='$class' data-gap='l' data-gap-l='$gap_l' data-row='m' data-wrap-m data-col>" .
							$content .
						'</ul>' .
					'</div>'
				);
		}

} // End Columns
