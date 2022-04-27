<?php
/**
 * Additional settings for Reading page
 *
 * @package hey-tony
 */

namespace HT\Admin;

/**
 * Imports
 */

use HT\HT as HT;

/**
 * Class
 */

class Reading {

		/*
		 * Constructor
		 */

		public function __construct() {
				add_action( 'admin_init', [$this, 'setup'] );
		}

		/**
		 * Register and render fields
		 */

		public function setup() {
				foreach ( HT::$post_types_info as $pt => $info ) {
						if ( 'post' === $pt ) {
								continue;
						}

						$name  = $pt . '_posts_per_page';
						$label = $info['label'];

						register_setting(
								'reading',
								$name,
								[
									'type'              => 'number',
									'sanitize_callback' => function( $value ) {
											return filter_var( $value, FILTER_VALIDATE_INT );
									},
								]
						);

						add_settings_field(
								$name . '_field', // id
								$label . ' page(s) shows at most', // title
								function() use ( $name ) {
										$value = (int) get_option( $name, '' ); ?>
										<div>
											<input
												type="number"
												name="<?php echo esc_attr( $name ); ?>"
												id="<?php echo esc_attr( $name ) . '_id'; ?>"
												value="<?php echo esc_attr( $value ); ?>"
												step="1"
												min="1"
												class="small-text"
											> posts
										</div>
										<?php
								}, // callback
								'reading', // page
								'default', // section
								[] // args
						);
				}
		}

} // End Reading
