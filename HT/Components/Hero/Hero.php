<?php
/**
 * Components - Hero
 *
 * @package hey-tony
 */
namespace HT\Components\Hero;

/* Imports */

use HT\HT as HT;

/**
 * Class - customize hero and output image.
 */
class Hero {
	/**
	 * Background color stored in ACF.
	 *
	 * @var string
	 */
	public static $background_color = 'foreground-base';

	/**
	 * Text color based on $background_color.
	 *
	 * @var string
	 */
	public static $color = 'background-base';

	/**
	 * Grayscale check based on $background_color.
	 *
	 * @var bool
	 */
	public static $is_grayscale = false;

	/**
	 * Featured image check.
	 *
	 * @var bool
	 */
	public static $has_image = false;

	/**
	 * Can display image.
	 *
	 * @var bool
	 */
	public static $display_image = true;

	/**
	 * Store image id.
	 *
	 * @var int
	 */
	public static $image_id = 0;

	/**
	 * Masked image for swoop.
	 *
	 * @var string
	 */
	public static $swoop_image = '';

	/**
	 * Path in assets folder for css and js.
	 *
	 * @var string
	 */
	public static $asset_path = 'Components/Hero/Hero';

	/**
	 * Set shortcode action, class and archive filters and scripts.
	 *
	 * @return void
	 */
	public function __construct() {
		add_shortcode( 'ht-hero-image', [$this, 'shortcode'] );
		add_shortcode( 'ht-archive-title', [$this, 'shortcode_archive_title'] );
		add_action( 'wp', [$this, 'set_props'] );
		add_filter( 'language_attributes', [$this, 'filter_html_atts'], 10, 1 );
		add_filter( 'body_class', [$this, 'filter_body_class'], 10, 1 );
		add_filter( 'get_the_archive_title_prefix', [$this, 'filter_archive_prefix'], 10, 1 );

		HT::$scripts_styles['ht-hero'] = [
			'script'    => self::$asset_path,
			'condition' => function() {
				if ( ! self::$display_image || is_singular( 'post' ) ) {
					return false;
				}

				if ( ! self::$has_image ) {
					return false;
				}

				return true;
			},
		];
	}

	/**
	 * Set properties after WP object set up.
	 *
	 * @return void
	 */
	public function set_props() {
		global $post;

		if ( ! is_object( $post ) || ! isset( $post->ID ) ) {
			return;
		}

		/* Id */

		$id = $post->ID;

		if ( is_home() ) {
			$id = (int) get_option( 'page_for_posts' );
		}

		/* Background color */

		$bg = get_field( 'background_color', $id );

		if ( ( is_singular( 'post' ) || is_singular( 'work' ) || is_singular( 'service' ) ) && ! is_singular( 'testimonial' ) ) {
			$bg = 'foreground-base';
		}

		if ( is_post_type_archive( 'service' ) ) {
			$bg = 'foreground-base';
		}

		if ( is_archive() && ! is_post_type_archive() && ! is_tax() ) {
			$bg = get_field( 'background_color', (int) get_option( 'page_for_posts' ) );
		}

		$bg = $bg ? $bg : 'background-dark';

		/* Set property values */

		self::$image_id         = get_post_thumbnail_id( $id );
		self::$has_image        = (bool) self::$image_id;
		self::$display_image    = ! ( is_post_type_archive() || is_archive() || is_home() );
		self::$background_color = $bg;
		self::$is_grayscale     = 'foreground-base' === $bg ? false : true;
		self::$color            = 'background-dark' === $bg ? 'foreground-base' : 'background-base';
	}

	/**
	 * Add id and style to html element.
	 *
	 * @param string $output
	 * @return string
	 */
	public function filter_html_atts( $output ) {
		$output .= ' id="ht"';

		$color   = HT::$colors[ self::$color ];
		$bg      = HT::$colors[ self::$background_color ];
		$over_bg = self::$has_image && self::$display_image && ! is_singular( 'post' ) ? 'transparent' : $bg;

		$output .= " style='--ht-hero-color:$color;--ht-hero-bg:$bg;--ht-hero-over-bg:$over_bg'";

		return $output;
	}

	/**
	 * Add classes to body.
	 *
	 * @param string[] $classes
	 * @return string[]
	 */
	public function filter_body_class( $classes ) {
		$n = HT::$namespace;

		$classes[] = $n;

		if ( self::$is_grayscale ) {
			$classes[] = "$n-hero-gray";
		}

		return $classes;
	}

	/**
	 * Output hero image.
	 *
	 * @return string
	 */
	public static function render() {
		/* No hero image on archive */

		if ( ! self::$display_image ) {
			return '';
		}

		/* Store if single post */

		$is_single = is_singular( 'post' );

		/* Image output */

		$image_output = '';

		if ( self::$image_id ) {
			$image_data = HT::get_image( self::$image_id, $is_single ? 'fusion-800' : '1536x1536' );
			$classes    =
				$is_single ? 'l-absolute l-top-0 l-left-0 l-wd-full l-ht-full' : 'c-hero__media l-wd-full';

			if ( $image_data ) {
				$src    = esc_url( $image_data['url'] );
				$srcset = esc_attr( $image_data['srcset'] );
				$sizes  = esc_attr( $image_data['sizes'] );
				$width  = esc_attr( $image_data['width'] );
				$height = esc_attr( $image_data['height'] );
				$alt    = esc_attr( $image_data['alt'] );

				$image_output = "<img class='$classes l-object-cover' src='$src' alt='$alt' srcset='$srcset' sizes='$sizes' width='$width' height='$height'>";

				self::$swoop_image = $is_single ? '' : "<img class='c-hero__foreign l-wd-full l-object-cover' src='$src' alt='' srcset='$srcset' sizes='$sizes' width='$width' height='$height'>";
			}
		}

		if ( empty( $image_output ) ) {
			return '';
		}

		/* Classes */

		$container_classes = 'l-breakout l-ht-full l-block l-absolute l-top-0 l-overflow-hidden';

		if ( $is_single ) {
			$container_classes = 'c-hero__rounded l-ar-1-1 l-block l-overflow-hidden l-relative';
		}

		/* Output */

		return "<span class='$container_classes'>$image_output</span>";
	}

	/**
	 * Shortcode to output hero image.
	 *
	 * @return string
	 */
	public function shortcode() {
		return self::render();
	}

	/**
	 * Shortcode to customize archive title.
	 *
	 * @return string
	 */
	public function shortcode_archive_title() {
		if ( is_search() ) {
			return (
				'<div class="l-inline t-wt-thin">Search &ndash;</div>' .
				get_search_query()
			);
		}

		return get_the_archive_title();
	}

	/**
	 * Customize archive title prefix.
	 *
	 * @param string $prefix
	 * @return string
	 */
	public function filter_archive_prefix( $prefix ) {
		$prefix = str_replace( ':', ' &ndash; ', $prefix );

		return "<div class='l-inline t-wt-thin'>$prefix</div>";
	}
}
