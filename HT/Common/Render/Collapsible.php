<?php
/**
 * Render collapsible
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

class Collapsible {

		/**
		 * Shortcode to output collapsible.
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
					"<div class='o-collapsible l-pt-xxs l-pb-xxs l-pt-xs-l l-pb-xs-l u-bt-1'$attr>" .
						"<$heading_level class='h4 l-m-0'>" .
							"<button class='o-collapsible__toggle l-flex l-w-100-pc u-oo-s' type='button' aria-controls='$id'>" .
								'<span class="l-flex" data-gap="s">' .
									'<span class="l-flex t-primary-base">' .
										'<span class="o-collapsible__icon l-w-xs l-flex" data-align="center" data-justify="center">' .
											'<i aria-hidden="true" class="awb-icon-minus"></i>' .
											'<i aria-hidden="true" class="awb-icon-plus"></i>' .
										'</span>' .
									'</span>' .
									"<span>$title</span>" .
								'</span>' .
							'</button>' .
						"</$heading_level>" .
						"<div class='o-collapsible__main' id='$id'>" .
							'<div class="l-pt-xxs l-pt-xs-l p-m l-mb-r-all ht-editor o-underline">' .
								$content .
							'</div>' .
						'</div>' .
					'</div>'
				);
		}

} // End Collapsible
