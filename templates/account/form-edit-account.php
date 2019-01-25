<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/wp-travel/account/form-edit-account.php.
 *
 * HOWEVER, on occasion WP Travel will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.wensolutions.com/document/template-structure/
 * @author  WEN SOLUTIONS
 * @package WP Travel/Templates
 * @version 1.3.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'wp_travel_before_edit_account_form' ); ?>

<form class="wp-travel-EditAccountForm edit-account" action="" method="post">

	<?php do_action( 'wp_travel_edit_account_form_start' ); ?>

	<div class="form-horizontal clearfix">
		<div class="form-group gap-20">
			<label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'First name:', 'wp-travel' ); ?></label>
			<div class="col-sm-8 col-md-9">
			<input type="text" class="wp-travel-Input form-control wp-travel-Input--text input-text" name="account_first_name" id="account_first_name" value="<?php echo esc_attr( $user->first_name ); ?>" />
			</div>
		</div>
	</div>

	<div class="form-horizontal clearfix">
		<div class="form-group gap-20">
			<label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'Last name:', 'wp-travel' ); ?></label>
			<div class="col-sm-8 col-md-9">
			<input type="text" class="wp-travel-Input form-control wp-travel-Input--text input-text" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $user->last_name ); ?>" />
			</div>
		</div>
	</div>

	<div class="form-horizontal clearfix">
		<div class="form-group gap-20">
			<label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'Email Address:', 'wp-travel' ); ?></label>
			<div class="col-sm-8 col-md-9">
			<input type="email" class="wp-travel-Input form-control wp-travel-Input--email input-text" name="account_email" id="account_email" value="<?php echo esc_attr( $user->user_email ); ?>" />
			</div>
		</div>
	</div>

	<div class="form-horizontal clearfix">
		<div class="form-group gap-20">
			<label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'Change password', 'wp-travel' ); ?></label>
			<div class="col-sm-8 col-md-9">
				<div class="onoffswitch">
					<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="wp-travel-dsh-change-pass-switch">
					<label class="onoffswitch-label" for="wp-travel-dsh-change-pass-switch">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>
			</div>
		</div>
	</div>

	<div id="wp-travel-dsh-change-pass" style="display:none;" class="ch-password clearfix">
		<div class="form-horizontal clearfix">
			<div class="form-group gap-20">
				<label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'Current Password (leave blank to leave unchanged):', 'wp-travel' ); ?></label>
				<div class="col-sm-8 col-md-9">
					<input type="password" class="wp-travel-Input form-control wp-travel-Input--password input-text" name="password_current" id="password_current" />
				</div>
			</div>
		</div>
		<div class="form-horizontal clearfix">
			<div class="form-group gap-20">
				<label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'New Password (leave blank to leave unchanged):', 'wp-travel' ); ?></label>
				<div class="col-sm-8 col-md-9">
					<input type="password" class="wp-travel-Input form-control wp-travel-Input--password input-text" name="password_1" id="password_1" />
				</div>
			</div>
		</div>
		<div class="form-horizontal clearfix">
			<div class="form-group gap-20">
				<label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'Confirm New Password (leave blank to leave unchanged)', 'wp-travel' ); ?></label>
				<div class="col-sm-8 col-md-9">
					<input type="password" class="wp-travel-Input form-control wp-travel-Input--password input-text" name="password_2" id="password_2" />
				</div>
			</div>
		</div>
	</div>

	<!-- <div class="clear"></div> -->

	<?php do_action( 'wp_travel_edit_account_form' ); ?>

	<p>
		<?php wp_nonce_field( 'wp_travel_save_account_details', 'wp_account_details_security' ); ?>
		<input type="submit" class="wp-travel-Button button" name="wp_travel_save_account_details" value="<?php esc_attr_e( 'Save changes', 'wp-travel' ); ?>">
		<input type="hidden" name="action" value="wp_travel_save_account_details" />
	</p>

	<?php do_action( 'wp_travel_edit_account_form_end' ); ?>
</form>

<?php do_action( 'wp_travel_after_edit_account_form' ); ?>
