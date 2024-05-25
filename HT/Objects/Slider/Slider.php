<?php
/**
 * Objects - Slider
 *
 * @package hey-tony
 */
namespace HT\Objects\Slider;

/* Imports */

use HT\HT as HT;

/**
 * Class - render slider
 */
class Slider {
	/**
	 * Output slider
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
					"<button class='l-relative l-overflow-hidden l-ar-1-1 l-wd-xs" . ( $i < $last_index ? ' l-mr-5xs' : '' ) . "' type='button' role='tab' tabindex='$tabindex' aria-selected='$selected' aria-label='$tab_label'$max_width>" .
						'<span class="l-m-auto l-absolute l-top-0 l-left-0 l-right-0 l-bottom-0 l-block b-radius-full"></span>' .
					'</button>' .
				'</li>'
			);

			if ( $required_length ) {
				$suffix       = $loop ? 'item' : 'group';
				$view_classes =
					'o-slider__view l-flex l-before l-relative ' . ( ! $loop ? 'l-gm-2xs l-gm-s-m' : 'l-col' );

				$panels .= (
					"<div class='o-slider__$suffix l-shrink-0 l-relative' id='$id' role='tabpanel' tabindex='0' aria-hidden='true' aria-label='$panel_label' data-selected='false'>" .
						"<div class='$view_classes'>" .
							( ! $loop ? '<div class="o-slider__inner l-flex l-col l-shrink-0">' : '<div>' ) .
								$slide .
							'</div>' .
						'</div>' .
					'</div>'
				);
			} else {
				$panels .= (
					"<li class='l-wd-1-3'>" .
						$slide .
					'</li>'
				);
			}
		}

		if ( ! $loop && $required_length ) {
			$panels .= '<div class="o-slider__offset l-shrink-0"></div>';
		}

		/* Output */

		if ( $required_length ) {
			return (
				'<div class="l-flex l-col js-slider" role="group">' .
					'<div class="o-slider l-overflow-hidden"' . ( $loop ? ' data-loop="true"' : '' ) . '>' .
						'<div class="o-slider__track l-pb-m l-relative" tabindex="-1">' .
							'<div class="o-slider__h l-flex l-gm-2xs l-gm-s-m">' .
								$panels .
							'</div>' .
						'</div>' .
					'</div>' .
					"<ul class='o-dots l-flex l-justify-center t-ls-none' role='tablist' aria-label='$capital_label controls'>" .
						$tablist .
					'</ul>' .
				'</div>'
			);
		} else {
			return (
				'<div class="ht-single-full">' .
					'<div class="fusion_builder_column l-px-container l-m-auto">' .
						"<ul class='l-flex l-col l-row-s l-wrap-s l-gm-2xs t-ls-none' role='list'>" .
							$panels .
						'</ul>' .
					'</div>' .
				'</div>'
			);
		}
	}
}
