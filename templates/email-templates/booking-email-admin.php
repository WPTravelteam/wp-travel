<?php 
/**
 * Booking Email template
 * @package WP_Travel
 */
?>
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
					<a href="{booking_edit_link}" class="wp-travel-veiw-more" target="_blank" style="color: #fcfffd;text-decoration: none;background: #dd402e;border-radius: 3px;display: block;font-size: 14px;margin: 20px auto;padding: 10px 20px;text-align: center;width: 130px;"><?php esc_html_e( 'View details on site', 'wp-travel' ) ?></a>
				</td>
			</tr>
