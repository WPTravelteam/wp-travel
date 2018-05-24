<?php
/**
 * Customer Lost Password Reset Form.
 *
 * This template can be overridden by copying it to yourtheme/wp-travel/account/form-reset-password.php.
 *
 * HOWEVER, on occasion wp-travel will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.wensolutions.com/document/template-structure/
 * @author      WenSolutions
 * @package     wp-travel/Templates
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Print Errors / Notices.
WP_Travel()->notices->print_notices( 'error', true ); ?>

<form method="post" class="wp-travel-ResetPassword lost_reset_password">

	<p><?php echo apply_filters( 'wp_travel_reset_password_message', esc_html__( 'Enter a new password below.', 'wp-travel' ) ); ?></p><?php // @codingStandardsIgnoreLine ?>

	<p class="wp-travel-form-row wp-travel-form-row--first form-row form-row-first">
		<label for="password_1"><?php esc_html_e( 'New password', 'wp-travel' ); ?> <span class="required">*</span></label>
		<input type="password" class="wp-travel-Input wp-travel-Input--text input-text" name="password_1" id="password_1" />
	</p>
	<p class="wp-travel-form-row wp-travel-form-row--last form-row form-row-last">
		<label for="password_2"><?php esc_html_e( 'Re-enter new password', 'wp-travel' ); ?> <span class="required">*</span></label>
		<input type="password" class="wp-travel-Input wp-travel-Input--text input-text" name="password_2" id="password_2" />
	</p>

	<input type="hidden" name="reset_key" value="<?php echo esc_attr( $args['key'] ); ?>" />
	<input type="hidden" name="reset_login" value="<?php echo esc_attr( $args['login'] ); ?>" />

	<div class="clear"></div>

	<?php do_action( 'wp_travel_resetpassword_form' ); ?>

	<p class="wp-travel-form-row form-row">
		<input type="hidden" name="wp_travel_reset_password" value="true" />
		<button type="submit" class="wp-travel-Button button" value="<?php esc_attr_e( 'Save', 'wp-travel' ); ?>"><?php esc_html_e( 'Save', 'wp-travel' ); ?></button>
	</p>

	<?php wp_nonce_field( 'wp_travel_reset_password_nonce' ); ?>

</form>
