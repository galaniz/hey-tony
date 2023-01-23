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

use HT\HT as HT;

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

		/* Label */

		$capital_label = ucfirst( $label );

		/* Create tablist and panels */

		$tablist    = '';
		$panels     = '';
		$last_index = $length - 1;
		$math       = round( 100 / $length ) . 'vw - ' . ( round( 100 / $length ) / 16 ) . 'rem';
		$max_width  = " style='max-width:calc($math)'";

		foreach ( $slides as $i => $slide ) {
			$index    = $i + 1;
			$selected = $i === $selected_index ? 'true' : 'false';
			$tabindex = $i === $selected_index ? 0 : -1;
			$id       = uniqid();

			$tab_label   = "$capital_label group $index";
			$panel_label = "$capital_label group $index";

			$tablist .= (
				'<li class="o-dot l-flex" role="presentation">' .
					"<button class='o-aspect-ratio l-w-s" . ( $i < $last_index ? ' l-mr-xxs' : '' ) . "' type='button' role='tab' tabindex='$tabindex' aria-selected='$selected' aria-label='$tab_label'$max_width>" .
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
						'<div class="o-slider__track l-pb-s u-p-r" tabindex="-1">' .
							'<div class="o-slider__h l-flex" data-gap="s" data-gap-l="r">' .
								$panels .
							'</div>' .
						'</div>' .
					'</div>' .
					"<ul class='o-dots l-flex' data-justify='center' role='tablist' aria-label='$capital_label controls'>" .
						$tablist .
					'</ul>' .
				'</div>'
			);
		} else {
			return (
				'<div class="ht-single-full">' .
					'<div class="fusion_builder_column l-ph-ctn l-m-auto">' .
						"<ul class='l-flex' data-gap='s' data-row='s' data-wrap-s data-col>" .
							$panels .
						'</ul>' .
					'</div>' .
				'</div>'
			);
		}
	}

} // End Slider
