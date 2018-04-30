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
		<form class="register-form">
			<span class="user-name">
				<input type="text" placeholder="name"/>
			</span>
			<span class="user-password">
				<input type="password" placeholder="password"/>
			</span>
			<span class="user-email">
				<input type="text" placeholder="email address"/>
			</span>
			<button>create</button>
			<p class="message">Already registered? <a href="#">Sign In</a></p>
		</form>
		<!-- Login Form -->
		<form method="post" class="login-form">
			<span class="user-username">
				<input name="username" type="text" placeholder="username"/>
			</span>
			<span class="user-password">
				<input name="password" type="password" placeholder="password"/>
			</span>
			<input class="" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'ep-travel' ); ?></span>
			<?php wp_nonce_field( 'wp-travel-login', 'wp-travel-login-nonce' ); ?>

		<button  type="submit" name="process_login" >login</button>
			<p class="message">Not registered? <a href="#">Create an account</a></p>
			<p class="info">
				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>">Forgot Password?</a>
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
