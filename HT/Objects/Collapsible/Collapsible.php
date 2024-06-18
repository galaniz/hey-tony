<?php
/**
 * Objects - Collapsible
 *
 * @package hey-tony
 */
namespace HT\Objects\Collapsible;

/* Imports */

use HT\HT as HT;

/**
 * Class - render collapsible.
 */
class Collapsible {
	/**
	 * Shortcode handle.
	 *
	 * @var string
	 */
	public static $handle = 'ht-collapsible';

	/**
	 * Path in assets folder for css and js.
	 *
	 * @var string
	 */
	public static $asset_path = 'Objects/Collapsible/Collapsible';

	/**
	 * Default shortcode and render attributes.
	 *
	 * @var array {
	 *  @type string $title
	 *  @type string $heading_level
	 *  @type string $size - l | s
	 *  @type string $toc - true | false
	 *  @type string $classes - Back end option
	 * }
	 */
	public static $default_atts = [
		'title'         => '',
		'heading_level' => 'h3',
		'size'          => 'l',
		'toc'           => false,
		'classes'       => '',
	];

	/**
	 * Set shortcode action, styles and scripts.
	 *
	 * @return void
	 */
	public function __construct() {
		add_shortcode( self::$handle, [$this, 'shortcode'] );

		HT::$shortcode_handles[] = self::$handle;

		HT::$scripts_styles[ self::$handle ] = [
			'style'  => self::$asset_path,
			'script' => self::$asset_path,
		];
	}

	/**
	 * Output collapsible.
	 *
	 * @param array  $atts - $default_atts
	 * @param string $content
	 * @return string
	 */
	public static function render( $atts, $content ) {
		/* Defaults */

		$atts = array_merge( self::$default_atts, $atts );

		[
			'title'         => $title,
			'heading_level' => $heading_level,
			'size'          => $size,
			'toc'           => $toc,
			'classes'       => $classes,
		] = $atts;

		/* Id */

		$id = uniqid();

		/* Classes */

		$container_classes = 'o-collapsible b-top';
		$heading_classes   = 'l-m-0';
		$content_classes   = '';

		if ( ! empty( $classes ) ) {
			$container_classes .= " $classes";
		}

		/* Attributes */

		$attr = '';

		/* Table of contents */

		if ( $toc ) {
			$attr .= ' data-toc="true"';
		}

		/* Size */

		$attr .= " data-size='$size'";

		if ( 'l' === $size ) {
			$container_classes .= ' l-pt-xs l-pb-xs l-pt-s-m l-pb-s-m';
			$heading_classes   .= ' h4';
			$content_classes    = 'l-pt-xs l-pt-s-m t-m l-mb-2xs-all ht-rich-text e-underline';
		}

		if ( 's' === $size ) {
			$container_classes .= ' l-pt-2xs l-pb-2xs';
			$heading_classes   .= ' t-m t-wt-extrabold l-mb-5xs';
			$content_classes    = 't-s e-underline-r';
		}

		/* Output */

		return (
			"<div class='$container_classes'$attr>" .
				"<$heading_level class='$heading_classes'>" .
					"<button class='o-collapsible__toggle l-flex l-wd-full outline-snug' type='button' aria-controls='$id' aria-expanded='false'>" .
						'<span class="o-collapsible__icon l-flex l-align-center l-justify-center t-primary-base no-js-none">' .
							'<i aria-hidden="true" class="o-collapsible__minus awb-icon-minus"></i>' .
							'<i aria-hidden="true" class="o-collapsible__plus awb-icon-plus"></i>' .
						'</span>' .
						"<span>$title</span>" .
					'</button>' .
				"</$heading_level>" .
				"<div class='o-collapsible__main e-trans no-js-collapsible' id='$id' data-collapsible-main>" .
					"<div class='$content_classes'>" .
						$content .
					'</div>' .
				'</div>' .
			'</div>'
		);
	}

	/**
	 * Shortcode to output collapsible.
	 *
	 * @param array  $atts - $default_atts
	 * @param string $content
	 * @return string
	 */
	public static function shortcode( $atts, $content ) {
		$atts = shortcode_atts( self::$default_atts, $atts, self::$handle );

		return self::render( $atts, $content );
	}
}
