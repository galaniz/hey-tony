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

use HT\Common\Render\Main_Nav;
use HT\Common\Render\Main_Footer;

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

				add_shortcode( 'get-main-nav', ['HT\Common\Render\Main_Nav', 'shortcode'] );
				add_shortcode( 'get-main-footer', ['HT\Common\Render\Main_Footer', 'shortcode'] );

				/* Actions */

				add_action( 'wp_enqueue_scripts', [$this, 'enqueue_styles'], 20 );

				/* Filters */

				add_filter( 'body_class', [$this, 'body_class'], 10, 1 );
				add_filter( 'language_attributes', [$this, 'html_id'], 10, 1 );
				add_filter( 'dynamic_sidebar_params', [$this, 'widget_title'], 10, 1 );
		}

		/**
		 * Register and enqueue scripts and styles.
		 */

		public function enqueue_styles() {
				$n = self::$namespace;

				wp_enqueue_style(
						"$n-style",
						get_stylesheet_directory_uri() . '/style.css',
						[],
						self::$script_ver
				);

				wp_register_script(
						"$n-script",
						get_stylesheet_directory_uri() . "/assets/public/js/$n.js",
						[],
						self::$script_ver,
						true
				);

				wp_localize_script(
						"$n-script",
						self::$namespace,
						[
							'ajax_url' => admin_url( 'admin-ajax.php' ),
						]
				);

				wp_enqueue_script( "$n-script" );
		}

		/**
		 * Add to body class.
		 */

		public function body_class( $classes ) {
				$classes[] = 'ht';
				return $classes;
		}

		/**
		 * Add id to html element.
		 */

		public function html_id( $output ) {
				$output .= ' id="ht"';
				return $output;
		}

		/**
		 * Filter widget title.
		 */

		public function widget_title( $params ) {
				$params[0]['before_widget'] = '<div>';
				$params[0]['after_widget']  = '</div>';
				$params[0]['before_title']  = '<div class="p-s u-fw-bold l-mb-s"><p role="heading" aria-level="2">';
				$params[0]['after_title']   = '</p></div>';

				return $params;
		}

} // End HT
