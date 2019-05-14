<?php
/**
 * EmailTemplate Functions.
 *
 * @package wp-travel/inc/
 */

/**
 * Booking Admin Email Default template
 * 
 * @return HTML
 */
function wp_travel_booking_admin_default_email_content(){
	
	ob_start();

	?>
		<table class="wp-travel-wrapper" width="100%" cellpadding="0" cellspacing="0" style="color: #5d5d5d;font-family: Roboto, sans-serif;margin: auto;">
			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="left" class="wp-travel-content-top" style="background: #fff;box-sizing: border-box;margin: 0;padding: 20px 25px;">
					<p style="line-height: 1.55;font-size: 14px;"><?php esc_html_e( 'Hello', 'wp-travel' ) ?> {sitename} <?php esc_html_e( 'Admin', 'wp-travel' ) ?>,</p>
					<p style="line-height: 1.55;font-size: 14px;"><?php esc_html_e( 'You have received bookings from', 'wp-travel' ) ?> {customer_name}:</p>
					<p style="line-height: 1.55;font-size: 14px;"><b><?php esc_html_e( 'Booking ID', 'wp-travel' ) ?>: <a href="{booking_edit_link}" target="_blank" style="color: #5a418b;text-decoration: none;">#{booking_id}</a> ({booking_arrival_date})</b></p>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="left" class="wp-travel-content-title" style="background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">				
					<h3 style="font-size: 16px;line-height: 1;margin: 0;margin-top: 30px;"><b><?php esc_html_e( 'Booking Details', 'wp-travel' ) ?>:</b></h3>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					<b><?php esc_html_e( 'Itinerary', 'wp-travel' ) ?></b>

				</td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					<a href="{itinerary_link}" target="_blank" style="color: #5a418b;text-decoration: none;">{itinerary_title}</a>
				</td>	
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					<b><?php esc_html_e( 'Pax', 'wp-travel' ) ?></b>
				</td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					{booking_no_of_pax}
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					<b><?php esc_html_e( 'Scheduled Date', 'wp-travel' ) ?></b>
				</td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{booking_scheduled_date}</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					<b><?php esc_html_e( 'Arrival Date', 'wp-travel' ) ?></b>
				</td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{booking_arrival_date}</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Departure Date', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{booking_departure_date}</td>
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="left" class="wp-travel-content-title" style="background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">				
					<h3 style="font-size: 16px;line-height: 1;margin: 0;margin-top: 30px;"><b><?php esc_html_e( 'Customer Details', 'wp-travel' ) ?>:</b></h3>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Name', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_name}</td>
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Country', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_country}</td>
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Address', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_address}</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Phone', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_phone}</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Email', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_email}</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Note', 'wp-travel' ) ?></b></td>
				
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_note}</td>
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="center">				
					<a href="{booking_edit_link}" class="wp-travel-veiw-more" target="_blank" style="color: #fcfffd;text-decoration: none;background: #dd402e;border-radius: 3px;display: block;font-size: 14px;margin: 20px auto;padding: 10px 20px;text-align: center;height: 30px;line-height: 30px;width: 200px;"><?php esc_html_e( 'View details on site', 'wp-travel' ) ?></a>
				</td>
			</tr>
		</table>	
	<?php 

$content = ob_get_contents();
ob_end_clean();
return $content;

}
/**
 * Booking Client Default Content.
 */
function wp_travel_booking_client_default_email_content(){

ob_start();

?>

	<table class="wp-travel-wrapper" width="100%" cellpadding="0" cellspacing="0" style="color: #5d5d5d;font-family: Roboto, sans-serif;margin: auto;">
			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="left" class="wp-travel-content-top" style="background: #fff;box-sizing: border-box;margin: 0;padding: 20px 25px;">
					<p style="line-height: 1.55;font-size: 14px;"><?php esc_html_e( 'Hello', 'wp-travel' ) ?> {customer_name},</p>
					<p style="line-height: 1.55;font-size: 14px;"><?php esc_html_e( 'Your booking has been received and is now being processed. Your order details are shown below for your reference', 'wp-travel' ) ?>:</p>
					<p style="line-height: 1.55;font-size: 14px;"><b><?php esc_html_e( 'Booking ID', 'wp-travel' ) ?>: <a href="{booking_edit_link}" target="_blank" style="color: #5a418b;text-decoration: none;">#{booking_id}</a> ({booking_arrival_date})</b></p>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="left" class="wp-travel-content-title" style="background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">				
					<h3 style="font-size: 16px;line-height: 1;margin: 0;margin-top: 30px;"><b><?php esc_html_e( 'Booking Details', 'wp-travel' ) ?>:</b></h3>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					<b><?php esc_html_e( 'Itinerary', 'wp-travel' ) ?></b>

				</td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					<a href="{itinerary_link}" target="_blank" style="color: #5a418b;text-decoration: none;">{itinerary_title}</a>
				</td>	
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					<b><?php esc_html_e( 'Pax', 'wp-travel' ) ?></b>
				</td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					{booking_no_of_pax}
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					<b><?php esc_html_e( 'Scheduled Date', 'wp-travel' ) ?></b>
				</td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{booking_scheduled_date}</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					<b><?php esc_html_e( 'Arrival Date', 'wp-travel' ) ?></b>
				</td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{booking_arrival_date}</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Departure Date', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{booking_departure_date}</td>
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="left" class="wp-travel-content-title" style="background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">				
					<h3 style="font-size: 16px;line-height: 1;margin: 0;margin-top: 30px;"><b><?php esc_html_e( 'Your Details', 'wp-travel' ) ?>:</b></h3>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Name', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_name}</td>
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Country', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_country}</td>
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Address', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_address}</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Phone', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_phone}</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Email', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_email}</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Note', 'wp-travel' ) ?></b></td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_note}</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{bank_deposit_table}</td>
			</tr>
	</table>

<?php 

$content = ob_get_contents();

ob_end_clean();

return $content;

}

/**
 * Enqueries Admin Default Email.
 */
function wp_travel_enquiries_admin_default_email_content(){

ob_start();

?>
	<table class="wp-travel-wrapper" width="100%" cellpadding="0" cellspacing="0" style="color: #5d5d5d;font-family: Roboto, sans-serif;margin: auto;">
			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="left" class="wp-travel-content-top" style="background: #fff;box-sizing: border-box;margin: 0;padding: 20px 25px;">
					<p style="line-height: 1.55;font-size: 14px;"><?php esc_html_e( 'Hello', 'wp-travel' ) ?> {sitename} <?php esc_html_e( 'Admin', 'wp-travel' ) ?>,</p>
					<p style="line-height: 1.55;font-size: 14px;"><?php esc_html_e( 'You have received trip enquiry from', 'wp-travel' ) ?> {customer_name}:</p>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="left" class="wp-travel-content-title" style="background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">				
					<h3 style="font-size: 16px;line-height: 1;margin: 0;margin-top: 30px;"><b><?php esc_html_e( 'Enquiry Details', 'wp-travel' ) ?>:</b></h3>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					<b><?php esc_html_e( 'Itinerary', 'wp-travel' ) ?></b>

				</td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					<a href="{itinerary_link}" target="_blank" style="color: #5a418b;text-decoration: none;">{itinerary_title}</a>
				</td>	
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					<b><?php esc_html_e( 'Name', 'wp-travel' ) ?></b>

				</td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					{customer_name}
				</td>	
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					<b><?php esc_html_e( 'E-mail', 'wp-travel' ) ?></b>

				</td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					{customer_email}
				</td>	
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					<b><?php esc_html_e( 'Enquiry Message', 'wp-travel' ) ?></b>

				</td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					{customer_note}
				</td>	
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="center">			
					<a href="{enquery_edit_link}" class="wp-travel-veiw-more" target="_blank" style="color: #fcfffd;text-decoration: none;background: #dd402e;border-radius: 3px;display: block;font-size: 14px;margin: 20px auto;padding: 10px 20px;text-align: center;height: 30px;line-height: 30px;width: 200px;"><?php esc_html_e( 'View details on site', 'wp-travel' ) ?>
				</td>
			</tr>
		</table>

<?php

$content = ob_get_contents();

ob_end_clean();

return $content;

}
/**
 * Payment Default Email Admin Content.
 * @return HTML
 */
function wp_travel_payment_admin_default_email_content() {

	ob_start();

	?>
	<table class="wp-travel-wrapper" width="100%" cellpadding="0" cellspacing="0" style="color: #5d5d5d;font-family: Roboto, sans-serif;margin: auto;">
			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="left" class="wp-travel-content-top" style="background: #fff;box-sizing: border-box;margin: 0;padding: 20px 25px;">
					<p style="line-height: 1.55;font-size: 14px;"><?php esc_html_e( 'Hello', 'wp-travel' ) ?> {sitename} <?php esc_html_e( 'Admin', 'wp-travel' ) ?>,</p>
					<p style="line-height: 1.55;font-size: 14px;"><?php esc_html_e( 'You have received payment from', 'wp-travel' ) ?> {customer_name}:</p>
					<p style="line-height: 1.55;font-size: 14px;"><b><?php esc_html_e( 'Booking ID', 'wp-travel' ) ?>: <a href="{booking_edit_link}" target="_blank" style="color: #5a418b;text-decoration: none;">#{booking_id}</a> ({booking_arrival_date})</b></p>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="left" class="wp-travel-content-title" style="background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">				
					<h3 style="font-size: 16px;line-height: 1;margin: 0;margin-top: 30px;"><b><?php esc_html_e( 'Booking Details', 'wp-travel' ) ?>:</b></h3>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					<b><?php esc_html_e( 'Itinerary', 'wp-travel' ) ?></b>

				</td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					<a href="{itinerary_link}" target="_blank" style="color: #5a418b;text-decoration: none;">{itinerary_title}</a>
				</td>	
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					<b><?php esc_html_e( 'Pax', 'wp-travel' ) ?></b>
				</td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					{booking_no_of_pax}
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					<b><?php esc_html_e( 'Scheduled Date', 'wp-travel' ) ?></b>
				</td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{booking_scheduled_date}</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
					<b><?php esc_html_e( 'Arrival Date', 'wp-travel' ) ?></b>
				</td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{booking_arrival_date}</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Departure Date', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{booking_departure_date}</td>
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="left" class="wp-travel-content-title" style="background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">				
					<h3 style="font-size: 16px;line-height: 1;margin: 0;margin-top: 30px;"><b><?php esc_html_e( 'Customer Details', 'wp-travel' ) ?>:</b></h3>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Name', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_name}</td>
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Country', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_country}</td>
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Address', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_address}</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Phone', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_phone}</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Email', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_email}</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Note', 'wp-travel' ) ?></b></td>
				
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_note}</td>
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="left" class="wp-travel-content-title" style="background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">				
					<h3 style="font-size: 16px;line-height: 1;margin: 0;margin-top: 30px;"><b><?php esc_html_e( 'Payment Details', 'wp-travel' ) ?>:</b></h3>
				</td>
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Payment Status', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{payment_status}</td>
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Payment Mode', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{payment_mode}</td>
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Trip Price', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{currency_symbol} {trip_price}</td>
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Payment Amount', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{currency_symbol} {payment_amount}</td>
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="center">				
					<a href="{booking_edit_link}" class="wp-travel-veiw-more" target="_blank" style="color: #fcfffd;text-decoration: none;background: #dd402e;border-radius: 3px;display: block;font-size: 14px;margin: 20px auto;padding: 10px 20px;text-align: center;height: 30px;line-height: 30px;width: 200px;"><?php esc_html_e( 'View details on site', 'wp-travel' ) ?></a>
				</td>
			</tr>
		</table>

	<?php 

	$content = ob_get_contents();

	ob_end_clean();

	return $content;

}

/**
 * Payment default client email template.
 * @return HTML
 */
function wp_travel_payment_client_default_email_content() {

	ob_start(); ?>	
	<table class="wp-travel-wrapper" width="100%" cellpadding="0" cellspacing="0" style="color: #5d5d5d;font-family: Roboto, sans-serif;margin: auto;">
			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="left" class="wp-travel-content-top" style="background: #fff;box-sizing: border-box;margin: 0;padding: 20px 25px;">
				<p style="line-height: 1.55;font-size: 14px;"><?php esc_html_e( 'Hello', 'wp-travel' ) ?> {customer_name},</p>
					<p style="line-height: 1.55;font-size: 14px;"><?php esc_html_e( 'Your payment has been received.', 'wp-travel' ) ?></p>
					<p style="line-height: 1.55;font-size: 14px;"><b><?php esc_html_e( 'Booking ID', 'wp-travel' ) ?>: <a href="{booking_edit_link}" target="_blank" style="color: #5a418b;text-decoration: none;">#{booking_id}</a> ({booking_arrival_date})</b></p>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td colspan="2" align="left" class="wp-travel-content-title" style="background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">				
					<h3 style="font-size: 16px;line-height: 1;margin: 0;margin-top: 30px;"><b><?php esc_html_e( 'Payment Details', 'wp-travel' ) ?>:</b></h3>
				</td>
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Payment Status', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{payment_status}</td>
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Payment Mode', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{payment_mode}</td>
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Trip Price', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{currency_symbol} {trip_price}</td>
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Payment Amount', 'wp-travel' ) ?></b></td>
				<td align="left" style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{currency_symbol} {payment_amount}</td>
			</tr>

		</table>

	<?php 

	$content = ob_get_contents();

	ob_end_clean();

	return $content;

}


/**
 * Will return admin email template.
 *
 * @return HTML
 */
function wp_travel_admin_email_template() {
	ob_start(); ?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>To admin</title>
		<style type="text/css">
			body{
				 background: #fcfcfc ;
				 color: #5d5d5d;
				 margin: 0;
				 padding: 0;
			}
			a{
				color: #5a418b;text-decoration: none;
			}
			.wp-travel-wrapper{
				color: #5d5d5d;
				font-family: Roboto, sans-serif;
				margin: auto;
			}
			.wp-travel-wrapper tr{background: #fff}
			.wp-travel-header td{
				background: #dd402e;
				box-sizing: border-box;
				margin: 0;
				padding: 20px 25px;
			}
			.wp-travel-header h2 {
				color: #fcfffd;
				font-size: 20px;
				margin: 0;
				padding: 0;
				text-align: center;
			}

			.wp-travel-content-top{
				background: #fff;
				box-sizing: border-box;
				margin: 0;
				padding: 20px 25px;
			}
			.wp-travel-content-top p{
				line-height: 1.55;
				font-size: 14px;
			}
			.wp-travel-content-title{
				background: #fff;
				box-sizing: border-box;
				margin: 0;
				padding: 0px 0px 8px 25px;
			}
			.wp-travel-content-title h3{font-size: 16px; line-height: 1; margin:0;margin-top: 30px}

			.wp-travel-content-head{width: 24%}
			.wp-travel-content-info{width: 76%}
			.wp-travel-content-head td,
			.wp-travel-content-info td{
				font-size: 14px;
				background: #fff;
				box-sizing: border-box;
				margin: 0;
				padding: 0px 0px 8px 25px;
			}
			.full-width{width: 100%!important}

			.wp-travel-veiw-more{
				background: #dd402e;
				border-radius: 3px;
				color: #fcfffd;
				display:block;
				font-size: 14px;
				margin: 20px auto;			
				padding: 10px 20px;
				text-align: center;
				text-decoration: none;
				width: 130px;
			}

			.wp-travel-footer td{
				background: #eaebed;
				box-sizing: border-box;
				font-size: 14px;
				padding: 10px 25px;
			}

			@media screen and ( max-width:600px ){
				table[class="wp-travel-wrapper"] {width: 100%!important}
			}
			@media screen and ( max-width:480px ){
				table[class="wp-travel-content-head"],
				table[class="wp-travel-content-info"] {width: 100%!important;}
				table[class="wp-travel-content-info"]{margin-bottom: 10px}

			}
		</style>
	</head>
	<body style="background: #fcfcfc;color: #5d5d5d;margin: 0;padding: 0;">
		<!-- Wrapper -->
		<table class="wp-travel-wrapper" width="600" cellpadding="0" cellspacing="0" style="color: #5d5d5d;font-family: Roboto, sans-serif;margin: auto;"> 
			<tr class="wp-travel-header" style="background: #fff;">			
				<td align="left" style="background: #dd402e;box-sizing: border-box;margin: 0;padding: 20px 25px;"> <!-- Header -->
					<h2 style="color: #fcfffd;font-size: 20px;margin: 0;padding: 0;text-align: center;"><?php esc_html_e( 'New Bookings', 'wp-travel' ) ?></h2>
				</td> <!-- /Header -->
			</tr>
			
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" class="wp-travel-content-top" style="background: #fff;box-sizing: border-box;margin: 0;padding: 20px 25px;">
					<p style="line-height: 1.55;font-size: 14px;"><?php esc_html_e( 'Hello', 'wp-travel' ) ?> {sitename} <?php esc_html_e( 'Admin', 'wp-travel' ) ?>,</p>
					<p style="line-height: 1.55;font-size: 14px;"><?php esc_html_e( 'You have received bookings from', 'wp-travel' ) ?> {customer_name}:</p>
					<p style="line-height: 1.55;font-size: 14px;"><b><?php esc_html_e( 'Booking ID', 'wp-travel' ) ?>: <a href="{booking_edit_link}" target="_blank" style="color: #5a418b;text-decoration: none;">#{booking_id}</a> ({booking_arrival_date})</b></p>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" class="wp-travel-content-title" style="background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">				
					<h3 style="font-size: 16px;line-height: 1;margin: 0;margin-top: 30px;"><b><?php esc_html_e( 'Booking Details', 'wp-travel' ) ?>:</b></h3>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Itinerary', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
								<a href="{itinerary_link}" target="_blank" style="color: #5a418b;text-decoration: none;">{itinerary_title}</a>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Pax', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{booking_no_of_pax}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Scheduled Date', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{booking_scheduled_date}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Arrival Date', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{booking_arrival_date}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Departure Date', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{booking_departure_date}</td>
						</tr>
					</table>
				</td>
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" class="wp-travel-content-title" style="background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">				
					<h3 style="font-size: 16px;line-height: 1;margin: 0;margin-top: 30px;"><b><?php esc_html_e( 'Customer Details', 'wp-travel' ) ?>:</b></h3>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Name', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_name}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Country', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_country}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Address', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_address}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Phone', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_phone}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Email', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_email}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head full-width" align="left" cellspacing="0" cellpadding="0" style="width: 100%!important;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Note', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info full-width" align="left" cellspacing="0" cellpadding="0" style="width: 100%!important;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_note}</td>
						</tr>
					</table>
				</td>
			</tr>
		
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="center">				
					<p style="display:inline-block;margin:0 auto;width:100%"><a href="{booking_edit_link}" class="wp-travel-veiw-more" target="_blank" style="color: #fcfffd;text-decoration: none;background: #dd402e;border-radius: 3px;display: block;font-size: 14px;margin: 20px auto;padding: 10px 20px;text-align: center;width: 130px;"><?php esc_html_e( 'View details on site', 'wp-travel' ) ?></a></p>
				</td>
			</tr>
			
			<tr class="wp-travel-footer" style="background: #fff;">
				<td align="center" style="background: #eaebed;box-sizing: border-box;font-size: 14px;padding: 10px 25px;">
					<p>{sitename} - <?php esc_html_e( 'Powered By', 'wp-travel' ) ?>: <a href="http://wptravel.io/" target="_blank" style="color: #5a418b;text-decoration: none;"><?php esc_html_e( 'WP Travel', 'wp-travel' ) ?>.</a></p>
					
				</td>
			</tr>
		</table><!-- /Wrapper -->
	</body>
	</html>

	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

/**
 * Will return customer email template.
 *
 * @return HTML
 */
function wp_travel_customer_email_template() {
	ob_start(); ?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>To admin</title>
		<style type="text/css">
			body{
				 background: #fcfcfc ;
				 color: #5d5d5d;
				 margin: 0;
				 padding: 0;
			}
			a{
				color: #5a418b;text-decoration: none;
			}
			.wp-travel-wrapper{
				color: #5d5d5d;
				font-family: Roboto, sans-serif;
				margin: auto;
			}
			.wp-travel-wrapper tr{background: #fff}
			.wp-travel-header td{
				background: #dd402e;
				box-sizing: border-box;
				margin: 0;
				padding: 20px 25px;
			}
			.wp-travel-header h2 {
				color: #fcfffd;
				font-size: 20px;
				margin: 0;
				padding: 0;
				text-align: center;
			}

			.wp-travel-content-top{
				background: #fff;
				box-sizing: border-box;
				margin: 0;
				padding: 20px 25px;
			}
			.wp-travel-content-top p{
				line-height: 1.55;
				font-size: 14px;
			}
			.wp-travel-content-title{
				background: #fff;
				box-sizing: border-box;
				margin: 0;
				padding: 0px 0px 8px 25px;
			}
			.wp-travel-content-title h3{font-size: 16px; line-height: 1; margin:0;margin-top: 30px}

			.wp-travel-content-head{width: 24%}
			.wp-travel-content-info{width: 76%}
			.wp-travel-content-head td,
			.wp-travel-content-info td{
				font-size: 14px;
				background: #fff;
				box-sizing: border-box;
				margin: 0;
				padding: 0px 0px 8px 25px;
			}
			.full-width{width: 100%!important}

			.wp-travel-veiw-more{
				background: #dd402e;
				border-radius: 3px;
				color: #fcfffd;
				display:block;
				font-size: 14px;
				margin: 20px auto;			
				padding: 10px 20px;
				text-align: center;
				text-decoration: none;
				width: 130px;
			}

			.wp-travel-footer td{
				background: #eaebed;
				box-sizing: border-box;
				font-size: 14px;
				padding: 10px 25px;
			}

			@media screen and ( max-width:600px ){
				table[class="wp-travel-wrapper"] {width: 100%!important}
			}
			@media screen and ( max-width:480px ){
				table[class="wp-travel-content-head"],
				table[class="wp-travel-content-info"] {width: 100%!important;}
				table[class="wp-travel-content-info"]{margin-bottom: 10px}

			}
		</style>
	</head>
	<body style="background: #fcfcfc;color: #5d5d5d;margin: 0;padding: 0;">
		<!-- Wrapper -->
		<table class="wp-travel-wrapper" width="600" cellpadding="0" cellspacing="0" style="color: #5d5d5d;font-family: Roboto, sans-serif;margin: auto;"> 
			<tr class="wp-travel-header" style="background: #fff;">			
				<td align="left" style="background: #dd402e;box-sizing: border-box;margin: 0;padding: 20px 25px;"> <!-- Header -->
					<h2 style="color: #fcfffd;font-size: 20px;margin: 0;padding: 0;text-align: center;"><?php esc_html_e( 'Thank you for your booking.', 'wp-travel' );?></h2>
				</td> <!-- /Header -->
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" class="wp-travel-content-top" style="background: #fff;box-sizing: border-box;margin: 0;padding: 20px 25px;">
					<p style="line-height: 1.55;font-size: 14px;"><?php esc_html_e( 'Hello', 'wp-travel' ) ?> {customer_name},</p>
					<p style="line-height: 1.55;font-size: 14px;"><?php esc_html_e( 'Your booking has been received and is now being processed. Your order details are shown below for your reference', 'wp-travel' ) ?>:</p>
					<p style="line-height: 1.55;font-size: 14px;"><b><?php esc_html_e( 'Booking ID', 'wp-travel' ) ?>: #{booking_id} ({booking_arrival_date})</b></p>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" class="wp-travel-content-title" style="background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">				
					<h3 style="font-size: 16px;line-height: 1;margin: 0;margin-top: 30px;"><b><?php esc_html_e( 'Booking Details', 'wp-travel' ) ?>:</b></h3>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Itinerary', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
								{itinerary_title}
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Pax', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{booking_no_of_pax}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Scheduled Date', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{booking_scheduled_date}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Arrival Date', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{booking_arrival_date}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Departure Date', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{booking_departure_date}</td>
						</tr>
					</table>
				</td>
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" class="wp-travel-content-title" style="background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">				
					<h3 style="font-size: 16px;line-height: 1;margin: 0;margin-top: 30px;"><b><?php esc_html_e( 'Your Details', 'wp-travel' ) ?>:</b></h3>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Name', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_name}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Country', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_country}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Address', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_address}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Phone', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_phone}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Email', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_email}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head full-width" align="left" cellspacing="0" cellpadding="0" style="width: 100%!important;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Note', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info full-width" align="left" cellspacing="0" cellpadding="0" style="width: 100%!important;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_note}</td>
						</tr>
					</table>
				</td>
			</tr>			
			<tr class="wp-travel-footer" style="background: #fff;">
				<td align="center" style="background: #eaebed;box-sizing: border-box;font-size: 14px;padding: 10px 25px;">
					<p>{sitename} - <?php esc_html_e( 'Powered By', 'wp-travel' ) ?>: <a href="http://wptravel.io/" target="_blank" style="color: #5a418b;text-decoration: none;"><?php esc_html_e( 'WP Travel', 'wp-travel' ) ?>.</a></p>
					
				</td>
			</tr>
		</table><!-- /Wrapper -->
	</body>
	</html>

	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

/**
 * Will return Enqueries admin email template.
 *
 * @return HTML
 */
function wp_travel_enqueries_admin_email_template() {
	ob_start(); ?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>To admin</title>
		<style type="text/css">
			body{
				 background: #fcfcfc ;
				 color: #5d5d5d;
				 margin: 0;
				 padding: 0;
			}
			a{
				color: #5a418b;text-decoration: none;
			}
			.wp-travel-wrapper{
				color: #5d5d5d;
				font-family: Roboto, sans-serif;
				margin: auto;
			}
			.wp-travel-wrapper tr{background: #fff}
			.wp-travel-header td{
				background: #dd402e;
				box-sizing: border-box;
				margin: 0;
				padding: 20px 25px;
			}
			.wp-travel-header h2 {
				color: #fcfffd;
				font-size: 20px;
				margin: 0;
				padding: 0;
				text-align: center;
			}

			.wp-travel-content-top{
				background: #fff;
				box-sizing: border-box;
				margin: 0;
				padding: 20px 25px;
			}
			.wp-travel-content-top p{
				line-height: 1.55;
				font-size: 14px;
			}
			.wp-travel-content-title{
				background: #fff;
				box-sizing: border-box;
				margin: 0;
				padding: 0px 0px 8px 25px;
			}
			.wp-travel-content-title h3{font-size: 16px; line-height: 1; margin:0;margin-top: 30px}

			.wp-travel-content-head{width: 24%}
			.wp-travel-content-info{width: 76%}
			.wp-travel-content-head td,
			.wp-travel-content-info td{
				font-size: 14px;
				background: #fff;
				box-sizing: border-box;
				margin: 0;
				padding: 0px 0px 8px 25px;
			}
			.full-width{width: 100%!important}

			.wp-travel-veiw-more{
				background: #dd402e;
				border-radius: 3px;
				color: #fcfffd;
				display:block;
				font-size: 14px;
				margin: 20px auto;			
				padding: 10px 20px;
				text-align: center;
				text-decoration: none;
				width: 130px;
			}

			.wp-travel-footer td{
				background: #eaebed;
				box-sizing: border-box;
				font-size: 14px;
				padding: 10px 25px;
			}

			@media screen and ( max-width:600px ){
				table[class="wp-travel-wrapper"] {width: 100%!important}
			}
			@media screen and ( max-width:480px ){
				table[class="wp-travel-content-head"],
				table[class="wp-travel-content-info"] {width: 100%!important;}
				table[class="wp-travel-content-info"]{margin-bottom: 10px}

			}
		</style>
	</head>
	<body style="background: #fcfcfc;color: #5d5d5d;margin: 0;padding: 0;">
		<!-- Wrapper -->
		<table class="wp-travel-wrapper" width="600" cellpadding="0" cellspacing="0" style="color: #5d5d5d;font-family: Roboto, sans-serif;margin: auto;"> 
			<tr class="wp-travel-header" style="background: #fff;">			
				<td align="left" style="background: #dd402e;box-sizing: border-box;margin: 0;padding: 20px 25px;"> <!-- Header -->
					<h2 style="color: #fcfffd;font-size: 20px;margin: 0;padding: 0;text-align: center;"><?php esc_html_e( 'New Trip Enquiry', 'wp-travel' ) ?></h2>
				</td> <!-- /Header -->
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" class="wp-travel-content-top" style="background: #fff;box-sizing: border-box;margin: 0;padding: 20px 25px;">
					<p style="line-height: 1.55;font-size: 14px;"><?php esc_html_e( 'Hello', 'wp-travel' ) ?> {sitename} <?php esc_html_e( 'Admin', 'wp-travel' ) ?>,</p>
					<p style="line-height: 1.55;font-size: 14px;"><?php esc_html_e( 'You have received trip enquiry from', 'wp-travel' ) ?> {customer_name}:</p>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" class="wp-travel-content-title" style="background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">				
					<h3 style="font-size: 16px;line-height: 1;margin: 0;margin-top: 30px;"><b><?php esc_html_e( 'Booking Details', 'wp-travel' ) ?>:</b></h3>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Itinerary', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
								<a href="{itinerary_link}" target="_blank" style="color: #5a418b;text-decoration: none;">{itinerary_title}</a>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" class="wp-travel-content-title" style="background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">				
					<h3 style="font-size: 16px;line-height: 1;margin: 0;margin-top: 30px;"><b><?php esc_html_e( 'Customer Details', 'wp-travel' ) ?>:</b></h3>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Name', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_name}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Email', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_email}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head full-width" align="left" cellspacing="0" cellpadding="0" style="width: 100%!important;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Enquiry Message', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info full-width" align="left" cellspacing="0" cellpadding="0" style="width: 100%!important;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{customer_note}</td>
						</tr>
					</table>
				</td>
			</tr>

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="center">				
					<a href="{enquery_edit_link}" class="wp-travel-veiw-more" target="_blank" style="color: #fcfffd;text-decoration: none;background: #dd402e;border-radius: 3px;display: block;font-size: 14px;margin: 20px auto;padding: 10px 20px;text-align: center;width: 130px;"><?php esc_html_e( 'View details on site', 'wp-travel' ) ?></a>
				</td>
			</tr>
			<tr class="wp-travel-footer" style="background: #fff;">
				<td align="center" style="background: #eaebed;box-sizing: border-box;font-size: 14px;padding: 10px 25px;">
					<p>{sitename} - <?php esc_html_e( 'Powered By', 'wp-travel' ) ?>: <a href="http://wptravel.io/" target="_blank" style="color: #5a418b;text-decoration: none;"><?php esc_html_e( 'WP Travel', 'wp-travel' ) ?>.</a></p>
					
				</td>
			</tr>
		</table><!-- /Wrapper -->
	</body>
	</html>

	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}


/**
 * Will return admin email template.
 *
 * @return HTML
 */
function wp_travel_payment_email_template_admin() {
	ob_start(); ?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>To admin</title>
		<style type="text/css">
			body{
				 background: #fcfcfc ;
				 color: #5d5d5d;
				 margin: 0;
				 padding: 0;
			}
			a{
				color: #5a418b;text-decoration: none;
			}
			.wp-travel-wrapper{
				color: #5d5d5d;
				font-family: Roboto, sans-serif;
				margin: auto;
			}
			.wp-travel-wrapper tr{background: #fff}
			.wp-travel-header td{
				background: #dd402e;
				box-sizing: border-box;
				margin: 0;
				padding: 20px 25px;
			}
			.wp-travel-header h2 {
				color: #fcfffd;
				font-size: 20px;
				margin: 0;
				padding: 0;
				text-align: center;
			}

			.wp-travel-content-top{
				background: #fff;
				box-sizing: border-box;
				margin: 0;
				padding: 20px 25px;
			}
			.wp-travel-content-top p{
				line-height: 1.55;
				font-size: 14px;
			}
			.wp-travel-content-title{
				background: #fff;
				box-sizing: border-box;
				margin: 0;
				padding: 0px 0px 8px 25px;
			}
			.wp-travel-content-title h3{font-size: 16px; line-height: 1; margin:0;margin-top: 30px}

			.wp-travel-content-head{width: 24%}
			.wp-travel-content-info{width: 76%}
			.wp-travel-content-head td,
			.wp-travel-content-info td{
				font-size: 14px;
				background: #fff;
				box-sizing: border-box;
				margin: 0;
				padding: 0px 0px 8px 25px;
			}
			.full-width{width: 100%!important}

			.wp-travel-veiw-more{
				background: #dd402e;
				border-radius: 3px;
				color: #fcfffd;
				display:block;
				font-size: 14px;
				margin: 20px auto;			
				padding: 10px 20px;
				text-align: center;
				text-decoration: none;
				width: 130px;
			}

			.wp-travel-footer td{
				background: #eaebed;
				box-sizing: border-box;
				font-size: 14px;
				padding: 10px 25px;
			}

			@media screen and ( max-width:600px ){
				table[class="wp-travel-wrapper"] {width: 100%!important}
			}
			@media screen and ( max-width:480px ){
				table[class="wp-travel-content-head"],
				table[class="wp-travel-content-info"] {width: 100%!important;}
				table[class="wp-travel-content-info"]{margin-bottom: 10px}

			}
		</style>
	</head>
	<body style="background: #fcfcfc;color: #5d5d5d;margin: 0;padding: 0;">
		<!-- Wrapper -->
		<table class="wp-travel-wrapper" width="600" cellpadding="0" cellspacing="0" style="color: #5d5d5d;font-family: Roboto, sans-serif;margin: auto;"> 
			<tr class="wp-travel-header" style="background: #fff;">			
				<td align="left" style="background: #dd402e;box-sizing: border-box;margin: 0;padding: 20px 25px;"> <!-- Header -->
					<h2 style="color: #fcfffd;font-size: 20px;margin: 0;padding: 0;text-align: center;"><?php esc_html_e( 'New Bookings Payment', 'wp-travel' ) ?></h2>
				</td> <!-- /Header -->
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" class="wp-travel-content-top" style="background: #fff;box-sizing: border-box;margin: 0;padding: 20px 25px;">
					<p style="line-height: 1.55;font-size: 14px;"><?php esc_html_e( 'Hello', 'wp-travel' ) ?> {sitename} <?php esc_html_e( 'Admin', 'wp-travel' ) ?>,</p>
					<p style="line-height: 1.55;font-size: 14px;"><?php esc_html_e( 'You have received payment from', 'wp-travel' ) ?> {customer_name}:</p>
					<p style="line-height: 1.55;font-size: 14px;"><b><?php esc_html_e( 'Booking ID', 'wp-travel' ) ?>: <a href="{booking_edit_link}" target="_blank" style="color: #5a418b;text-decoration: none;">#{booking_id}</a> ({booking_arrival_date})</b></p>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" class="wp-travel-content-title" style="background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">				
					<h3 style="font-size: 16px;line-height: 1;margin: 0;margin-top: 30px;"><b><?php esc_html_e( 'Booking Details', 'wp-travel' ) ?>:</b></h3>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Itinerary', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
								<a href="{itinerary_link}" target="_blank" style="color: #5a418b;text-decoration: none;">{itinerary_title}</a>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Pax', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{booking_no_of_pax}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Scheduled Date', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{booking_scheduled_date}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Arrival Date', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{booking_arrival_date}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Departure Date', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">{booking_departure_date}</td>
						</tr>
					</table>
				</td>
			</tr>
			<!-- /Payment Starts -->
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Payment Status', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
							{payment_status}
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Payment Mode', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
							{payment_mode}
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b> <?php esc_html_e( 'Trip Price', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
								{currency_symbol} {trip_price}
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Payment Amount', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
								{currency_symbol} {payment_amount}
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<!-- /Payment ends -->

			<tr class="wp-travel-content" style="background: #fff;">
				<td align="center">				
					<a href="{booking_edit_link}" class="wp-travel-veiw-more" target="_blank" style="color: #fcfffd;text-decoration: none;background: #dd402e;border-radius: 3px;display: block;font-size: 14px;margin: 20px auto;padding: 10px 20px;text-align: center;width: 130px;"><?php esc_html_e( 'View details ', 'wp-travel' ) ?></a>
				</td>
			</tr>
			<tr class="wp-travel-footer" style="background: #fff;">
				<td align="center" style="background: #eaebed;box-sizing: border-box;font-size: 14px;padding: 10px 25px;">
					<p>{sitename} - <?php esc_html_e( 'Powered By', 'wp-travel' ) ?>: <a href="http://wptravel.io/" target="_blank" style="color: #5a418b;text-decoration: none;"><?php esc_html_e( 'WP Travel', 'wp-travel' ) ?>.</a></p>
					
				</td>
			</tr>
		</table><!-- /Wrapper -->
	</body>
	</html>

	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

/**
 * Will return customer email template.
 *
 * @return HTML
 */
function wp_travel_payment_email_template_customer() {
	ob_start(); ?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>To customer</title>
		<style type="text/css">
			body{
				 background: #fcfcfc;
				 color: #5d5d5d;
				 margin: 0;
				 padding: 0;
			}
			a{
				color: #5a418b;text-decoration: none;
			}
			.wp-travel-wrapper{
				color: #5d5d5d;
				font-family: Roboto, sans-serif;
				margin: auto;
			}
			.wp-travel-wrapper tr{background: #fff}
			.wp-travel-header td{
				background: #dd402e;
				box-sizing: border-box;
				margin: 0;
				padding: 20px 25px;
			}
			.wp-travel-header h2 {
				color: #fcfffd;
				font-size: 20px;
				margin: 0;
				padding: 0;
				text-align: center;
			}

			.wp-travel-content-top{
				background: #fff;
				box-sizing: border-box;
				margin: 0;
				padding: 20px 25px;
			}
			.wp-travel-content-top p{
				line-height: 1.55;
				font-size: 14px;
			}
			.wp-travel-content-title{
				background: #fff;
				box-sizing: border-box;
				margin: 0;
				padding: 0px 0px 8px 25px;
			}
			.wp-travel-content-title h3{font-size: 16px; line-height: 1; margin:0;margin-top: 30px}

			.wp-travel-content-head{width: 24%}
			.wp-travel-content-info{width: 76%}
			.wp-travel-content-head td,
			.wp-travel-content-info td{
				font-size: 14px;
				background: #fff;
				box-sizing: border-box;
				margin: 0;
				padding: 0px 0px 8px 25px;
			}
			.full-width{width: 100%!important}

			.wp-travel-veiw-more{
				background: #dd402e;
				border-radius: 3px;
				color: #fcfffd;
				display:block;
				font-size: 14px;
				margin: 20px auto;			
				padding: 10px 20px;
				text-align: center;
				text-decoration: none;
				width: 130px;
			}

			.wp-travel-footer td{
				background: #eaebed;
				box-sizing: border-box;
				font-size: 14px;
				padding: 10px 25px;
			}

			@media screen and ( max-width:600px ){
				table[class="wp-travel-wrapper"] {width: 100%!important}
			}
			@media screen and ( max-width:480px ){
				table[class="wp-travel-content-head"],
				table[class="wp-travel-content-info"] {width: 100%!important;}
				table[class="wp-travel-content-info"]{margin-bottom: 10px}

			}
		</style>
	</head>
	<body style="background: #fcfcfc;color: #5d5d5d;margin: 0;padding: 0;">
		<!-- Wrapper -->
		<table class="wp-travel-wrapper" width="600" cellpadding="0" cellspacing="0" style="color: #5d5d5d;font-family: Roboto, sans-serif;margin: auto;"> 
			<tr class="wp-travel-header" style="background: #fff;">			
				<td align="left" style="background: #dd402e;box-sizing: border-box;margin: 0;padding: 20px 25px;"> <!-- Header -->
					<h2 style="color: #fcfffd;font-size: 20px;margin: 0;padding: 0;text-align: center;"><?php esc_html_e( 'Thank you for your Payment.', 'wp-travel' );?></h2>
				</td> <!-- /Header -->
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left" class="wp-travel-content-top" style="background: #fff;box-sizing: border-box;margin: 0;padding: 20px 25px;">
					<p style="line-height: 1.55;font-size: 14px;"><?php esc_html_e( 'Hello', 'wp-travel' ) ?> {customer_name},</p>
					<p style="line-height: 1.55;font-size: 14px;"><?php esc_html_e( 'Your payment has been received.', 'wp-travel' ) ?>:</p>
					<p style="line-height: 1.55;font-size: 14px;"><b><?php esc_html_e( 'Booking ID', 'wp-travel' ) ?>: #{booking_id} ({booking_arrival_date})</b></p>
				</td>
			</tr>			
			<!-- /Payment Starts -->
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Payment Status', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
							{payment_status}
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Payment Mode', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
							{payment_mode}
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b> <?php esc_html_e( 'Trip Price', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
								{currency_symbol} {trip_price}
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="wp-travel-content" style="background: #fff;">
				<td align="left">
					<table class="wp-travel-content-head" align="left" cellspacing="0" cellpadding="0" style="width: 24%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;"><b><?php esc_html_e( 'Payment Amount', 'wp-travel' ) ?></b></td>
						</tr>
					</table>
					<table class="wp-travel-content-info" align="left" cellspacing="0" cellpadding="0" style="width: 76%;">
						<tr style="background: #fff;">
							<td style="font-size: 14px;background: #fff;box-sizing: border-box;margin: 0;padding: 0px 0px 8px 25px;">
								{currency_symbol} {payment_amount}
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<!-- /Payment ends -->
			<tr class="wp-travel-footer" style="background: #fff;">
				<td align="center" style="background: #eaebed;box-sizing: border-box;font-size: 14px;padding: 10px 25px;">
					<p>{sitename} - <?php esc_html_e( 'Powered By', 'wp-travel' ) ?>: <a href="http://wptravel.io/" target="_blank" style="color: #5a418b;text-decoration: none;"><?php esc_html_e( 'WP Travel', 'wp-travel' ) ?>.</a></p>
					
				</td>
			</tr>
		</table><!-- /Wrapper -->
	</body>
	</html>

	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}