<?php
/**
 * Lost password form
 *
 * This template can be overridden by copying it to yourtheme/wp-travel/myaccount/form-lost-password.php.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Notices.
?>

<form method="post" class="woocommerce-ResetPassword lost_reset_password">

	<p><?php echo apply_filters( 'woocommerce_lost_password_message', esc_html__( 'Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.', 'wp-travel' ) ); ?></p><?php // @codingStandardsIgnoreLine ?>

	<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
		<label for="user_login"><?php esc_html_e( 'Username or email', 'wp-travel' ); ?></label>
		<input class="woocommerce-Input woocommerce-Input--text input-text" type="text" name="user_login" id="user_login" />
	</p>

	<div class="clear"></div>

	<?php do_action( 'woocommerce_lostpassword_form' ); ?>

	<p class="woocommerce-form-row form-row">
		<input type="hidden" name="wc_reset_password" value="true" />
		<button type="submit" class="woocommerce-Button button" value="<?php esc_attr_e( 'Reset password', 'wp-travel' ); ?>"><?php esc_html_e( 'Reset password', 'wp-travel' ); ?></button>
	</p>

	<?php wp_nonce_field( 'lost_password' ); ?>

</form>
