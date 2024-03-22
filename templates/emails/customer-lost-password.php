<?php
/**
 * Customer Lost Password email Template
 *
 * This template can be overridden by copying it to yourtheme/wp-travel/emails/customer-lost-password.php.
 *
 * HOWEVER, on occasion wp-travel will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see     http://docs.wensolutions.com/document/template-structure/
 * @author  WenSolutions
 * @package WP_Travel
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Set Login Data and Reset Keys.
$user_login = $args['user_login'];
$reset_key  = $args['reset_key'];


$url = add_query_arg(
	array(
		'action' => 'rp',
		'key'    => $reset_key,
		'login'  => rawurlencode( $user_login ),
	),
	wp_lostpassword_url()
);

?>

<p><?php esc_html_e( 'Someone requested that the password be reset for the following account:', 'wp-travel' ); ?></p>
<p><?php echo esc_html__( 'Username: ', 'wp-travel' ). esc_html( $user_login ); ?></p>
<p><?php esc_html_e( 'If this was a mistake, just ignore this email and nothing will happen.', 'wp-travel' ); ?></p>
<p><?php esc_html_e( 'To reset your password, visit the following address:', 'wp-travel' ); ?></p>
<p>
	<a class="link" href="<?php echo esc_url( $url ); ?>">
			<?php esc_html_e( 'Click here to reset your password', 'wp-travel' ); ?></a>
</p>
<p><?php esc_html_e( 'Powered by', 'wp-travel' ); ?><a href="http://wptravel.io" target="_blank"> <?php esc_html_e( 'WP Travel', 'wp-travel' ); ?></a></p>
<p></p>
