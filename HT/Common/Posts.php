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
use HT\Common\Render\Overlap;
use Formation\Utils;

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
							'meta_key'       => '',
							'meta_value'     => '',
							'meta_type'      => 'string',
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
					'meta_key'       => $meta_key,
					'meta_value'     => $meta_value,
					'meta_type'      => $meta_type,
					'ids'            => $ids,
					'return_array'   => $return_array,
				] = $atts;

				/* Process args + more variables */

				$posts_per_page = (int) $posts_per_page;
				$archive        = is_post_type_archive( $type ) || ( 'post' === $type && is_archive() ) ? true : false;
				$output         = '';

				if ( 'work' === $type ) {
						$layout = 'overlap';
				}

				if ( 'int' === $meta_type ) {
						$meta_value = (int) $meta_value;
				}

				if ( 'string' === $meta_type ) {
						$meta_value = strval( $meta_value );
				}

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

						if ( $ids ) {
								$post_ids = explode( ',', $ids );

								$post_ids = array_map(
										function( $v ) {
												return (int) $v;
										},
										$post_ids
								);

								$args['post__in'] = $post_ids;
						}

						if ( $meta_key && $meta_value ) {
								$args['meta_query'][] = [
									'key'     => $meta_key,
									'value'   => $meta_value,
									'compare' => 'LIKE',
								];
						}

						$q = new \WP_Query( $args );
				}

				/* The Loop */

				if ( $q->have_posts() ) {
						$total     = $q->found_posts;
						$row_count = $q->post_count;

						while ( $q->have_posts() ) {
								$q->the_post();

								$id       = get_the_ID();
								$text     = get_the_content();
								$title    = get_the_title();
								$link     = get_the_permalink( $id );
								$media_id = get_post_thumbnail_id( $id );

								$content = '';

								if ( 'testimonial' === $type ) {
										$content = Testimonial::render(
												[
													'text'     => $text,
													'title'    => $title,
													'subtitle' => get_field( 'subtitle', $id ),
													'media_id' => $media_id,
													'center'   => ! $archive,
													'large'    => ! $archive,
												]
										);
								}

								if ( 'columns' === $layout ) {
										$output .= Columns::render_column(
												[
													'content' => $content,
												]
										);
								}

								if ( 'overlap' === $layout ) {
										$pretitle = '';

										if ( 'work' === $type ) {
												$cat = Utils::get_first_cat( $id, 'work_category' );

												if ( $cat ) {
														$pretitle = $cat[0];
												}
										}

										$excerpt = Utils::get_excerpt(
												[
													'post_id' => $id,
													'content' => $text,
													'words'   => true,
													'length'  => 20,
												]
										);

										$output .= Overlap::render_item(
												[
													'title'    => $title,
													'link'     => $link,
													'excerpt'  => $excerpt,
													'media_id' => $media_id,
													'pretitle' => $pretitle,
												]
										);
								}
						} // end while

						if ( ! $return_array ) {
								if ( 'columns' === $layout ) {
										$output = Columns::render(
												[
													'content' => $output,
													'width'   => $archive ? 50 : 100,
												]
										);
								}

								if ( 'overlap' === $layout ) {
										$output = Overlap::render(
												[
													'content' => $output,
												]
										);
								}
						}

						wp_reset_postdata();
				}

				/* Output */

				return $output;
		}

} // End Posts
