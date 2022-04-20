<?php
/**
 * Extend Formation Ajax trait
 *
 * @package hey-tony
 */

namespace HT\Pub;

/**
 * Imports
 */

use HT\Common\Posts;
use Formation\Pub\Ajax as FrmAjax;

/**
 * Class
 */

class Ajax {

		/**
		 * Namespace for FrmAjax.
		 *
		 * @var string $namespace
		 */

		public static $namespace = 'ht';

		/**
		 * Formation ajax callbacks.
		 */

		use FrmAjax;

		/**
		 * Output posts requested through ajax.
		 *
		 * @param string $post_type
		 * @param array $args
		 * @return string or array of html output
		 */

		public static function render_ajax_posts( $args = [] ) {
				if ( isset( $args['post_type'] ) ) {
						$args['type'] = $args['post_type'];
				}

				$q_args = $args;

				if ( isset( $q_args['is_home'] ) ) {
						unset( $q_args['is_home'] );
				}

				$args['return_array'] = true;
				$args['query_args']   = $q_args;

				return Posts::shortcode( $args, '' );
		}

} // End Ajax
