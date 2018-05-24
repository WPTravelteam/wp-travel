<?php
/**
 * Lost password form
 *
 * This template can be overridden by copying it to yourtheme/wp-travel/account/form-lostpassword.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Notices.
WP_Travel()->notices->print_notices( 'error', true );
?>

<form method="post" class="wp-travel-ResetPassword lost_reset_password">

	<p><?php echo apply_filters( 'wp_travel_lost_password_message', esc_html__( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'wp-travel' ) ); ?></p><?php // @codingStandardsIgnoreLine ?>

	<p class="wp-travel-form-row wp-travel-form-row--first form-row form-row-first">
		<label for="user_login"><?php esc_html_e( 'Username or email', 'wp-travel' ); ?></label>
		<input class="wp-travel-Input wp-travel-Input--text input-text" type="text" name="user_login" id="user_login" />
	</p>

	<div class="clear"></div>

	<?php do_action( 'wp_travel_lostpassword_form' ); ?>

	<p class="wp-travel-form-row form-row">
		<input type="hidden" name="wp_travel_reset_password" value="true" />
		<button type="submit" class="wp-travel-Button button" value="<?php esc_attr_e( 'Reset password', 'wp-travel' ); ?>"><?php esc_html_e( 'Reset password', 'wp-travel' ); ?></button>
	</p>

	<?php wp_nonce_field( 'wp_travel_lost_password' ); ?>

</form>
