<?php
/**
 * Admin - User
 *
 * @package hey-tony
 */
namespace HT\Admin\User;

/* Imports */

use HT\HT as HT;
use Formation\Common\Field\Field;

/**
 * Class - add field to user admin page.
 */
class User {
	/**
	 * Store form ids and names.
	 *
	 * @var array {
	 *  @type string $name
	 *  @type string $id
	 *  @type string $description_id
	 *  @type string $nonce_action
	 *  @type string $nonce_name
	 * }
	 */
	public static $form = [
		'name'           => 'nicename',
		'id'             => 'user-nicename',
		'description_id' => 'user-nicename-description',
		'nonce_action'   => 'save_nicename',
		'nonce_name'     => 'nicename_field',
	];

	/**
	 * Set user admin actions.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'show_user_profile', [$this, 'on_edit_user'], 10, 1 );
		add_action( 'edit_user_profile', [$this, 'on_edit_user'], 10, 1 );
		add_action( 'profile_update', [$this, 'on_save_user'], 10, 1 );
	}

	/**
	 * Edit custom user fields.
	 *
	 * @param  object $user WP_User object
	 * @return void
	 */
	public function on_edit_user( $user ) {
		$form = [];

		foreach ( self::$form as $k => $v ) {
			$form[ $k ] = HT::$namespace . '_' . $v;
		}
		?>
		<h2>Additional Info</h2>
		<table class="form-table" role="presentation">
			<tbody>
				<tr class="user-user-login-wrap">
					<th>
						<label for="<?php echo esc_attr( $form['id'] ); ?>">Nicename</label>
					</th>
					<td>
						<input type="text" name="<?php echo esc_attr( $form['name'] ); ?>" id="<?php echo esc_attr( $form['id'] ); ?>" aria-describedby="<?php echo esc_attr( $form['description_id'] ); ?>" value="<?php echo esc_attr( $user->user_nicename ); ?>" class="regular-text"> <span class="description">Must be unique.</span>
						<p class="description" id="<?php echo esc_attr( $form['description_id'] ); ?>">This field will update to last part of the author URL to hide real username.</p>
						<?php wp_nonce_field( esc_html( $form['nonce_action'] ), esc_html( $form['nonce_name'] ) ); ?>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Save custom user fields.
	 *
	 * @param  string $user_id
	 * @return void
	 */
	public function on_save_user( $user_id ) {
		$nonce_name   = HT::$namespace . '_' . self::$form['nonce_name'];
		$nonce_action = HT::$namespace . '_' . self::$form['nonce_action'];

		if ( ! isset( $_POST[ $nonce_name ] ) || ! wp_verify_nonce( $_POST[ $nonce_name ], $nonce_action ) ) {
			return false;
		}

		/* Nicename value */

		$input_name = HT::$namespace . '_' . self::$form['name'];

		if ( ! isset( $_POST[ $input_name ] ) ) {
			return false;
		}

		$nicename = sanitize_text_field( $_POST[ $input_name ] );

		/* Prevent endless loop */

		remove_action( 'profile_update', [$this, 'on_save_user'], 10, 1 );

		/* Update user */

		wp_update_user(
			[
				'ID'            => $user_id,
				'user_nicename' => $nicename,
			]
		);
	}
}
