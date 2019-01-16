<?php
/**
 * Handle WP Travel Email Templates
 *
 * @package WP Travel
 */

/**
 * WP Travel email templates class.
 */
class WP_Travel_Emails {

	/**
	 * Constructor.
	 */
	function __construct() {

	}

	/**
	 * Email Content Type headers.
	 */
	public function email_headers( $from, $replyTo ) {

		// To send HTML mail, the Content-type header must be set.
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

		if ( $from ) :
			// Create email headers.
			$headers .= 'From: ' . $from . "\r\n";
		endif;
		if ( $replyTo ) :
			$headers .= 'Reply-To: ' . $replyTo . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
		endif;

		return $headers;
	}
	/**
	 * Email Styles
	 * Email Static CSS
	 */
	public function wp_travel_email_styles() {

		$styles = '<style type="text/css">
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
		#newBookingDetails thead th {
			padding: 15px 10px;
		}
		#newBookingDetails thead > tr > th {
			border: 1px solid #e1e1e1;
			background: #f0f0f0;
		}
		
		#newBookingDetails tbody td {
			padding: 15px 10px;
		}
		#newBookingDetails tbody > tr > td {
			border: 1px solid #f0f0f0;
		}
		@media screen and ( max-width:600px ){
			table[class="wp-travel-wrapper"] {width: 100%!important}
		}
		@media screen and ( max-width:480px ){
			table[class="wp-travel-content-head"],
			table[class="wp-travel-content-info"] {width: 100%!important;}
			table[class="wp-travel-content-info"]{margin-bottom: 10px}

		}
		</style>';

		return $styles;

	}
	/**
	 * WP Travel Get Email Template
	 *
	 * @param string $type booking | payment | enquiry.
	 * @param string $sent_to admin | client.
	 * @return HTML
	 */
	public function wp_travel_get_email_template( $type, $sent_to ) {

		if ( '' === $type || '' === $sent_to ) {
			return;
		}

		$email_template = array();

		$settings = wp_travel_get_settings();

		switch ( $type ) {

			case 'bookings':
				if ( 'admin' == $sent_to ) {
					// Set Headings.
					$header_details = array(
						'header_title' => isset( $settings['booking_admin_template_settings']['admin_title'] ) && '' !== $settings['booking_admin_template_settings']['admin_title'] ? $settings['booking_admin_template_settings']['admin_title'] : __( 'New Booking', 'wp-travel' ),
						'header_color' => isset( $settings['booking_admin_template_settings']['admin_header_color'] ) && '' !== $settings['booking_admin_template_settings']['admin_header_color'] ? $settings['booking_admin_template_settings']['admin_header_color'] : '#dd402e',
					);

					$email_template['subject'] = isset( $settings['booking_admin_template_settings']['admin_subject'] ) && '' !== $settings['booking_admin_template_settings']['admin_subject'] ? $settings['booking_admin_template_settings']['admin_subject'] : __( 'New Booking', 'wp-travel' );
					// Set Contents.
					$email_content = isset( $settings['booking_admin_template_settings']['email_content'] ) && '' !== $settings['booking_admin_template_settings']['email_content'] ? $settings['booking_admin_template_settings']['email_content'] : wp_travel_booking_admin_default_email_content();

				} elseif ( 'client' == $sent_to ) {
					// Set Headings.
					$header_details = array(
						'header_title' => isset( $settings['booking_client_template_settings']['client_title'] ) && '' !== $settings['booking_client_template_settings']['client_title'] ? $settings['booking_client_template_settings']['client_title'] : __( 'Booking Confirmed', 'wp-travel' ),
						'header_color' => isset( $settings['booking_client_template_settings']['client_header_color'] ) && '' !== $settings['booking_client_template_settings']['client_header_color'] ? $settings['booking_client_template_settings']['client_header_color'] : '#dd402e',
					);

					$email_template['subject'] = isset( $settings['booking_client_template_settings']['client_subject'] ) && '' !== $settings['booking_client_template_settings']['client_subject'] ? $settings['booking_client_template_settings']['client_subject'] : __( 'Thank you for your booking.', 'wp-travel' );
					// Set Contents.
					$email_content = isset( $settings['booking_client_template_settings']['email_content'] ) && '' !== $settings['booking_client_template_settings']['email_content'] ? $settings['booking_client_template_settings']['email_content'] : wp_travel_booking_client_default_email_content();
				}
				break;

			case 'payments':
				if ( 'admin' == $sent_to ) {
					// Set Headings.
					$header_details = array(
						'header_title' => isset( $settings['payment_admin_template_settings']['admin_title'] ) && '' !== $settings['payment_admin_template_settings']['admin_title'] ? $settings['payment_admin_template_settings']['admin_title'] : __( 'New Booking', 'wp-travel' ),
						'header_color' => isset( $settings['payment_admin_template_settings']['admin_header_color'] ) && '' !== $settings['payment_admin_template_settings']['admin_header_color'] ? $settings['payment_admin_template_settings']['admin_header_color'] : '#dd402e',
					);

					$email_template['subject'] = isset( $settings['payment_admin_template_settings']['admin_subject'] ) && '' !== $settings['payment_admin_template_settings']['admin_subject'] ? $settings['payment_admin_template_settings']['admin_subject'] : __( 'New Booking', 'wp-travel' );
					// Set Contents.
					$email_content = isset( $settings['payment_admin_template_settings']['email_content'] ) && '' !== $settings['payment_admin_template_settings']['email_content'] ? $settings['payment_admin_template_settings']['email_content'] : wp_travel_payment_admin_default_email_content();

				} elseif ( 'client' == $sent_to ) {
					// Set Headings.
					$header_details = array(
						'header_title' => isset( $settings['payment_client_template_settings']['client_title'] ) && '' !== $settings['payment_client_template_settings']['client_title'] ? $settings['payment_client_template_settings']['client_title'] : __( 'Booking Confirmed', 'wp-travel' ),
						'header_color' => isset( $settings['payment_client_template_settings']['client_header_color'] ) && '' !== $settings['payment_client_template_settings']['client_header_color'] ? $settings['payment_client_template_settings']['client_header_color'] : '#dd402e',
					);

					$email_template['subject'] = isset( $settings['payment_client_template_settings']['client_subject'] ) && '' !== $settings['payment_client_template_settings']['client_subject'] ? $settings['payment_client_template_settings']['client_subject'] : __( 'Thank you for your booking.', 'wp-travel' );
					// Set Contents.
					$email_content = isset( $settings['payment_client_template_settings']['email_content'] ) && '' !== $settings['payment_client_template_settings']['email_content'] ? $settings['payment_client_template_settings']['email_content'] : wp_travel_payment_client_default_email_content();
				}
				break;

			case 'enquiry':
				if ( 'admin' == $sent_to ) {
					// Set Headings.
					$header_details = array(
						'header_title' => isset( $settings['enquiry_admin_template_settings']['admin_title'] ) && '' !== $settings['enquiry_admin_template_settings']['admin_title'] ? $settings['enquiry_admin_template_settings']['admin_title'] : __( 'New Enquiry', 'wp-travel' ),
						'header_color' => isset( $settings['enquiry_admin_template_settings']['admin_header_color'] ) && '' !== $settings['enquiry_admin_template_settings']['admin_header_color'] ? $settings['enquiry_admin_template_settings']['admin_header_color'] : '#dd402e',
					);

					$email_template['subject'] = isset( $settings['enquiry_admin_template_settings']['admin_subject'] ) && '' !== $settings['enquiry_admin_template_settings']['admin_subject'] ? $settings['enquiry_admin_template_settings']['admin_subject'] : __( 'New Enquiry', 'wp-travel' );
					// Set Contents.
					$email_content = isset( $settings['enquiry_admin_template_settings']['email_content'] ) && '' !== $settings['enquiry_admin_template_settings']['email_content'] ? $settings['enquiry_admin_template_settings']['email_content'] : wp_travel_enquiries_admin_default_email_content();
				}
				break;

			// @since 1.8.0
			case $type:
				$email_data = array(
					'header_details' => array(
						'header_title' => '',
						'header_color' => '',
					),
					'subject'        => '',
					'email_content'  => '',
				);
				$email_data = apply_filters( 'wp_travel_email_template_type', $email_data, $type, $sent_to );
				$header_details            = $email_data['header_details'];
				$email_template['subject'] = $email_data['subject'];
				$email_content             = $email_data['email_content'];
				break;
		}

		$email_template['mail_header']  = $this->wp_travel_email_heading( $sent_to, $header_details );
		$email_template['mail_content'] = $email_content;
		$email_template['mail_footer']  = $this->wp_travel_email_footer();

		return $email_template;

	}
	/**
	 * Email Header
	 *
	 * @param string $sent_to admin | client
	 * @param array  $details data
	 * @return HTML
	 */
	public function wp_travel_email_heading( $sent_to, $details = array() ) {
		ob_start(); ?><!DOCTYPE html>
		<html>
		<head>
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<title><?php sprintf( 'TO %s', strtoupper( $sent_to ) ); ?></title>
			<?php echo $this->wp_travel_email_styles(); ?>
		</head>
		<body style="background: #fcfcfc;color: #5d5d5d;margin: 0;padding: 0;">
			<!-- Wrapper -->
			<table class="wp-travel-wrapper" width="100%" cellpadding="0" cellspacing="0" style="color: #5d5d5d;font-family: Roboto, sans-serif;margin: auto;"> 
				<tr class="wp-travel-header">			
					<td colspan="2" align="left" style="background: <?php echo esc_attr( $details['header_color'] ); ?>;box-sizing: border-box;margin: 0;padding: 20px 25px;"> <!-- Header -->
						<h2 style="color: #fcfffd;font-size: 20px;margin: 0;padding: 0;text-align: center;"><?php echo esc_html( $details['header_title'] ); ?></h2>
					</td> <!-- /Header -->
				</tr>
			</table>    
		<?php
		$email_header = ob_get_clean();
		return $email_header;
	}

	/**
	 * Email Footer
	 *
	 * @return HTML Email Footer Template.
	 */
	public function wp_travel_email_footer() {

		ob_start();
		$disable_powerby = apply_filters( 'wp_travel_disable_email_template_poweredby', false );

		if ( ! $disable_powerby ) {
			?>
			<table class="wp-travel-wrapper" width="100%" cellpadding="0" cellspacing="0" style="color: #5d5d5d;font-family: Roboto, sans-serif;margin: auto;">
				<tr class="wp-travel-footer" style="background: #fff;">
					<td colspan="2" align="center" style="background: #eaebed;box-sizing: border-box;font-size: 14px;padding: 20px 25px;">
						<p><?php echo apply_filters( 'wp_travel_email_template_footer_text', sprintf( __( 'Powered By: %1$1sWP Travel.%2$2s', 'wp-travel' ), '<a href="http://wptravel.io/" target="_blank" style="color: #5a418b;text-decoration: none;">', '</a>' ) ); ?>.</p>
					</td>
				</tr>
			</table><!-- /Wrapper -->
		<?php } ?>
			</body>
			</html>
		<?php
		$email_footer = ob_get_clean();
		return $email_footer;
	}
}
