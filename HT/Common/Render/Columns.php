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
							'fill'    => false,
							'class'   => '',
						],
						$args
				);

				[
					'content' => $content,
					'border'  => $border,
					'width'   => $width,
					'fill'    => $fill,
					'class'   => $class,
				] = $args;

				/* Content required */

				if ( ! $content ) {
						return '';
				}

				/* Output */

				$classes = "l-w-$width-pc l-flex";

				if ( $class ) {
						$classes .= " $class";
				}

				$inner_class = 'l-flex u-p-r';

				if ( $border ) {
						$inner_class .= ' o-border';
				}

				if ( $fill ) {
						$inner_class .= ' l-flex-grow';
				}

				return (
					"<li class='$classes'>" .
						"<div class='$inner_class'>" .
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
							'content'  => '',
							'border'   => true,
							'class'    => '',
							'overflow' => 'hidden',
						],
						$args
				);

				[
					'content'  => $content,
					'border'   => $border,
					'class'    => $class,
					'overflow' => $overflow,
				] = $args;

				/* Content required */

				if ( ! $content ) {
						return '';
				}

				$gap_l = $border ? 'xl' : 'l';
				$class = 'l-flex' . ( $class ? " $class" : '' );

				/* Output */

				return (
					'<div' . ( 'hidden' === $overflow ? ' class="u-o-h"' : '' ) . '>' .
						"<ul class='$class' data-gap='l' data-gap-l='$gap_l' data-row='m' data-wrap-m data-col>" .
							$content .
						'</ul>' .
					'</div>'
				);
		}

} // End Columns
