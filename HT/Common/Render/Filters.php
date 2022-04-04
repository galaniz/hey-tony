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

		public static function get_filter_args() {
				$post_type = is_archive() ? get_queried_object()->name : false;
				$tax       = 'category';
				$n         = HT::$namespace;
				$options   = [];
				$args      = [];
				$val       = '';

				if ( 'work' === $post_type ) {
						$tax = 'work_category';

						$terms = get_terms(
								[
									'taxonomy'   => $tax,
									'hide_empty' => true,
								]
						);

						if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
								foreach ( $terms as $term ) {
										$options[] = [
											'label' => $term->name,
											'value' => $term->term_id,
										];
								}
						}
				}

				if ( $options ) {
						$args[] = [
							'id'                => $n . '_cat_' . uniqid(),
							'type'              => 'radio-group',
							'options'           => $options,
							'value'             => $val,
							'label'             => 'Categories',
							'label_class'       => 'u-v-h',
							'opt_buttons_class' => 'l-flex',
							'opt_buttons_attr'  => [
								'data-gap'     => 's',
								'data-align'   => 'center',
								'data-justify' => 'end',
								'data-wrap'    => '',
							],
							'opt_button_class'  => 'fusion-button button-flat button-small button-default button-1 fusion-button-default-span fusion-button-default-type button-outline',
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

		public static function render() {
				$fields = self::get_filter_args();
				$output = '';

				if ( ! empty( $fields ) ) {
						foreach ( $fields as $f ) {
								$field_output = '';

								$f['name'] = $f['id'];

								Field::render(
										[
											'fields'   => [ $f ],
											'no_group' => true,
										],
										$field_output
								);

								$output .= $field_output;
						}
				}

				/* Outupt */

				if ( $output ) {
						return (
							'<div class="u-p-r u-f-p">' .
								'<form class="o-form" data-type="filters">' .
									'<div class="l-flex t-primary-base" data-align="center" data-justify="right" data-wrap>' .
										$output .
									'</div>' .
								'</form>' .
							'</div>'
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
				return self::render();
		}

} // End Filters
