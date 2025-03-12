<?php
/**
 * Components - Table Of Contents
 *
 * @package hey-tony
 */
namespace HT\Components\TableOfContents;

/* Imports */

use HT\HT as HT;
use HT\Objects\Collapsible\Collapsible;

/**
 * Class - render single table of contents.
 */
class TableOfContents {
	/**
	 * Shortcode handle.
	 *
	 * @var string
	 */
	public static $handle = 'ht-table-of-contents';

	/**
	 * Path in assets folder for css and js.
	 *
	 * @var string
	 */
	public static $asset_path = 'Components/TableOfContents/TableOfContents';

	/**
	 * Default shortcode and render attributes.
	 *
	 * @var array {
	 *  @type string $title
	 *  @type string $include - Comma separated list h2,h3,h4,h5,h6
	 * }
	 */
	public static $default_atts = [
		'title'   => 'In This Article',
		'include' => 'h2,h3,h4',
	];

	/**
	 * Set save post and shortcode actions, styles and scripts.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'save_post_post', [$this, 'on_save_headings'], 10, 2 );
		add_shortcode( self::$handle, [$this, 'shortcode'] );

		HT::$scripts_styles[ self::$handle ] = [
			'style'        => self::$asset_path,
			'script'       => self::$asset_path,
			'condition'    => function() {
				if ( ! is_singular( 'post' ) ) {
					return false;
				}

				return true;
			}
		];
	}

	/**
	 * Save heading ids on post save.
	 *
	 * @param int     $post_id
	 * @param WP_Post $post
	 * @return null
	 */
	public function on_save_headings( $post_id, $post, ) {
		/* Bounce if autosave or revision */

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		/* Content required */

		$content = $post->post_content;

		if ( empty( $content ) ) {
			return;
		}

		/* Store headings info */

		$headings = [];

		/* Find headings and update heading output */

		$content = preg_replace_callback(
			'/(<h.*(?:>))/',
			function( $matches ) use ( &$headings ) {
				$match = $matches[0];

				/* Item info */

				$title    = wp_strip_all_tags( $match );
				$tag_name = preg_match( '/h[2-6]/', $match, $tag );
				$type     = $tag[0];
				$id       = preg_replace( [ '/[^\w\s]|_/', '/\s+/' ], [ '', '-' ], strtolower( $title ) );
				$item     = [
					'id'    => $id,
					'title' => $title,
					'type'  => $type,
				];

				/* Heading with id */

				$new_heading = str_replace( ">$title", " id='$id'>$title", $match );

				/* Children prop shorthand */

				$ch = 'children';

				/* Add h2s */

				if ( 'h2' === $type ) {
					$headings[] = $item;

					return $new_heading;
				}

				/* Add h3s */

				$l2 = count( $headings ) - 1;

				if ( -1 === $l2 ) {
					$headings[] = [];
					$l2         = 0;
				}

				if ( ! isset( $headings[ $l2 ][ $ch ] ) ) {
					$headings[ $l2 ][ $ch ] = [];
				}

				if ( 'h3' === $type ) {
					$headings[ $l2 ][ $ch ][] = $item;

					return $new_heading;
				}

				/* Add h4s */

				$l3 = count( $headings[ $l2 ][ $ch ] ) - 1;

				if ( -1 === $l3 ) {
					$headings[ $l2 ][ $ch ][] = [];
					$l3                       = 0;
				}

				if ( ! isset( $headings[ $l2 ][ $ch ][ $l3 ][ $ch ] ) ) {
					$headings[ $l2 ][ $ch ][ $l3 ][ $ch ] = [];
				}

				if ( 'h4' === $type ) {
					$headings[ $l2 ][ $ch ][ $l3 ][ $ch ][] = $item;

					return $new_heading;
				}

				/* Add h5s */

				$l4 = count( $headings[ $l2 ][ $ch ][ $l3 ][ $ch ] ) - 1;

				if ( -1 === $l4 ) {
					$headings[ $l2 ][ $ch ][ $l3 ][ $ch ][] = [];
					$l4                                     = 0;
				}

				if ( ! isset( $headings[ $l2 ][ $ch ][ $l3 ][ $ch ][ $l4 ][ $ch ] ) ) {
					$headings[ $l2 ][ $ch ][ $l3 ][ $ch ][ $l4 ][ $ch ] = [];
				}

				if ( 'h5' === $type ) {
					$headings[ $l2 ][ $ch ][ $l3 ][ $ch ][ $l4 ][ $ch ][] = $item;

					return $new_heading;
				}

				/* Add h6s */

				$l5 = count( $headings[ $l2 ][ $ch ][ $l3 ][ $ch ][ $l4 ][ $ch ] ) - 1;

				if ( -1 === $l5 ) {
					$headings[ $l2 ][ $ch ][ $l3 ][ $ch ][ $l4 ][ $ch ][] = [];
					$l5 = 0;
				}

				if ( ! isset( $headings[ $l2 ][ $ch ][ $l3 ][ $ch ][ $l4 ][ $ch ] ) ) {
					$headings[ $l2 ][ $ch ][ $l3 ][ $ch ][ $l4 ][ $ch ][ $l5 ][ $ch ] = [];
				}

				if ( 'h6' === $type ) {
					$headings[ $l2 ][ $ch ][ $l3 ][ $ch ][ $l4 ][ $ch ][ $l5 ][ $ch ][] = $item;

					return $new_heading;
				}
			},
			$content
		);

		/* Save headings info */

		if ( count( $headings ) > 0 ) {
			update_post_meta( $post_id, HT::$namespace . '_post_headings', $headings );
		}

		/* Re-save post content */

		remove_action( 'save_post_post', [$this, 'on_save_headings'], 10, 2 );

		wp_update_post(
			[
				'ID'           => $post_id,
				'post_content' => $content,
			]
		);

		add_action( 'save_post_post', [$this, 'on_save_headings'], 10, 2 );
	}

	/**
	 * Recursively output list of heading structure.
	 *
	 * @param array $headings[] {
	 *  @type string $id
	 *  @type string $title
	 *  @type string $type
	 *  @type array  $children[] - $headings[]
	 * }
	 * @param array $include[] - ['h2', 'h3', 'h4']
	 * @param int   $depth
	 * @return string
	 */
	public static function render_list( $headings, $include = ['h2', 'h3', 'h4'], $depth = -1 ) {
		/* Store item output */

		$output = '';

		/* Increment depth */

		$depth++;

		/* Recurse headings */

		foreach ( $headings as $item ) {
			/* Defaults */

			$args = array_merge(
				[
					'id'       => '',
					'title'    => '',
					'type'     => '',
					'children' => null,
				],
				$item
			);

			[
				'id'       => $id,
				'title'    => $title,
				'type'     => $type,
				'children' => $children,
			] = $args;

			/* Check if nested to recurse */

			$has_children = is_array( $children ) && ! empty( $children );

			/* Id, title and type included required for link */

			$has_link = ! empty( $id ) && ! empty( $title ) && in_array( $type, $include, true );

			/* Output */

			$output .= (
				'<li class="o-toc__item">' .
					( $has_link ? "<a class='o-toc__link' href='#$id'>$title</a>" : '' ) .
					( $has_children ? self::render_list( $children, $include, $depth ) : '' ) .
				'</li>'
			);
		}

		/* Output */

		$classes = 't-ls-none';

		if ( $depth > 0 ) {
			$classes .= ' o-toc__list';
		}

		return "<ul class='$classes' role='list' data-depth='$depth'>$output</ul>";
	}

	/**
	 * Output table of contents.
	 *
	 * @param array $atts - $default_atts
	 * @return string
	 */
	public static function render( $atts ) {
		/* Defaults */

		$atts = array_merge( self::$default_atts, $atts );

		[
			'title'   => $title,
			'include' => $include,
		] = $atts;

		/* Check headings data exist */

		$headings = get_post_meta( get_the_ID(), HT::$namespace . '_post_headings', true );

		/* Must be an array with contents */

		if ( ! is_array( $headings ) || empty( $headings ) ) {
			return '';
		}

		/* Output */

		return (
			'<div class="o-toc outline-snug">' .
				Collapsible::render(
					[
						'title'         => $title,
						'heading_level' => 'div',
						'size'          => 's',
						'toc'           => true,
					],
					self::render_list( $headings, explode( ',', $include ) )
				) .
			'</div>'
		);
	}

	/**
	 * Shortcode to output table of contents.
	 *
	 * @param array $atts - $default_atts
	 * @return string
	 */
	public function shortcode( $atts ) {
		$atts = shortcode_atts( self::$default_atts, $atts, self::$handle );

		return self::render( $atts );
	}
}
