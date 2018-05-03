<?php
/**
 * Admin Settings Tabs / Contents.
 *
 * @package WP_Travel
 */

// Add tab.
add_filter( 'wp_travel_settings_tabs', 'wp_travel_add_additional_settings_tabs' );
// Add tab content callback.
add_action( 'wp_travel_tabs_content_settings', 'wp_travel_account_settings_tab_callback', 12, 2 );

/**
 * Wp_travel_add_additional_settings_tabs Add Additional Tabs in settings.
 *
 * @param array $tabs_array.
 */
function wp_travel_add_additional_settings_tabs( $tabs_array ) {

	$tabs_array['account_options_global'] = array(
		'tab_label' => __( 'Account Settings', 'wp-travel' ),
		'content_title' => __( 'Account Settings', 'wp-travel' ),
	);

	return $tabs_array;

}
/**
 * Account Settings tab Callback.
 *
 * @param  Array $tab  List of tabs.
 * @param  Array $args Settings arg list.
 */
function wp_travel_account_settings_tab_callback( $tab, $args ) {

	if ( 'account_options_global' !== $tab ) {
		return;
	}

	$selected_account_page = isset( $args['settings']['myaccount_page_id'] ) ? $args['settings']['myaccount_page_id'] : wp_travel_get_page_id( 'wp-travel-dashboard' );
	
	$enable_checkout_customer_registration =  isset( $args['settings']['enable_checkout_customer_registration'] ) ? $args['settings']['enable_checkout_customer_registration'] : 'yes';

	$enable_my_account_customer_registration =  isset( $args['settings']['enable_my_account_customer_registration'] ) ? $args['settings']['enable_my_account_customer_registration'] : 'yes';

	$generate_username_from_email =  isset( $args['settings']['generate_username_from_email'] ) ? $args['settings']['generate_username_from_email'] : 'yes';

	$generate_user_password =  isset( $args['settings']['generate_user_password'] ) ? $args['settings']['generate_user_password'] : 'yes';

		echo '<table class="form-table">';
			echo '<tr>';
				echo '<th>';
					echo '<label for="cart-page-id">' . esc_html__( 'My Account Page', 'wp-travel' ) . '</label>';
				echo '</th>';
				echo '<td>';
					wp_dropdown_pages(array(
						'depth'                 => 0,
						'child_of'              => 0,
						'selected'              => $selected_account_page,
						'echo'                  => 1,
						'name'                  => 'myaccount_page_id',
						'id'                    => 'my-account-page-id', // string
						'class'                 => null, // string
						'show_option_none'      => null, // string
						'show_option_no_change' => null, // string
						'option_none_value'     => null, // string
					));
					echo '<p class="description">' . esc_html__( 'Choose the page to use as account dashboard for registered users', 'wp-travel' ) . '</p>';
				echo '</td>';
			echo '<tr>';

			echo '<tr>';
				echo '<th>';
					echo '<label for="currency">' . esc_html_e( 'Customer Registration', 'wp-travel' ) .  '</label>';
				echo '</th>';
				echo '<td>';
					echo '<span class="show-in-frontend checkbox-default-design">';
						echo '<label data-on="ON" data-off="OFF">';
							echo '<input' . checked( $enable_checkout_customer_registration, 'yes', false ) . ' value="1" name="enable_checkout_customer_registration" id="enable_checkout_customer_registration" type="checkbox" />';						
							echo '<span class="switch">';
							echo '</span>';
						echo '</label>';
					echo '</span>';
					echo '<p class="description"><label for="enable_checkout_customer_registration">' . esc_html__( 'Enable customer registration on the "Checkout" page.', 'wp-travel' ) . '</label></p>';
				echo '</td>';
				echo '<td>';
					echo '<span class="show-in-frontend checkbox-default-design">';
						echo '<label data-on="ON" data-off="OFF">';
							echo '<input' . checked( $enable_my_account_customer_registration, 'yes', false ) . ' value="1" name="enable_my_account_customer_registration" id="enable_my_account_customer_registration" type="checkbox" />';						
							echo '<span class="switch">';
							echo '</span>';
						echo '</label>';
					echo '</span>';
					echo '<p class="description"><label for="enable_my_account_customer_registration">' . esc_html__( 'Enable customer registration on the "My Account" page.', 'wp-travel' ) . '</label></p>';
				echo '</td>';
			echo '<tr>';
			echo '<tr>';
				echo '<th>';
					echo '<label for="currency">' . esc_html_e( 'Account Creation', 'wp-travel' ) .  '</label>';
				echo '</th>';
				echo '<td>';
					echo '<span class="show-in-frontend checkbox-default-design">';
						echo '<label data-on="ON" data-off="OFF">';
							echo '<input' . checked( $generate_username_from_email, 'yes', false ) . ' value="1" name="generate_username_from_email" id="generate_username_from_email" type="checkbox" />';						
							echo '<span class="switch">';
							echo '</span>';
						echo '</label>';
					echo '</span>';
					echo '<p class="description"><label for="generate_username_from_email">' . esc_html__( ' Automatically generate username from customer email.', 'wp-travel' ) . '</label></p>';
				echo '</td>';
				echo '<td>';
					echo '<span class="show-in-frontend checkbox-default-design">';
						echo '<label data-on="ON" data-off="OFF">';
							echo '<input' . checked( $generate_user_password, 'yes', false ) . ' value="1" name="generate_user_password" id="generate_user_password" type="checkbox" />';						
							echo '<span class="switch">';
							echo '</span>';
						echo '</label>';
					echo '</span>';
					echo '<p class="description"><label for="generate_user_password">' . esc_html__( ' Automatically generate customer password', 'wp-travel' ) . '</label></p>';
				echo '</td>';
			echo '</tr>';

		echo '</table>';

}

// Add settings to the save settings Array.
add_filter( 'wp_travel_before_save_settings', 'wp_travel_add_additional_settings_array' );

/**
 * Wp_travel_add_additional_settings_array Save Additional Settings.
 *
 * @param array $settings_array settings array.
 */
function wp_travel_add_additional_settings_array( $settings_array ) {

	// Account Page.
	$myaccount_page_id = isset( $_POST['myaccount_page_id'] ) ? $_POST['myaccount_page_id'] : '';
	$settings_array['myaccount_page_id'] = $myaccount_page_id;

	// Checkout Page customer registration.
	$enable_checkout_customer_registration = ( isset( $_POST['enable_checkout_customer_registration'] ) && '' !== $_POST['enable_checkout_customer_registration'] ) ? 'yes' : 'no';
	$settings_array['enable_checkout_customer_registration'] = $enable_checkout_customer_registration;

	// My Account Page customer registration.
	$enable_my_account_customer_registration = ( isset( $_POST['enable_my_account_customer_registration'] ) && '' !== $_POST['enable_my_account_customer_registration'] ) ? 'yes' : 'no';
	$settings_array['enable_my_account_customer_registration'] = $enable_my_account_customer_registration;

	// Generate Username from email.
	$generate_username_from_email = ( isset( $_POST['generate_username_from_email'] ) && '' !== $_POST['generate_username_from_email'] ) ? 'yes' : 'no';
	$settings_array['generate_username_from_email'] = $generate_username_from_email;

	// Generate User Password.
	$generate_user_password = ( isset( $_POST['generate_user_password'] ) && '' !== $_POST['generate_user_password'] ) ? 'yes' : 'no';
	$settings_array['generate_user_password'] = $generate_user_password;

	return $settings_array;

}

add_filter( 'wp_travel_create_pages', 'wp_travel_create_account_page' );

/**
 * Create Account page wp_travel_create_account_page
 *
 * @param array $pages Pages Array.
 */
function wp_travel_create_account_page( $pages ) {

	$pages['wp-travel-dashboard'] = array(
		'name'    => _x( 'wp-travel-dashboard', 'Page slug', 'wp-travel' ),
		'title'   => _x( 'WP Travel Dashboard', 'Page title', 'wp-travel' ),
		'content' => '[' . apply_filters( 'wp_travel_account_shortcode_tag', 'wp_travel_user_account' ) . ']',
	);

	return $pages;

}
