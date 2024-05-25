<?php
/**
 * Objects - Collapsible
 *
 * @package hey-tony
 */
namespace HT\Objects\Collapsible;

/* Imports */

use HT\HT as HT;

/**
 * Class - render collapsible
 */
class Collapsible {
	/**
	 * Shortcode to output collapsible
	 *
	 * @param array $atts
	 * @param string $content
	 * @return string
	 */
	public static function shortcode( $atts, $content ) {
		$atts = shortcode_atts(
			[
				'title'         => '',
				'heading_level' => 'h3',
				'accordion'     => '',
			],
			$atts,
			'ht-collapsible'
		);

		/* Destructure */

		[
			'title'         => $title,
			'heading_level' => $heading_level,
			'accordion'     => $accordion,
		] = $atts;

		/* Output */

		$id   = uniqid();
		$attr = $accordion ? " data-accordion='$accordion'" : '';

		return (
			"<div class='o-collapsible l-pt-xs l-pb-xs l-pt-s-m l-pb-s-m b-top'$attr>" .
				"<$heading_level class='h4 l-m-0'>" .
					"<button class='o-collapsible__toggle l-flex l-wd-full outline-snug' type='button' aria-controls='$id' aria-expanded='false'>" .
						'<span class="l-flex l-gm-2xs">' .
							'<span class="l-flex t-primary-base">' .
								'<span class="o-collapsible__icon l-wd-2xs l-flex l-align-center l-justify-center">' .
									'<i aria-hidden="true" class="awb-icon-minus"></i>' .
									'<i aria-hidden="true" class="awb-icon-plus"></i>' .
								'</span>' .
							'</span>' .
							"<span>$title</span>" .
						'</span>' .
					'</button>' .
				"</$heading_level>" .
				"<div class='o-collapsible__main' id='$id'>" .
					'<div class="l-pt-xs l-pt-s-m p-m l-mb-2xs-all ht-rich-text e-underline">' .
						$content .
					'</div>' .
				'</div>' .
			'</div>'
		);
	}
}
