<?php

?>

	
<div class="login-page">
  <div class="form">
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
    <form class="login-form">
    	<span class="user-username">
      		<input type="text" placeholder="username"/>
    	</span>
    	<span class="user-password">
      		<input type="password" placeholder="password"/>
    	</span>
      <button>login</button>
      <p class="message">Not registered? <a href="#">Create an account</a></p>

      <p class="info"><a href="#">Forgot Password?</a></p>
    </form>

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

<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('.login-page .message a').click(function(){
		   $('.login-page form.login-form,.login-page form.register-form').animate({height: "toggle", opacity: "toggle"}, "slow");
		});

		$('.login-page .info a').click(function(){
			$('.login-page form.login-form,.login-page form.register-form').hide();
		   $('.login-page form.forgot-form').animate({height: "toggle", opacity: "toggle"}, "slow");
		});
	});
	
</script>

