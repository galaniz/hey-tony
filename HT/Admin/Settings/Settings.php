<?php
/**
 * Admin - Settings
 *
 * @package hey-tony
 */
namespace HT\Admin\Settings;

/* Imports */

use HT\HT as HT;
use Formation\Admin\Settings\Settings as FRMSettings;

/**
 * Class - add fields to general settings
 */
class Settings {
	/**
	 * Set admin action
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_init', [$this, 'init'] );
	}

	/**
	 * Add cookie text field to general settings
	 *
	 * @return void
	 */
	public function init() {
		$fields = [
			[
				'name'    => HT::$namespace . '_cookie_text',
				'label'   => 'Cookie Notice Text',
				'section' => 'default',
				'type'    => 'richtext',
				'wpautop' => true,
				'on_save' => function( $value ) {
					return wp_kses( $value, 'post' );
				},
			],
		];

		$general_settings = new FRMSettings(
			[
				'fields' => $fields,
				'page'   => 'general',
			]
		);
	}
}
