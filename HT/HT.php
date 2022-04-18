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

use HT\Utils;
use HT\Common\Render\Main_Nav;
use HT\Common\Render\Main_Footer;
use HT\Common\Render\Hero_Image;
use HT\Common\Render\Swoop;
use HT\Common\Render\Meta;
use HT\Common\Render\Filters;
use HT\Common\Render\Stat;
use HT\Common\Posts;
use HT\Admin\Reading;
use HT\Admin\User;
use HT\Pub\Ajax;

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
		 * For filtering posts.
		 *
		 * @var array $load_posts_query
		 */

		public static $load_posts_query        = [];
		public static $load_posts_query_static = [];

		/**
		 * Check for query args in get params - for pagination.
		 *
		 * @var array $get_query_args
		 */

		public static $get_query_args = [];

		/**
		 * Url params based on $get_query_args - for pagination.
		 *
		 * @var array $query_url
		 */

		public static $get_query_url_params = [];

		/**
		 * Store post type slug and layout.
		 *
		 * @var array $cpt
		 */

		public static $post_types_info = [
			'post'        => [
				'layout' => 'cards',
			],
			'work'        => [
				'label'  => 'Work',
				'layout' => 'overlap',
			],
			'testimonial' => [
				'label'  => 'Testimonials',
				'layout' => 'columns',
			],
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
				add_shortcode( 'ht-meta', ['HT\Common\Render\Meta', 'shortcode'] );
				add_shortcode( 'ht-filters', ['HT\Common\Render\Filters', 'shortcode'] );
				add_shortcode( 'ht-stat', ['HT\Common\Render\Stat', 'shortcode'] );
				add_shortcode( 'ht-posts', ['HT\Common\Posts', 'shortcode'] );

				/* Actions */

				add_action( 'after_setup_theme', [$this, 'init'] );
				add_action( 'wp', [$this, 'wp'] );
				add_action( 'wp_enqueue_scripts', [$this, 'enqueue_styles'], 20 );
				add_action( 'wp_head', [$this, 'head'] );
				add_action( 'pre_get_posts', [$this, 'pre_get_posts'] );
				add_action( 'avada_after_main_content', [$this, 'render_loader'] );

				Ajax::ajax_actions();

				/* Filters */

				add_filter( 'body_class', [$this, 'body_class'], 10, 1 );
				add_filter( 'language_attributes', [$this, 'html_id'], 10, 1 );
				add_filter( 'dynamic_sidebar_params', [$this, 'widget_title'], 10, 1 );

				/* Admin */

				if ( is_admin() ) {
						$reading_settings = new Reading();
						$user_settings    = new User();
				}
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

				$id = $post->ID;

				if ( is_home() ) {
						$id = (int) get_option( 'page_for_posts' );
				}

				self::set_meta( $id );
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

		public function pre_get_posts( $query ) {
				if ( ! is_admin() && $query->is_main_query() ) {
						/* Update posts_per_page set in Reading settings */

						if ( is_home() || is_category() || is_archive() ) {
								$ppp = Utils::get_posts_per_page();

								if ( $ppp ) {
										$query->set( 'posts_per_page', $ppp );
								}
						}

						if ( is_tax() || is_post_type_archive() ) {
								$post_type = $query->get( 'post_type' );
								$ppp       = Utils::get_posts_per_page( $post_type );

								if ( $ppp ) {
										$query->set( 'posts_per_page', $ppp );
								}
						}

						/* Check for get params that affect queries */

						$get_taxonomy = '';
						$get_terms    = '';
						$get_paged    = '';
						$url_query    = [];

						/* phpcs:disable */
						if ( isset( $_GET['pg'] ) ) {
								$get_paged = (int) sanitize_text_field( $_GET['pg'] );
								$url_query['pg'] = $get_taxonomy;
						}

						if ( isset( $_GET['taxonomy'] ) ) {
								$get_taxonomy = sanitize_text_field( $_GET['taxonomy'] );
								$url_query['taxonomy'] = $get_taxonomy;
						}

						if ( isset( $_GET['terms'] ) ) {
								$get_terms = sanitize_text_field( $_GET['terms'] );
								$url_query['terms'] = (int) $get_terms;
						}
						/* phpcs:enable */

						if ( ! empty( $url_query ) ) {
								self::$get_query_url_params = $url_query;
						}

						if ( $get_taxonomy && $get_terms ) {
								self::$get_query_args['tax_query'] = [
									[
										'taxonomy' => $get_taxonomy,
										'terms'    => $get_terms,
									],
								];
						}

						if ( $get_paged ) {
								self::$get_query_args['paged'] = $get_paged;
						}

						if ( is_array( self::$get_query_args ) && count( self::$get_query_args ) > 0 ) {
								foreach ( self::$get_query_args as $q => $a ) {
										$query->set( $q, $a );
								}
						}
				}
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
						"$n-script-compat",
						get_stylesheet_directory_uri() . "/assets/public/js/$n-compat.js",
						[],
						self::$script_ver,
						true
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

				// wp_enqueue_script( "$n-script-compat" );
				wp_enqueue_script( "$n-script" );
		}

		/**
		 * Output loader for pagination.
		 */

		public function render_loader() {
				echo (
					'<div class="c-loader l-flex l-w-100-pc l-m-0-all u-p-a u-r-0 u-t-0 u-b-0 l-breakout js-load-more-loader" data-align="center" data-justify="center" data-hide>' .
						'<div class="l-w-r t-primary-base u-br-100-pc u-b-m">' .
							'<div class="o-aspect-ratio"></div>' .
						'</div>' .
					'</div>'
				);
		}

		/**
		 * Add to body class.
		 */

		public function body_class( $classes ) {
				$classes[] = 'ht';

				if ( self::$hero_grayscale ) {
						$classes[] = 'ht-hero-grayscale';
				}

				$swoop_size = 's';

				if ( is_front_page() ) {
						$swoop_size = 'r';
				}

				if ( is_single() || is_singular( 'work' ) || is_archive() ) {
						$swoop_size = 'xs';
				}

				$classes[] = "ht-swoop-$swoop_size";

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

				if ( is_single() || is_singular( 'work' ) ) {
						$hero_background_color = 'foreground-base';
				}

				/* Blog related archives */

				if ( is_archive() && ! is_post_type_archive() && ! is_tax() ) {
						$hero_background_color = get_field( 'background_color', (int) get_option( 'page_for_posts' ) );
				}

				self::$hero_background_color = $hero_background_color ? $hero_background_color : 'background-dark';
				self::$nav_light             = 'background-dark' === self::$hero_background_color ? false : true;
				self::$hero_grayscale        = 'foreground-base' === self::$hero_background_color ? false : true;
				self::$hero_color            = 'background-dark' === self::$hero_background_color ? 'foreground-base' : 'background-base';
		}

} // End HT
