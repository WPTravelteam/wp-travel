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
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        
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
    public function wp_travel_email_styles(){

        echo '<style type="text/css">
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
    </style>';

    }
    /**
     * get default email content.
     * @param string $type Email type 
     * @param string $sentTo Sent To 
     * @return HTML
     */
    public function wp_travel_get_default_email_content( $type, $sentTo ){

        ob_start();

            wp_travel_get_template_part('email-templates/' . $type . '-email', $sentTo );

        $template = ob_get_clean();

        return $template;


    }
    /**
     * WP Travel Get Email Template
     * @param string $type booking | payment | enquiry
     * @param string $sentTo admin | client
     * @return HTML
     */
    public function wp_travel_get_email_template( $type, $sentTo ){

        if ( '' === $type || '' === $sentTo ){
            return;
        }
        
        $settings = wp_travel_get_settings();
        
        switch( $type ){
            
            case 'bookings' :
            
            if ( $sentTo == 'admin' ) {
                
                //Set Headings.
                $header_details = array(
                    'header_title' => isset( $settings['booking_admin_template_settings']['admin_title'] ) ? $settings['booking_admin_template_settings']['admin_title'] :  __( 'New Booking', 'wp-travel' ),
                    'header_color' => isset( $settings['booking_admin_template_settings']['admin_header_color'] ) ? $settings['booking_admin_template_settings']['admin_header_color'] : '#dd402e',
                    
                );
                //Set Contents.
                $email_content = isset( $settings['booking_admin_template_settings']['email_content'] ) ? $settings['booking_admin_template_settings']['email_content'] : $this->wp_travel_get_default_email_content( 'booking', 'admin' );
    
                }
                elseif ( $sentTo == 'client' ){
    
    
                }
            break;
    
        }

        $email_template = $this->wp_travel_email_heading( $sentTo , $header_details );

        $email_template .= $email_content; 

        $email_template .= $this->wp_travel_email_footer();

        return $email_template;


    }
    /**
     * Email Header
     * @param string $sentTo admin | client
     * @param array $details data
     * @return HTML
     */
    public function wp_travel_email_heading( $sentTo, $details = array() ){

        ob_start();
    ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php sprintf( 'TO %s', strtoupper( $sentTo ) ); ?></title>
            <?php $this->wp_travel_email_styles(); ?>
        </head>
        <body style="background: #fcfcfc;color: #5d5d5d;margin: 0;padding: 0;">
            <!-- Wrapper -->
            <table class="wp-travel-wrapper" width="600" cellpadding="0" cellspacing="0" style="color: #5d5d5d;font-family: Roboto, sans-serif;margin: auto;"> 
                <tr class="wp-travel-header" style="background: #fff;">			
                    <td align="left" style="background: <?php echo esc_attr( $details['header_color'] ); ?>;box-sizing: border-box;margin: 0;padding: 20px 25px;"> <!-- Header -->
                        <h2 style="color: #fcfffd;font-size: 20px;margin: 0;padding: 0;text-align: center;"><?php echo esc_html( $details['header_title'] ); ?></h2>
                    </td> <!-- /Header -->
                </tr>
            
    <?php 

    $email_header = ob_get_clean();

    return $email_header;

    }
    
    /**
     * Email Footer 
     * 
     * @return HTML Email Footer Template.
     */
    public function wp_travel_email_footer(){
    
    ob_start();

    ?>
        <tr class="wp-travel-footer" style="background: #fff;">
                    <td align="center" style="background: #eaebed;box-sizing: border-box;font-size: 14px;padding: 10px 25px;">
                        <p>{sitename} - <?php esc_html_e( 'Powered By', 'wp-travel' ) ?>: <a href="http://wptravel.io/" target="_blank" style="color: #5a418b;text-decoration: none;"><?php esc_html_e( 'WP Travel', 'wp-travel' ) ?>.</a></p>
                        
                    </td>
                </tr>
            </table><!-- /Wrapper -->
        </body>
        </html>
    <?php 

    $email_footer = ob_get_clean();

    return $email_footer;

    }
}
