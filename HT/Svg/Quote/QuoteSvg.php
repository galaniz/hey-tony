<?php
/**
 * Svg - Quote
 *
 * @package hey-tony
 */

namespace HT\Svg\Quote;

/* Imports */

use HT\Svg\Sprites\Sprites;

/**
 * Class - output svg for quote mark.
 */
class QuoteSvg {
	/**
	 * Add to svg sprite and output link to it.
	 *
	 * @param string $classes
	 * @return string
	 */
	public static function render( $classes = '' ) {
		/* Attributes */

		$id       = 'quote';
		$width    = '45';
		$height   = '30';
		$view_box = "0 0 $width $height";

		/* Add to sprite */

		Sprites::$svg[ $id ] = [
			'view_box' => $view_box,
			'output'   => (
				'<path d="M8.77,30c-1.22,0-2.43-.24-3.53-.71-1.1-.47-2.06-1.17-2.8-2.02C.86,25.56,0,23.42,0,21.21c.06-2.86.62-5.69,1.67-8.39C3.34,8.34,7.22,3.24,11.1,0h6.99c-1.54,2.57-2.73,5.29-3.55,8.1-.87,2.97-1.39,3.39-1.55,6.45,1.3.69,2.37,1.66,3.11,2.83.89,1.12,1.4,3.44,1.44,4.81,0,1.01-.21,2.02-.65,2.96-.44.94-1.08,1.79-1.9,2.51-.78.76-1.74,1.37-2.82,1.77-1.07.4-2.23.6-3.4.57ZM35.67,30c-1.22,0-2.43-.24-3.53-.71-1.1-.47-2.06-1.17-2.8-2.02-1.58-1.71-2.45-3.85-2.44-6.05.06-2.86.62-5.69,1.67-8.39,1.67-4.48,5.55-9.58,9.44-12.82h6.99c-1.54,2.57-2.73,5.29-3.55,8.1-.87,2.97-1.39,3.39-1.55,6.45,1.3.69,2.37,1.66,3.11,2.83.89,1.12,1.4,3.44,1.44,4.81,0,1.01-.21,2.02-.65,2.96-.44.94-1.08,1.79-1.9,2.51-.78.76-1.74,1.37-2.82,1.77-1.07.4-2.23.6-3.4.57Z" fill="currentcolor" />'
			),
		];

		/* Classes output */

		if ( $classes ) {
			$classes = " class='$classes'";
		}

		/* Output */

		return (
			"<svg width='$width' height='$height' aria-hidden='true' focusable='false' role='img'$classes>" .
				"<use xlink:href='#$id' />" .
			'</svg>'
		);
	}
}
