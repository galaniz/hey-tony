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
use HT\Common\Render\Meta;
use HT\Common\Render\Filters;
use HT\Common\Render\Stat;
use HT\Common\Render\Accent;
use HT\Common\Render\Tabs;
use HT\Common\Render\Collapsible;
use HT\Common\Render\Device;
use HT\Common\Posts;
use HT\Admin\User;
use HT\Admin\General;
use Formation\Formation as FRM;
use Formation\Pub\Ajax;
use Formation\Admin\Settings\Reading;
use Formation\Utils;

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

	public static $pt = [
		'post'          => [
			'layout'  => 'cards',
			'nav'     => true,
			'reading' => false,
		],
		'blog_archives' => [
			'label'  => 'Blog archive',
			'layout' => 'cards',
		],
		'search'        => [
			'label'  => 'Search',
			'layout' => 'columns',
		],
		'work'          => [
			'label'  => 'Work',
			'layout' => 'overlap',
			'nav'    => true,
		],
		'testimonial'   => [
			'label'  => 'Testimonials',
			'layout' => 'columns',
			'nav'    => true,
		],
		'service'       => [
			'label'  => 'Services',
			'layout' => 'cards',
			'nav'    => true,
		],
	];

	/**
	 * Store taxonomy post types.
	 *
	 * @var array $tax_pt
	 */

	public static $tax_pt = [
		'category'      => 'post',
		'post_tag'      => 'post',
		'work_category' => 'work',
	];

	/**
	 * Script attributes to add in filter.
	 *
	 * @var array $script_attributes
	 */

	public static $script_attributes = [
		'script-compat' => 'nomodule',
		'script'        => 'type="module"',
	];

	/**
	 * Constructor
	 */

	public function __construct() {
		/* Set variables in Formation */

		FRM::$namespace         = self::$namespace;
		FRM::$script_ver        = self::$script_ver;
		FRM::$script_attributes = self::$script_attributes;
		FRM::$pt                = self::$pt;
		FRM::$tax_pt            = self::$tax_pt;
		FRM::$scripts           = [
			[
				'handle' => 'script-compat',
				'url'    => get_stylesheet_directory_uri() . '/assets/public/js/' . self::$namespace . '-compat.js',
			],
			[
				'handle' => 'script',
				'url'    => get_stylesheet_directory_uri() . '/assets/public/js/' . self::$namespace . '.js',
			],
		];

		/* Shortcodes */

		add_shortcode( 'ht-main-nav', ['HT\Common\Render\Main_Nav', 'shortcode'] );
		add_shortcode( 'ht-main-footer', ['HT\Common\Render\Main_Footer', 'shortcode'] );
		add_shortcode( 'ht-hero-image', ['HT\Common\Render\Hero_Image', 'shortcode'] );
		add_shortcode( 'ht-swoop', ['HT\Common\Render\Swoop', 'shortcode'] );
		add_shortcode( 'ht-meta', ['HT\Common\Render\Meta', 'shortcode'] );
		add_shortcode( 'ht-filters', ['HT\Common\Render\Filters', 'shortcode'] );
		add_shortcode( 'ht-stat', ['HT\Common\Render\Stat', 'shortcode'] );
		add_shortcode( 'ht-accent', ['HT\Common\Render\Accent', 'shortcode'] );
		add_shortcode( 'ht-tabs', ['HT\Common\Render\Tabs', 'shortcode'] );
		add_shortcode( 'ht-collapsible', ['HT\Common\Render\Collapsible', 'shortcode'] );
		add_shortcode( 'ht-device', ['HT\Common\Render\Device', 'shortcode'] );
		add_shortcode( 'ht-posts', ['HT\Common\Posts', 'shortcode'] );

		add_shortcode( 'ht-archive-title', [$this, 'archive_title'] );
		add_filter( 'get_the_archive_title_prefix', [$this, 'filter_archive_prefix'], 10, 1 );

		/* Actions */

		add_action( 'after_setup_theme', [$this, 'init'] );
		add_action( 'wp', [$this, 'wp'] );
		add_action( 'wp_enqueue_scripts', [$this, 'enqueue_assets'], 20 );
		add_action( 'wp_head', [$this, 'enqueue_style'], 4000000 );
		add_action( 'pre_get_posts', [$this, 'query_vars'] );
		add_action( 'avada_before_main_container', [$this, 'render_loader'] );
		add_action( 'avada_before_wrapper_container_close', [$this, 'render_cookie_notice'] );
		add_action( 'wp_loaded', [$this, 'widgets'] );
		add_action( 'dynamic_sidebar_before', [$this, 'widget_before'], 10, 2 );
		add_action( 'dynamic_sidebar_after', [$this, 'widget_after'], 10, 2 );

		static::ajax_actions();

		/* Filters */

		add_filter( 'body_class', [$this, 'body_class'], 10, 1 );
		add_filter( 'language_attributes', [$this, 'html_id'], 10, 1 );
		add_filter( 'dynamic_sidebar_params', [$this, 'widget_title'], 10, 1 );
		add_filter( 'nav_menu_css_class', [$this, 'pt_nav_classes'], 10, 2 );
		add_filter( 'widget_nav_menu_args', [$this, 'widget_nav_args'], 10, 4 );
		add_filter( 'script_loader_tag', [$this, 'add_script_attributes'], 10, 2 );

		/* Admin */

		if ( is_admin() ) {
			$user_settings    = new User();
			$reading_settings = new Reading();
			$general_settings = new General();
		}
	}

	/**
	 * Shortcode output for archive title.
	 */

	public function archive_title() {
		if ( is_search() ) {
			return (
				'<div class="u-d-i u-fw-var">Search &ndash;</div>' .
				get_search_query()
			);
		}

		return get_the_archive_title();
	}

	/**
	 * Filter archive title prefix.
	 */

	public function filter_archive_prefix( $prefix ) {
		$prefix = str_replace( ':', ' &ndash; ', $prefix );
		return "<div class='u-d-i u-fw-var'>$prefix</div>";
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
	 * Alter query vars for posts.
	 */

	public function query_vars( $query ) {
		FRM::query_vars( $query );

		if ( ! is_admin() && $query->is_main_query() ) {
			$post_type = $query->get( 'post_type' );
			$ppp       = 0;

			if ( $query->is_archive ) {
				$ppp = static::get_posts_per_page( 'blog_archives' );
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

	public function enqueue_assets() {
		FRM::scripts();

		/* Remove Gutenberg root variables */

		wp_dequeue_style( 'global-styles' );
	}

	/**
	 * Output child styles.
	 */

	public function enqueue_style() {
		$id  = self::$namespace . '-style-css';
		$url = get_stylesheet_directory_uri() . '/style.css';

		/* phpcs:disable */
		$link = "<link rel='stylesheet' id='$id' href='$url' type='text/css' media='all'>";

		echo $link;
		/* phpcs:enable */
	}

	/**
	 * Output loader for pagination.
	 */

	public function render_loader() {
		echo (
			'<aside class="c-loader l-flex u-p-f u-t-0 u-b-0 u-l-0 u-r-0 u-oo-s js-load-more-loader" data-align="center" data-justify="center" tabindex="0" aria-label="Loading" data-hide>' .
				'<div class="l-w-r t-primary-base u-br-100-pc u-b-m">' .
					'<div class="o-aspect-ratio"></div>' .
				'</div>' .
			'</aside>'
		);
	}

	/**
	 * Output cookie notice before end of #wrapper.
	 */

	public function render_cookie_notice() {
		$cookie_text = get_option(
			self::$namespace . '_cookie_text',
			'We use cookies to give you the best experience. By continuing to use this site, you consent to the use of cookies.'
		);

		/* phpcs:disable */
		echo (
			'<aside class="c-notice l-mw-r l-ph-ctn l-pb-xxxs o-underline u-p-f u-b-0 u-r-0 u-d-n">' .
				'<div class="t-bg-background-base p-xs u-p-r u-o-h u-tlrb-b u-fw-b">' .
					"<p class='u-p-r'>$cookie_text</p>" .
					'<button class="t-foreground-base l-w-s l-h-s u-p-a u-t-0 u-r-0 u-oo-s" type="button">' .
						'<span class="u-v-h">Close cookie notice</span>' .
						'<svg role="img" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 14 14" width="14" height="14">' .
							'<path fill="currentColor" d="M8.12133 6.70724L13.4144 1.41421L12.0002 0L6.70712 5.29303L1.41421 0.00012402L0 1.41434L5.29291 6.70724L0.000151038 12L1.41436 13.4142L6.70712 8.12146L12 13.4143L13.4142 12.0001L8.12133 6.70724Z" />' .
						'</svg>' .
					'</button>' .
				'</div>' .
			'</aside>'
		);
		/* phpcs:enable */
	}

	/**
	 * Register widget area.
	 */

	public function widgets() {
		$n = self::$namespace;

		register_sidebar(
			[
				'name'          => 'Footer Widget 4',
				'id'            => "$n-footer-widget-4",
				'before_widget' => '',
				'after_widget'  => '',
				'before_title'  => '',
				'after_title'   => '',
			]
		);

		register_sidebar(
			[
				'name'          => 'Single Post Widget',
				'id'            => "$n-single-post-widget",
				'before_widget' => '',
				'after_widget'  => '',
				'before_title'  => '',
				'after_title'   => '',
			]
		);
	}

	/**
	 * Before and after widget display.
	 */

	public function widget_before( $index, $has_widgets ) {
		$n = self::$namespace;

		if ( "$n-single-post-widget" === $index && $has_widgets ) {
			echo '<div class="l-mb-l-all">';
		}
	}

	public function widget_after( $index, $has_widgets ) {
		$n = self::$namespace;

		if ( "$n-single-post-widget" === $index && $has_widgets ) {
			echo '</div>';
		}
	}

	/**
	 * Add to body class.
	 */

	public function body_class( $classes ) {
		$n = self::$namespace;

		$classes[] = $n;

		if ( self::$hero_grayscale ) {
			$classes[] = "$n-hero-grayscale";
		}

		$swoop_size = 's';

		if ( is_front_page() ) {
			$swoop_size = 'r';
		}

		if ( is_single() || is_singular( 'work' ) || is_singular( 'testimonial' ) ) {
			$swoop_size = 'xs';
		}

		if ( ( is_archive() && ! is_post_type_archive() ) || is_post_type_archive( 'service' ) || is_search() ) {
			$swoop_size = 'xs';
		}

		$classes[] = "$n-swoop-$swoop_size";

		return $classes;
	}

	/**
	 * Add id and styles to html element.
	 */

	public function html_id( $output ) {
		$output .= ' id="ht"';

		$c  = self::$colors[ self::$hero_color ];
		$bg = self::$colors[ self::$hero_background_color ];

		$output .= " style='--ht-hero-c:$c;--ht-hero-bg-c:$bg'";

		return $output;
	}

	/**
	 * Filter widget title.
	 */

	public function widget_title( $params ) {
		$heading_level = 2;
		$id            = $params[0]['id'] ?? '';
		$n             = self::$namespace;

		if ( "$n-single-post-widget" === $id ) {
			$heading_level = 3;
		}

		$params[0]['before_widget'] = '<div>';
		$params[0]['after_widget']  = '</div>';
		$params[0]['before_title']  = '<div class="p-s u-fw-b l-mb-s"><p role="heading" aria-level="' . $heading_level . '">';
		$params[0]['after_title']   = '</p></div>';

		return $params;
	}

	/**
	 * Add current class to nav for custom post type.
	 */

	public function pt_nav_classes( $classes, $item ) {
		return FRM::pt_nav_classes( $classes, $item );
	}

	/**
	 * Add class to widget nav menu.
	 */

	public function widget_nav_args( $nav_menu_args, $nav_menu, $args, $instance ) {
		$class = 'l-mb-s-all p';
		$attr  = '';
		$id    = $args['id'];
		$n     = self::$namespace;

		if ( 'avada-footer-widget-1' === $id ) {
			$class = 'l-flex p-m';
			$attr  = ' data-gap="s" data-wrap';
		}

		if ( "$n-single-post-widget" === $id ) {
			$class = 'l-flex p-s';
			$attr  = ' data-gap="s" data-wrap';
		}

		$nav_menu_args['items_wrap'] = '<ul id="%1$s" class="' . $class . '"' . $attr . '>%3$s</ul>';

		return $nav_menu_args;
	}

	/**
	 * Add attributes to scripts.
	 */

	public function add_script_attributes( $tag, $handle ) {
		return FRM::add_script_attributes( $tag, $handle );
	}

	/**
	 * Set values of meta.
	 *
	 * @param int $id
	 */

	public static function set_meta( $id ) {
		$hero_background_color = get_field( 'background_color', $id );

		if ( ( is_single() || is_singular( 'work' ) || is_singular( 'service' ) ) && ! is_singular( 'testimonial' ) ) {
			$hero_background_color = 'foreground-base';
		}

		/* Service archive */

		if ( is_post_type_archive( 'service' ) ) {
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

	/**
	 * Formation Utility methods.
	 */

	use Utils;

	/**
	 * Filter Avada social icons output.
	 *
	 * @param string $html
	 * @return string
	 */

	public static function filter_social( $html ) {
		$html = str_replace(
			['class="fusion-social-networks-wrapper"', 'style=', '<a class="', 'a>'],
			['class="fusion-social-networks-wrapper l-flex" data-gap="s"', 'data-style=', '<div><a class="l-m-0 ', 'a></div>'],
			$html
		);

		return $html;
	}

	/**
	 * Formation ajax callbacks.
	 */

	use Ajax;

	/**
	 * Output posts requested through ajax.
	 *
	 * @param string $post_type
	 * @param array $args
	 * @return string or array of html output
	 */

	public static function render_ajax_posts( $args = [] ) {
		if ( isset( $args['post_type'] ) ) {
			$args['type'] = $args['post_type'];
		}

		$q_args = $args;

		if ( isset( $q_args['is_home'] ) ) {
			unset( $q_args['is_home'] );
		}

		$args['return_array'] = true;
		$args['query_args']   = $q_args;

		return Posts::shortcode( $args, '' );
	}

} // End HT
