<?php
/**
 * Objects - Tabs
 *
 * @package hey-tony
 */
namespace HT\Objects\Tabs;

/* Imports */

use HT\HT as HT;

/**
 * Class - render tabs
 */
class Tabs {
	/**
	 * Shortcode to output tabs
	 *
	 * @param array $atts
	 * @param string $content
	 * @return string
	 */
	public static function shortcode( $atts, $content ) {
		$atts = shortcode_atts(
			[
				'index' => 0,
				'half'  => false,
			],
			$atts,
			'ht-tabs'
		);

		/* Destructure */

		[
			'index' => $index,
			'half'  => $half,
		] = $atts;

		$index = (int) $index;
		$half  = filter_var( $half, FILTER_VALIDATE_BOOLEAN );

		/* Half width class */

		$half_class = $half ? ' l-mw-half' : '';

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
				$classes = 't-foreground-base t-bg-inherit l-wd-s l-block b-all b-radius-full l-relative p-s t-wt-bold';
			}

			if ( $step ) {
				$label = (
					'<div class="l-relative l-overflow-hidden l-ar-1-1 l-flex l-align-center l-justify-center">' .
						"<span class='a-hide-vis'>$title</span>" .
						"<div class='l-absolute' aria-hidden='true'>$index</div>" .
					'</div>'
				);
			}

			$list[] = (
				'<li class="l-flex t-bg-inherit t-ws-nowrap" role="presentation">' .
					"<a href='#$id' role='tab' aria-selected='$current' id='$tab_id' class='$classes' tabindex='$tabindex'>$label</a>" .
				'</li>'
			);

			/* Image */

			$img = '';

			if ( $img_id ) {
				$image = HT::get_image( $img_id, 'medium_large' );

				if ( $image ) {
					$src    = esc_url( $image['url'] );
					$srcset = esc_attr( $image['srcset'] );
					$sizes  = esc_attr( $image['sizes'] );
					$wid    = esc_attr( $image['width'] );
					$height = esc_attr( $image['height'] );
					$alt    = esc_attr( $image['alt'] );

					$img = (
						'<div class="l-shrink-0">' .
							"<img class='l-wd-2xl' src='$src' alt='$alt' srcset='$srcset' sizes='$sizes' width='$wid' height='$height' loading='lazy'>" .
						'</div>'
					);
				}
			}

			/* Tab panel */

			$panels[] = (
				"<section class='l-pt-2xs' id='$id' tabindex='$tabindex' role='tabpanel' aria-labelledby='$tab_id' data-selected='$selected'" . ( $selected ? '' : ' hidden' ) . '>' .
					'<div class="l-flex l-wrap l-gm-s">' .
						$img .
						'<div class="l-mb-2xs-all l-wd-1-3 l-grow-1">' .
							'<div class="h4">' .
								"<h3 class='l-m-0'>$title</h3>" .
							'</div>' .
							'<div class="p-m l-mb-2xs-all ht-rich-text e-underline">' .
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
				"<div class='l-mw-full$half_class l-pb-2xs l-pb-s-m t-bg-inherit'>" .
					'<div class="l-ht-' . ( $step ? 's' : 'xs' ) . ' l-overflow-hidden t-bg-inherit">' .
						'<div class="o-overflow l-overflow-x-auto l-pb-2xs l-flex t-bg-inherit">' .
							'<div class="l-grow-1 l-relative t-bg-inherit">' .
								'<ul class="l-flex l-justify-between l-gm-s l-relative l-before t-bg-inherit t-ls-none outline-snug" role="tablist">' .
									implode( '', $list ) .
								'</ul>' .
							'</div>' .
						'</div>' .
					'</div>' .
				'</div>' .
				"<div class='l-mw-full$half_class'>" .
					implode( '', $panels ) .
				'</div>' .
			'</div>'
		);
	}
}
