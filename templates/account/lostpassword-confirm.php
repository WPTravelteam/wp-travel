<?php
/**
 * Lost password confirmation text.
 *
 * This template can be overridden by copying it to yourtheme/wp-travel/account/lost-password-confirmation.php.
 *
 * HOWEVER, on occasion WP Travel will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Print Errors / Notices.
wp_travel_print_notices();

//echo esc_html__( 'Password reset email has been sent.', 'wp-travel' );
?>

<p class="col-xs-12 wp-travel-notice-success wp-travel-notice"><?php echo apply_filters( 'wp_travel_lost_password_message', __( 'A password reset email has been sent to the email address for your account, but may take several minutes to show up in your inbox. Please wait at least 10 minutes before attempting another reset.', 'wp-travel' ) ); ?></p>
