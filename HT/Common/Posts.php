<?php
/**
 * Posts shortcode
 *
 * @package hey-tony
 */

namespace HT\Common;

/**
 * Imports
 */

use HT\Common\Render\Columns;
use HT\Common\Render\Testimonial;

/**
 * Class
 */

class Posts {

		/**
		 * Shortcode to output different post types.
		 *
		 * @param array $atts
		 * @param string $content
		 * @return string
		 */

		public static function shortcode( $atts, $content ) {
				$atts = shortcode_atts(
						[
							'type'           => '',
							'posts_per_page' => 10,
							'layout'         => 'columns',
							'ids'            => '',
							'return_array'   => false,
						],
						$atts,
						'ht-posts'
				);

				/* Destructure */

				[
					'type'           => $type,
					'posts_per_page' => $posts_per_page,
					'layout'         => $layout,
					'ids'            => $ids,
					'return_array'   => $return_array,
				] = $atts;

				/* Variables */

				$posts_per_page = (int) $posts_per_page;

				$archive = is_post_type_archive( $type );

				if ( 'post' === $type && is_archive() ) {
						$archive = true;
				}

				$output = '';

				/* Process query */

				if ( $archive ) {
						global $wp_query;
						$q = $wp_query;
				} else {
						$args = [
							'post_type'        => $type,
							'posts_per_page'   => $posts_per_page,
							'suppress_filters' => true,
						];

						$q = new WP_Query( $args );
				}

				/* The Loop */

				if ( $q->have_posts() ) {
						$total     = $q->found_posts;
						$row_count = $q->post_count;

						while ( $q->have_posts() ) {
								$q->the_post();

								$content = '';

								if ( 'testimonial' === $type ) {
										$content = Testimonial::render(
												[
													'center' => false,
													'large'  => false,
												]
										);
								}

								if ( 'columns' === $layout ) {
										$output .= Columns::render_column( $content );
								}
						}

						if ( ! $return_array ) {
								if ( 'columns' === $layout ) {
										$output = Columns::render( $output );
								}
						}
				}

				/* Output */

				return $output;
		}

} // End Posts
