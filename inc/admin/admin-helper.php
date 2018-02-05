<?php
/**
 * Admin Helper
 *
 * @package inc/admin/
 */

/**
 * All Admin Init hooks listed here.
 *
 * @since 1.0.7
 */
function wp_travel_admin_init() {
	add_action( 'wp_trash_post', 'wp_travel_clear_booking_count_transient', 10 ); // @since 1.0.7
	if ( version_compare( WP_TRAVEL_VERSION, '1.0.6', '>' ) ) {
		wp_travel_upgrade_to_110();
	}
	if ( version_compare( WP_TRAVEL_VERSION, '1.2.0', '>' )) {
		include_once sprintf( '%s/upgrade/update-121.php', WP_TRAVEL_ABSPATH );
	}
}

function wp_travel_admin_footer_styles() {
	global $_wp_admin_css_colors;
	$current_color_scheme = get_user_option( 'admin_color' );
	$active_color_code = $_wp_admin_css_colors[$current_color_scheme]->colors[3];
	?>
	<style>
		#menu-posts-<?php echo WP_TRAVEL_POST_TYPE; ?> ul li:last-child a{
			color: <?php echo $active_color_code; ?>!important;
		}
	</style>
	<?php
}

add_action( 'admin_footer', 'wp_travel_admin_footer_styles' );

function wp_travel_marketplace_page() {

	?>
	<div class="wrap">
		<div id="poststuff">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Marketplace', 'wp-travel' ) ?></h1>
			<div id="post-body">
				<div class="wp-travel-marketplace-tab-wrap">
					<ul>
						<li class=""><a href="#tabs-1"><?php esc_html_e( 'Addons', 'wp-travel' ) ?></a></li>
						<li class=""><a href="#tabs-2"><?php esc_html_e( 'Themes', 'wp-travel' ) ?></a></li>
					</ul>
					<div id="tabs-1" class="tab-pannel">
						<div class="marketplace-module clearfix">
							<div class="single-module">
								<div class="single-module-image">
									<a href="http://wptravel.io/downloads/standard-paypal/" target="_blank">
									<img width="423" height="237" src="<?php echo plugins_url( '/wp-travel/assets/images/paypal-addons.png' ) ?>" class="" alt="">
									</a>
								</div>
								<div class="single-module-content clearfix">
									<h4 class="text-title"><a href="http://wptravel.io/downloads/standard-paypal/" target="_blank"><span class="icon-logo"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span> WP Travel Standard PayPal</a></h4>
									<a class="btn-default pull-left" href="http://wptravel.io/downloads/standard-paypal/" target="_blank">View Detail</a>
								</div>
							</div>
						</div>
					</div>
					<div id="tabs-2" class="tab-pannel">
						<div class="marketplace-module clearfix">
							<div class="single-module">
								<div class="single-module-image">
									<a href="http://wensolutions.com/themes/travel-log/" target="_blank">
									<img width="423" height="237" src="<?php echo plugins_url( '/wp-travel/assets/images/devices_web.png' ) ?>" class="" alt="" >
									</a>
								</div>
								<div class="single-module-content clearfix">
									<h4 class="text-title"><a href="http://wensolutions.com/themes/travel-log/" target="_blank"><span class="icon-logo"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span> Travel Log</a></h4>
									<a class="btn-default pull-left" href="http://wensolutions.com/themes/travel-log/" target="_blank">View Detail</a>
									<a class="btn-default pull-right" href="https://downloads.wordpress.org/theme/travel-log.zip" target="_blank">Download</a>
								</div>
							</div>
						</div>
					</div>
				</div>


				<div id="aside-wrap" class="single-module-side">

		<div id="wp_travel_support_block_id" class="postbox ">
			<button type="button" class="handlediv" aria-expanded="true">
				<span class="screen-reader-text">Toggle panel: Support</span>
				<span class="toggle-indicator-acc" aria-hidden="true"></span>
			</button>
			<h2 class="hndle ui-sortable-handle">
				<span>Support</span>
			</h2>
			<div class="inside">

		       <div class="thumbnail">
		            <img src="<?php echo plugins_url( '/wp-travel/assets/images/support-image.png' ) ?>">
		             <p class="text-justify">Click Below for support. </p>
		             <p class="text-center"><a href="http://wptravel.io/support/" target="_blank" class="button button-primary">Get Support Here</a></p>
		       </div>

			</div>
		</div>

		<div id="wp_travel_doc_block_id" class="postbox ">
			<button type="button" class="handlediv" aria-expanded="true">
				<span class="screen-reader-text">Toggle panel: Documentation</span>
				<span class="toggle-indicator" aria-hidden="true"></span>
			</button>
			<h2 class="hndle ui-sortable-handle">
				<span>Documentation</span>
			</h2>
			<div class="inside">

		       <div class="thumbnail">
		            <img src="<?php echo plugins_url( '/wp-travel/assets/images/docico.png' ) ?>">
		             <p class="text-justify">Click Below for our full Documentation about logo slider. </p>
		             <p class="text-center"><a href="http://wptravel.io/documentations/" target="_blank" class="button button-primary">Get Documentation Here</a></p>
		       </div>

			</div>
		</div>

		<div id="wp_travel_review_block_id" class="postbox ">
			<button type="button" class="handlediv" aria-expanded="true">
				<span class="screen-reader-text">Toggle panel: Reviews</span>
				<span class="toggle-indicator" aria-hidden="true"></span>
			</button>
			<h2 class="hndle ui-sortable-handle">
				<span>Reviews</span>
			</h2>
			<div class="inside">

				<div class="thumbnail">
					<p class="text-center">
						<i class="dashicons dashicons-star-filled" aria-hidden="true"></i>
						<i class="dashicons dashicons-star-filled" aria-hidden="true"></i>
						<i class="dashicons dashicons-star-filled" aria-hidden="true"></i>
						<i class="dashicons dashicons-star-filled" aria-hidden="true"></i>
						<i class="dashicons dashicons-star-filled" aria-hidden="true"></i>
					</p>
					<h5>"The plugin is very intuitive and fresh.
The layout fits well into theme with flexibility to different shortcodes.
Its great plugin for travel or tour agent websites."</h5>
					<span class="by"><strong> <a href="https://profiles.wordpress.org/muzdat" target="_blank">muzdat</a></strong></span>

				</div>
				<div class="thumbnail last">
					<h5>"Please fill free to leave us a review, if you found this plugin helpful."</h5>
					<p class="text-center"><a href="https://wordpress.org/plugins/wp-travel/#reviews" target="_blank" class="button button-primary">Leave a Review</a></p>
				</div>
			</div>
		</div>


	</div>


</div>
</div>
</div>
	<?php
}


function docs_support_page_callback(){ 
	?>

	<div class="wrap">
		<div id="poststuff">
			<div id="docs-and-support-page">
				<div class="about-header">
	                <div class="about-text WP-Travel-about-text">
		                <div class="left-side-section">
		                	<strong>Welcome to WP Travel.</strong>
		                		<p>Thanks for installing and we hope you will enjoy using WP Travel. </p>
		                		<p>We hardly recomend you to install <a class="link-simple" href="" target="_blank">Travel Log</a> theme for best Front End experiences.</p>
		                        <p class="WP-Travel-actions">
		                        <a class="button button-primary button-large" href="#add_new_trips_links" target="_blank">Add New Trips For You Site</a>
		                        <span>OR</span>
		                        <a href="wptravel.io/demo" class="link-simple" target="_blank"> <strong>Visit Demo</strong></a>
		                    </p>
	                    </div>
	                    <div class="WP-Travel-badge">
		                	<span class="icon-logo"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span>
		                	<p>
		                  		 Version: 1.2.1                
		                	</p>
		                </div>
	                </div>
	                
	                <div class="wrap-footer">
	                    <table class="form-table">
			                 <tbody>
			                 <tr>
			                    <th scope="row">Get add-ons and tips...</th>
				                    <td>
				                        <form name="klawoo_subscribe" action="#" method="POST" accept-charset="utf-8">
				                            <input class="regular-text ltr" type="text" name="email" id="email" placeholder="Email">
				                            <input type="hidden" name="list" value="7I763v6Ldrs3YhJeee5EOgFA">
				                            <input type="submit" name="submit" id="submit" class="button button-primary" value="Subscribe">
				                            <br>
				                            <div id="klawoo_response"></div>
				                        </form>
				                    </td>
				                 </tr>
				            </tbody>
			            </table>
			            <div class="WP-Travel-support">
		                    Questions? Need Help?                    
			                <div id="WP-Travel-contact-us" class="WP-Travel-contact-us">
			                	<a class="thickbox" href="http://wptravel.io/contact/" target="_blank">Contact Us</a>
			                </div>
		                </div>
			        </div>
	        	</div>

				<div class="feature-section col two-col">
		<div class="col">
			<h3>Description</h3>
			<p class="wp-travel-summary">
				WP Travel is an easy to use and awesome plugin that you can use with any travel site. With WP travel you can simply add the post type to display the packages and WP Travel comes with booking feature as well.  Beside this  plugin provides various kind of feature, setting which makes this plugin more attractive.

				The compatibility of the plugin is also one of the features. It can also be modified very easily through custom templates.
			</p>
			<h3>Feature Overview</h3>
			<ul class="wp-travel-feature_list">
				<li class="wp-travel-feature">
					Get your travel site ready just on few clicks. With our user-friendly system &amp; complete documentation, you won't have any trouble while using the system.				</li>
				<li class="wp-travel-feature">
					WP Travel includes in-build booking system for your travel site. Users can easily book itineraries from your site and you can track all bookings from the backend.				</li>
				<li class="wp-travel-feature">
					Data are very important for all business. WP Travel has in-build booking stat that helps you to generate the report from different date range, types and locations.				</li>
				<li class="wp-travel-feature">
					With our payment processing features, you can get partial or full payment for each booking. All that payment will be tracked in the backend and also you can view stat of payments.			</li>
				<li class="wp-travel-feature">
					WP travel plugin is translation ready in order to fulfill customer's needs from all around the world. You can translate WP Travel to any language with the help of WPML Translation Plugin and for the translation of the string, you can use Loco Translate.			</li>
				<li class="wp-travel-feature">
					FAQs provide the opportunity to group all those questions that customers ask over and over again related to trips. Also, the itinerary timeline is the new feature added to WP travel plugin which will display the timeline of the trips in tree-like structure.			</li>
				<li class="wp-travel-feature">
					Our team is dedicated to continuous development of the plugin. We will be continuously adding new features to the plugin.				</li>
				<li class="wp-travel-feature">
					If you found any issues in the plugin, you can directly contact us or add your issues or problems on support forum.			</li>
			</ul>
		</div>

		<div class="col last-feature">
			<div class="es-form-setup">
				<h3>Add Trip</h3>
				<p class="wp-travel-faq">
					<a href="http://wptravel.io/documentations/user-documentation/how-to-create-your-very-first-trip/" target="_blank">How to Add Trip to the website?</a>				</p>
				<p>
					After the activation of the required plugin, you will find the Trips menu on the dashboard.
					Now  to create the trip of your choice go to Admin Panel > Trips > Add New and begin entering the required content as per your wish.
					Furthermore, to help you and make you clear we have explained each and every field below in the Overview section.
				</p>
				<h2> Additional Trip settings</h2>
				<ul class="wp-travel-faq_list">
					<li class="wp-travel-faq">
						<a href="#" target="_blank">How to Globally Set the Tabs Format Shown on Single Trip Page?</a>
					</li>
					<li class="wp-travel-faq">
						<a href="#" target="_blank">How to change the label of tabs shown on Single Trip Page?</a>
					</li>
					<li class="wp-travel-faq">
						See all the Frequently Asked Question Solution For WP Travel Plugin
						<a href="http://wptravel.io/faq/" target="_blank">Frequently Asked Question</a>
					</li>
					<li class="wp-travel-faq">
					You can find your solution about the problem of WP Travel from our Support page or you can create a support for free. Feel free to ask a question about the problem, this will eventually help the growth of the plugin furthermore.
						<a href="http://wptravel.io/support-forum/" target="_blank">Support Forum</a>
					</li>
				</ul>
			</div>
			<br>
			<div class="es-setting">
				<h2>General Plugin Configuration</h2>
				<ul class="wp-travel-faq_list">
					<li class="wp-travel-faq">
						 <a href="http://wptravel.io/documentations/user-documentation/" target="_blank">
						 	See User Documentation to know the plugin and how it works
						 </a>
				
						</li>

						<li class="wp-travel-faq">
						 <a href="http://wptravel.io/documentations/developer-documentation/" target="_blank">
						 	See Developer Documentation to know the plugin in depth.
						 </a>
				
						</li>
				</ul>
			</div>
		</div>
	</div>










			</div>
		</div>
	</div>
	<div id="wpfooter" role="contentinfo">
		<p id="footer-left" class="alignleft">
		If you like <strong>WP Travel</strong>, please consider leaving us a <a target="_blank" href="https://wordpress.org/support/plugin/wp-travel/reviews/">★★★★★</a> rating. A huge thank you from WEN Solutions in advance!	</p>
		<p id="footer-upgrade" class="alignright">
			WP Travel version: <strong>1.2.1</strong>	
			</p>
	</div>


<?php

}



function wp_travel_clear_booking_count_transient( $post_id ) {
	if ( ! $post_id ) {
		return;
	}
	global $post_type;
	if ( 'itinerary-booking' !== $post_type ) {
		return;
	}
	$itinerary_id = get_post_meta( $post_id, 'wp_travel_post_id', true );
	delete_site_transient( "_transient_wt_booking_count_{$itinerary_id}" );
}

function wp_travel_get_booking_count( $itinerary_id ) {
	if ( ! $itinerary_id ) {
		return 0;
	}
	global $wpdb;
	// delete_site_transient( "_transient_wt_booking_count_{$itinerary_id}" );
	$booking_count = get_site_transient( "_transient_wt_booking_count_{$itinerary_id}" );
	// error_log( 'booking  count ' . $booking_count . ' itinerary id ' . $itinerary_id );
	if ( ! $booking_count ) {
		// error_log( 'no count ' . $itinerary_id );
		$booking_count = 0;
		$query = "SELECT count( itinerary_id ) as booking_count FROM {$wpdb->posts} P
		JOIN ( Select distinct( post_id ), meta_value as itinerary_id from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_post_id' and meta_value > 0 ) I on P.ID = I.post_id  where post_type='itinerary-booking' and post_status='publish' and itinerary_id={$itinerary_id} group by itinerary_id";
		$results = $wpdb->get_row( $query );
		if ( $results ) {
			$booking_count = $results->booking_count;
		}
		set_site_transient( "_transient_wt_booking_count_{$itinerary_id}", $booking_count );

		// Post meta only for sorting.
		update_post_meta( $itinerary_id, 'wp_travel_booking_count', $booking_count );
	}
	return $booking_count;
}

/*
 * ADMIN COLUMN - HEADERS
 */
add_filter( 'manage_edit-' . WP_TRAVEL_POST_TYPE . '_columns', 'wp_travel_itineraries_columns' );

/**
 * Customize Admin column.
 *
 * @param  Array $booking_columns List of columns.
 * @return Array                  [description]
 */
function wp_travel_itineraries_columns( $itinerary_columns ) {
	$comment = isset( $itinerary_columns['comments'] ) ?  $itinerary_columns['comments'] : '';
	$date = $itinerary_columns['date'];
	unset( $itinerary_columns['date'] );
	unset( $itinerary_columns['comments'] );

	$itinerary_columns['booking_count'] = __( 'Booking', 'wp-travel' );
	$itinerary_columns['featured'] = __( 'Featured', 'wp-travel' );
	if ( $comment ) {
		$itinerary_columns['comments'] = $comment;
	}
	$itinerary_columns['date'] = __( 'Date', 'wp-travel' );
	return $itinerary_columns;
}

/*
 * ADMIN COLUMN - CONTENT
 */
add_action( 'manage_' . WP_TRAVEL_POST_TYPE . '_posts_custom_column', 'wp_travel_itineraries_manage_columns', 10, 2 );

/**
 * Add data to custom column.
 *
 * @param  String $column_name Custom column name.
 * @param  int 	  $id          Post ID.
 */
function wp_travel_itineraries_manage_columns( $column_name, $id ) {
	switch ( $column_name ) {
		case 'booking_count':
			$booking_count = wp_travel_get_booking_count( $id );
			echo esc_html( $booking_count );
			break;
		case 'featured':
			$featured = get_post_meta( $id, 'wp_travel_featured', true );
			$featured = ( isset( $featured ) && '' != $featured ) ? $featured : 'no';

			$icon_class = ' dashicons-star-empty ';
			if ( ! empty( $featured ) && 'yes' === $featured ) {
				$icon_class = ' dashicons-star-filled ';
			}
			$nonce = wp_create_nonce( 'wp_travel_featured_nounce' );
			printf( '<a href="#" class="wp-travel-featured-post dashicons %s" data-post-id="%d"  data-nonce="%s"></a>', $icon_class, $id, $nonce );
			break;
		default:
			break;
	} // end switch
}

/*
 * ADMIN COLUMN - SORTING - MAKE HEADERS SORTABLE
 * https://gist.github.com/906872
 */
add_filter( 'manage_edit-' . WP_TRAVEL_POST_TYPE . '_sortable_columns', 'wp_travel_itineraries_sort' );
function wp_travel_itineraries_sort( $columns ) {

	$custom = array(
		'booking_count' 	=> 'booking_count',
	);
	return wp_parse_args( $custom, $columns );
}

/*
 * ADMIN COLUMN - SORTING - ORDERBY
 * http://scribu.net/wordpress/custom-sortable-columns.html#comment-4732
 */
add_filter( 'request', 'wp_travel_itineraries_column_orderby' );

/**
 * Manage Order By custom column.
 *
 * @param  Array $vars Order By array.
 * @return Array       Order By array.
 */
function wp_travel_itineraries_column_orderby( $vars ) {
	if ( isset( $vars['orderby'] ) && 'booking_count' == $vars['orderby'] ) {
		$vars = array_merge( $vars, array(
			'meta_key' => 'wp_travel_booking_count',
			'orderby' => 'meta_value',
		) );
	}
	return $vars;
}



/**
 * Ajax for adding feature aditem.
 */
function wp_travel_featured_admin_ajax() {
	if ( ! wp_verify_nonce( $_POST['nonce'], 'wp_travel_featured_nounce' ) ) {
		exit( 'invalid' );
	}

	header( 'Content-Type: application/json' );
	$post_id = intval( $_POST['post_id'] );
	$featured_status = esc_attr( get_post_meta( $post_id, 'wp_travel_featured', true ) );
	$new_status = $featured_status == 'yes' ? 'no' : 'yes';
	update_post_meta( $post_id, 'wp_travel_featured', $new_status );
	echo json_encode( array(
		'ID' => $post_id,
		'new_status' => $new_status,
	) );
	die();
}
add_action( 'wp_ajax_wp_travel_featured_post', 'wp_travel_featured_admin_ajax' );


add_action( 'post_submitbox_misc_actions', 'wp_travel_publish_metabox' );
// add_action( 'save_post', 'save_article_or_box' );
function wp_travel_publish_metabox() {
	global $post;
	if ( get_post_type( $post ) === 'itinerary-booking' ) {
	?>
		<div class="misc-pub-section misc-pub-booking-status">
			<?php
			$status = wp_travel_get_booking_status();
			$label_key = get_post_meta( $post->ID, 'wp_travel_booking_status', true );
			?>

			<label for="wp-travel-post-id"><?php esc_html_e( 'Booking Status', 'wp-travel' ); ?></label>
			<select id="wp_travel_booking_status" name="wp_travel_booking_status" >
			<?php foreach ( $status as $value => $st ) : ?>
				<option value="<?php echo esc_html( $value ); ?>" <?php selected( $value, $label_key ) ?>>
					<?php echo esc_html( $status[ $value ]['text'] ); ?>
				</option>
			<?php endforeach; ?>
			</select>
		</div>

	<?php
	}
}

add_action('wp_ajax_wp_travel_add_itinerary_content_data', 'wp_travel_add_itinerary_content_data');
add_action('wp_ajax_nopriv_wp_travel_add_itinerary_content_data', 'wp_travel_add_itinerary_content_data');
if ( ! function_exists( 'wp_travel_add_itinerary_content_data' ) ) {
	/**
	 * Admin Itineraries Data Content Tabs Load.
	 * @since 1.1.0
	 * @return Null
 	*/
	function wp_travel_add_itinerary_content_data() {

		$uid = $_POST['itinerary_id'];
		$itinerary_label = __( 'Day X', 'wp-travel' );
		$itinerary_title = __( 'Your Plan', 'wp-travel' );
		// $itinerary_settings = array(
		// 	'quicktags' 	=> array('buttons' => 'em,strong,link'),
		// 	'quicktags' 	=> true,
		// 	'tinymce' 		=> true,
		// 	'textarea_rows'	=> 10,
		// 	'textarea_name' => 'wp_travel_trip_itinerary_data['.$uid.'][desc]',
		// ); ?>
		<div class="panel panel-default">
			<div class="panel-heading" role="tab" id="heading-<?php echo esc_attr( $uid ) ?>">
				<h4 class="panel-title">
					<div class="wp-travel-sorting-handle"></div>
					<a role="button" data-toggle="collapse" data-parent="#accordion-itinerary-data" href="#collapse-<?php echo esc_attr( $uid ) ?>" aria-expanded="true" aria-controls="collapse-<?php echo esc_attr( $uid ) ?>">

					<span bind="itinerary_label_<?php echo esc_attr( $uid ) ?>" class="itinerary-label"><?php echo esc_html( $itinerary_label ); ?></span>, 
					<span bind="itinerary_title_<?php echo esc_attr( $uid ) ?>" class="itinerary-label"><?php echo esc_html( $itinerary_title ); ?></span>
					<span class="collapse-icon"></span>
					</a>
					<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
				</h4>
			</div>
			<div id="collapse-<?php echo esc_attr( $uid ) ?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-<?php echo esc_attr( $uid ) ?>">
			<div class="panel-body">
				<div class="panel-wrap panel-wrap-itinerary">
					<label><?php esc_html_e( 'Label', 'wp-travel' ); ?></label>
					<input bind="itinerary_label_<?php echo esc_attr( $uid ) ?>" type="text" name="wp_travel_trip_itinerary_data[<?php echo $uid; ?>][label]" value="<?php echo esc_html( $itinerary_label ); ?>">
				</div>
				<div class="panel-wrap panel-wrap-itinerary">
					<label><?php esc_html_e( 'Title', 'wp-travel' ); ?></label>
					<input bind="itinerary_title_<?php echo esc_attr( $uid ) ?>" type="text" name="wp_travel_trip_itinerary_data[<?php echo $uid; ?>][title]" value="<?php echo esc_html( $itinerary_title ); ?>">
				</div>
				<div class="panel-wrap panel-wrap-itinerary">
					<label><?php esc_html_e( 'Itinerary Date', 'wp-travel' ); ?></label>
					<input class="wp-travel-datepicker" type="text" name="wp_travel_trip_itinerary_data[<?php echo esc_attr( $uid ) ?>][date]" value="">
				</div>
				<div class="panel-wrap panel-wrap-itinerary">
					<label><?php esc_html_e( 'Itinerary Time', 'wp-travel' ); ?></label>
					<input class="wp-travel-timepicker" type="text" name="wp_travel_trip_itinerary_data[<?php echo esc_attr( $uid ) ?>][time]" value="">
				</div>
				<div class="panel-wrap panel-wrap-itinerary">
					<label><?php esc_html_e( 'Description', 'wp-travel' ); ?></label>
					<div class="wp-travel-itinerary">
						<textarea name="wp_travel_trip_itinerary_data[<?php echo $uid; ?>][desc]" ></textarea>
					</div>
				</div>
				<!-- <div class="wp-travel-itinerary" style="padding:10px"> -->
					<?php // wp_editor( $_POST['default_text'], $_POST['itinerary_id'], $itinerary_settings); ?>
				<!-- </div> -->
			</div>
			</div>
		</div>
		<?php
		exit;
	}
}

function wp_travel_upgrade_to_110() {
	$itineraries = get_posts( array( 'post_type' => 'itineraries', 'post_status' => 'publish' ) );
	$current_db_version = get_option( 'wp_travel_version' );
	if ( ! $current_db_version ) {
		include_once sprintf( '%s/upgrade/106-110.php', WP_TRAVEL_ABSPATH );
	}
	if ( count( $itineraries ) > 0 ) {
		include_once sprintf( '%s/upgrade/106-110.php', WP_TRAVEL_ABSPATH );
	}
}

/**
 * Stat Data for Payment.
 *
 * @param Array $stat_data
 * @return void
 */
// function wp_travel_payment_stat_data( $stat_data, $request ) {
// 	if ( ! $stat_data ) {
// 		return;
// 	}
	
// 	global $wpdb;

// 	// Default variables.
// 	$query_limit = apply_filters( 'wp_travel_stat_default_query_limit', 10 );
// 	$limit = "limit {$query_limit}";
// 	$where = '';
// 	$groupby = '';

// 	$from_date = '';
// 	if ( isset( $request['booking_stat_from'] ) && '' !== $request['booking_stat_from'] ) {
// 		$from_date = $request['booking_stat_from'];
// 	}
// 	$to_date = '';
// 	if ( isset( $request['booking_stat_to'] ) && '' !== $request['booking_stat_to'] ) {
// 		$to_date = $request['booking_stat_to'] . ' 23:59:59';
// 	}
// 	$country = '';
// 	if ( isset( $request['booking_country'] ) && '' !== $request['booking_country'] ) {
// 		$country = $request['booking_country'];
// 	}

// 	$itinerary = '';
// 	if ( isset( $request['booking_itinerary'] ) && '' !== $request['booking_itinerary'] ) {
// 		$itinerary = $request['booking_itinerary'];
// 	}

// 	// Setting conditions.
// 	if ( '' !== $from_date || '' !== $to_date || '' !== $country || '' !== $itinerary ) {
// 		// Set initial load to false if there is extra get variables.
// 		$initial_load = false;
// 		if ( '' !== $itinerary ) {
// 			$where 	 .= " and I.itinerary_id={$itinerary} ";
// 		}
// 		if ( '' !== $country ) {
// 			$where   .= " and country='{$country}'";
// 		}

// 		if ( '' !== $from_date && '' !== $to_date ) {

// 			$date_format = 'Y-m-d H:i:s';

// 			$booking_from = date( $date_format, strtotime( $from_date ) );
// 			$booking_to   = date( $date_format, strtotime( $to_date ) );

// 			$where 	 .= " and payment_date >= '{$booking_from}' and payment_date <= '{$booking_to}' ";
// 		}
// 		$limit = '';
// 	}

// 	// Payment Data Default Query.
// 	$initial_transient = $results = get_site_transient( '_transient_wt_booking_payment_stat_data' );
// 	if ( ( ! $initial_load ) || ( $initial_load && ! $results ) ) {
// 		$query = "Select count( BOOKING.ID ) as no_of_payment, YEAR( payment_date ) as payment_year, Month( payment_date ) as payment_month, DAY( payment_date ) as payment_day, sum( AMT.payment_amount ) as payment_amount from {$wpdb->posts} BOOKING 
// 		join ( 
// 			Select distinct( PaymentMeta.post_id ), meta_value as payment_id, PaymentPost.post_date as payment_date from {$wpdb->posts} PaymentPost 
// 			join {$wpdb->postmeta} PaymentMeta on PaymentMeta.meta_value = PaymentPost.ID    
// 			WHERE PaymentMeta.meta_key = 'wp_travel_payment_id'
// 		) PMT on BOOKING.ID = PMT.post_id
// 		join ( Select distinct( post_id ), meta_value as country from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_country' ) C on BOOKING.ID = C.post_id 
// 		join ( Select distinct( post_id ), meta_value as itinerary_id from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_post_id' ) I on BOOKING.ID = I.post_id
// 		join ( Select distinct( post_id ), meta_value as payment_status from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_payment_status' and meta_value = 'paid' ) PSt on PMT.payment_id = PSt.post_id
// 		join ( Select distinct( post_id ), case when meta_value IS NULL or meta_value = '' then '0' else meta_value
//        end as payment_amount from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_payment_amount'  ) AMT on PMT.payment_id = AMT.post_id
// 		where post_status='publish' and post_type = 'itinerary-booking' {$where}
// 		group by YEAR( payment_date ), Month( payment_date ), DAY( payment_date ) order by YEAR( payment_date ), Month( payment_date ), DAY( payment_date ) asc {$limit}";
// 		$results = $wpdb->get_results( $query );
// 		// set initial load transient for stat data.
// 		if ( $initial_load && ! $initial_transient ) {
// 			set_site_transient( '_transient_wt_booking_payment_stat_data', $results );
// 		}
// 	}
// 	// End of Payment Data Default Query.
// 	$payment_data = array();
// 	$payment_label = array();
// 	$date_format = 'jS M, Y';
// 	$payment_stat_from = $payment_stat_to = date( $date_format );
// 	$total_sales = 0;

// 	if ( $results ) {
// 		foreach ( $results as $result ) {
// 			$label_date = $result->payment_year . '-' . $result->payment_month . '-' . $result->payment_day;
// 			$label_date = date( $date_format, strtotime( $label_date ) );
// 			$payment_data[] = $result->no_of_payment;
// 			$payment_label[] = $label_date;
// 			$total_sales += $result->payment_amount;
// 		}
// 	}

// 	if ( isset( $request['chart_type'] ) &&  'payment' == $request['chart_type'] ) {
// 		$payment_data2[] = array(
// 			'label' => esc_html__( 'Payment', 'wp-travel' ),
// 			'backgroundColor' => '#1DFE0E',
// 			'borderColor' => '#1DFE0E',
// 			'data' => $payment_data,
// 			'fill' => false,
// 		);
// 		// $stat_data['labels'] = json_encode( $payment_label );
// 		// $stat_data['datasets'] = json_encode( $payment_data2 );
// 	}

// 	// $stat_data['total_sales'] = number_format( $total_sales, 2, '.', '' );
// 	return $stat_data;
// }

/*
 * ADMIN COLUMN - HEADERS
 */
add_filter( 'manage_edit-itinerary-booking_columns', 'wp_travel_booking_payment_columns', 20 );

/**
 * Customize Admin column.
 *
 * @param  Array $booking_columns List of columns.
 * @return Array                  [description]
 */
function wp_travel_booking_payment_columns( $booking_columns ) {

	$date = $booking_columns['date'];
	unset( $booking_columns['date'] );

	$booking_columns['payment_mode'] = __( 'Payment Mode', 'wp-travel' );
	$booking_columns['payment_status'] = __( 'Payment Status', 'wp-travel' );
	$booking_columns['date'] = $date;
	return $booking_columns;
}

/*
 * ADMIN COLUMN - CONTENT
 */
add_action( 'manage_itinerary-booking_posts_custom_column', 'wp_travel_booking_payment_manage_columns', 10, 2 );

/**
 * Add data to custom column.
 *
 * @param  String $column_name Custom column name.
 * @param  int 	  $id          Post ID.
 */
function wp_travel_booking_payment_manage_columns( $column_name, $id ) {
	switch ( $column_name ) {
		case 'payment_status':
			$payment_id = get_post_meta( $id , 'wp_travel_payment_id' , true );
			$booking_option = get_post_meta( $payment_id , 'wp_travel_booking_option' , true );

			$payment_status = get_post_meta( $payment_id , 'wp_travel_payment_status' , true );
			if ( 'booking_only' === $booking_option || '' === $booking_option ) {
				$label_key = 'pending';
				if ( '' === $payment_status ) {
					update_post_meta( $payment_id , 'wp_travel_payment_status' , $label_key );
				}
			} else {
				$label_key = get_post_meta( $payment_id , 'wp_travel_payment_status' , true );
			}
			if ( ! $label_key ) {
				$label_key = 'N/A';
				update_post_meta( $payment_id , 'wp_travel_payment_status' , $label_key );
			}
			$status = wp_travel_get_payment_status();
			echo '<span class="wp-travel-status wp-travel-payment-status" style="background: ' . esc_attr( $status[ $label_key ]['color'], 'wp-travel' ) . ' ">' . esc_attr( $status[ $label_key ]['text'], 'wp-travel' ) . '</span>';
			break;
		case 'payment_mode':
			$mode = wp_travel_get_payment_mode();
			$payment_id = get_post_meta( $id , 'wp_travel_payment_id' , true );
			$label_key = get_post_meta( $payment_id , 'wp_travel_payment_mode' , true );
			if ( ! $label_key ) {
				$label_key = 'N/A';
				update_post_meta( $payment_id , 'wp_travel_payment_mode' , $label_key );
			}
			echo '<span >' . esc_attr( $mode[ $label_key ]['text'], 'wp-travel' ) . '</span>';
			break;
		default:
			break;
	} // end switch
}

/*
 * ADMIN COLUMN - SORTING - MAKE HEADERS SORTABLE
 * https://gist.github.com/906872
 */
// add_filter( 'manage_edit-itinerary-booking_sortable_columns', 'wp_travel_booking_payment_sort' );
// function wp_travel_booking_payment_sort( $columns ) {

// 	$custom = array(
// 		'payment_status' => 'payment_status',
// 		'payment_mode' 	 => 'payment_mode',
// 	);
// 	return wp_parse_args( $custom, $columns );
// 	/* or this way
// 		$columns['concertdate'] = 'concertdate';
// 		$columns['city'] = 'city';
// 		return $columns;
// 	*/
// }

/*
 * ADMIN COLUMN - SORTING - ORDERBY
 * http://scribu.net/wordpress/custom-sortable-columns.html#comment-4732
 */
add_filter( 'request', 'wp_travel_booking_payment_column_orderby' );

/**
 * Manage Order By custom column.
 *
 * @param  Array $vars Order By array.
 * @since 1.0.0
 * @return Array       Order By array.
 */
function wp_travel_booking_payment_column_orderby( $vars ) {
	if ( isset( $vars['orderby'] ) && 'payment_status' == $vars['orderby'] ) {
		$vars = array_merge( $vars, array(
			'meta_key' => 'wp_travel_payment_status',
			'orderby' => 'meta_value',
		) );
	}
	if ( isset( $vars['orderby'] ) && 'payment_mode' == $vars['orderby'] ) {
		$vars = array_merge( $vars, array(
			'meta_key' => 'wp_travel_payment_mode',
			'orderby' => 'meta_value',
		) );
	}
	return $vars;
}

