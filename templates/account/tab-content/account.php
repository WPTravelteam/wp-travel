<?php
$current_user = $args['current_user'];
?>

  <div class="account-setting">
	<div class="title">
	  <h3><?php esc_html_e( 'My Account', 'wp-travel' ); ?></h3>
	</div>
	<?php
	echo wptravel_get_template_html(
		'account/form-edit-account.php',
		array(
			'user' => $current_user,
		)
	);
	?>
  </div>
