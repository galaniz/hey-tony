<?php
/**
 * HT Avada child theme class
 *
 * @package hey-tony
 */

namespace HT;

/**
 * Imports
 */

use function HT\Shortcodes\get_main_nav;

/**
 * Class
 */

class HT {

		/**
		 * Namespace for handles.
		 *
		 * @var string $namespace
		 */

		public static $namespace = 'ht';

		/**
		 * Script version for wp_enqueue.
		 *
		 * @var string $script_ver
		 */

		public static $script_ver = '1.0.0';

		/**
		 * Constructor
		 */

		public function __construct() {
				/* Shortcodes */

				add_shortcode( 'get-main-nav', get_main_nav );

				/* Actions */

				add_action( 'wp_enqueue_scripts', [$this, 'theme_enqueue_styles'], 20 );
		}

		/**
		 * Register and enqueue scripts and styles.
		 */

		public function theme_enqueue_styles() {
				wp_enqueue_style(
						self::$namespace . '-child-style',
						get_stylesheet_directory_uri() . '/style.css',
						[],
						self::$script_ver
				);
		}

} // End HT
