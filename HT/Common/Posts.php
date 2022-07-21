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
use HT\Common\Render\Cards;
use HT\Common\Render\Columns;
use HT\Common\Render\Testimonial;
use HT\Common\Render\Overlap;
use HT\Common\Render\Slider;
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

				$link_classes = 't-foreground-base l-w-s u-d-b u-b-s u-pb-hover u-br-100-pc u-p-r';

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
					'prev' => HT::get_attr_as_str( $prev ),
					'next' => HT::get_attr_as_str( $next ),
				];
		}

		/**
		 * Simple layout for output.
		 *
		 * @param array $args
		 * @return string
		 */

		private static function simple( $args ) {
				$args = array_merge(
						[
							'title'         => '',
							'link'          => '',
							'pretitle'      => '',
							'pretitle_link' => '',
							'pretitle_a11y' => '',
							'heading_level' => 'h2',
							'index'         => 0,
						],
						$args
				);

				[
					'title'         => $title,
					'link'          => $link,
					'pretitle'      => $pretitle,
					'pretitle_link' => $pretitle_link,
					'pretitle_a11y' => $pretitle_a11y,
					'heading_level' => $heading_level,
					'index'         => $index,
				] = $args;

				/* Pretitle */

				$pretitle_output = '';

				if ( $pretitle ) {
						if ( $pretitle_a11y ) {
								$pretitle_a11y = "<span class='u-v-h'>$pretitle_a11y</span>";
						}

						if ( $pretitle_link ) {
								$pretitle = "<a class='u-p-r u-zi-2' href='$pretitle_link'>$pretitle_a11y$pretitle</a>";
						} else {
								$pretitle = "<p class='l-m-0 u-p-r u-zi-2'>$pretitle_a11y$pretitle</p>";
						}

						$pretitle_output = (
							'<div class="o-underline-r p-xs u-fw-b l-mb-xs l-flex">' .
								$pretitle .
							'</div>'
						);
				}

				/* Output */

				return (
					'<li>' .
						'<div class="l-pb-xxxs l-pb-xs-l' . ( $index > 1 ? ' u-bt-1' : '' ) . ' u-ul-c">' .
							'<div' . ( $index > 1 ? ' class="l-pt-xxxs l-pt-xs-l"' : '' ) . '>' .
								$pretitle_output .
								'<div class="h5 t-foreground-base u-c-i u-ul-w">' .
									"<$heading_level class='l-m-0 o-underline-r'>" .
										"<a href='$link'><span class='u-p-r'>$title</span></a>" .
									"</$heading_level>" .
								'</div>' .
							'</div>' .
						'</div>' .
					'</li>'
				);
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
							'type'               => '',
							'posts_per_page'     => 10,
							'category'           => '',
							'meta_key'           => '',
							'meta_value'         => '',
							'meta_type'          => 'string',
							'ids'                => '',
							'section_title'      => '',
							'a11y_section_title' => '',
							'heading_level'      => 'h2',
							'slider'             => '',
							'current_single'     => false,
							'pagination'         => false,
							'type_archive'       => true,
							'return_array'       => false, // back end
							'query_args'         => [], // back end
							'is_home'            => false, // back end
						],
						$atts,
						'ht-posts'
				);

				/* Destructure */

				[
					'type'               => $type,
					'posts_per_page'     => $posts_per_page,
					'category'           => $category,
					'meta_key'           => $meta_key,
					'meta_value'         => $meta_value,
					'meta_type'          => $meta_type,
					'ids'                => $ids,
					'section_title'      => $section_title,
					'a11y_section_title' => $a11y_section_title,
					'heading_level'      => $heading_level,
					'slider'             => $slider,
					'current_single'     => $current_single,
					'pagination'         => $pagination,
					'type_archive'       => $type_archive,
					'return_array'       => $return_array,
					'query_args'         => $query_args,
					'is_home'            => $is_home,
				] = $atts;

				/* Process args + more variables */

				$posts_per_page = (int) $posts_per_page;
				$post_type      = $type;
				$search         = is_search();
				$archive        = is_archive() || is_home() || $search || is_post_type_archive( $type );
				$is_home        = filter_var( $is_home, FILTER_VALIDATE_BOOLEAN ) || is_home();
				$is_front       = is_front_page();
				$single         = is_singular( $type );
				$tax            = 'category';
				$overflow       = 'hidden';
				$output         = '';

				$type_archive = filter_var( $type_archive, FILTER_VALIDATE_BOOLEAN );
				$pagination   = filter_var( $pagination, FILTER_VALIDATE_BOOLEAN );
				$no_posts     = false;
				$row_count    = 0;
				$offset       = 0;
				$total        = 0;
				$total_pages  = 0;

				$current_single = filter_var( $current_single, FILTER_VALIDATE_BOOLEAN );
				$current_single = $current_single && 1 === $posts_per_page ? true : false;

				if ( ! $type_archive ) {
						$archive = false;
				}

				if ( $section_title || $a11y_section_title ) {
						$heading_level = 'h3';
				}

				if ( -1 !== $posts_per_page && $pagination && $archive ) {
						$posts_per_page_type = $type;

						if ( 'post' === $type && is_archive() ) {
								$posts_per_page_type = 'blog_archives';
						}

						$posts_per_page = HT::get_posts_per_page( $posts_per_page_type );
				}

				if ( 'int' === $meta_type ) {
						$meta_value = (int) $meta_value;
				}

				if ( 'string' === $meta_type ) {
						$meta_value = strval( $meta_value );
				}

				if ( 'work' === $type ) {
						$tax = 'work_category';
				}

				/* Layout */

				$layout         = 'columns';
				$pre_def_layout = HT::$pt[ $type ] ?? false;

				if ( $pre_def_layout ) {
						$layout = $pre_def_layout['layout'];
				}

				if ( 'work' === $type && $single ) {
						$layout = 'columns';
				}

				if ( 'cards' !== $layout ) {
						$slider = false;
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

						if ( $category && 'post' === $type ) {
								$args['category_name'] = $category;
						}

						if ( $category && 'work' === $type ) {
								if ( ! isset( $args['tax_query'] ) ) {
										$args['tax_query'] = [];
								}

								$args['tax_query'][] = [
									'taxonomy' => $tax,
									'field'    => 'slug',
									'terms'    => $category,
								];
						}

						if ( $single && ! $current_single ) {
								$post_id = get_the_ID();

								$args['post__not_in'] = [$post_id];

								$terms = get_the_terms( $post_id, $tax );

								if ( $terms && ! is_wp_error( $terms ) ) {
										$terms = wp_list_pluck( $terms, 'slug' );

										if ( ! isset( $args['tax_query'] ) ) {
												$args['tax_query'] = [];
										}

										$args['tax_query'][] = [
											'taxonomy' => $tax,
											'field'    => 'slug',
											'terms'    => $terms,
										];
								}
						}

						if ( $current_single ) {
								$args['p'] = get_the_ID();
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

						if ( 'search' === $args['post_type'] ) {
								$args['post_type'] = 'any';
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
						$slides    = [];

						while ( $q->have_posts() ) {
								$q->the_post();

								$id            = get_the_ID();
								$title         = get_the_title();
								$link          = get_the_permalink( $id );
								$media_id      = get_post_thumbnail_id( $id );
								$excerpt       = '';
								$content       = '';
								$pretitle      = '';
								$pretitle_a11y = '';
								$pretitle_link = '';

								if ( 'work' === $type || 'post' === $type || 'service' === $type || 'search' === $type ) {
										$excerpt = HT::get_excerpt(
												[
													'post_id'          => $id,
													'words'            => true,
													'length'           => 'cards' === $layout ? 14 : 20,
													'remove_shortcode' => true,
												]
										);
								}

								if ( 'work' === $type || 'post' === $type || 'search' === $type ) {
										$cat = HT::get_first_cat( $id, $tax );

										if ( $cat ) {
												$pretitle      = $cat[0];
												$pretitle_link = $cat[1];
												$pretitle_a11y = 'Category ';
										}
								}

								if ( 'post' === $type && $is_front && $index > 0 ) {
										$layout = 'simple';

										$output .= self::simple(
												[
													'title'         => $title,
													'link'          => $link,
													'pretitle'      => $pretitle,
													'pretitle_link' => $pretitle_link,
													'pretitle_a11y' => $pretitle_a11y,
													'heading_level' => $heading_level,
													'index'         => $index,
												]
										);
								}

								if ( 'columns' === $layout ) {
										$fill  = false;
										$class = '';

										if ( 'testimonial' === $type ) {
												$content = Testimonial::render(
														[
															'text'     => do_shortcode( get_the_content() ),
															'title'    => $title,
															'subtitle' => get_field( 'subtitle', $id ),
															'media_id' => $media_id,
															'center'   => ! ( $archive || $return_array ),
															'large'    => ! ( $archive || $return_array ),
														]
												);
										}

										if ( 'work' === $type ) {
												$fill     = true;
												$class    = 'o-flush u-b-op';
												$overflow = '';

												$content = Columns::render_content(
														[
															'title'         => $title,
															'link'          => $link,
															'excerpt'       => $excerpt,
															'media_id'      => $media_id,
															'pretitle'      => $pretitle,
															'pretitle_link' => $pretitle_link,
															'pretitle_a11y' => $pretitle_a11y,
															'heading_level' => $heading_level,
														]
												);
										}

										if ( 'search' === $type ) {
												$content = Columns::render_content(
														[
															'title'         => $title,
															'link'          => $link,
															'excerpt'       => $excerpt,
															'heading_level' => $heading_level,
															'heading_large' => false,
															'flush'         => false,
														]
												);
										}

										$output .= Columns::render_column(
												[
													'content' => $content,
													'width'   => ( $archive || $return_array || $single ) && ! $current_single ? 50 : 100,
													'fill'    => $fill,
													'class'   => $class,
												]
										);
								}

								if ( 'cards' === $layout ) {
										$card_args = [
											'title'         => $title,
											'link'          => $link,
											'media_id'      => $media_id,
											'heading_level' => $heading_level,
											'index'         => 10,
										];

										if ( ! $single ) {
												$card_args['pretitle']      = $pretitle;
												$card_args['pretitle_link'] = $pretitle_link;
												$card_args['pretitle_a11y'] = $pretitle_a11y;

												if ( 'service' === $type ) {
														$card_args['excerpt'] = $excerpt;
												}
										} else {
												$card_args['small'] = true;
												$card_args['theme'] = 'background-dark';
										}

										if ( $is_home ) {
												$card_args['index'] = $index;

												if ( $index <= 1 ) {
														$card_args['excerpt'] = $excerpt;
												}
										}

										if ( 'post' === $type && $is_front ) {
												$card_args['index']    = $index;
												$card_args['excerpt']  = $excerpt;
												$card_args['li_class'] = 'u-d-ib';
												$card_args['width']    = 100;
										}

										if ( $slider ) {
												$card_args['width']   = 0;
												$card_args['excerpt'] = '';
												$card_args['small']   = true;
										}

										$card_output = Cards::render_card( $card_args );

										$slides[] = $card_output;

										$output .= $card_output;
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
													'pretitle_a11y' => $pretitle_a11y,
													'heading_level' => $heading_level,
												]
										);
								}

								$index++;
						} // end while

						/* Output */

						if ( ! $return_array ) {
								if ( 'columns' === $layout ) {
										$columns_class = 'work' === $type ? 'l-pt-xs l-pb-xs l-pt-r-l l-pb-r-l' : '';

										$output = Columns::render(
												[
													'content'  => $output,
													'class'    => $pagination ? 'js-insert u-empty' : $columns_class,
													'overflow' => $overflow,
												]
										);
								}

								if ( 'cards' === $layout || 'simple' === $layout ) {
										if ( $slider ) {
												$output = Slider::render(
														[
															'slides' => $slides,
															'label'  => $type,
															'loop'   => 'loop' === $slider ? true : false,
														]
												);
										} else {
												$output = Cards::render(
														[
															'content' => $output,
															'class'   => $pagination ? 'js-insert u-empty' : '',
															'flex'    => 'cards' === $layout ? true : false,
														]
												);
										}
								}

								if ( 'overlap' === $layout ) {
										$output = Overlap::render(
												[
													'content' => $output,
													'class'   => $pagination ? 'js-insert u-empty' : '',
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

						$padding = 'post' === $type || 'service' === $type ? 'l-pt-xs l-pt-l-s' : 'l-pt-r l-pt-l-l';

						$output = (
							'<div class="js-load-more-no-res o-notice" data-type="info" aria-live="polite" style="display:' . ( $no_posts ? 'block' : 'none' ) . ';">' .
								'<div class="l-flex u-c-i" data-gap="s" data-align="center" data-justify="def" data-wrap>' .
									'<div>' .
										'<p class="l-m-0 u-fw-b">Sorry looks like nothing was found.</p>' .
									'</div>' .
									'<div>' .
										'<button class="fusion-button button-flat button-small button-default button-1 fusion-button-default-span fusion-button-default-type button-outline js-load-more-no-res__btn" type="button">Reset</button>' .
									'</div>' .
								'</div>' .
							'</div>' .
							'<div class="u-p-r">' .
								$output .
								"<nav class='$padding' aria-label='" . ucwords( $type ) . " pagination'>" .
									'<ul class="l-flex" data-justify="center" data-align="center" data-gap="s">' .
										'<li>' .
											'<a ' . $link_attrs['prev'] . '>' .
												'<div class="o-aspect-ratio l-flex" data-justify="center" data-align="center">' .
													$caret .
												'</div>' .
												'<div class="u-v-h">Previous items</div>' .
											'</a>' .
										'</li>' .
										'<li class="p-s u-fw-b u-ta-c">' .
											'<p class="l-m-0" aria-live="polite">' .
												'<span class="u-v-h">Page </span>' .
												"<span class='js-load-more-current'>$current_page</span>" .
												'<span class="u-v-h"> of </span>' .
												'<span aria-hidden="true"> / </span>' .
												"<span class='js-load-more-total'>$total_pages</span>" .
											'</p>' .
										'</li>' .
										'<li>' .
											'<a ' . $link_attrs['next'] . '>' .
												'<div class="o-aspect-ratio l-flex" data-justify="center" data-align="center">' .
													$caret .
												'</div>' .
												'<div class="u-v-h">Next items</div>' .
											'</a>' .
										'</li>' .
									'</ul>' .
								'</nav>' .
							'</div>' .
							'<div class="js-load-more-err l-flex l-pt-xs" data-justify="center" role="alert" style="display:none;">' .
								'<div class="o-notice l-mw-r l-m-auto u-ta-c u-c-i" data-type="error">' .
									'<p class="l-m-0 u-fw-b">Oops, somewthing went wrong. Please try again later.</p>' .
								'</div>' .
							'</div>'
						);

						$query_static = [
							'is_home' => is_home(),
						];

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

						if ( is_tax( 'work_category' ) ) {
								$query_static['tax_query'] = [
									[
										'taxonomy' => 'work_category',
										'field'    => 'term_id',
										'terms'    => get_queried_object()->term_id,
									],
								];
						}

						if ( ! empty( $query_static ) ) {
								additional_script_data( HT::$namespace . '_load_posts_query_static', $query_static );
						}
				}

				/* Return */

				if ( ! $return_array ) {
						if ( $a11y_section_title ) {
								$output = (
									'<section>' .
										"<h2 class='u-v-h'>$section_title</h2>" .
										$output .
									'</section>'
								);
						} elseif ( $section_title ) {
								$padding = 'l-pt-s l-pb-s l-pt-r-l l-pb-r-l';

								$section_title = (
									'<div class="h4 u-p-r' . ( $slider ? ' u-ta-c l-ph-ctn' : '' ) . '">' .
										"<h2>$section_title</h2>" .
									'</div>'
								);

								if ( 'work' === $type ) {
										$padding        = 'l-pt-s';
										$section_title .= '<div class="l-m-full u-b-op u-bt-1"></div>';
								}

								$output = (
									"<section class='$padding'>" .
										$section_title .
										$output .
									'</section>'
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
