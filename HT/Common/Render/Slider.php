<?php
/**
 * Render slider
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

class Slider {

		/**
		 * Output slider.
		 *
		 * @return string
		 */

		public static function render( $args = [] ) {
				$args = array_merge(
						[
							'slides'         => [],
							'label'          => '',
							'selected_index' => 0,
							'loop'           => false,
						],
						$args
				);

				[
					'slides'         => $slides,
					'label'          => $label,
					'selected_index' => $selected_index,
					'loop'           => $loop,
				] = $args;

				$loop = filter_var( $loop, FILTER_VALIDATE_BOOLEAN );

				/* Slides required */

				$length = count( $slides );

				if ( ! $length || ! $label ) {
						return '';
				}

				$required_length = $length > 3;

				/* Create tablist and panels */

				$tablist = '';
				$panels  = '';

				foreach ( $slides as $i => $slide ) {
						$index    = $i + 1;
						$selected = $i === $selected_index ? 'true' : 'false';
						$tabindex = $i === $selected_index ? 0 : -1;
						$id       = uniqid();

						$tab_label   = "Go to $label group $index";
						$panel_label = ucfirst( $label ) . " group $index";

						$tablist .= (
							'<li class="o-dot l-flex" role="presentation">' .
								"<button class='o-aspect-ratio l-w-s l-mr-xxs' type='button' role='tab' tabindex='$tabindex' aria-selected='$selected' aria-label='$tab_label'>" .
									'<span class="l-m-auto u-p-a u-t-0 u-l-0 u-r-0 u-b-0 u-d-b u-br-100-pc"></span>' .
								'</button>' .
							'</li>'
						);

						if ( $required_length ) {
								$suffix = $loop ? 'item' : 'group';

								$panels .= (
									"<div class='o-slider__$suffix l-flex-shrink-0 u-p-r' id='$id' role='tabpanel' tabindex='0' aria-hidden='true' aria-label='$panel_label' data-selected='false'>" .
										'<div class="o-slider__view l-flex u-tlrb-b u-p-r"' . ( ! $loop ? ' data-gap="s" data-gap-l="r"' : ' data-col' ) . '>' .
											( ! $loop ? '<div class="o-slider__inner l-flex l-flex-shrink-0" data-col>' : '<div>' ) .
												$slide .
											'</div>' .
										'</div>' .
									'</div>'
								);
						} else {
								$panels .= (
									"<li class='l-w-33-pc'>" .
										$slide .
									'</li>'
								);
						}
				}

				if ( ! $loop && $required_length ) {
						$panels .= '<div class="o-slider__offset l-flex-shrink-0"></div>';
				}

				/* Output */

				if ( $required_length ) {
						return (
							'<div class="l-flex js-slider" role="group" data-col>' .
								'<div class="o-slider u-o-h"' . ( $loop ? ' data-loop="true"' : '' ) . '>' .
									'<div class="o-slider__track l-pb-s u-p-r ht-single-full">' .
										'<div class="o-slider__h fusion_builder_column l-ph-ctn l-flex" data-gap="s" data-gap-l="r">' .
											$panels .
										'</div>' .
									'</div>' .
								'</div>' .
								"<ul class='o-dots l-flex' data-justify='center' role='tablist' aria-label='Select $label group to show'>" .
									$tablist .
								'</ul>' .
							'</div>'
						);
				} else {
						return (
							'<div class="ht-single-full">' .
								'<div class="fusion_builder_column l-ph-ctn l-m-auto">' .
									"<ul class='l-flex' data-gap='r' data-row='s' data-wrap-s data-col>" .
										$panels .
									'</ul>' .
								'</div>' .
							'</div>'
						);
				}
		}

} // End Slider