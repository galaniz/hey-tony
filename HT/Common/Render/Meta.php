<?php
/**
 * Render single post meta
 *
 * @package hey-tony
 */

namespace HT\Common\Render;

/**
 * Imports
 */

use HT\HT as HT;

/**
 * Class
 */

class Meta {

		/**
		 * Output meta.
		 *
		 * @return string
		 */

		public static function render( $args = [] ) {
				$args = array_merge(
						[
							'archive_label'    => '',
							'categories_label' => '',
							'tags_label'       => '',
							'date_label'       => '',
							'up_date_label'    => '',
							'author_label'     => '',
							'inline'           => true,
							'border'           => false,
							'items'            => '', // comma separated list
							'justify'          => 'center',
						],
						$args
				);

				/* Destructure */

				[
					'archive_label'    => $archive_label,
					'categories_label' => $categories_label,
					'tags_label'       => $tags_label,
					'date_label'       => $date_label,
					'up_date_label'    => $up_date_label,
					'author_label'     => $author_label,
					'inline'           => $inline,
					'border'           => $border,
					'items'            => $items,
					'justify'          => $justify,
				] = $args;

				$inline = filter_var( $inline, FILTER_VALIDATE_BOOLEAN );
				$border = filter_var( $border, FILTER_VALIDATE_BOOLEAN );

				/* Items required */

				if ( ! $items ) {
						return '';
				}

				/* Additional variables */

				$items = explode( ',', $items );

				$id        = get_the_ID();
				$post_type = get_post_type( $id );
				$tax       = 'category';

				if ( 'work' === $post_type ) {
						$tax = 'work_category';
				}

				$output = [];

				/* Loop through items */

				foreach ( $items as $item ) {
						switch ( $item ) {
								case 'archive':
										$archive_link = get_post_type_archive_link( $post_type );

										if ( $archive_link && $archive_label ) {
												/* phpcs:ignore */
												$caret = file_get_contents( get_stylesheet_directory() . '/assets/public/svg/caret-right.svg' ); // Ignore: local path

												$output[] = (
													'<div class="p-s u-fw-b o-underline-r l-pb-1 u-oo-s">' .
														"<a class='l-flex' data-align='center' href='$archive_link'>" .
															"<div class='l-flex l-mr-xxs u-t-180'>$caret</div>" .
															'<span class="u-v-h">Back to </span>' .
															$archive_label .
														'</a>' .
													'</div>'
												);
										}
										break;
								case 'category':
										$cat = HT::get_first_cat( $id, $tax );

										if ( $cat ) {
												$output[] = '<div class="p-s u-fw-b o-underline-r l-pb-1 u-oo-s"><a class="l-flex" href="' . $cat[1] . '">' . $cat[0] . '</a></div>';
										}

										break;
								case 'categories':
										$terms = wp_get_post_terms( $id, $tax );

										if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
												$terms_output = '';

												foreach ( $terms as $term ) {
														$terms_output .= (
															'<div>' .
																'<a class="fusion-button button-flat button-small button-default button-1 fusion-button-default-span fusion-button-default-type button-outline" href="' . get_term_link( $term->term_id, $tax ) . '">' . $term->name . '</a>' .
															'</div>'
														);
												}

												$output[] = (
													"<div class='l-flex' data-align='center' data-justify='$justify' data-gap='xs' data-wrap>" .
														( $categories_label ? "<div class='p-s u-fw-b'><p class='l-m-0'>$categories_label</p></div>" : '' ) .
														'<div>' .
															"<div class='l-flex t-primary-base' data-align='center' data-justify='$justify' data-gap='xs' data-wrap>" .
																$terms_output .
															'</div>' .
														'</div>' .
													'</div>'
												);
										}

										break;
								case 'tags':
										$post_tags = get_the_tags( $id );

										if ( $post_tags ) {
												$tags_output = '';

												foreach ( $post_tags as $tag ) {
														$tags_output .= (
															'<div class="p-s u-fw-b">' .
																'<a class="l-flex" href="' . get_tag_link( $tag->term_id ) . '">' . $tag->name . '</a>' .
															'</div>'
														);
												}

												$output[] = (
													"<div class='l-flex o-underline' data-align='center' data-justify='$justify' data-gap='xs' data-wrap>" .
														( $tags_label ? "<div class='p-s u-fw-b'><p class='l-m-0'>$tags_label</p></div>" : '' ) .
														'<div>' .
															"<div class='l-flex t-primary-base' data-align='center' data-justify='$justify' data-gap='xs' data-wrap>" .
																$tags_output .
															'</div>' .
														'</div>' .
													'</div>'
												);
										}

										break;
								case 'date':
										$og_date   = get_the_date();
										$og_date_c = get_the_date( 'c' );
										$up_date   = '';

										if ( $up_date_label ) {
												$up_date   = get_the_modified_date();
												$up_date_c = get_the_modified_date( 'c' );

												$up_date = ". $up_date_label <time class='u-d-i' datetime='$up_date_c'>$up_date</time>.";
										}

										$og_date = "<time class='u-d-i' datetime='$og_date_c'>$og_date</time>";

										if ( $date_label ) {
												$og_date = "$date_label $og_date";
										}

										$output[] = (
											'<div class="p-xs">' .
												'<p class="l-m-0">' .
													$og_date .
													$up_date .
												'</p>' .
											'</div>'
										);

										break;
								case 'author':
										$author_id   = get_post_field( 'post_author', $id );
										$author_name = get_the_author_meta( 'display_name', $author_id );

										if ( $author_name ) {
												$author_url = get_author_posts_url( $author_id );

												if ( $author_url ) {
														$author_name = "<a href='$author_url' class='u-fw-b'>$author_name</a>";
												}

												$output[] = (
													'<div class="p-s o-underline-r l-pt-xxxs u-oo-s">' .
														'<p>' .
															( $author_label ? "$author_label " : '' ) .
															$author_name .
														'</p>' .
													'</div>'
												);
										}

										break;
						}
				}

				/* Output */

				if ( empty( $output ) ) {
						return '';
				}

				$attr = "data-gap='s' data-align='center' data-justify='$justify'";

				if ( $inline ) {
						$attr .= ' data-gap-l="r" data-wrap';
				} else {
						$attr .= ' data-col';
				}

				$output = array_map(
						function( $o ) use ( $inline ) {
								if ( $inline ) {
										return (
											'<div class="l-flex-grow u-ta-c">' .
												'<div class="o-border-s u-p-r u-tlrb-b">' .
													$o .
												'</div>' .
											'</div>'
										);
								} else {
										return "<div class='u-ta-c'>$o</div>";
								}
						},
						$output
				);

				return (
					( $border ? '<div class="l-pt-xs l-pt-s-l"><div class="l-pt-xs l-pt-s-l u-bt-1">' : '' ) .
					"<div class='l-flex' data-justify='$justify'>" .
						'<div class="u-d-ib' . ( $inline ? ' u-o-h' : '' ) . '">' .
							"<div class='l-flex' $attr>" .
								implode( '', $output ) .
							'</div>' .
						'</div>' .
					'</div>' .
					( $border ? '</div></div>' : '' )
				);
		}

		/**
		 * Shortcode to output meta.
		 *
		 * @param array $atts
		 * @param string $content
		 * @return string
		 */

		public static function shortcode( $atts, $content ) {
				$atts = shortcode_atts(
						[
							'archive_label'    => '',
							'categories_label' => '',
							'tags_label'       => '',
							'date_label'       => '',
							'up_date_label'    => '',
							'author_label'     => '',
							'inline'           => true,
							'border'           => false,
							'items'            => '', // comma separated list
							'justify'          => 'center',
						],
						$atts,
						'ht-meta'
				);

				return self::render( $atts );
		}

} // End Meta
