<?php
/**
 * Customer new account email
 *
 * This template can be overridden by copying it to yourtheme/wp-travel/emails/customer-new-account.php.
 *
 * HOWEVER, on occasion WP Travel will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.wensolutions.com/document/template-structure/
 * @author 		WEN Solutions
 * @package 	WP_Travel/Templates/Emails
 * @version     1.2.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$settings = wp_travel_get_settings();

$generate_username_from_email =  isset( $settings['generate_username_from_email'] ) ? $settings['generate_username_from_email'] : 'no';
$generate_user_password =  isset( $settings['generate_user_password'] ) ? $settings['generate_user_password'] : 'no';

?>

	<p><?php printf( __( 'Thanks for creating an account on %1$s. Your username is %2$s', 'wp-travel' ), esc_html( $blogname ), '<strong>' . esc_html( $user_login ) . '</strong>' ); ?></p>

<?php if ( 'yes' === $generate_username_from_email && $password_generated ) : ?>

	<p><?php printf( __( 'Your password has been automatically generated: %s', 'wp-travel' ), '<strong>' . esc_html( $user_pass ) . '</strong>' ); ?></p>

<?php endif; ?>

	<p><?php printf( __( 'You can access your account area to view your Trip Bookings and change your password here: %s.', 'wp-travel' ), make_clickable( esc_url( wp_travel_get_page_permalink( 'wp-travel-dashboard' ) ) ) ); ?></p>
	<p><?php _e( 'Powered by', 'wp-travel' ); ?><a href="http://wptravel.io" target="_blank"> <?php _e( 'WP Travel', 'wp-travel' ); ?></a></p>
<?php

