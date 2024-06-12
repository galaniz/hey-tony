<?php
/**
 * Objects - Posts
 *
 * @package hey-tony
 */
namespace HT\Objects\Posts;

/* Imports */

use HT\HT as HT;
use HT\Objects\Testimonial\Testimonial;
use HT\Objects\Overlap\Overlap;
use HT\Objects\Overlap\OverlapVertical;
use HT\Objects\Flush\Flush;
use HT\Objects\Slider\Slider;
use HT\Svg\Caret\CaretSvg;
use function Formation\additional_script_data;

/**
 * Class - render posts shortcode.
 */
class Posts {
	/**
	 * Set shortcode action.
	 *
	 * @return void
	 */
	public function __construct() {
		add_shortcode( 'ht-posts', [$this, 'shortcode'] );
	}

	/**
	 * Pagination link attributes.
	 *
	 * @param array $args {
	 *  @type string $base_link
	 *  @type string $type
	 *  @type int    $current_page
	 *  @type int    $ppp
	 *  @type int    $total
	 *  @type int    $total_pages
	 * }
	 * @return array
	 */
	private static function get_link_attrs( $args = [] ) {
		/* Defaults */

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

		/* Common classes */

		$link_classes = 't-foreground-base l-wd-xs l-ht-xs l-flex b-all e-color b-radius-full l-relative';

		/* Attributes */

		$prev = [
			'class' => "$link_classes js-load-more-prev",
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

		/* Output */

		return [
			'prev' => HT::get_attr_as_str( $prev ),
			'next' => HT::get_attr_as_str( $next ),
		];
	}

	/**
	 * Simple layout for output.
	 *
	 * @param array $args {
	 *  @type string $title
	 *  @type string $link
	 *  @type string $pretitle
	 *  @type string $pretitle_link
	 *  @type string $pretitle_a11y
	 *  @type string $heading_level
	 *  @type int    $index
	 * }
	 * @return string
	 */
	private static function simple( $args ) {
		/* Defaults */

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
				$pretitle_a11y = "<span class='a-hide-vis'>$pretitle_a11y</span>";
			}

			if ( $pretitle_link ) {
				$pretitle = "<a class='l-relative l-zi-2' href='$pretitle_link'>$pretitle_a11y$pretitle</a>";
			} else {
				$pretitle = "<p class='l-m-0 l-relative l-zi-2'>$pretitle_a11y$pretitle</p>";
			}

			$pretitle_output = (
				'<div class="e-underline-r t-xs t-wt-bold l-mb-4xs l-flex">' .
					$pretitle .
				'</div>'
			);
		}

		/* Output */

		return (
			'<li' . ( 1 === $index ? ' class="l-block"' : '' ) . '>' .
				'<div class="l-pb-2xs l-pb-s-m ' . ( $index > 1 ? 'b-top' : 'l-inline-block' ) . ' e-underline--primary">' .
					'<div' . ( $index > 1 ? ' class="l-pt-2xs l-pt-s-m"' : '' ) . '>' .
						$pretitle_output .
						'<div class="h5 t-foreground-base t-inherit e-underline--thick">' .
							"<$heading_level class='l-m-0 e-underline-r'>" .
								"<a href='$link'><span class='l-relative'>$title</span></a>" .
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
	 * @param array $atts {
	 *  @type string $type
	 *  @type int    $posts_per_page
	 *  @type string $category
	 *  @type string $meta_key
	 *  @type string $meta_value
	 *  @type string $meta_type
	 *  @type string $ids
	 *  @type string $section_title
	 *  @type string $section_title_heading_level
	 *  @type string $a11y_section_title
	 *  @type string $heading_level
	 *  @type string $slider
	 *  @type bool   $current_single
	 *  @type bool   $pagination
	 *  @type bool   $type_archive
	 *  @type bool   $return_array - Back end
	 *  @type array  $query_args - Back end
	 *  @type bool   $is_home - Back end
	 * }
	 * @param string $content
	 * @return string
	 */
	public static function shortcode( $atts, $content ) {
		/* Defaults */

		$atts = shortcode_atts(
			[
				'type'                        => '',
				'posts_per_page'              => 10,
				'category'                    => '',
				'meta_key'                    => '',
				'meta_value'                  => '',
				'meta_type'                   => 'string',
				'ids'                         => '',
				'section_title'               => '',
				'section_title_heading_level' => 'h2',
				'a11y_section_title'          => '',
				'heading_level'               => 'h2',
				'slider'                      => '',
				'current_single'              => false,
				'pagination'                  => false,
				'type_archive'                => true,
				'return_array'                => false,
				'query_args'                  => [],
				'is_home'                     => false,
			],
			$atts,
			'ht-posts'
		);

		[
			'type'                        => $type,
			'posts_per_page'              => $posts_per_page,
			'category'                    => $category,
			'meta_key'                    => $meta_key,
			'meta_value'                  => $meta_value,
			'meta_type'                   => $meta_type,
			'ids'                         => $ids,
			'section_title'               => $section_title,
			'section_title_heading_level' => $section_title_heading_level,
			'a11y_section_title'          => $a11y_section_title,
			'heading_level'               => $heading_level,
			'slider'                      => $slider,
			'current_single'              => $current_single,
			'pagination'                  => $pagination,
			'type_archive'                => $type_archive,
			'return_array'                => $return_array,
			'query_args'                  => $query_args,
			'is_home'                     => $is_home,
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

		$layout         = 'flush';
		$pre_def_layout = HT::$pt[ $type ] ?? false;

		if ( $pre_def_layout ) {
			$layout = $pre_def_layout['layout'];
		}

		if ( 'work' === $type && $single ) {
			$layout = 'flush';
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
							'length'           => 'cards' === $layout ? 15 : 20,
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

				if ( 'flush' === $layout ) {
					$fill  = false;

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
						$overflow = '';

						$content = Flush::render_content(
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
						$content = Flush::render_content(
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

					$output .= Flush::render_item(
						[
							'content' => $content,
							'width'   => ( $archive || $return_array || $single ) && ! $current_single ? 50 : 100,
							'fill'    => $fill,
							'type'    => $type,
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
						$card_args['li_class'] = 'l-inline-block';
						$card_args['width']    = 100;
					}

					if ( $slider ) {
						$card_args['width']   = 0;
						$card_args['excerpt'] = '';
						$card_args['small']   = true;
					}

					$card_output = OverlapVertical::render_item( $card_args );

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
			}

			/* Output */

			if ( ! $return_array ) {
				if ( 'flush' === $layout ) {
					$cards_class = 'work' === $type ? 'l-pt-s l-pb-s l-pt-l-m l-pb-l-m' : '';

					$output = Flush::render(
						[
							'content'  => $output,
							'class'    => $pagination ? 'js-insert l-empty' : $cards_class,
							'overflow' => $overflow,
							'type'     => $type,
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
						$output = OverlapVertical::render(
							[
								'content' => $output,
								'class'   => $pagination ? 'js-insert l-empty' : '',
								'flex'    => 'cards' === $layout ? true : false,
							]
						);
					}
				}

				if ( 'overlap' === $layout ) {
					$output = Overlap::render(
						[
							'content' => $output,
							'class'   => $pagination ? 'js-insert l-empty' : '',
						]
					);
				}
			}

			wp_reset_postdata();
		}

		/* Pagination */

		if ( -1 !== $posts_per_page && $pagination && $archive ) {
			if ( ! $total ) {
				$count_posts = wp_count_posts();
				$total       = $count_posts ? $count_posts->publish : 0;
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

			$caret_left  = CaretSvg::render( 'left', 'l-flex l-m-auto l-ht-3xs l-wd-3xs' );
			$caret_right = CaretSvg::render( 'right', 'l-flex l-m-auto l-ht-3xs l-wd-3xs' );

			/* Output */

			$padding = 'post' === $type || 'service' === $type ? 'l-pt-s l-pt-3xl-s' : 'l-pt-l l-pt-3xl-m';

			$output = (
				'<div class="js-load-more-no-res o-notice" data-type="info" aria-live="polite" style="display:' . ( $no_posts ? 'block' : 'none' ) . ';">' .
					'<div class="l-flex l-align-center l-justify-between l-wrap l-gm-2xs t-inherit">' .
						'<div>' .
							'<p class="l-m-0 t-wt-bold">Sorry looks like nothing was found.</p>' .
						'</div>' .
						'<div>' .
							'<button class="fusion-button button-flat button-small button-default button-1 fusion-button-default-span fusion-button-default-type button-outline js-load-more-no-res__btn" type="button">Reset</button>' .
						'</div>' .
					'</div>' .
				'</div>' .
				'<div class="l-relative">' .
					$output .
					"<nav class='$padding' aria-label='" . ucwords( $type ) . " pagination'>" .
						'<ul class="l-flex l-align-center l-justify-center l-gm-2xs t-ls-none" role="list">' .
							'<li>' .
								'<a ' . $link_attrs['prev'] . '>' .
									$caret_left .
									'<div class="a-hide-vis">Previous items</div>' .
								'</a>' .
							'</li>' .
							'<li class="t-s t-wt-bold t-align-center">' .
								'<p class="l-m-0" aria-live="polite">' .
									'<span class="a-hide-vis">Page </span>' .
									"<span class='js-load-more-current'>$current_page</span>" .
									'<span class="a-hide-vis"> of </span>' .
									'<span aria-hidden="true"> / </span>' .
									"<span class='js-load-more-total'>$total_pages</span>" .
								'</p>' .
							'</li>' .
							'<li>' .
								'<a ' . $link_attrs['next'] . '>' .
									$caret_right .
									'<div class="a-hide-vis">Next items</div>' .
								'</a>' .
							'</li>' .
						'</ul>' .
					'</nav>' .
				'</div>' .
				'<div class="js-load-more-err l-flex l-justify-center l-pt-s" role="alert" style="display:none;">' .
					'<div class="o-notice l-wd-full l-m-auto t-align-center t-inherit" data-type="error">' .
						'<p class="l-m-0 t-wt-bold">Oops, somewthing went wrong. Please try again later.</p>' .
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
				$output .= additional_script_data(
					[
						'name'   => HT::$namespace . '_load_posts_query_static',
						'data'   => $query_static,
						'admin'  => false,
						'action' => false,
					]
				);
			}
		}

		/* Return */

		if ( ! $return_array ) {
			if ( $output ) {
				if ( $a11y_section_title ) {
					$output = (
						'<section>' .
							"<$section_title_heading_level class='a-hide-vis'>$section_title</$section_title_heading_level>" .
							$output .
						'</section>'
					);
				} elseif ( $section_title ) {
					$padding = 'l-pt-m l-pb-m l-pt-l-m l-pb-l-m';

					$section_title = (
						'<div class="h4 l-relative' . ( $slider ? ' t-align-center l-px-container' : '' ) . '">' .
							"<$section_title_heading_level>$section_title</$section_title_heading_level>" .
						'</div>'
					);

					if ( 'work' === $type ) {
						$padding        = 'l-pt-m';
						$section_title .= '<div class="l-m-full b-top"></div>';
					}

					$output = (
						"<section class='$padding'>" .
							$section_title .
							$output .
						'</section>'
					);
				}
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
}
