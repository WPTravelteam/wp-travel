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
 * @see 	    http://docs.wensolutions.com/document/template-structure/
 * @author      WenSolutions
 * @package     wp-travel/Templates
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Set Login Data and Reset Keys.
$user_login = $args['user_login'];
$reset_key  = $args['reset_key'];

?>

<p><?php _e( 'Someone requested that the password be reset for the following account:', 'wp-travel' ); ?></p>
<p><?php printf( __( 'Username: %s', 'wp-travel' ), $user_login ); ?></p>
<p><?php _e( 'If this was a mistake, just ignore this email and nothing will happen.', 'wp-travel' ); ?></p>
<p><?php _e( 'To reset your password, visit the following address:', 'wp-travel' ); ?></p>
<p>
	<a class="link" href="<?php echo esc_url( add_query_arg( array( 'key' => $reset_key, 'login' => rawurlencode( $user_login ) ), wp_lostpassword_url() ) ); ?>">
			<?php _e( 'Click here to reset your password', 'wp-travel' ); ?></a>
</p>
<p><?php _e( 'Powered by', 'wp-travel' ); ?><a href="http://wptravel.io" target="_blank"> <?php _e( 'WP Travel', 'wp-travel' ); ?></a></p>
<p></p>
