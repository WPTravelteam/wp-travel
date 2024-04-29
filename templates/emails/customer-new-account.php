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
 * @see     https://docs.wensolutions.com/document/template-structure/
 * @author  WEN Solutions
 * @package WP_Travel
 * @version 1.2.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$settings = wptravel_get_settings();

$generate_username_from_email = isset( $settings['generate_username_from_email'] ) ? $settings['generate_username_from_email'] : 'no';
$generate_user_password       = isset( $settings['generate_user_password'] ) ? $settings['generate_user_password'] : 'no';

?>

	<p><?php echo  esc_html__( 'Thanks for creating an account on ', 'wp-travel' ).esc_html( $blogname ). esc_html__( ' Your username is ', 'wp-travel' ).'<strong>' . esc_html( $user_login ) . '</strong>'; ?></p>

<?php if ( 'yes' === $generate_user_password && $password_generated ) : ?>

	<p><?php echo  esc_html__( 'Your password has been automatically generated: ', 'wp-travel' ). '<strong>' . esc_html( $user_pass ) . '</strong>'; ?></p>

<?php endif; ?>

	<p><?php echo  esc_html__( 'You can access your account area to view your Trip Bookings and change your password here: ', 'wp-travel' ). esc_html( make_clickable( esc_url( wptravel_get_page_permalink( 'wp-travel-dashboard' ) ) ) ); ?></p>
	<p><?php echo esc_html__( 'Powered by', 'wp-travel' ); ?><a href="http://wptravel.io" target="_blank"> <?php echo esc_html__( 'WP Travel', 'wp-travel' ); ?></a></p>
<?php

