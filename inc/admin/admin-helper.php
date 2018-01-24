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
	wp_travel_upgrade_to_110();
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
				<!--<div class="panel-wrap panel-wrap-itinerary">
					<label><?php // esc_html_e( 'Itinerary Date', 'wp-travel' ); ?></label>
					<input class="wp-travel-datepicker" type="text" name="wp_travel_trip_itinerary_data[<?php // echo esc_attr( $uid ) ?>][date]" value="">
				</div>
				<div class="panel-wrap panel-wrap-itinerary">
					<label><?php // esc_html_e( 'Itinerary Time', 'wp-travel' ); ?></label>
					<input class="wp-travel-timepicker" type="text" name="wp_travel_trip_itinerary_data[<?php // echo esc_attr( $uid ) ?>][time]" value="">
				</div> !-->
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
