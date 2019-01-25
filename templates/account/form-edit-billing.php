<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/wp-travel/account/form-edit-billing.php.
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

do_action( 'wp_travel_before_edit_billing_form' );

$biling_data = get_user_meta( $user->ID, 'wp_travel_customer_billing_details', true );

// Set Vars.
$billing_address = isset( $biling_data['billing_address'] ) ? $biling_data['billing_address'] : '';
$billing_city    = isset( $biling_data['billing_city'] ) ? $biling_data['billing_city'] : '';
$billing_company = isset( $biling_data['billing_company'] ) ? $biling_data['billing_company'] : '';
$billing_zip     = isset( $biling_data['billing_zip_code'] ) ? $biling_data['billing_zip_code'] : '';
$billing_country = isset( $biling_data['billing_country'] ) ? $biling_data['billing_country'] : '';
$billing_phone   = isset( $biling_data['billing_phone'] ) ? $biling_data['billing_phone'] : '';
?>

<form class="wp-travel-EditAccountForm edit-account" action="" method="post">

	<?php do_action( 'wp_travel_edit_billing_form_start' ); ?>

	<div class="form-horizontal clearfix">
		<div class="form-group gap-20">
			<label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'Address:', 'wp-travel' ); ?></label>
			<div class="col-sm-8 col-md-9">
			<input type="text" class="wp-travel-Input form-control wp-travel-Input--text input-text" name="customer_billing_address" id="customer_billing_address" value="<?php echo esc_attr( $billing_address ); ?>" />
			</div>
		</div>
	</div>

	<div class="form-horizontal clearfix">
		<div class="form-group gap-20">
			<label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'City:', 'wp-travel' ); ?></label>
			<div class="col-sm-8 col-md-9">
			<input type="text" class="wp-travel-Input form-control wp-travel-Input--text input-text" name="customer_billing_city" id="customer_billing_city" value="<?php echo esc_attr( $billing_city ); ?>" />
			</div>
		</div>
	</div>

	<div class="form-horizontal clearfix">
		<div class="form-group gap-20">
			<label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'Company:', 'wp-travel' ); ?></label>
			<div class="col-sm-8 col-md-9">
			<input type="text" class="wp-travel-Input form-control wp-travel-Input--text input-text" name="customer_billing_company" id="customer_billing_company" value="<?php echo esc_attr( $billing_company ); ?>" />
			</div>
		</div>
	</div>

	<div class="form-horizontal clearfix">
		<div class="form-group gap-20">
			<label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'Zip/Postal code:', 'wp-travel' ); ?></label>
			<div class="col-sm-8 col-md-9">
			<input type="text" class="wp-travel-Input form-control wp-travel-Input--text input-text" name="customer_zip_code" id="customer_zip_code" value="<?php echo esc_attr( $billing_zip ); ?>" />
			</div>
		</div>
	</div>

	<div class="form-horizontal clearfix">
		<div class="form-group gap-20">
			<label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'Country:', 'wp-travel' ); ?></label>
			<div class="col-sm-8 col-md-9">
				<select class="selectpicker form-control" name="customer_country">
					<option value=""><?php esc_html_e( 'Select a Country', 'wp-travel' ) ?></option>

					<?php 
					$country_list = wp_travel_get_countries();
					foreach ( $country_list as $key => $value ) : ?>
						<option value="<?php echo esc_html( $key ); ?>" <?php selected( $key, $billing_country ) ?>>
							<?php echo esc_html( $value ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	</div>

	<div class="form-horizontal clearfix">
		<div class="form-group gap-20">
			<label class="col-sm-4 col-md-3 control-label"><?php esc_html_e( 'Phone:', 'wp-travel' ); ?></label>
			<div class="col-sm-8 col-md-9">
			<input type="text" class="wp-travel-Input form-control wp-travel-Input--text input-text" name="customer_phone" id="customer_phone" value="<?php echo esc_attr( $billing_phone ); ?>" />
			</div>
		</div>
	</div>

	<!-- <div class="clear"></div> -->

	<?php do_action( 'wp_travel_edit_billing_form' ); ?>

	<p>
		<?php wp_nonce_field( 'wp_travel_save_user_meta_billing_address', 'wp_billing_address_security' ); ?>
		<input type="submit" class="wp-travel-Button button" name="wp_travel_save_user_meta_billing_address" value="<?php esc_attr_e( 'Save changes', 'wp-travel' ); ?>">
		<input type="hidden" name="action" value="wp_travel_save_user_meta_billing_address" />
	</p>

	<?php do_action( 'wp_travel_edit_billing_form_end' ); ?>
</form>

<?php do_action( 'wp_travel_after_edit_billing_form' ); ?>
