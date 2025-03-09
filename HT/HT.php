<?php
/**
 * HT
 *
 * @package hey-tony
 */
namespace HT;

/* Imports */

use HT\Components\Navigation\Navigation;
use HT\Components\Filters\Filters;
use HT\Components\Footer\Footer;
use HT\Components\Hero\Hero;
use HT\Components\Notice\Notice;
use HT\Components\Loader\Loader;
use HT\Components\TableOfContents\TableOfContents;
use HT\Objects\Swoop\Swoop;
use HT\Objects\Meta\Meta;
use HT\Objects\Stat\Stat;
use HT\Objects\Tabs\Tabs;
use HT\Objects\Collapsible\Collapsible;
use HT\Objects\Device\Device;
use HT\Objects\Posts\Posts;
use HT\Objects\Slider\Slider;
use HT\Objects\LinkList\LinkList;
use HT\Effects\Accent\Accent;
use HT\Svg\Sprites\Sprites;
use HT\Admin\User\User;
use HT\Admin\Settings\Settings;
use Formation\Formation as FRM;
use Formation\Pub\Ajax;
use Formation\Admin\Settings\Reading;
use Formation\Utils;

/**
 * Class - avada child theme class.
 */
class HT {
	/**
	 * Namespace to avoid collisions.
	 *
	 * @var string
	 */
	public static $namespace = 'ht';

	/**
	 * Theme colors.
	 *
	 * @var array
	 */
	public static $colors = [
		'primary-base'    => '#1b4ecc',
		'primary-light'   => '#4b7fff',
		'primary-dark'    => '#0734a1',
		'foreground-base' => '#202020',
		'background-base' => '#f8f3f0',
		'background-dark' => '#ede8e6',
	];

	/**
	 * Query params to filter posts.
	 *
	 * @var array
	 */
	public static $load_posts_query = [];

	/**
	 * Persistent query params to filter posts.
	 *
	 * @var array
	 */
	public static $load_posts_query_static = [];

	/**
	 * Query args in get params for pagination.
	 *
	 * @var array
	 */
	public static $get_query_args = [];

	/**
	 * Url params based on $get_query_args for pagination.
	 *
	 * @var array
	 */
	public static $get_query_url_params = [];

	/**
	 * Store post type slug and layout.
	 *
	 * @var array
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
			'layout' => 'flush',
		],
		'work'          => [
			'label'  => 'Work',
			'layout' => 'overlap',
			'nav'    => true,
		],
		'testimonial'   => [
			'label'  => 'Testimonials',
			'layout' => 'flush',
			'nav'    => true,
		],
		'service'       => [
			'label'  => 'Services',
			'layout' => 'cards',
			'nav'    => true,
		],
		'ht_tool'       => [
			'label' => 'Tools',
			'layout' => 'media-text',
		],
	];

	/**
	 * Store taxonomy post types.
	 *
	 * @var array
	 */
	public static $tax_pt = [
		'category'      => 'post',
		'post_tag'      => 'post',
		'work_category' => 'work',
	];

	/**
	 * Global script and style handle.
	 *
	 * @var string
	 */
	public static $global_handle = 'ht-global';

	/**
	 * Script version for enqueue.
	 *
	 * @var string
	 */
	public static $script_version = '1.0.0';

	/**
	 * Store conditional functions to load styles and scripts.
	 *
	 * @var array
	 */
	public static $scripts_styles = [];

	/**
	 * Store scripts that need module attribute.
	 *
	 * @var string[]
	 */
	public static $scripts_modules = [];

	/**
	 * Store theme shortcode handles for asset loading.
	 *
	 * @var string[]
	 */
	public static $shortcode_handles = [];

	/**
	 * Store theme shortcode asset dependency handles.
	 *
	 * @var string[]
	 */
	public static $dependency_handles = [];

	/**
	 * Check slider from shortcode in content.
	 *
	 * @var bool
	 */
	public static $has_slider = false;

	/**
	 * Store theme handles in contents for asset loading.
	 *
	 * @var string[]
	 */
	public static $content_handles = [];

	/**
	 * Set properties and init shortcodes, actions and filters.
	 *
	 * @return void
	 */
	public function __construct() {
		/* Set Formation props */

		FRM::$namespace = self::$namespace;
		FRM::$pt        = self::$pt;
		FRM::$tax_pt    = self::$tax_pt;

		/* Global script/style */

		self::$scripts_styles[ self::$global_handle ] = [
			'style'  => 'Global/Global',
			'script' => 'Global/Global',
		];

		/* Shortcodes */

		new Hero();
		new Navigation();
		new Footer();
		new Swoop();
		new Meta();
		new Filters();
		new Stat();
		new Accent();
		new Tabs();
		new Collapsible();
		new TableOfContents();
		new Device();
		new Posts();
		new Slider();
		new LinkList();
		new Notice();
		new Loader();

		/* Sprites */

		new Sprites();

		/* Actions */

		add_action( 'wp_enqueue_scripts', [$this, 'add_assets'] );
		add_action( 'pre_get_posts', [$this, 'on_query_vars'] );
		add_action( 'wp_head', [$this, 'add_styles'] );

		static::ajax_actions();

		/* Unused assets check */

		add_action( 'wp_footer', [$this, 'archive_dequeue'] );
		add_action( 'get_header', [$this, 'single_shortcodes' ] );
		add_filter( 'the_content', [$this, 'archive_shortcodes'] );

		/* Filters */

		add_filter( 'nav_menu_css_class', [$this, 'filter_nav_classes'], 10, 2 );
		add_filter( 'wp_script_attributes', [$this, 'filter_script_attributes'], 10, 2 );

		/* Admin */

		if ( is_admin() ) {
			$user_settings    = new User();
			$reading_settings = new Reading();
			$general_settings = new Settings();
		}
	}

	/**
	 * Check for shortcodes in content and add to $content_shortcodes
	 *
	 * @param string $content
	 * @return void
	 */
	public function set_content_shortcodes( $content ) {
		/* Handles check */

		foreach ( self::$shortcode_handles as $handle ) {
			if ( ! has_shortcode( $content, $handle ) ) {
				continue;
			}

			self::$content_handles[] = $handle;

			/* Add dependencies (avoid loading unnecessarily) */

			$dependencies = self::$scripts_styles[ $handle ]['dependencies'] ?? [];
			$merged       = array_merge( self::$content_handles, array_keys( $dependencies ) );

			self::$content_handles = array_unique( $merged );
		}

		/* Slider check */

		if ( self::$has_slider ) {
			return;
		}

		/* Hack for single service */

		if ( is_singular( 'service' ) ) {
			self::$has_slider = true;

			return;
		}

		/* Check for ht-posts shortcode */

		preg_match_all( '/\[ht-posts(.*?)\]/', $content, $matches );

		if ( ! is_array( $matches ) || count( $matches ) < 2 ) {
			return;
		}

		$has_slider = false;

		/* Check slider attribute */

		foreach ( $matches[1] as $match ) {
			if ( strpos( $match, 'slider' ) !== false ) {
				$has_slider = true;
				break;
			}
		}

		self::$has_slider = $has_slider;
	}

	/**
	 * Dequeue scripts and styles not in $content_shortcodes.
	 *
	 * @return void
	 */
	public function dequeue_unused_shortcode_assets() {
		$all_handles = array_unique( array_merge( self::$shortcode_handles, self::$dependency_handles ) );

		foreach ( $all_handles as $handle ) {
			if ( in_array( $handle, self::$content_handles, true ) ) {
				continue;
			}

			wp_dequeue_style( $handle );
			wp_dequeue_script( $handle );
		}

		if ( ! self::$has_slider ) {
			wp_dequeue_style( Slider::$handle );
			wp_dequeue_script( Slider::$handle );
		}
	}

	/**
	 * Check single content for shortcodes.
	 *
	 * @return void
	 */
	public function single_shortcodes() {
		if ( ! is_singular() ) {
			return;
		}

		$this->set_content_shortcodes( get_the_content() );
	}

	/**
	 * Dequeue unused styles and scripts on archive.
	 *
	 * @return void
	 */
	public function archive_dequeue() {
		if ( is_singular() ) {
			return;
		}

		$this->dequeue_unused_shortcode_assets();
	}

	/**
	 * Check if archive content contains shortcodes.
	 *
	 * @param string $content
	 * @return string
	 */
	public function archive_shortcodes( $content ) {
		if ( is_singular() ) {
			return $content;
		}

		$this->set_content_shortcodes( $content );

		return $content;
	}

	/**
	 * Add no js, admin styles and check shortcodes on singular content.
	 *
	 * @return void
	 */
	public function add_styles() {
		if ( is_admin_bar_showing() ) {
			echo '<style>.admin-bar .c-nav.fusion-flex-container.fusion-builder-row-1 { margin-top: var(--adminbar-height); }</style>';
		}

		$theme_url = get_stylesheet_directory_uri();

		/* phpcs:disable */
		echo "<noscript><link rel='stylesheet' href='$theme_url/assets/css/Global/GlobalNoJs.css' media='all'></noscript>";
		/* phpcs:enable */
	}

	/**
	 * Register and enqueue scripts and styles.
	 *
	 * @return void
	 */
	public function add_assets() {
		/* Urls */

		$theme_url = get_stylesheet_directory_uri();
		$css_url   = "$theme_url/assets/css/";
		$js_url    = "$theme_url/assets/js/";

		/* Global is dependency */

		$global_dep = [ self::$global_handle ];

		/* Get styles and scripts to load */

		foreach ( self::$scripts_styles as $handle => $props ) {
			$condition = $props['condition'] ?? null;
			$enqueue   = true;

			if ( is_callable( $condition ) ) {
				$enqueue = $condition();
			}

			if ( ! $enqueue ) {
				continue;
			}

			$style        = $props['style'] ?? '';
			$script       = $props['script'] ?? '';
			$style_dep    = $handle === self::$global_handle ? [] : $global_dep;
			$script_dep   = $handle === self::$global_handle ? [] : $global_dep;
			$dependencies = $props['dependencies'] ?? [];
			$is_shortcode = in_array( $handle, self::$shortcode_handles, true );

			foreach ( $dependencies as $dep_handle => $dep_props ) {
				$dep_style  = $dep_props['style'] ?? '';
				$dep_script = $dep_props['script'] ?? '';

				if ( ! empty( $dep_style ) ) {
					wp_register_style(
						$dep_handle,
						"$css_url$dep_style.css",
						$style_dep,
						self::$script_version,
						'all'
					);

					wp_enqueue_style( $dep_handle );

					$style_dep[] = $dep_handle;
				}

				if ( ! empty( $dep_script ) ) {
					self::$scripts_modules[] = "$dep_handle-js";

					wp_register_script(
						$dep_handle,
						"$js_url$dep_script.js",
						$script_dep,
						self::$script_version,
						true
					);

					wp_enqueue_script( $dep_handle );

					$script_dep[] = $dep_handle;
				}

				if ( $is_shortcode ) {
					self::$dependency_handles[] = $dep_handle;
				}
			}

			if ( ! empty( $style ) ) {
				wp_register_style(
					$handle,
					"$css_url$style.css",
					$handle === self::$global_handle ? ['fusion-dynamic-css'] : $style_dep,
					self::$script_version,
					'all'
				);

				wp_enqueue_style( $handle );
			}

			if ( ! empty( $script ) ) {
				self::$scripts_modules[] = "$handle-js";

				wp_register_script(
					$handle,
					"$js_url$script.js",
					$script_dep,
					self::$script_version,
					true
				);

				wp_enqueue_script( $handle );
			}
		}

		/* Localize scripts */

		wp_localize_script(
			self::$global_handle,
			self::$namespace,
			[
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			]
		);

		/* Remove Gutenberg assets */

		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'wc-block-style' ); // Removes woocommerce block css
		wp_dequeue_style( 'global-styles' ); // Removes theme.json
		remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
		remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );

		/* Remove embed script */

		wp_deregister_script( 'wp-embed' );

		/* Single dequeue unused */

		if ( is_singular() ) {
			$this->dequeue_unused_shortcode_assets();
		}
	}

	/**
	 * Add attributes to scripts.
	 *
	 * @param  array $attr
	 * @return array
	 */
	public function filter_script_attributes( $attr ) {
		$id = $attr['id'] ?? '';

		if ( empty( $id ) || empty( $attr['src'] ) ) {
			return $attr;
		}

		if ( in_array( $id, self::$scripts_modules, true ) ) {
			$attr['type'] = 'module';
		}

		return $attr;
	}

	/**
	 * Add current class to nav for custom post types.
	 *
	 * @param string[] $classes
	 * @param WP_Post  $item
	 * @return string[]
	 */
	public function filter_nav_classes( $classes, $item ) {
		return FRM::pt_nav_classes( $classes, $item );
	}

	/**
	 * Alter query vars for posts.
	 *
	 * @param WP_Query $query
	 * @return void
	 */
	public function on_query_vars( $query ) {
		FRM::query_vars( $query );

		/* Only run on front end and main query */

		if ( is_admin() ) {
			return;
		}

		if ( ! $query->is_main_query() ) {
			return;
		}

		/* Posts per page */

		$post_type = $query->get( 'post_type' );
		$ppp       = 0;

		if ( $query->is_archive ) {
			$ppp = static::get_posts_per_page( 'blog_archives' );
		}

		/* Check get params that affect queries */

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

		/* Store params and additional args */

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

		/* Update query */

		if ( is_array( self::$get_query_args ) && count( self::$get_query_args ) > 0 ) {
			foreach ( self::$get_query_args as $q => $a ) {
				$query->set( $q, $a );
			}
		}
	}

	/**
	 * Formation utility methods.
	 */
	use Utils;

	/**
	 * Formation ajax callbacks.
	 */
	use Ajax;

	/**
	 * Output posts requested through ajax.
	 *
	 * @param array $args
	 * @return string|array
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
}
