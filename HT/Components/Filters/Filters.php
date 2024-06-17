<?php
/**
 * Components - Filters
 *
 * @package hey-tony
 */
namespace HT\Components\Filters;

/* Imports */

use HT\HT as HT;
use HT\Components\Loader\Loader;
use HT\Objects\Overflow\Overflow;
use Formation\Common\Field\Field;
use function Formation\additional_script_data;

/**
 * Class - render filters for archive.
 */
class Filters {
	/**
	 * Shortcode handle.
	 *
	 * @var string
	 */
	public static $handle = 'ht-filters';

	/**
	 * Path in assets folder for css and js.
	 *
	 * @var string
	 */
	public static $asset_path = 'Components/Filters/Filters';

	/**
	 * Set shortcode action and styles.
	 *
	 * @return void
	 */
	public function __construct() {
		add_shortcode( self::$handle, [$this, 'shortcode'] );

		HT::$shortcode_handles[] = self::$handle;

		HT::$scripts_styles[ self::$handle ] = [
			'style'        => self::$asset_path,
			'script'       => self::$asset_path,
			'dependencies' => [
				'ht-overflow' => [
					'style'  => Overflow::$asset_path,
					'script' => Overflow::$asset_path,
				],
				'ht-loader'   => [
					'style' => Loader::$asset_path,
				],
			],
		];
	}

	/**
	 * Get args for filters on archives.
	 *
	 * @param string $post_type
	 * @return array[] {
	 *  @type string $id
	 *  @type string $type
	 *  @type array  $options
	 *  @type string $value
	 *  @type string $label
	 *  @type string $label_class
	 *  @type string $opt_buttons_class
	 *  @type string $opt_button_class
	 *  @type string $field_class
	 *  @type array  $load_posts_query {
	 *   @type array[] $tax_query {
	 *    @type string $taxonomy
	 *    @type string $terms
	 *   }
	 *  }
	 * }
	 */
	public static function get_filter_args( $post_type ) {
		$tax     = 'category';
		$options = [];
		$args    = [];
		$val     = '';

		$tax_url_param   = HT::$get_query_url_params['taxonomy'] ?? false;
		$terms_url_param = HT::$get_query_url_params['terms'] ?? false;

		if ( 'work' === $post_type ) {
			$tax = 'work_category';
		}

		$options = HT::get_terms_as_options( $tax );

		if ( empty( $options ) ) {
			return $args;
		}

		if ( 'post' === $post_type && is_category() ) {
			$c = get_the_category();

			if ( ! empty( $c ) ) {
				$val = $c[0]->cat_ID;
			}
		}

		if ( $tax === $tax_url_param && $terms_url_param ) {
			$val = $terms_url_param;
		}

		$id = 'tax_' . uniqid();

		$args[] = [
			'id'                => $id,
			'type'              => 'radio-group',
			'options'           => $options,
			'value'             => $val,
			'label'             => 'Categories',
			'label_class'       => 'a-hide-vis',
			'opt_buttons_class' => 'l-flex l-align-center l-gm-3xs outline-snug',
			'opt_button_class'  => 'fusion-button button-small button-outline l-block',
			'field_class'       => 'o-overflow l-mw-full l-overflow-x-auto',
			'load_posts_query'  => [
				'tax_query' => [
					[
						'taxonomy' => $tax,
						'terms'    => '%value:int',
					],
				],
			],
		];

		return $args;
	}

	/**
	 * Output filters.
	 *
	 * @param string $post_type
	 * @return string
	 */
	public static function render( $post_type = '' ) {
		if ( ! $post_type || ( ! is_post_type_archive() && ! is_home() ) ) {
			return '';
		}

		$fields = self::get_filter_args( $post_type );
		$output = '';

		if ( ! empty( $fields ) ) {
			foreach ( $fields as $f ) {
				$id                  = $f['id'] ?? false;
				$load_posts_query    = $f['load_posts_query'] ?? false;
				$load_posts_query_id = $f['load_posts_query_id'] ?? $id;

				if ( ! $id || ! $load_posts_query ) {
					continue;
				}

				$f['class'] = 'a-hide-input js-load-more-filter' . ( isset( $f['class'] ) ? ' ' . $f['class'] : '' );
				$f['name']  = $id;

				$field_output = '';

				Field::render(
					[
						'fields'   => [ $f ],
						'no_group' => true,
					],
					$field_output
				);

				$output .= $field_output;

				if ( $load_posts_query ) {
					HT::$load_posts_query[ 'ht_' . $load_posts_query_id ] = $load_posts_query;
				}
			}

			$output .= additional_script_data(
				[
					'name'   => HT::$namespace . '_load_posts_query',
					'data'   => HT::$load_posts_query,
					'admin'  => false,
					'action' => false,
				]
			);
		}

		/* Output */

		if ( $output ) {
			return (
				'<form class="o-form js-load-more-filter-form" data-type="filters" aria-label="' . ucwords( $post_type ) . ' filters">' .
					'<div class="l-flex l-align-center l-justify-end l-wrap l-gm-3xs l-gm-2xs-m t-ws-nowrap">' .
						'<div class="t-primary-base outline-snug" style="max-width:73.125rem;">' .
							'<div class="l-overflow-hidden">' .
								$output .
							'</div>' .
						'</div>' .
						'<div>' .
							'<button class="fusion-button button-small t-bg-foreground-base t-background-base l-block" type="submit">Filter</button>' .
						'</div>' .
					'</div>' .
				'</form>'
			);
		}

		return '';
	}

	/**
	 * Shortcode to output filters.
	 *
	 * @param array $atts {
	 *  @type string $type
	 * }
	 * @return string
	 */
	public function shortcode( $atts ) {
		$atts = shortcode_atts(
			[
				'type' => 'post',
			],
			$atts,
			self::$handle
		);

		return self::render( $atts['type'] );
	}
}
