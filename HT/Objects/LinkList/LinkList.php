<?php
/**
 * Objects - Link List
 *
 * @package hey-tony
 */
namespace HT\Objects\LinkList;

/* Imports */

use HT\HT as HT;

/**
 * Class - list of links widgets.
 */
class LinkList {
	/**
	 * Store widgets to register.
	 *
	 * @var array[] {
	 *  @type string $name
	 *  @type string $id
	 *  @type string $type
	 * }
	 */
	public static $widgets = [
		[
			'name' => 'Single Post Widget',
			'id'   => 'single-post-widget',
			'type' => 'single',
		],
	];

	/**
	 * Store single widget type id.
	 *
	 * @var string
	 */
	public static $single_id = '';

	/**
	 * Store footer widget type id.
	 *
	 * @var string
	 */
	public static $footer_id = '';

	/**
	 * Set widget actions and filters.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_loaded', [$this, 'add_widgets'] );
		add_action( 'dynamic_sidebar_before', [$this, 'on_before'], 10, 2 );
		add_action( 'dynamic_sidebar_after', [$this, 'on_after'], 10, 2 );
		add_filter( 'dynamic_sidebar_params', [$this, 'filter_title'], 10, 1 );
		add_filter( 'widget_nav_menu_args', [$this, 'filter_nav_args'], 10, 3 );
	}

	/**
	 * Register widget areas.
	 *
	 * @return void
	 */
	public function add_widgets() {
		if ( count( self::$widgets ) === 0 ) {
			return;
		}

		$n = HT::$namespace;

		foreach ( self::$widgets as $w ) {
			[
				'name' => $name,
				'id'   => $id,
				'type' => $type,
			] = $w;

			$wid = "$n-$id";

			if ( 'single' === $type ) {
				self::$single_id = $wid;
			}

			if ( 'footer' === $type ) {
				self::$footer_id = $wid;
			}

			register_sidebar(
				[
					'name'          => $name,
					'id'            => $wid,
					'before_widget' => '',
					'after_widget'  => '',
					'before_title'  => '',
					'after_title'   => '',
				]
			);
		}
	}

	/**
	 * Before widget display.
	 *
	 * @param string $index
	 * @param bool   $has_widgets
	 * @return void
	 */
	public function on_before( $index, $has_widgets ) {
		if ( ! $has_widgets || empty( self::$single_id ) ) {
			return;
		}

		if ( self::$single_id === $index ) {
			echo '<div class="l-mb-s-all">';
		}
	}

	/**
	 * After widget display.
	 *
	 * @param string $index
	 * @param bool   $has_widgets
	 * @return void
	 */
	public function on_after( $index, $has_widgets ) {
		if ( ! $has_widgets || empty( self::$single_id ) ) {
			return;
		}

		if ( self::$single_id === $index ) {
			echo '</div>';
		}
	}

	/**
	 * Filter widget title.
	 *
	 * @see https://developer.wordpress.org/reference/hooks/dynamic_sidebar_params/
	 *
	 * @param array $params
	 * @return array
	 */
	public function filter_title( $params ) {
		$heading_level = 2;
		$id            = $params[0]['id'] ?? '';

		if ( self::$single_id === $id ) {
			$heading_level = 3;
		}

		$params[0]['before_widget'] = '<div>';
		$params[0]['after_widget']  = '</div>';
		$params[0]['before_title']  = "<div class='t-s t-wt-bold l-mb-3xs'><p role='heading' aria-level='$heading_level'>";
		$params[0]['after_title']   = '</p></div>';

		return $params;
	}

	/**
	 * Add classes to widget nav menu.
	 *
	 * @see https://developer.wordpress.org/reference/hooks/widget_nav_menu_args/
	 *
	 * @param array   $nav_menu_args
	 * @param WP_Term $nav_menu
	 * @param array   $args
	 * @return array
	 */
	public function filter_nav_args( $nav_menu_args, $nav_menu, $args ) {
		$class = 'l-mb-3xs-all t';
		$id    = $args['id'];

		if ( 'avada-footer-widget-1' === $id ) {
			$class = 'l-flex l-wrap l-gm-2xs t-m';
		}

		if ( self::$single_id === $id ) {
			$class = 'l-flex l-wrap l-gm-2xs t-s';
		}

		$nav_menu_args['items_wrap'] = '<ul id="%1$s" class="' . $class . ' t-ls-none" role="list">%3$s</ul>';

		return $nav_menu_args;
	}
}
