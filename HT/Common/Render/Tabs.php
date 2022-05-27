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
		 * Shortcode to output tabs.
		 *
		 * @param array $atts
		 * @param string $content
		 * @return string
		 */

		public static function shortcode( $atts, $content ) {
				$atts = shortcode_atts(
						[
							'index' => 0,
						],
						$atts,
						'ht-tabs'
				);

				/* Destructure */

				[
					'index' => $index,
				] = $atts;

				$index = (int) $index;

				/* ACF fields */

				$display_tabs = get_field( 'display_tabs' )[0] ?? false;

				if ( '1' !== $display_tabs ) {
						return '';
				}

				$step = get_field( 'tabs_step' )[0] ?? false;
				$step = '1' === $step ? true : false;

				$tabs_list   = get_field( 'tabs_list' );
				$tabs_panels = get_field( 'tabs_panels' );

				if ( ! $tabs_list || ! $tabs_panels ) {
						return '';
				}

				$tabs_list   = explode( '***', $tabs_list )[ $index ] ?? false;
				$tabs_panels = explode( '<p>***</p>', $tabs_panels )[ $index ] ?? false;

				if ( ! $tabs_list || ! $tabs_panels ) {
						return '';
				}

				$tabs_list   = explode( ',', $tabs_list );
				$tabs_panels = explode( '<p>&nbsp;</p>', $tabs_panels );

				/* Create markup */

				$list   = [];
				$panels = [];

				foreach ( $tabs_list as $i => $t ) {
						$t_items = explode( ' : ', $t );

						$label    = $t_items[0] ?? '';
						$title    = $t_items[1] ?? '';
						$img_id   = (int) $t_items[2] ?? 0;
						$selected = $t_items[3] ?? false;
						$id       = str_replace( ' ', '-', strtolower( $title ) );
						$tab_id   = "$id-label";
						$index    = $i + 1;
						$current  = $selected ? 'true' : 'false';
						$tabindex = $selected ? '0' : '-1';

						/* Tab list item */

						$classes = 't-bg-inherit fusion-button button-small button-outline avada-noscroll';

						if ( $step ) {
								$classes = 't-foreground-base t-bg-inherit l-w-m u-d-b u-b-s u-br-100-pc u-p-r p-s u-fw-b';
						}

						if ( $step ) {
								$label = (
									'<div class="o-aspect-ratio l-flex" data-justify="center" data-align="center">' .
										"<span class='u-v-h'>$title</span>" .
										"<div class='u-p-a' aria-hidden='true'>$index</div>" .
									'</div>'
								);
						}

						$list[] = (
							'<li class="l-flex t-bg-inherit u-ws-nw" role="presentation">' .
								"<a href='#$id' role='tab' aria-selected='$current' id='$tab_id' class='$classes' tabindex='$tabindex'>$label</a>" .
							'</li>'
						);

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

										$img = (
											'<div class="l-flex-shrink-0">' .
												"<img class='l-w-l' src='$src' alt='$alt' srcset='$srcset' sizes='$sizes' width='$wid' height='$height' loading='lazy'>" .
											'</div>'
										);
								}
						}

						/* Tab panel */

						$panels[] = (
							"<section class='l-pt-xxxs' id='$id' tabindex='$tabindex' role='tabpanel' aria-labelledby='$tab_id' data-selected='$selected'" . ( $selected ? '' : ' hidden' ) . '>' .
								'<div class="l-flex" data-gap="r" data-wrap>' .
									$img .
									'<div class="l-mb-r-all l-w-33-pc l-min-w l-flex-grow">' .
										'<div class="h4">' .
											"<h3 class='l-m-0'>$title</h3>" .
										'</div>' .
										'<div class="p-m l-mb-r-all ht-editor o-underline">' .
											$tabs_panels[ $i ] .
										'</div>' .
									'</div>' .
								'</div>' .
							'</section>'
						);
				}

				/* Output */

				return (
					'<div class="o-tabs t-bg-inherit">' .
						'<div class="l-mw-full l-mw-half t-bg-inherit">' .
							'<div class="l-pb-xxxs l-pb-xs-l t-bg-inherit">' .
								'<div class="u-o-xy l-flex t-bg-inherit">' .
									'<div class="l-flex-grow u-p-r t-bg-inherit">' .
										'<ul class="l-flex u-tlrb-b t-bg-inherit u-oo-s" role="tablist" data-gap="r" data-justify="def">' .
											implode( '', $list ) .
										'</ul>' .
									'</div>' .
								'</div>' .
							'</div>' .
						'</div>' .
						'<div class="l-mw-full l-mw-half">' .
							implode( '', $panels ) .
						'</div>' .
					'</div>'
				);
		}

} // End Tabs
