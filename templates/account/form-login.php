<?php
/**
 * Login / Register Form template.
 *
 * @package WP_Travel
 */

// Print Errors / Notices.
wp_travel_print_notices();

$nonce_value = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : '';
$nonce_value = isset( $_POST['wp-travel-register-nonce'] ) ? $_POST['wp-travel-register-nonce'] : $nonce_value;

$login_form_toogle = '';
$reg_form_toogle   = '';

$settings = wp_travel_get_settings();

$enable_my_account_customer_registration = isset( $settings['enable_my_account_customer_registration'] ) ? $settings['enable_my_account_customer_registration'] : 'yes';

$generate_username_from_email = isset( $settings['generate_username_from_email'] ) ? $settings['generate_username_from_email'] : 'no';
$generate_user_password = isset( $settings['generate_user_password'] ) ? $settings['generate_user_password'] : 'no';

if ( ! empty( $_POST['register'] ) && wp_verify_nonce( $nonce_value, 'wp-travel-register' ) ) {

	$login_form_toogle = 'style="display:none"';
	$reg_form_toogle   = 'style="display:block"';

}

?>
<div class="wp-travel-dashboard-form">
	<div class="login-page">
		<?php if ( has_custom_logo() ) : ?>
			<div class="login-logo">
				<?php the_custom_logo(); ?>
			</div>
		<?php endif; ?>
		<div class="form">
		<?php if ( 'yes' === $enable_my_account_customer_registration ) : ?>
			<!-- Registration form -->
			<form method="post" class="register-form" <?php echo $reg_form_toogle; ?> >
				<h3><?php esc_html_e( 'Register', 'wp-travel' );  ?></h3>
				<?php if ( 'no' === $generate_username_from_email ) : ?>
					<span class="user-name">
						<input name="username" type="text" placeholder="<?php echo esc_attr__( 'Username', 'wp-travel' ); ?>"/>
					</span>
				<?php endif; ?>
				<span class="user-email">
					<input name="email" type="text" placeholder="<?php echo esc_attr__( 'Email Address', 'wp-travel' ); ?>"/>
				</span>
				<?php if ( 'no' === $generate_user_password ) : ?>
					<span class="user-password">
						<input name="password" type="password" placeholder="<?php echo esc_attr__( 'Password', 'wp-travel' ); ?>"/>
					</span>
				<?php endif; ?>
					<div class="wrapper">
						<!--<div class="float-left">
							<input class="" name="terms-condition" type="checkbox" id="terms-condition" value="forever" />
							<label for="terms-condition"><span>I have read and agree to the <a href="#">Terms of Use </a>and <a href="#">Privacy Policy</a></span></label>
						</div> -->
					</div>

				<?php wp_nonce_field( 'wp-travel-register', 'wp-travel-register-nonce' ); ?>
				<button  type="submit" name="register" value="<?php esc_attr_e( 'Register', 'wp-travel' ); ?>" ><?php esc_attr_e( 'Register', 'wp-travel' ); ?></button>
				<p class="message"><?php echo esc_attr__( 'Already registered?', 'wp-travel' ); ?> <a href="#"><?php echo esc_attr__( 'Sign In', 'wp-travel' ); ?></a></p>
			</form>
		<?php endif; ?>
			<!-- Login Form -->
			<form method="post" class="login-form" <?php echo $login_form_toogle; ?> >
					<h3><?php esc_html_e( 'Login', 'wp-travel' ); ?></h3>
					<span class="user-username">
						<input name="username" type="text" placeholder="<?php echo esc_attr__( 'Username', 'wp-travel' ); ?>"/>
					</span>
					<span class="user-password">
						<input name="password" type="password" placeholder="<?php echo esc_attr__( 'Password', 'wp-travel' ); ?>"/>
					</span>
					<div class="wrapper">

						<div class="float-left">
							<input class="" name="rememberme" type="checkbox" id="rememberme" value="forever" />
							<?php wp_nonce_field( 'wp-travel-login', 'wp-travel-login-nonce' ); ?>
							<label for="rememberme"><?php esc_html_e( 'Remember me', 'wp-travel' ); ?></label>
						</div>
						<div class="float-right">
							<p class="info">
								<a href="<?php echo esc_url( wp_travel_lostpassword_url() ); ?>"><?php echo esc_html__( 'Forgot Password ?', 'wp-travel' ); ?></a>
							</p>
						</div>
					</div>
				<button  type="submit" name="login" value="<?php esc_attr_e( 'Login', 'wp-travel' ); ?>" ><?php esc_attr_e( 'Login', 'wp-travel' ); ?></button>
				<?php if ( 'yes' === $enable_my_account_customer_registration ) : ?>
					<p class="message"><?php echo esc_html__( 'Not registered?', 'wp-travel' ); ?> <a href="#"><?php echo esc_html__( 'Create an account', 'wp-travel' ); ?></a></p>
				<?php endif; ?>
			</form>
		</div>
	</div>
</div>
