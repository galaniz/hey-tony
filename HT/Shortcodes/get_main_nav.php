<?php
/**
 * Shortcode: get main naivgation
 *
 * @package hey-tony
 */

namespace HT\Shortcodes;

/**
 * Imports
 */

use function HT\Render\main_nav;

/**
 * Output main navigation.
 *
 * @return string
 */

function get_main_nav( $atts, $content ) {
		return main_nav();
}
