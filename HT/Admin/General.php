<?php
/**
 * Additional fields for general settings.
 *
 * @package hey-tony
 */

namespace HT\Admin;

/**
 * Imports
 */

use HT\HT as HT;
use Formation\Admin\Settings\Settings;

/**
 * Class
 */

class General {

		/*
		 * Constructor
		 */

		public function __construct() {
				/* Register settings */

				add_action( 'admin_init', [$this, 'setup'] );
		}

		/**
		 * Register settings
		 */

		public function setup() {
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

				$general_settings = new Settings(
						[
							'fields' => $fields,
							'page'   => 'general',
						]
				);
		}

} // End General
