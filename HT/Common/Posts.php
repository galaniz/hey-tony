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

use HT\HT as HT;
use HT\Utils;
use HT\Common\Render\Cards;
use HT\Common\Render\Columns;
use HT\Common\Render\Testimonial;
use HT\Common\Render\Overlap;
use function Formation\additional_script_data;

/**
 * Class
 */

class Posts {

		/**
		 * Pagination link attributes.
		 *
		 * @param array $args
		 * @return string
		 */

		private static function get_link_attrs( $args = [] ) {
				$args = array_merge(
						[
							'base_link'    => '',
							'type'         => '',
							'current_page' => 1,
							'ppp'          => 1,
							'total'        => 1,
							'total_pages'  => 1,
						],
						$args
				);

				[
					'base_link'    => $base_link,
					'type'         => $type,
					'current_page' => $current_page,
					'ppp'          => $ppp,
					'total'        => $total,
					'total_pages'  => $total_pages,
				] = $args;

				$link_classes = 't-foreground-base l-w-s u-d-b u-b-s u-b-hover u-br-100-pc u-p-r';

				$prev = [
					'class' => "$link_classes u-t-180 js-load-more-prev",
					'role'  => 'link',
				];

				$next = [
					'class'          => "$link_classes js-load-more",
					'data-base-link' => $base_link,
					'data-type'      => $type,
					'data-page'      => $current_page,
					'data-per-page'  => $ppp,
					'data-total'     => $total,
					'role'           => 'link',
				];

				$prev_link = '';
				$next_link = '';

				$prev_url_params = HT::$get_query_url_params;
				$next_url_params = HT::$get_query_url_params;

				if ( $current_page > 1 ) {
						$prev_link             = $base_link;
						$prev_url_params['pg'] = $current_page - 1;
				}

				if ( $current_page < $total_pages ) {
						$next_link             = $base_link;
						$next_url_params['pg'] = $current_page + 1;
				}

				$prev_url_params = http_build_query( $prev_url_params );
				$next_url_params = http_build_query( $next_url_params );

				if ( ! $prev_link ) {
						$prev['aria-disabled'] = 'true';
				} else {
						$prev_link   .= ( $prev_url_params ? "?$prev_url_params" : '' );
						$prev['href'] = $prev_link;
				}

				if ( ! $next_link ) {
						$next['aria-disabled'] = 'true';
				} else {
						$next_link   .= ( $next_url_params ? "?$next_url_params" : '' );
						$next['href'] = $next_link;
				}

				return [
					'prev' => Utils::get_attr_as_str( $prev ),
					'next' => Utils::get_attr_as_str( $next ),
				];
		}

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
							'section_title'  => '',
							'pagination'     => false,
							'return_array'   => false, // back end
							'query_args'     => [], // back end
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
					'section_title'  => $section_title,
					'pagination'     => $pagination,
					'return_array'   => $return_array,
					'query_args'     => $query_args,
				] = $atts;

				/* Process args + more variables */

				$posts_per_page = (int) $posts_per_page;
				$post_type      = $type;
				$search         = is_search();
				$archive        = is_archive() || is_home() || $search || is_post_type_archive( $type );
				$single         = is_singular( $type );

				$pagination  = filter_var( $pagination, FILTER_VALIDATE_BOOLEAN );
				$no_posts    = false;
				$row_count   = 0;
				$offset      = 0;
				$total       = 0;
				$total_pages = 0;

				if ( -1 !== $posts_per_page && $pagination && $archive ) {
						$posts_per_page_type = $type;

						if ( 'search' === $type ) {
								$posts_per_page_type = 'post';
						}

						$posts_per_page = Utils::get_posts_per_page( $posts_per_page_type );
				}

				if ( 'search' === $type ) {
						$post_type = 'any';
				}

				$output = '';

				$pre_def_layout = HT::$post_types_info[ $type ] ?? false;

				if ( $pre_def_layout ) {
						$layout = $pre_def_layout['layout'];
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
							'post_type'      => $post_type,
							'posts_per_page' => $posts_per_page,
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

						if ( $single ) {
								$post_id = get_the_ID();

								$args['post__not_in'] = [$post_id];

								$taxonomy = 'category';

								if ( 'work' === $type ) {
										$taxonomy = 'work_category';
								}

								$terms = get_the_terms( $post_id, $taxonomy );

								if ( $terms && ! is_wp_error( $terms ) ) {
										$terms = wp_list_pluck( $terms, 'slug' );

										$args['tax_query'] = [
											[
												'taxonomy' => $taxonomy,
												'field'    => 'slug',
												'terms'    => $terms,
											],
										];
								}
						}

						if ( $meta_key && $meta_value ) {
								$args['meta_query'][] = [
									'key'     => $meta_key,
									'value'   => $meta_value,
									'compare' => 'LIKE',
								];
						}

						/* Merge query_args with args */

						if ( is_array( $query_args ) && count( $query_args ) > 0 ) {
								$args = array_replace_recursive( $args, $query_args );
						}

						$q = new \WP_Query( $args );
				}

				/* Query info */

				if ( is_object( $q ) ) {
						$total       = $q->found_posts;
						$total_pages = $q->max_num_pages;
						$no_posts    = ! $q->found_posts;
				}

				/* The Loop */

				if ( $q->have_posts() ) {
						$row_count = $q->post_count;
						$index     = 0;

						while ( $q->have_posts() ) {
								$q->the_post();

								$id       = get_the_ID();
								$text     = apply_filters( 'the_content', get_the_content() );
								$title    = get_the_title();
								$link     = get_the_permalink( $id );
								$media_id = get_post_thumbnail_id( $id );
								$pretitle = '';
								$excerpt  = '';
								$content  = '';

								if ( 'testimonial' === $type ) {
										$content = Testimonial::render(
												[
													'text'     => $text,
													'title'    => $title,
													'subtitle' => get_field( 'subtitle', $id ),
													'media_id' => $media_id,
													'center'   => ! ( $archive || $return_array ),
													'large'    => ! ( $archive || $return_array ),
												]
										);
								}

								if ( 'columns' === $layout ) {
										$output .= Columns::render_column(
												[
													'content' => $content,
													'width'   => $archive || $return_array ? 50 : 100,
												]
										);
								}

								if ( 'cards' === $layout || 'overlap' === $layout ) {
										$pretitle      = '';
										$pretitle_link = '';

										$excerpt = Utils::get_excerpt(
												[
													'post_id' => $id,
													'content' => $text,
													'words'   => true,
													'length'  => 'cards' === $layout ? 15 : 20,
												]
										);

										$tax = 'category';

										if ( 'work' === $type ) {
												$tax = 'work_category';
										}

										$cat = Utils::get_first_cat( $id, $tax );

										if ( $cat ) {
												$pretitle      = $cat[0];
												$pretitle_link = $cat[1];
										}
								}

								if ( 'cards' === $layout ) {
										$card_args = [
											'title'    => $title,
											'link'     => $link,
											'excerpt'  => $excerpt,
											'media_id' => $media_id,
											'index'    => 10,
										];

										if ( ! $single ) {
												$card_args['pretitle']      = $pretitle;
												$card_args['pretitle_link'] = $pretitle_link;
										} else {
												$card_args['small'] = true;
												$card_args['theme'] = 'background-dark';
										}

										if ( is_home() ) {
												$card_args['index'] = $index;
										}

										$output .= Cards::render_card( $card_args );
								}

								if ( 'overlap' === $layout ) {
										$output .= Overlap::render_item(
												[
													'title'         => $title,
													'link'          => $link,
													'excerpt'       => $excerpt,
													'media_id'      => $media_id,
													'pretitle'      => $pretitle,
													'pretitle_link' => $pretitle_link,
												]
										);
								}

								$index++;
						} // end while

						/* Output */

						if ( ! $return_array ) {
								if ( 'columns' === $layout ) {
										$output = Columns::render(
												[
													'content' => $output,
													'class'   => $pagination ? 'js-insert' : '',
												]
										);
								}

								if ( 'cards' === $layout ) {
										$output = Cards::render(
												[
													'content' => $output,
													'class'   => $pagination ? 'js-insert' : '',
												]
										);
								}

								if ( 'overlap' === $layout ) {
										$output = Overlap::render(
												[
													'content' => $output,
													'class'   => $pagination ? 'js-insert' : '',
												]
										);
								}
						}

						wp_reset_postdata();
				}

				/* Pagination */

				if ( -1 !== $posts_per_page && $pagination && $archive ) {
						if ( ! $total ) {
								$total = wp_count_posts( $post_type )->publish;
						}

						$current_page = HT::$get_query_args['paged'] ?? 1;

						if ( $no_posts ) {
								$current_page = 0;
						}

						$base_link = get_post_type_archive_link( $type );

						if ( $search ) {
								$base_link = get_search_link();
						}

						$link_attrs = self::get_link_attrs(
								[
									'base_link'    => $base_link,
									'type'         => $type,
									'current_page' => $current_page,
									'ppp'          => $posts_per_page,
									'total'        => $total,
									'total_pages'  => $total_pages,
								]
						);

						/* phpcs:ignore */
						$caret = file_get_contents( get_stylesheet_directory() . '/assets/public/svg/caret-right.svg' ); // Ignore: local path

						/* Output */

						$padding = 'post' === $type ? 'l-pt-xs l-pt-l-s' : 'l-pt-r l-pt-l-l';

						$output = (
							'<div class="u-p-r" aria-live="polite">' .
								$output .
								"<div class='$padding'>" .
									'<div class="l-flex" data-justify="center" data-align="center" data-gap="s">' .
										'<div>' .
											'<a ' . $link_attrs['prev'] . '>' .
												'<div class="o-aspect-ratio l-flex" data-justify="center" data-align="center">' .
													$caret .
												'</div>' .
												'<div class="u-v-h">Previous</div>' .
											'</a>' .
										'</div>' .
										'<div class="p-s u-fw-b u-ta-c">' .
											'<p class="l-m-0">' .
												"<span class='js-load-more-current'>$current_page</span>" .
												'<span class="u-v-h"> of </span>' .
												'<span aria-hidden="true"> / </span>' .
												"<span class='js-load-more-total'>$total_pages</span>" .
											'</p>' .
										'</div>' .
										'<div>' .
											'<a ' . $link_attrs['next'] . '>' .
												'<div class="o-aspect-ratio l-flex" data-justify="center" data-align="center">' .
													$caret .
												'</div>' .
												'<div class="u-v-h">Next</div>' .
											'</a>' .
										'</div>' .
									'</div>' .
								'</div>' .
							'</div>'
						);

						$query_static = [];

						if ( $search ) {
								$query_static['s'] = get_search_query();
						}

						if ( is_author() ) {
								$query_static['author'] = get_queried_object_id();
						}

						if ( is_tag() ) {
								$query_static['tag_id'] = get_queried_object()->term_id;
						}

						if ( is_category() ) {
								$query_static['cat'] = get_queried_object()->term_id;
						}

						if ( ! empty( $query_static ) ) {
								additional_script_data( HT::$namespace . '_load_posts_query_static', $query_static );
						}
				}

				/* Return */

				if ( ! $return_array ) {
						if ( $section_title ) {
								$output = (
									'<div class="l-pt-s l-pb-s l-pt-r-l l-pb-r-l">' .
										'<div class="h4">' .
											"<h2>$section_title</h2>" .
										'</div>' .
										$output .
									'</div>'
								);
						}

						return $output;
				} else {
						return [
							'row_count' => $row_count,
							'output'    => $output,
							'total'     => $total,
						];
				}
		}

} // End Posts
