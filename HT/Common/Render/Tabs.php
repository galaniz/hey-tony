<?php
/**
 * Render tabs
 *
 * @package hey-tony
 */

namespace HT\Common\Render;

/**
 * Imports
 */

use HT\Utils;

/**
 * Class
 */

class Tabs {

		/**
		 * Shortcode to output tablist.
		 *
		 * @param array $atts
		 * @param string $content
		 * @return string
		 */

		public static function shortcode_tablist( $atts, $content ) {
				$atts = shortcode_atts(
						[
							'ids'      => '', // Comma separated list
							'titles'   => '', // Comma separated list
							'tab_ids'  => '', // Comma separated list
							'selected' => 1,
							'steps'    => false,
						],
						$atts,
						'ht-tablist'
				);

				/* Destructure */

				[
					'ids'      => $ids,
					'titles'   => $titles,
					'tab_ids'  => $tab_ids,
					'selected' => $selected,
					'steps'    => $steps,
				] = $atts;

				/* Process variables */

				$selected = ( (int) $selected ) - 1;
				$ids      = explode( ',', $ids );
				$titles   = explode( ',', $titles );
				$tab_ids  = explode( ',', $tab_ids );
				$steps    = filter_var( $steps, FILTER_VALIDATE_BOOLEAN );
				$classes  = 't-bg-inherit fusion-button button-small button-outline avada-noscroll';

				if ( $steps ) {
						$classes = 't-foreground-base t-bg-inherit l-w-m u-d-b u-b-s u-br-100-pc u-p-r p-s u-fw-b';
				}

				/* Ids, tab_ids and titles required */

				if ( empty( $ids ) || empty( $tab_ids ) || empty( $titles ) ) {
						return '';
				}

				/* Output */

				$items = [];

				foreach ( $ids as $i => $id ) {
						$index   = $i + 1;
						$link    = $tab_ids[ $i ];
						$title   = $titles[ $i ];
						$current = $i === $selected ? 'true' : 'false';

						if ( $steps ) {
								$title = (
									'<div class="o-aspect-ratio l-flex" data-justify="center" data-align="center">' .
										"<span class='u-v-h'>$title</span>" .
										"<div class='u-p-a' aria-hidden='true'>$index</div>" .
									'</div>'
								);
						}

						$items[] = (
							'<li class="l-flex t-bg-inherit u-ws-nw" role="presentation">' .
								"<a href='#$link' role='tab' aria-selected='$current' id='$id' class='$classes'>$title</a>" .
							'</li>'
						);
				}

				return (
					'<div class="l-pb-xxxs l-pb-xs-l t-bg-inherit">' .
						'<div class="u-o-xy l-flex t-bg-inherit">' .
							'<div class="l-flex-grow u-p-r t-bg-inherit">' .
								'<ul class="l-flex u-tlrb-b t-bg-inherit u-oo-s" role="tablist" data-gap="r" data-justify="def">' .
									implode( '', $items ) .
								'</ul>' .
							'</div>' .
						'</div>' .
					'</div>'
				);
		}

		/**
		 * Shortcode to output single tab.
		 *
		 * @param array $atts
		 * @param string $content
		 * @return string
		 */

		public static function shortcode_tab( $atts, $content ) {
				$atts = shortcode_atts(
						[
							'id'         => '',
							'tablist_id' => '',
							'title'      => '',
							'img_id'     => 0,
							'selected'   => false,
						],
						$atts,
						'ht-tab'
				);

				/* Destructure */

				[
					'id'         => $id,
					'tablist_id' => $tablist_id,
					'title'      => $title,
					'img_id'     => $img_id,
					'selected'   => $selected,
				] = $atts;

				$img_id   = (int) $img_id;
				$selected = filter_var( $selected, FILTER_VALIDATE_BOOLEAN );
				$hidden   = ! $selected;

				$data_selected = $selected ? 'true' : 'false';

				/* Id, tablist id + title required */

				if ( ! $id || ! $tablist_id || ! $title ) {
						return '';
				}

				/* Image */

				$img = '';

				if ( $img_id ) {
						$image = Utils::get_image( $img_id, 'medium_large' );

						if ( $image ) {
								$src    = esc_url( $image['url'] );
								$srcset = esc_attr( $image['srcset'] );
								$sizes  = esc_attr( $image['sizes'] );
								$wid    = esc_attr( $image['width'] );
								$height = esc_attr( $image['height'] );
								$alt    = esc_attr( $image['alt'] );

								$img = "<img class='l-w-l' src='$src' alt='$alt' srcset='$srcset' sizes='$sizes' width='$wid' height='$height' loading='lazy'>";
						}
				}

				/* Output */

				return (
					"<section class='l-pt-xxxs' id='$id' tabindex='-1' role='tabpanel' aria-labelledby='$tablist_id' data-selected='$data_selected'" . ( $selected ? '' : ' hidden' ) . '>' .
						'<div class="l-mb-l-all">' .
							$img .
							'<div class="l-mb-r-all">' .
								'<div class="h4">' .
									"<h3 class='l-m-0'>$title</h3>" .
								'</div>' .
								'<div class="p-m l-mb-r-all">' .
									wpautop( $content ) .
								'</div>' .
							'</div>' .
						'</div>' .
					'</section>'
				);
		}

} // End Tabs
