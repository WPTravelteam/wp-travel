<?php 

/**
 *Adding the Review Admin Notice
 */

class WP_Travel_Review_Admin_Notice {

    public function __construct() {

        if( !get_option( 'wptravel_review_notice_date' ) ){
            update_option( 'wptravel_review_status', 0 );
        }

        if( !get_option( 'wptravel_review_notice_date' ) ){
            update_option( 'wptravel_review_notice_date', date("Y/m/d") );
        }

		if(  get_option( 'wptravel_review_status' ) == 0 && get_option( 'wptravel_review_notice_date' ) <= date("Y/m/d") ){
            add_action( 'admin_notices', array( $this, 'wptravel_review_admin_notice' ) );
        }

        add_action( 'wp_ajax_wptravel_review_later', array( $this, 'wptravel_review_later' ) );
		add_action( 'wp_ajax_nopriv_wptravel_review_later', array( $this, 'wptravel_review_later' ) );

        add_action( 'wp_ajax_wptravel_gave_review_already', array( $this, 'wptravel_gave_review_already' ) );
		add_action( 'wp_ajax_nopriv_wptravel_gave_review_already', array( $this, 'wptravel_gave_review_already' ) );

        add_action( 'wp_ajax_wptravel_review_now', array( $this, 'wptravel_review_now' ) );
		add_action( 'wp_ajax_nopriv_wptravel_review_now', array( $this, 'wptravel_review_now' ) );
	}  
    
    public function wptravel_review_admin_notice() {
        echo '<div id="wp-travel-review-notice" class="notice notice-warning is-dismissible">
              <h3>Are you enjoying WP Travel?</h3>
              <p>Hey there! It’s been a while since you used the WP Travel plugin. We trust that our plugin has been beneficial in building your travel booking website. If you have a moment to spare, we would greatly appreciate your feedback on WP Travel by leaving a rating. Thank you very much!</p>
              <p>- WP Travel Team</p>
              <div class="wp-travel-review-action-section" style="margin:20px 0px;display: flex;justify-content: space-between;">
                <div>
                    <a id="wptravel-review-now" href="https://wordpress.org/plugins/wp-travel/#reviews" target="_blank">Yes, you deserve it &#9733; &#9733; &#9733; &#9733; &#9733;</a>
                    <a id="wptravel-review-later" href="#" style="margin-right: 20px;">Remind me later</a>
                    <a id="wptravel-review-already" href="#">I already review</a>
                </div>
                <div>
                    <a href="https://www.facebook.com/wptravel.io" target="_blank" style="text-decoration:none;margin-right: 20px;"><i class="fab fa-facebook"></i> Join our community</a>
                    <a href="https://wptravel.io/docs-category/faq/" target="_blank" style="text-decoration:none;margin-right: 20px;"><i class="fas fa-question-circle"></i> Got Question?</a>
                    <a href="https://wptravel.io/wp-travel-documentations/" target="_blank" style="text-decoration:none"><i class="fas fa-print"></i> Documentation</a>
                </div>
              </div>
              <style>
                #wptravel-review-now{
                    background:rgba(0,0,0,.3);
                    color:#fff;
                    text-decoration:none;
                    padding:10px 20px;
                    border-radius:2px;
                    margin-right: 20px;
                }
                #wptravel-review-now:hover{
                    background: #135e96;
                }
              </style>
              </div>'; 
    }

    public function wptravel_review_later(){

        $user = wp_get_current_user();
		$allowed_roles = array( 'editor', 'administrator', 'author' );

		if ( !array_intersect( $allowed_roles, $user->roles ) ) {
			return wp_send_json( array( 'result' => 'Authentication error' ) );
		}

		$permission = WP_Travel::verify_nonce();

		if ( ! $permission || is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		}

        update_option( 'wptravel_review_notice_date', date('Y/m/d', strtotime( date('Y/m/d') . ' + 3 days') ) );
        header("Refresh:0");
    }

    public function wptravel_gave_review_already(){ 
        update_option( 'wptravel_review_status', 1 );
        header("Refresh:0");
    }

    public function wptravel_review_now(){ 
        update_option( 'wptravel_review_status', 1 );

        $to = 'wptravel.io@gmail.com';
        $from = get_option( 'admin_email' );
        $subject = 'Interested to leave a review';
        $email   = new WP_Travel_Emails();
        $headers = $email->email_headers( $from, $to );
        $username = wp_get_current_user()->data->user_login;
        $body    = "Hey WP Travel Team,<br><br>Congrats, I want to let you know that,Mr/Mrs. {$username} has already reviewed your WP Travel WordPress plugin.<br><br>Thank You";
        wp_mail( $to, $subject, $body, $headers );
        header("Refresh:0");
    }
}

new WP_Travel_Review_Admin_Notice();



