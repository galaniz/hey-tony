<?php
/**
 * Render filters for archive
 *
 * @package hey-tony
 */

namespace HT\Common\Render;

/**
 * Imports
 */

use HT\HT as HT;
use Formation\Common\Field\Field;
use function Formation\additional_script_data;

/**
 * Class
 */

class Filters {

	/**
	 * Get args for filters on archives.
	 *
	 * @param string $post_type
	 * @return array
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

		if ( $options ) {
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
				'label_class'       => 'u-v-h',
				'opt_buttons_class' => 'l-flex',
				'opt_buttons_attr'  => [
					'data-gap'   => 'xs',
					'data-gap-l' => 's',
					'data-align' => 'center',
				],
				'opt_button_class'  => 'fusion-button button-small button-outline u-d-b',
				'field_class'       => 'o-overflow l-mw-full l-pb-xxxs u-o-xy',
				'load_posts_query'  => [
					'tax_query' => [
						[
							'taxonomy' => $tax,
							'terms'    => '%value:int',
						],
					],
				],
			];
		}

		return $args;
	}

	/**
	 * Output filters.
	 *
	 * @return string
	 */

	public static function render( $post_type = '' ) {
		if ( ! $post_type ) {
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

				$f['class'] = 'js-load-more-filter' . ( isset( $f['class'] ) ? ' ' . $f['class'] : '' );
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

		/* Outupt */

		if ( $output ) {
			return (
				'<form class="o-form js-load-more-filter-form" data-type="filters" aria-label="' . ucwords( $post_type ) . ' filters">' .
					'<div class="l-flex u-ws-nw" data-justify="right" data-gap="xs" data-gap-l="s" data-align="center" data-justify="right" data-wrap>' .
						'<div class="t-primary-base u-oo-s" style="max-width:73.125rem;">' .
							'<div class="l-h-s u-o-h">' .
								$output .
							'</div>' .
						'</div>' .
						'<div>' .
							'<button class="fusion-button button-small t-bg-foreground-base t-background-base u-d-b" type="submit">Filter</button>' .
						'</div>' .
					'</div>' .
				'</form>'
			);
		} else {
			return '';
		}
	}

	/**
	 * Shortcode to output filters.
	 *
	 * @param array $atts
	 * @param string $content
	 * @return string
	 */

	public static function shortcode( $atts, $content ) {
		$atts = shortcode_atts(
			[
				'type' => 'post',
			],
			$atts,
			'ht-filters'
		);

		return self::render( $atts['type'] );
	}

} // End Filters
