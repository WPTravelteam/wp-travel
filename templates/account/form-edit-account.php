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

// Print Errors / Notices.
WP_Travel()->notices->print_notices( 'error', true );

do_action( 'wp_travel_before_edit_account_form' ); ?>

<form class="wp-travel-EditAccountForm edit-account" action="" method="post">

	<?php do_action( 'wp_travel_edit_account_form_start' ); ?>

    <div class="form-horizontal clearfix">
        <div class="form-group gap-20">
            <label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'First name:', 'wp-travel' ); ?></label>
            <div class="col-sm-8 col-md-9">
            <input type="text" class="wp-travel-Input wp-travel-Input--text input-text" name="account_first_name" id="account_first_name" value="<?php echo esc_attr( $user->first_name ); ?>" />
            </div>
        </div>
    </div>

    <div class="form-horizontal clearfix">
        <div class="form-group gap-20">
            <label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'Last name:', 'wp-travel' ); ?></label>
            <div class="col-sm-8 col-md-9">
            <input type="text" class="wp-travel-Input wp-travel-Input--text input-text" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $user->last_name ); ?>" />
            </div>
        </div>
    </div>

     <div class="form-horizontal clearfix">
        <div class="form-group gap-20">
            <label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'Email Address:', 'wp-travel' ); ?></label>
            <div class="col-sm-8 col-md-9">
            <input type="email" class="wp-travel-Input wp-travel-Input--email input-text" name="account_email" id="account_email" value="<?php echo esc_attr( $user->user_email ); ?>" />
            </div>
        </div>
    </div>

	<fieldset>
		<legend><?php esc_html_e( 'Password change', 'wp-travel' ); ?></legend>

		<div class="form-horizontal clearfix">
			<div class="form-group gap-20">
				<label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'Current password (leave blank to leave unchanged)', 'wp-travel' ); ?></label>
				<div class="col-sm-8 col-md-9">
					<input type="password" class="wp-travel-Input wp-travel-Input--password input-text" name="password_current" id="password_current" />
				</div>
			</div>
		</div>


		<div class="form-horizontal clearfix">
			<div class="form-group gap-20">
				<label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'New password (leave blank to leave unchanged)', 'wp-travel' ); ?></label>
				<div class="col-sm-8 col-md-9">
					<input type="password" class="wp-travel-Input wp-travel-Input--password input-text" name="password_1" id="password_1" />
				</div>
			</div>
		</div>

		<div class="form-horizontal clearfix">
			<div class="form-group gap-20">
				<label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'Confirm new password', 'wp-travel' ); ?></label>
				<div class="col-sm-8 col-md-9">
					<input type="password" class="wp-travel-Input wp-travel-Input--password input-text" name="password_2" id="password_2" />
				</div>
			</div>
		</div>
	</fieldset>
	<div class="clear"></div>

	<?php do_action( 'wp_travel_edit_account_form' ); ?>

	<p>
		<?php wp_nonce_field( 'wp_travel_save_account_details' ); ?>
		<button type="submit" class="wp-travel-Button button" name="wp_travel_save_account_details" value="<?php esc_attr_e( 'Save changes', 'wp-travel' ); ?>"><?php esc_html_e( 'Save changes', 'wp-travel' ); ?></button>
		<input type="hidden" name="action" value="wp_travel_save_account_details" />
	</p>

	<?php do_action( 'wp_travel_edit_account_form_end' ); ?>
</form>

<?php do_action( 'wp_travel_after_edit_account_form' ); ?>
