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
use HT\Common\Render\Hero_Image;
use HT\Common\Render\Swoop;
use HT\Common\Render\Filters;
use HT\Common\Posts;
use HT\Utils;

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
		 * Hero background color (ACF).
		 *
		 * @var string $hero_background_color
		 */

		public static $hero_background_color = 'foreground-base';

		/**
		 * Hero color based on $hero_background_color.
		 *
		 * @var string $hero_color
		 */

		public static $hero_color = 'background-base';

		/**
		 * Hero grayscale based on $hero_background_color.
		 *
		 * @var string $hero_grayscale
		 */

		public static $hero_grayscale = false;

		/**
		 * Nav color based on $hero_background_color.
		 *
		 * @var boolean $nav_light
		 */

		public static $nav_light = true;

		/**
		 * Theme colors.
		 *
		 * @var array $colors
		 */

		public static $colors = [
			'primary-base'    => '#1B4ECC',
			'primary-light'   => '#4B7FFF',
			'primary-dark'    => '#0734A1',
			'foreground-base' => '#202020',
			'background-base' => '#F8F3F0',
			'background-dark' => '#EDE8E6',
		];

		/**
		 * Constructor
		 */

		public function __construct() {
				/* Shortcodes */

				add_shortcode( 'ht-main-nav', ['HT\Common\Render\Main_Nav', 'shortcode'] );
				add_shortcode( 'ht-main-footer', ['HT\Common\Render\Main_Footer', 'shortcode'] );
				add_shortcode( 'ht-hero-image', ['HT\Common\Render\Hero_Image', 'shortcode'] );
				add_shortcode( 'ht-swoop', ['HT\Common\Render\Swoop', 'shortcode'] );
				add_shortcode( 'ht-filters', ['HT\Common\Render\Filters', 'shortcode'] );
				add_shortcode( 'ht-posts', ['HT\Common\Posts', 'shortcode'] );

				/* Actions */

				add_action( 'after_setup_theme', [$this, 'init'] );
				add_action( 'wp', [$this, 'wp'] );
				add_action( 'wp_enqueue_scripts', [$this, 'enqueue_styles'], 20 );
				add_action( 'wp_head', [$this, 'head'] );

				/* Filters */

				add_filter( 'body_class', [$this, 'body_class'], 10, 1 );
				add_filter( 'language_attributes', [$this, 'html_id'], 10, 1 );
				add_filter( 'dynamic_sidebar_params', [$this, 'widget_title'], 10, 1 );
		}

		/**
		 * Initalize theme.
		 */

		public function init() {
				register_nav_menus(
						[
							'footer_navigation' => 'Footer Navigation',
						]
				);
		}

		/**
		 * After WP object is set up.
		 */

		public function wp() {
				global $post;

				if ( ! is_object( $post ) || ! isset( $post->ID ) ) {
						return;
				}

				self::set_meta( $post->ID );
		}

		/**
		 * Output in head tag.
		 */

		public function head() {
				/* phpcs:disable */ ?>
					<style id="ht">
						:root {
							--ht-hero-c: <?php echo self::$colors[self::$hero_color] ?>;
							--ht-hero-bg-c: <?php echo self::$colors[self::$hero_background_color] ?>;
						}
					</style>
				<?php /* phpcs:enable */
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

				if ( self::$hero_grayscale ) {
						$classes[] = 'ht-hero-grayscale';
				}

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
				$params[0]['before_title']  = '<div class="p-s u-fw-b l-mb-s"><p role="heading" aria-level="2">';
				$params[0]['after_title']   = '</p></div>';

				return $params;
		}

		/**
		 * Set values of meta.
		 *
		 * @param int $id
		 */

		public static function set_meta( $id ) {
				$hero_background_color = get_field( 'background_color', $id );

				self::$hero_background_color = $hero_background_color ? $hero_background_color : 'background-dark';
				self::$nav_light             = 'background-dark' === self::$hero_background_color ? false : true;
				self::$hero_grayscale        = 'foreground-base' === self::$hero_background_color ? false : true;
				self::$hero_color            = 'background-dark' === self::$hero_background_color ? 'foreground-base' : 'background-base';
		}

		/**
		 * Utilities.
		 */

		use Utils;

} // End HT
