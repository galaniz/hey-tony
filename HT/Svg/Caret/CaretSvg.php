<?php
/**
 * Svg - Caret
 *
 * @package hey-tony
 */

namespace HT\Svg\Caret;

/* Imports */

use HT\Svg\Sprites\Sprites;

/**
 * Class - output svg for caret icon.
 */
class CaretSvg {
	/**
	 * Svg path values by direction.
	 *
	 * @var array {
	 *  @type string $right
	 *  @type string $left
	 *  @type string $up
	 *  @type string $down
	 * }
	 */
	private static $paths = [
		'right' => 'M6.21,5l-4.14,4.14.86.86,5-5L2.93,0l-.86.86,4.14,4.14Z',
		'left'  => 'M3.79,5L7.93.86l-.86-.86L2.07,5l5,5,.86-.86L3.79,5Z',
		'up'    => 'M5,3.79l4.14,4.14.86-.86L5,2.07,0,7.07l.86.86L5,3.79Z',
		'down'  => 'M5,6.21L.86,2.07l-.86.86,5,5,5-5-.86-.86-4.14,4.14Z',
	];

	/**
	 * Add to svg sprite and output link to it.
	 *
	 * @param string $direction
	 * @param string $classes
	 * @return string
	 */
	public static function render( $direction = 'left', $classes = '' ) {
		/* Attributes */

		$id       = "caret-$direction";
		$width    = '10';
		$height   = '10';
		$view_box = "0 0 $width $height";
		$path     = self::$paths[ $direction ] ?? '';

		/* Path value required */

		if ( empty( $path ) ) {
			return '';
		}

		/* Add to sprite */

		Sprites::$svg[ $id ] = [
			'view_box' => $view_box,
			'output'   => (
				"<path d='$path' fill='currentcolor' />"
			),
		];

		/* Classes output */

		if ( $classes ) {
			$classes = " class='$classes'";
		}

		/* Output */

		return (
			"<svg width='$width' height='$height' aria-hidden='true' focusable='false' role='img'$classes>" .
				"<use xlink:href='#${id}' />" .
			'</svg>'
		);
	}
}
