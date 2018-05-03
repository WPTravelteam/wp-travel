<?php
/**
 * Login / Register Form template.
 *
 * @package WP_Travel
 */

// Print Errors / Notices.
WP_Travel()->notices->print_notices( 'error', true );
?>
<div class="login-page">
	<div class="form">
		<!-- Registration form -->
		<form method="post" class="register-form">
			<span class="user-name">
				<input name="username" type="text" placeholder="<?php echo esc_attr( 'Username', 'wp-travel' ); ?>"/>
			</span>
			<span class="user-email">
				<input name="email" type="text" placeholder="<?php echo esc_attr( 'Email Address', 'wp-travel' ); ?>"/>
			</span>
			<span class="user-password">
				<input name="password" type="password" placeholder="<?php echo esc_attr( 'Password', 'wp-travel' ); ?>"/>
			</span>
			<?php wp_nonce_field( 'wp-travel-register', 'wp-travel-register-nonce' ); ?>
			<button  type="submit" name="register" value="<?php esc_attr_e( 'Register', 'wp-travel' ); ?>" ><?php esc_attr_e( 'Register', 'wp-travel' ); ?></button>
			<p class="message"><?php echo esc_attr( 'Already registered?', 'wp-travel' ); ?> <a href="#"><?php echo esc_attr( 'Sign In', 'wp-travel' ); ?></a></p>
		</form>
		<!-- Login Form -->
		<form method="post" class="login-form">
			<span class="user-username">
				<input name="username" type="text" placeholder="<?php echo esc_attr( 'Username', 'wp-travel' ); ?>"/>
			</span>
			<span class="user-password">
				<input name="password" type="password" placeholder="<?php echo esc_attr( 'Password', 'wp-travel' ); ?>"/>
			</span>
			<input class="" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'wp-travel' ); ?></span>
			<?php wp_nonce_field( 'wp-travel-login', 'wp-travel-login-nonce' ); ?>

		<button  type="submit" name="login" value="<?php esc_attr_e( 'Login', 'wp-travel' ); ?>" ><?php esc_attr_e( 'Login', 'wp-travel' ); ?></button>
			<p class="message"><?php echo esc_html( 'Not registered?', 'wp-travel' ); ?> <a href="#"><?php echo esc_html( 'Create an account', 'wp-travel' ); ?></a></p>
			<p class="info">
				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php echo esc_html( 'Forgot Password ?', 'wp-travel' ); ?></a>
			</p>
		</form>
		<!-- Password Reset -->
		<form class="forgot-form">
				<span class="user-username">
					<input type="text" placeholder="username or email"/>
				</span>
			<button>Send</button>
				<p class="message">Not registered? <a href="#">Create an account</a></p>
				<p class="message">Already registered? <a href="#">Sign In</a></p>
		</form>
	</div>
</div>

<script type="text/javascript" src="http://skynet.wensolutions.com/travel-log/wp-content/plugins/wp-travel/assets/js/easy-responsive-tabs.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('.login-page .message a').click(function(e){
			e.preventDefault();
			$('.login-page form.login-form,.login-page form.register-form').animate({height: "toggle", opacity: "toggle"}, "slow");
		});
	});
</script>
