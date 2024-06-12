<?php
/**
 * Svg - Sprites
 *
 * @package maple
 */

namespace HT\Svg\Sprites;

/**
 * Class - output svg sprites.
 */
class Sprites {
	/**
	 * Store svg data.
	 *
	 * @var array
	 */
	public static $svg = [];

	/**
	 * Set avada body action to output.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'avada_after_main_container', [$this, 'render'] );
	}

	/**
	 * Output all svgs.
	 *
	 * @return void
	 */
	public function render() {
		$sprites_output = '';

		foreach ( self::$svg as $id => $svg ) {
			[
				'view_box' => $view_box,
				'output'   => $output,
			] = $svg;

			$sprites_output .= "<symbol id='$id' viewBox='$view_box'>$output</symbol>";
		}

		if ( ! empty( $sprites_output ) ) {
			$sprites_output = "<svg xmlns='http://www.w3.org/2000/svg' class='l-none'>$sprites_output</svg>";
		}

		/* phpcs:disable */
		echo $sprites_output;
		/* phpcs:enable */
	}
}
