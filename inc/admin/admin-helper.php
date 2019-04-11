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
	if ( version_compare( WP_TRAVEL_VERSION, '1.3.6', '>' )) {
		include_once sprintf( '%s/upgrade/update-137.php', WP_TRAVEL_ABSPATH );
	}
}

function wp_travel_marketplace_page() {

	$addons_data = get_transient( 'wp_travel_marketplace_addons_list' );

	if ( ! $addons_data ) {

		$addons_data = file_get_contents( 'https://wptravel.io/edd-api/products/?number=-1' );
		set_transient( 'wp_travel_marketplace_addons_list', $addons_data );

	}

	if ( ! empty( $addons_data ) ) :

		$addons_data = json_decode( $addons_data );
		$addons_data = $addons_data->products;

    endif;
    
    // Hardcoded themes data.
	$themes_data = array(
		'travel-base-pro' => array(
			'name' => __( 'Travel Base Pro', 'wp-travel' ),
			'type' => 'premium',
			'img_url' => 'https://wptravel.io/wp-content/themes/wptravel/images/travel-base-pro.png',
			'demo_url' => 'https://wptravel.io/demo/?demo=travel-base-pro',
			'detail_url' => 'https://themepalace.com/downloads/travel-base-pro/',
		),
		'travel-base' => array(
			'name' => __( 'Travel Base', 'wp-travel' ),
			'type' => 'free',
			'img_url' => 'https://wptravel.io/wp-content/themes/wptravel/images/travel-base-free.png',
			'demo_url' => 'https://wptravel.io/demo/?demo=travel-base-pro',
			'detail_url' => 'https://themepalace.com/downloads/travel-base/',
		),
		'travel-ultimate-pro' => array(
			'name' => __( 'Travel Ultimate Pro', 'wp-travel' ),
			'type' => 'premium',
			'img_url' => 'https://wptravel.io/wp-content/themes/wptravel/images/travel-ultimate-pro.png',
			'demo_url' => 'https://wptravel.io/demo/?demo=travel-ultimate-pro',
			'detail_url' => 'https://themepalace.com/downloads/travel-ultimate-pro/',
		),
		'travel-ultimate' => array(
			'name' => __( 'Travel Ultimate', 'wp-travel' ),
			'type' => 'free',
			'img_url' => 'https://wptravel.io/wp-content/themes/wptravel/images/travel-ultimate-free.png',
			'demo_url' => 'https://wptravel.io/demo/?demo=travel-ultimate-pro',
			'detail_url' => 'https://themepalace.com/downloads/travel-ultimate/',
		),
		'pleased-pro' => array(
			'name' => __( 'Pleased Pro', 'wp-travel' ),
			'type' => 'premium',
			'img_url' => 'https://wptravel.io/wp-content/themes/wptravel/images/pleased-pro.png',
			'demo_url' => 'https://wptravel.io/demo/?demo=pleased-pro',
			'detail_url' => 'https://themepalace.com/downloads/pleased-pro/',
		),
		'pleased' => array(
			'name' => __( 'Pleased', 'wp-travel' ),
			'type' => 'free',
			'img_url' => 'https://wptravel.io/wp-content/themes/wptravel/images/pleased-free.png',
			'demo_url' => 'https://wptravel.io/demo/?demo=pleased-pro',
			'detail_url' => 'https://themepalace.com/downloads/pleased/',
		),
		'travel-gem-pro' => array(
			'name' => __( 'Travel Gem Pro', 'wp-travel' ),
			'type' => 'premium',
			'img_url' => 'https://wptravel.io/wp-content/themes/wptravel/images/travel-gem-pro.png',
			'demo_url' => 'https://wptravel.io/demo/?demo=travel-gem-pro',
			'detail_url' => 'https://themepalace.com/downloads/travel-gem-pro/',
		),
		'travel-gem' => array(
			'name' => __( 'Travel Gem', 'wp-travel' ),
			'type' => 'free',
			'img_url' => 'https://wptravel.io/wp-content/themes/wptravel/images/travel-gem-free.png',
			'demo_url' => 'https://wptravel.io/demo/?demo=travel-gem-pro',
			'detail_url' => 'https://themepalace.com/downloads/travel-gem/',
		),
		'tourable-pro' => array(
			'name' => __( 'Tourable Pro', 'wp-travel' ),
			'type' => 'premium',
			'img_url' => 'https://wptravel.io/wp-content/themes/wptravel/images/tourable-pro.png',
			'demo_url' => 'https://wptravel.io/demo/?demo=tourable-pro',
			'detail_url' => 'https://themepalace.com/downloads/tourable-pro/',
		),
		'tourable' => array(
			'name' => __( 'Tourable', 'wp-travel' ),
			'type' => 'free',
			'img_url' => 'https://wptravel.io/wp-content/themes/wptravel/images/tourable-free.png',
			'demo_url' => 'https://wptravel.io/demo/?demo=tourable-pro',
			'detail_url' => 'https://themepalace.com/downloads/tourable/',
		),
		'travel-log' => array(
			'name' => __( 'Travel Log', 'wp-travel' ),
			'type' => 'free',
			'img_url' => plugins_url( '/wp-travel/assets/images/devices_web.png' ),
			'demo_url' => 'https://wptravel.io/demo/?demo=travel-log',
			'detail_url' => 'http://wensolutions.com/themes/travel-log/',
		),
	);

    $info_btn_text = __( 'View Demo', 'wp-travel' );
    $download_btn_text = __( 'View Detail', 'wp-travel' );

	?>
	<div class="wrap">
		<div id="poststuff">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Marketplace', 'wp-travel' ) ?></h1>
			<div id="post-body">
				<div class="wp-travel-marketplace-tab-wrap">
					<ul>

						<li class=""><a href="#tabs-1"><?php esc_html_e( 'Addons', 'wp-travel' ) ?></a></li>
						<?php if ( $addons_data ) : ?>
							<li class=""><a href="#tabs-2"><?php esc_html_e( 'Themes', 'wp-travel' ) ?></a></li>
						<?php endif; ?>
					</ul>
					<div id="tabs-2" class="tab-pannel">
						<div class="marketplace-module clearfix">
                            <?php foreach ( $themes_data as $theme ) : ?>
                                <div class="single-module">
                                    <div class="single-module-image">
                                        <a href="<?php echo esc_url( $theme['demo_url'] ); ?>" target="_blank">
                                        <img width="423" height="237" src="<?php echo esc_url( $theme['img_url'] ); ?>" class="" alt="" >
                                        </a>
                                    </div>
                                    <div class="single-module-content clearfix">
                                        <h4 class="text-title"><a href="<?php echo esc_url( $theme['detail_url'] ); ?>" target="_blank">
                                        <span class="dashicons-wp-travel">
                                        </span><?php echo esc_html( $theme['name'] ); ?></a></h4>
                                        <a class="btn-default pull-left" href="<?php echo esc_url( $theme['demo_url'] ); ?>" target="_blank"><?php echo esc_html( $info_btn_text ); ?></a>
                                        <a class="btn-default pull-right" href="<?php echo esc_url( $theme['detail_url'] ); ?>" target="_blank"><?php echo esc_html( $download_btn_text ); ?></a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
						</div>
					</div>
					<?php if ( $addons_data ) : ?>
						<div id="tabs-1" class="tab-pannel">
							<div class="marketplace-module clearfix">
							<?php foreach( $addons_data as $key => $product ) :
								$prod_info = $product->info;
							?>

								<div class="single-module">
									<div class="single-module-image">
										<a href="<?php echo esc_url( $prod_info->link ); ?>" target="_blank">
										<img width="423" height="237" src="<?php echo esc_url( $prod_info->thumbnail ); ?>" class="" alt="">
										</a>
									</div>
									<div class="single-module-content clearfix">
										<h4 class="text-title">
											<a href="<?php echo esc_url( $prod_info->link ); ?>" target="_blank">
												<span class="dashicons-wp-travel">
												</span>
												<?php echo esc_html( $prod_info->title ); ?>
											</a>
										</h4>
										<a class="btn-default pull-left" href="<?php echo esc_url( $prod_info->link ); ?>" target="_blank"><?php esc_html_e( 'View Detail', 'wp-travel' ) ?></a>
										<a class="btn-default pull-right" href="<?php echo esc_url( $prod_info->link ); ?>" target="_blank">
											<?php
											if ( isset( $product->pricing->amount ) && $product->pricing->amount < 1 ) {
												esc_html_e( 'Download', 'wp-travel' );
											} else {
												esc_html_e( 'Purchase', 'wp-travel' );
											}
											?>
										</a>
									</div>
								</div>

							<?php endforeach; ?>

							</div>
						</div>
					<?php endif; ?>

				</div>


				<div id="aside-wrap" class="single-module-side">

		<div id="wp_travel_support_block_id" class="postbox ">
			<button type="button" class="handlediv" aria-expanded="true">
				<span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Support', 'wp-travel' ) ?></span>
				<span class="toggle-indicator-acc" aria-hidden="true"></span>
			</button>
			<h2 class="hndle ui-sortable-handle">
				<span><?php esc_html_e( 'Support', 'wp-travel' ) ?></span>
			</h2>
			<div class="inside">

			<div class="thumbnail">
				<img src="<?php echo plugins_url( '/wp-travel/assets/images/support-image.png' ) ?>">
					<p class="text-justify"><?php esc_html_e( 'Click Below for support.', 'wp-travel' ) ?> </p>
					<p class="text-center"><a href="http://wptravel.io/support/" target="_blank" class="button button-primary"><?php esc_html_e( 'Get Support Here', 'wp-travel' ) ?></a></p>
			</div>

			</div>
		</div>

		<div id="wp_travel_doc_block_id" class="postbox ">
			<button type="button" class="handlediv" aria-expanded="true">
				<span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Documentation', 'wp-travel' ) ?></span>
				<span class="toggle-indicator" aria-hidden="true"></span>
			</button>
			<h2 class="hndle ui-sortable-handle">
				<span><?php esc_html_e( 'Documentation', 'wp-travel' ) ?></span>
			</h2>
			<div class="inside">

				<div class="thumbnail">
					<img src="<?php echo plugins_url( '/wp-travel/assets/images/docico.png' ) ?>">
						<p class="text-justify"><?php esc_html_e( 'Click Below for our full Documentation about logo slider.', 'wp-travel' ) ?> </p>
						<p class="text-center"><a href="http://wptravel.io/documentations/" target="_blank" class="button button-primary"><?php esc_html_e( 'Get Documentation Here', 'wp-travel' ) ?></a></p>
				</div>

			</div>
		</div>

		<div id="wp_travel_review_block_id" class="postbox ">
			<button type="button" class="handlediv" aria-expanded="true">
				<span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Reviews', 'wp-travel' ) ?></span>
				<span class="toggle-indicator" aria-hidden="true"></span>
			</button>
			<h2 class="hndle ui-sortable-handle">
				<span><?php esc_html_e( 'Reviews', 'wp-travel' ) ?></span>
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
					<h5><?php esc_html_e( '"The plugin is very intuitive and fresh.
The layout fits well into theme with flexibility to different shortcodes.
Its great plugin for travel or tour agent websites."', 'wp-travel' ) ?></h5>
					<span class="by"><strong> <a href="https://profiles.wordpress.org/muzdat" target="_blank"><?php esc_html_e( 'muzdat', 'wp-travel' ) ?></a></strong></span>

				</div>
				<div class="thumbnail last">
					<h5><?php esc_html_e( '"Please fill free to leave us a review, if you found this plugin helpful."', 'wp-travel' ) ?></h5>
					<p class="text-center"><a href="https://wordpress.org/plugins/wp-travel/#reviews" target="_blank" class="button button-primary"><?php esc_html_e( 'Leave a Review', 'wp-travel' ) ?></a></p>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</div>
<?php
}

// Upsell Message Callback for Download submenu. WP Travel > Downloads.
function wp_travel_get_download_upsell() {
    ?>
    <h2><?php echo esc_html( 'Downloads' ); ?></h2>
    <?php
    if ( ! class_exists( 'WP_Travel_Downloads_Core' ) ) :
        $args = array(
            'title'      => __( 'Need to add your downloads ?', 'wp-travel' ),
            'content'    => __( 'By upgrading to Pro, you can add your downloads in all of your trips !', 'wp-travel' ),
            'link'       => 'https://wptravel.io/downloads/wp-travel-downloads/',
            'link_label' => __( 'Get WP Travel Downloads Addon', 'wp-travel' ),
        );
        wp_travel_upsell_message( $args );
    endif;
}

/**
 * Modify Admin Footer Message.
 */
function wp_travel_modify_admin_footer_admin_settings_page(){

	printf(__('Love %1s, Consider leaving us a %2s rating, also checkout %3s . A huge thanks in advance!', 'wp-travel' ), '<strong>WP Travel ?</strong>','<a target="_blank" href="https://wordpress.org/support/plugin/wp-travel/reviews/">★★★★★</a>', '<a target="_blank" href="https://wptravel.io/downloads/">WP Travel add-ons</a>' );
}
/**
 * Modify Admin Footer Message.
 */
function wp_travel_modify_admin_footer_version(){

	printf(__('WP Travel version: %s', 'wp-travel' ), '<strong>' . WP_TRAVEL_VERSION . '</strong>' );

}
/**
 * Add Footer Custom Text Hook.
 */
function wp_travel_doc_support_footer_custom_text(){

	$screen = get_current_screen();

	if ( WP_TRAVEL_POST_TYPE === $screen->post_type ) {

		add_filter('admin_footer_text', 'wp_travel_modify_admin_footer_admin_settings_page');
		add_filter( 'update_footer', 'wp_travel_modify_admin_footer_version', 11 );
	}
}

add_action( 'current_screen', 'wp_travel_doc_support_footer_custom_text' );

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
	$booking_count = get_site_transient( "_transient_wt_booking_count_{$itinerary_id}" );
	if ( ! $booking_count ) {
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

function wp_travel_itineraries_sort( $columns ) {

	$custom = array(
		'booking_count' 	=> 'booking_count',
	);
	return wp_parse_args( $custom, $columns );
}
/*
 * ADMIN COLUMN - SORTING - MAKE HEADERS SORTABLE
 * https://gist.github.com/906872
 */
add_filter( 'manage_edit-' . WP_TRAVEL_POST_TYPE . '_sortable_columns', 'wp_travel_itineraries_sort' );

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
add_action( 'post_submitbox_misc_actions', 'wp_travel_publish_metabox' );

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



/**
 * Add data to custom column.
 *
 * @param  String $column_name Custom column name.
 * @param  int 	  $id          Post ID.
 */
function wp_travel_booking_payment_manage_columns( $column_name, $id ) {
	switch ( $column_name ) {
		case 'payment_status':
            $payment_id = get_post_meta( $id, 'wp_travel_payment_id', true );
            if ( is_array( $payment_id ) ) {
                if ( count( $payment_id ) > 0 ) {
                    $payment_id = $payment_id[0];
                }
            }
			$booking_option = get_post_meta( $payment_id , 'wp_travel_booking_option' , true );

			$label_key = get_post_meta( $payment_id , 'wp_travel_payment_status' , true );
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
			$label_key = get_post_meta( $payment_id, 'wp_travel_payment_mode' , true );

			if ( ! $label_key ) {
				$label_key = 'N/A';
				$is_partial_enabled = get_post_meta( $payment_id, 'wp_travel_is_partial_payment', true );
				if ( ! $is_partial_enabled ) {
					$label_key = 'full';
				}
				update_post_meta( $payment_id , 'wp_travel_payment_mode' , $label_key );
			}
			echo '<span >' . esc_attr( $mode[ $label_key ]['text'], 'wp-travel' ) . '</span>';
			break;
		default:
			break;
	} // end switch
}
/*
 * ADMIN COLUMN - CONTENT
 */
add_action( 'manage_itinerary-booking_posts_custom_column', 'wp_travel_booking_payment_manage_columns', 10, 2 );

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
add_filter( 'request', 'wp_travel_booking_payment_column_orderby' );

/**
 * Create a page and store the ID in an option.
 *
 * @param mixed $slug Slug for the new page
 * @param string $option Option name to store the page's ID
 * @param string $page_title (default: '') Title for the new page
 * @param string $page_content (default: '') Content for the new page
 * @param int $post_parent (default: 0) Parent for the new page
 * @return int page ID
 */
function wp_travel_create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0 ) {
	global $wpdb;

	$option_value     = get_option( $option );

	if ( $option_value > 0 && ( $page_object = get_post( $option_value ) ) ) {
		if ( 'page' === $page_object->post_type && ! in_array( $page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ) ) ) {
			// Valid page is already in place
			if ( strlen( $page_content ) > 0 ) {
				// Search for an existing page with the specified page content (typically a shortcode)
				$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
			} else {
				// Search for an existing page with the specified page slug
				$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", $slug ) );
			}

			$valid_page_found = apply_filters( 'wp_travel_create_page_id', $valid_page_found, $slug, $page_content );

			if ( $valid_page_found ) {
				if ( $option ) {
					update_option( $option, $valid_page_found );
				}
				return $valid_page_found;
			}
		}
	}

	// Search for a matching valid trashed page
	if ( strlen( $page_content ) > 0 ) {
		// Search for an existing page with the specified page content (typically a shortcode)
		$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
	} else {
		// Search for an existing page with the specified page slug
		$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug ) );
	}

	if ( $trashed_page_found ) {
		$page_id   = $trashed_page_found;
		$page_data = array(
			'ID'             => $page_id,
			'post_status'    => 'publish',
		);
	 	wp_update_post( $page_data );
	} else {
		$page_data = array(
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => 1,
			'post_name'      => $slug,
			'post_title'     => $page_title,
			'post_content'   => $page_content,
			'post_parent'    => $post_parent,
			'comment_status' => 'closed',
		);
		$page_id = wp_insert_post( $page_data );
	}

	if ( $option ) {
		update_option( $option, $page_id );
	}

	return $page_id;
}

/**
 * Tour Extras Multiselect Options.
 */
function wp_travel_admin_tour_extra_multiselect( $post_id, $context = false, $fetch_key, $table_row = false ) {

$tour_extras = wp_count_posts( 'tour-extras' );
// Check Tour Extras Count.
if( 0 == $tour_extras->publish ) {
	ob_start(); ?>

	<?php if ( $table_row ) : ?><td><?php  else : ?><div class="one-third"><?php endif ; ?>
	<label for=""><?php echo esc_html( 'Trip Extras', 'wp-travel-coupon-pro' ); ?></label>
	<?php if ( $table_row ) : ?></td><?php  else : ?></div><?php endif ; ?>

	<?php if ( $table_row ) : ?><td><?php  else : ?><div class="two-third"><?php endif ; ?>
	<?php echo sprintf( '<p class="wp-travel-trip-extra-notice good" id="pass-strength-result">Please <a class="button-link" href="post-new.php?post_type=tour-extras">Click here </a> to add Trip Extra first.</p>'); ?>
	<?php if ( $table_row ) : ?></td><?php  else : ?></div><?php endif ;

	$data = ob_get_clean();
return $data;
}

if( empty( $post_id ) || empty( $fetch_key ) )
	return;
$name = 'wp_travel_tour_extras[]';
if ( $context && 'pricing_options' === $context ) {
	$pricing_options = get_post_meta( $post_id, 'wp_travel_pricing_options', true );
	$trip_extras = isset( $pricing_options[$fetch_key]['tour_extras'] ) && ! empty( $pricing_options[$fetch_key]['tour_extras'] ) ? $pricing_options[$fetch_key]['tour_extras'] : false;
	$name = 'wp_travel_pricing_options[' . $fetch_key . '][tour_extras][]';
} elseif( ! $context && 'wp_travel_tour_extras' === $fetch_key ) {
	$trip_extras = get_post_meta( $post_id, 'wp_travel_tour_extras', true );
}

$restricted_trips = ( $trip_extras ) ? $trip_extras: array();

ob_start(); ?>
	<?php if ( $table_row ) : ?><td><?php  else : ?><div><div class="one-third"><?php endif ; ?>
		<label for=""><?php echo esc_html( 'Trip Extras', 'wp-travel-coupon-pro' ); ?></label>
	<?php if ( $table_row ) : ?></td><td><?php  else : ?></div><div class="two-third"><?php endif ; ?>

		<?php $itineraries = wp_travel_get_tour_extras_array(); ?>

		<div class="custom-multi-select">
			<?php
			$count_options_data = count( $restricted_trips );
			$count_itineraries = count( $itineraries );
			$multiple_checked_all = '';
			if ( $count_options_data == $count_itineraries ) {
				$multiple_checked_all = 'checked=checked';
			}

			$multiple_checked_text = __( 'Select multiple', 'wp-travel' );
			if ( $count_itineraries > 0 ) {
				$multiple_checked_text = $count_options_data . __( ' item selected', 'wp-travel' );
			}
			?>
			<span class="select-main">
				<span class="selected-item"><?php echo esc_html( $multiple_checked_text ); ?></span>
				<span class="carret"></span>
				<span class="close"></span>
				<ul class="wp-travel-multi-inner">
					<li class="wp-travel-multi-inner">
						<label class="checkbox wp-travel-multi-inner">
							<input <?php echo esc_attr( $multiple_checked_all ); ?> type="checkbox"  id="wp-travel-multi-input-1" class="wp-travel-multi-inner multiselect-all" value="multiselect-all"><?php esc_html_e( 'Select all', 'wp-travel' ); ?>
						</label>
					</li>
					<?php
					foreach ( $itineraries as $key => $iti ) {

						$checked = '';
						$selecte_list_class = '';

						if ( in_array( $key, $restricted_trips ) ) {

							$checked = 'checked=checked';
							$selecte_list_class = 'selected';

						}

					?>
						<li class="wp-travel-multi-inner <?php echo esc_attr( $selecte_list_class ) ?>">
							<label class="checkbox wp-travel-multi-inner ">
								<input <?php echo esc_attr( $checked ); ?>  name="<?php echo esc_attr( $name ); ?>" type="checkbox" id="wp-travel-multi-input-<?php echo esc_attr( $key ); ?>" class="wp-travel-multi-inner multiselect-value" value="<?php echo esc_attr( $key ); ?>">  <?php echo esc_html( $iti ); ?>
							</label>
						</li>
					<?php } ?>
				</ul>
			</span>

		</div>
	<?php if ( $table_row ) : ?></td><?php  else : ?></div></div><?php endif ; ?>
<?php
$data = ob_get_clean();
return $data;

}

add_action( 'wp_travel_extras_pro_options', 'wp_travel_extras_pro_option_fields' );

/**
 * WP Travel Tour Extras Pro fields.
 *
 * @return void
 */
function wp_travel_extras_pro_option_fields() {

	$is_pro_enabled = apply_filters( 'wp_travel_extras_is_pro_enabled', false );

	if ( $is_pro_enabled ) {
		do_action( 'wp_travel_extras_pro_single_options' );
		return;
	}
	?>
	<tr class="pro-options-note"><td colspan="10"><?php esc_html_e( 'Pro options', 'wp-travel' ); ?></td></tr>
	<tr class="wp-travel-pro-mockup-option">
		<td><label for="extra-item-price"><?php esc_html_e( 'Price', 'wp-travel' ); ?></label>
			<span class="tooltip-area" title="<?php esc_html_e( 'Item Price', 'wp-travel' ); ?>">
				<i class="wt-icon wt-icon-question-circle" aria-hidden="true"></i>
			</span>
		</td>
		<td>
			<span id="coupon-currency-symbol" class="wp-travel-currency-symbol">
					<?php echo wp_travel_get_currency_symbol(); ?>
			</span>
			<input disabled="disabled" type="number" min="1" step="0.01" id="extra-item-price" placeholder="<?php echo esc_attr__( 'Price', 'wp-travel' ); ?>" >
		</td>
	</tr>
	<tr class="wp-travel-pro-mockup-option">
		<td><label for="extra-item-sale-price"><?php esc_html_e( 'Sale Price', 'wp-travel' ); ?></label>
			<span class="tooltip-area" titl.e="<?php esc_html_e( 'Sale Price(Leave Blank to disable sale)', 'wp-travel' ); ?>">
				<i class="wt-icon wt-icon-question-circle" aria-hidden="true"></i>
			</span>
		</td>
		<td>
			<span id="coupon-currency-symbol" class="wp-travel-currency-symbol">
				<?php echo wp_travel_get_currency_symbol(); ?>
			</span>
			<input type="number" min="1" step="0.01" id="extra-item-sale-price" placeholder="<?php echo esc_attr__( 'Sale Price', 'wp-travel' ); ?>" disabled="disabled" >
		</td>
	</tr>
	<tr class="wp-travel-pro-mockup-option">
		<td><label for="extra-item-price-per"><?php esc_html_e( 'Price Per', 'wp-travel' ); ?></label>
		</td>
		<td>
			<select disabled="disabled" id="extra-item-price-per">
				<option value="unit"><?php esc_html_e( 'Unit', 'wp-travel' ); ?></option>
				<option value="person"><?php esc_html_e( 'Person', 'wp-travel' ); ?></option>
			</select>
		</td>
	</tr>
	<tr class="wp-travel-upsell-message">
		<td colspan="2">
			<div class="wp-travel-pro-feature-notice">
				<h4><?php esc_html_e( 'Want to use above pro features?', 'wp-travel' ); ?></h4>
				<p><?php esc_html_e( 'By upgrading to Pro, you can get features with gallery, detail extras page in Front-End and more !', 'wp-travel' ); ?></p>
				<a target="_blank" href="https://themepalace.com/downloads/wp-travel-tour-extras/"><?php esc_html_e( 'Get Tour Extras Addon', 'wp-travel' ); ?></a>
			</div>
		</td>
	</tr>

	<?php
}

/**
 * Font Awesome array.
 *
 * @return array icons.
 */
function wp_travel_fa_icons() {

    return array (
        'fab fa-500px' => __( '500px', 'wp-travel' ),
        'fab fa-accessible-icon' => __( 'accessible-icon', 'wp-travel' ),
        'fab fa-accusoft' => __( 'accusoft', 'wp-travel' ),
        'fas fa-address-book' => __( 'address-book', 'wp-travel' ),
        'far fa-address-book' => __( 'address-book', 'wp-travel' ),
        'fas fa-address-card' => __( 'address-card', 'wp-travel' ),
        'far fa-address-card' => __( 'address-card', 'wp-travel' ),
        'fas fa-adjust' => __( 'adjust', 'wp-travel' ),
        'fab fa-adn' => __( 'adn', 'wp-travel' ),
        'fab fa-adversal' => __( 'adversal', 'wp-travel' ),
        'fab fa-affiliatetheme' => __( 'affiliatetheme', 'wp-travel' ),
        'fab fa-algolia' => __( 'algolia', 'wp-travel' ),
        'fas fa-align-center' => __( 'align-center', 'wp-travel' ),
        'fas fa-align-justify' => __( 'align-justify', 'wp-travel' ),
        'fas fa-align-left' => __( 'align-left', 'wp-travel' ),
        'fas fa-align-right' => __( 'align-right', 'wp-travel' ),
        'fas fa-allergies' => __( 'allergies', 'wp-travel' ),
        'fab fa-amazon' => __( 'amazon', 'wp-travel' ),
        'fab fa-amazon-pay' => __( 'amazon-pay', 'wp-travel' ),
        'fas fa-ambulance' => __( 'ambulance', 'wp-travel' ),
        'fas fa-american-sign-language-interpreting' => __( 'american-sign-language-interpreting', 'wp-travel' ),
        'fab fa-amilia' => __( 'amilia', 'wp-travel' ),
        'fas fa-anchor' => __( 'anchor', 'wp-travel' ),
        'fab fa-android' => __( 'android', 'wp-travel' ),
        'fab fa-angellist' => __( 'angellist', 'wp-travel' ),
        'fas fa-angle-double-down' => __( 'angle-double-down', 'wp-travel' ),
        'fas fa-angle-double-left' => __( 'angle-double-left', 'wp-travel' ),
        'fas fa-angle-double-right' => __( 'angle-double-right', 'wp-travel' ),
        'fas fa-angle-double-up' => __( 'angle-double-up', 'wp-travel' ),
        'fas fa-angle-down' => __( 'angle-down', 'wp-travel' ),
        'fas fa-angle-left' => __( 'angle-left', 'wp-travel' ),
        'fas fa-angle-right' => __( 'angle-right', 'wp-travel' ),
        'fas fa-angle-up' => __( 'angle-up', 'wp-travel' ),
        'fab fa-angrycreative' => __( 'angrycreative', 'wp-travel' ),
        'fab fa-angular' => __( 'angular', 'wp-travel' ),
        'fab fa-app-store' => __( 'app-store', 'wp-travel' ),
        'fab fa-app-store-ios' => __( 'app-store-ios', 'wp-travel' ),
        'fab fa-apper' => __( 'apper', 'wp-travel' ),
        'fab fa-apple' => __( 'apple', 'wp-travel' ),
        'fab fa-apple-pay' => __( 'apple-pay', 'wp-travel' ),
        'fas fa-archive' => __( 'archive', 'wp-travel' ),
        'fas fa-arrow-alt-circle-down' => __( 'arrow-alt-circle-down', 'wp-travel' ),
        'far fa-arrow-alt-circle-down' => __( 'arrow-alt-circle-down', 'wp-travel' ),
        'fas fa-arrow-alt-circle-left' => __( 'arrow-alt-circle-left', 'wp-travel' ),
        'far fa-arrow-alt-circle-left' => __( 'arrow-alt-circle-left', 'wp-travel' ),
        'fas fa-arrow-alt-circle-right' => __( 'arrow-alt-circle-right', 'wp-travel' ),
        'far fa-arrow-alt-circle-right' => __( 'arrow-alt-circle-right', 'wp-travel' ),
        'fas fa-arrow-alt-circle-up' => __( 'arrow-alt-circle-up', 'wp-travel' ),
        'far fa-arrow-alt-circle-up' => __( 'arrow-alt-circle-up', 'wp-travel' ),
        'fas fa-arrow-circle-down' => __( 'arrow-circle-down', 'wp-travel' ),
        'fas fa-arrow-circle-left' => __( 'arrow-circle-left', 'wp-travel' ),
        'fas fa-arrow-circle-right' => __( 'arrow-circle-right', 'wp-travel' ),
        'fas fa-arrow-circle-up' => __( 'arrow-circle-up', 'wp-travel' ),
        'fas fa-arrow-down' => __( 'arrow-down', 'wp-travel' ),
        'fas fa-arrow-left' => __( 'arrow-left', 'wp-travel' ),
        'fas fa-arrow-right' => __( 'arrow-right', 'wp-travel' ),
        'fas fa-arrow-up' => __( 'arrow-up', 'wp-travel' ),
        'fas fa-arrows-alt' => __( 'arrows-alt', 'wp-travel' ),
        'fas fa-arrows-alt-h' => __( 'arrows-alt-h', 'wp-travel' ),
        'fas fa-arrows-alt-v' => __( 'arrows-alt-v', 'wp-travel' ),
        'fas fa-assistive-listening-systems' => __( 'assistive-listening-systems', 'wp-travel' ),
        'fas fa-asterisk' => __( 'asterisk', 'wp-travel' ),
        'fab fa-asymmetrik' => __( 'asymmetrik', 'wp-travel' ),
        'fas fa-at' => __( 'at', 'wp-travel' ),
        'fab fa-audible' => __( 'audible', 'wp-travel' ),
        'fas fa-audio-description' => __( 'audio-description', 'wp-travel' ),
        'fab fa-autoprefixer' => __( 'autoprefixer', 'wp-travel' ),
        'fab fa-avianex' => __( 'avianex', 'wp-travel' ),
        'fab fa-aviato' => __( 'aviato', 'wp-travel' ),
        'fab fa-aws' => __( 'aws', 'wp-travel' ),
        'fas fa-backward' => __( 'backward', 'wp-travel' ),
        'fas fa-balance-scale' => __( 'balance-scale', 'wp-travel' ),
        'fas fa-ban' => __( 'ban', 'wp-travel' ),
        'fas fa-band-aid' => __( 'band-aid', 'wp-travel' ),
        'fab fa-bandcamp' => __( 'bandcamp', 'wp-travel' ),
        'fas fa-barcode' => __( 'barcode', 'wp-travel' ),
        'fas fa-bars' => __( 'bars', 'wp-travel' ),
        'fas fa-baseball-ball' => __( 'baseball-ball', 'wp-travel' ),
        'fas fa-basketball-ball' => __( 'basketball-ball', 'wp-travel' ),
        'fas fa-bath' => __( 'bath', 'wp-travel' ),
        'fas fa-battery-empty' => __( 'battery-empty', 'wp-travel' ),
        'fas fa-battery-full' => __( 'battery-full', 'wp-travel' ),
        'fas fa-battery-half' => __( 'battery-half', 'wp-travel' ),
        'fas fa-battery-quarter' => __( 'battery-quarter', 'wp-travel' ),
        'fas fa-battery-three-quarters' => __( 'battery-three-quarters', 'wp-travel' ),
        'fas fa-bed' => __( 'bed', 'wp-travel' ),
        'fas fa-beer' => __( 'beer', 'wp-travel' ),
        'fab fa-behance' => __( 'behance', 'wp-travel' ),
        'fab fa-behance-square' => __( 'behance-square', 'wp-travel' ),
        'fas fa-bell' => __( 'bell', 'wp-travel' ),
        'far fa-bell' => __( 'bell', 'wp-travel' ),
        'fas fa-bell-slash' => __( 'bell-slash', 'wp-travel' ),
        'far fa-bell-slash' => __( 'bell-slash', 'wp-travel' ),
        'fas fa-bicycle' => __( 'bicycle', 'wp-travel' ),
        'fab fa-bimobject' => __( 'bimobject', 'wp-travel' ),
        'fas fa-binoculars' => __( 'binoculars', 'wp-travel' ),
        'fas fa-birthday-cake' => __( 'birthday-cake', 'wp-travel' ),
        'fab fa-bitbucket' => __( 'bitbucket', 'wp-travel' ),
        'fab fa-bitcoin' => __( 'bitcoin', 'wp-travel' ),
        'fab fa-bity' => __( 'bity', 'wp-travel' ),
        'fab fa-black-tie' => __( 'black-tie', 'wp-travel' ),
        'fab fa-blackberry' => __( 'blackberry', 'wp-travel' ),
        'fas fa-blind' => __( 'blind', 'wp-travel' ),
        'fab fa-blogger' => __( 'blogger', 'wp-travel' ),
        'fab fa-blogger-b' => __( 'blogger-b', 'wp-travel' ),
        'fab fa-bluetooth' => __( 'bluetooth', 'wp-travel' ),
        'fab fa-bluetooth-b' => __( 'bluetooth-b', 'wp-travel' ),
        'fas fa-bold' => __( 'bold', 'wp-travel' ),
        'fas fa-bolt' => __( 'bolt', 'wp-travel' ),
        'fas fa-bomb' => __( 'bomb', 'wp-travel' ),
        'fas fa-book' => __( 'book', 'wp-travel' ),
        'fas fa-bookmark' => __( 'bookmark', 'wp-travel' ),
        'far fa-bookmark' => __( 'bookmark', 'wp-travel' ),
        'fas fa-bowling-ball' => __( 'bowling-ball', 'wp-travel' ),
        'fas fa-box' => __( 'box', 'wp-travel' ),
        'fas fa-box-open' => __( 'box-open', 'wp-travel' ),
        'fas fa-boxes' => __( 'boxes', 'wp-travel' ),
        'fas fa-braille' => __( 'braille', 'wp-travel' ),
        'fas fa-briefcase' => __( 'briefcase', 'wp-travel' ),
        'fas fa-briefcase-medical' => __( 'briefcase-medical', 'wp-travel' ),
        'fab fa-btc' => __( 'btc', 'wp-travel' ),
        'fas fa-bug' => __( 'bug', 'wp-travel' ),
        'fas fa-building' => __( 'building', 'wp-travel' ),
        'far fa-building' => __( 'building', 'wp-travel' ),
        'fas fa-bullhorn' => __( 'bullhorn', 'wp-travel' ),
        'fas fa-bullseye' => __( 'bullseye', 'wp-travel' ),
        'fas fa-burn' => __( 'burn', 'wp-travel' ),
        'fab fa-buromobelexperte' => __( 'buromobelexperte', 'wp-travel' ),
        'fas fa-bus' => __( 'bus', 'wp-travel' ),
        'fab fa-buysellads' => __( 'buysellads', 'wp-travel' ),
        'fas fa-calculator' => __( 'calculator', 'wp-travel' ),
        'fas fa-calendar' => __( 'calendar', 'wp-travel' ),
        'far fa-calendar' => __( 'calendar', 'wp-travel' ),
        'fas fa-calendar-alt' => __( 'calendar-alt', 'wp-travel' ),
        'far fa-calendar-alt' => __( 'calendar-alt', 'wp-travel' ),
        'fas fa-calendar-check' => __( 'calendar-check', 'wp-travel' ),
        'far fa-calendar-check' => __( 'calendar-check', 'wp-travel' ),
        'fas fa-calendar-minus' => __( 'calendar-minus', 'wp-travel' ),
        'far fa-calendar-minus' => __( 'calendar-minus', 'wp-travel' ),
        'fas fa-calendar-plus' => __( 'calendar-plus', 'wp-travel' ),
        'far fa-calendar-plus' => __( 'calendar-plus', 'wp-travel' ),
        'fas fa-calendar-times' => __( 'calendar-times', 'wp-travel' ),
        'far fa-calendar-times' => __( 'calendar-times', 'wp-travel' ),
        'fas fa-camera' => __( 'camera', 'wp-travel' ),
        'fas fa-camera-retro' => __( 'camera-retro', 'wp-travel' ),
        'fas fa-capsules' => __( 'capsules', 'wp-travel' ),
        'fas fa-car' => __( 'car', 'wp-travel' ),
        'fas fa-caret-down' => __( 'caret-down', 'wp-travel' ),
        'fas fa-caret-left' => __( 'caret-left', 'wp-travel' ),
        'fas fa-caret-right' => __( 'caret-right', 'wp-travel' ),
        'fas fa-caret-square-down' => __( 'caret-square-down', 'wp-travel' ),
        'far fa-caret-square-down' => __( 'caret-square-down', 'wp-travel' ),
        'fas fa-caret-square-left' => __( 'caret-square-left', 'wp-travel' ),
        'far fa-caret-square-left' => __( 'caret-square-left', 'wp-travel' ),
        'fas fa-caret-square-right' => __( 'caret-square-right', 'wp-travel' ),
        'far fa-caret-square-right' => __( 'caret-square-right', 'wp-travel' ),
        'fas fa-caret-square-up' => __( 'caret-square-up', 'wp-travel' ),
        'far fa-caret-square-up' => __( 'caret-square-up', 'wp-travel' ),
        'fas fa-caret-up' => __( 'caret-up', 'wp-travel' ),
        'fas fa-cart-arrow-down' => __( 'cart-arrow-down', 'wp-travel' ),
        'fas fa-cart-plus' => __( 'cart-plus', 'wp-travel' ),
        'fab fa-cc-amazon-pay' => __( 'cc-amazon-pay', 'wp-travel' ),
        'fab fa-cc-amex' => __( 'cc-amex', 'wp-travel' ),
        'fab fa-cc-apple-pay' => __( 'cc-apple-pay', 'wp-travel' ),
        'fab fa-cc-diners-club' => __( 'cc-diners-club', 'wp-travel' ),
        'fab fa-cc-discover' => __( 'cc-discover', 'wp-travel' ),
        'fab fa-cc-jcb' => __( 'cc-jcb', 'wp-travel' ),
        'fab fa-cc-mastercard' => __( 'cc-mastercard', 'wp-travel' ),
        'fab fa-cc-paypal' => __( 'cc-paypal', 'wp-travel' ),
        'fab fa-cc-stripe' => __( 'cc-stripe', 'wp-travel' ),
        'fab fa-cc-visa' => __( 'cc-visa', 'wp-travel' ),
        'fab fa-centercode' => __( 'centercode', 'wp-travel' ),
        'fas fa-certificate' => __( 'certificate', 'wp-travel' ),
        'fas fa-chart-area' => __( 'chart-area', 'wp-travel' ),
        'fas fa-chart-bar' => __( 'chart-bar', 'wp-travel' ),
        'far fa-chart-bar' => __( 'chart-bar', 'wp-travel' ),
        'fas fa-chart-line' => __( 'chart-line', 'wp-travel' ),
        'fas fa-chart-pie' => __( 'chart-pie', 'wp-travel' ),
        'fas fa-check' => __( 'check', 'wp-travel' ),
        'fas fa-check-circle' => __( 'check-circle', 'wp-travel' ),
        'far fa-check-circle' => __( 'check-circle', 'wp-travel' ),
        'fas fa-check-square' => __( 'check-square', 'wp-travel' ),
        'far fa-check-square' => __( 'check-square', 'wp-travel' ),
        'fas fa-chess' => __( 'chess', 'wp-travel' ),
        'fas fa-chess-bishop' => __( 'chess-bishop', 'wp-travel' ),
        'fas fa-chess-board' => __( 'chess-board', 'wp-travel' ),
        'fas fa-chess-king' => __( 'chess-king', 'wp-travel' ),
        'fas fa-chess-knight' => __( 'chess-knight', 'wp-travel' ),
        'fas fa-chess-pawn' => __( 'chess-pawn', 'wp-travel' ),
        'fas fa-chess-queen' => __( 'chess-queen', 'wp-travel' ),
        'fas fa-chess-rook' => __( 'chess-rook', 'wp-travel' ),
        'fas fa-chevron-circle-down' => __( 'chevron-circle-down', 'wp-travel' ),
        'fas fa-chevron-circle-left' => __( 'chevron-circle-left', 'wp-travel' ),
        'fas fa-chevron-circle-right' => __( 'chevron-circle-right', 'wp-travel' ),
        'fas fa-chevron-circle-up' => __( 'chevron-circle-up', 'wp-travel' ),
        'fas fa-chevron-down' => __( 'chevron-down', 'wp-travel' ),
        'fas fa-chevron-left' => __( 'chevron-left', 'wp-travel' ),
        'fas fa-chevron-right' => __( 'chevron-right', 'wp-travel' ),
        'fas fa-chevron-up' => __( 'chevron-up', 'wp-travel' ),
        'fas fa-child' => __( 'child', 'wp-travel' ),
        'fab fa-chrome' => __( 'chrome', 'wp-travel' ),
        'fas fa-circle' => __( 'circle', 'wp-travel' ),
        'far fa-circle' => __( 'circle', 'wp-travel' ),
        'fas fa-circle-notch' => __( 'circle-notch', 'wp-travel' ),
        'fas fa-clipboard' => __( 'clipboard', 'wp-travel' ),
        'far fa-clipboard' => __( 'clipboard', 'wp-travel' ),
        'fas fa-clipboard-check' => __( 'clipboard-check', 'wp-travel' ),
        'fas fa-clipboard-list' => __( 'clipboard-list', 'wp-travel' ),
        'fas fa-clock' => __( 'clock', 'wp-travel' ),
        'far fa-clock' => __( 'clock', 'wp-travel' ),
        'fas fa-clone' => __( 'clone', 'wp-travel' ),
        'far fa-clone' => __( 'clone', 'wp-travel' ),
        'fas fa-closed-captioning' => __( 'closed-captioning', 'wp-travel' ),
        'far fa-closed-captioning' => __( 'closed-captioning', 'wp-travel' ),
        'fas fa-cloud' => __( 'cloud', 'wp-travel' ),
        'fas fa-cloud-download-alt' => __( 'cloud-download-alt', 'wp-travel' ),
        'fas fa-cloud-upload-alt' => __( 'cloud-upload-alt', 'wp-travel' ),
        'fab fa-cloudscale' => __( 'cloudscale', 'wp-travel' ),
        'fab fa-cloudsmith' => __( 'cloudsmith', 'wp-travel' ),
        'fab fa-cloudversify' => __( 'cloudversify', 'wp-travel' ),
        'fas fa-code' => __( 'code', 'wp-travel' ),
        'fas fa-code-branch' => __( 'code-branch', 'wp-travel' ),
        'fab fa-codepen' => __( 'codepen', 'wp-travel' ),
        'fab fa-codiepie' => __( 'codiepie', 'wp-travel' ),
        'fas fa-coffee' => __( 'coffee', 'wp-travel' ),
        'fas fa-cog' => __( 'cog', 'wp-travel' ),
        'fas fa-cogs' => __( 'cogs', 'wp-travel' ),
        'fas fa-columns' => __( 'columns', 'wp-travel' ),
        'fas fa-comment' => __( 'comment', 'wp-travel' ),
        'far fa-comment' => __( 'comment', 'wp-travel' ),
        'fas fa-comment-alt' => __( 'comment-alt', 'wp-travel' ),
        'far fa-comment-alt' => __( 'comment-alt', 'wp-travel' ),
        'fas fa-comment-dots' => __( 'comment-dots', 'wp-travel' ),
        'fas fa-comment-slash' => __( 'comment-slash', 'wp-travel' ),
        'fas fa-comments' => __( 'comments', 'wp-travel' ),
        'far fa-comments' => __( 'comments', 'wp-travel' ),
        'fas fa-compass' => __( 'compass', 'wp-travel' ),
        'far fa-compass' => __( 'compass', 'wp-travel' ),
        'fas fa-compress' => __( 'compress', 'wp-travel' ),
        'fab fa-connectdevelop' => __( 'connectdevelop', 'wp-travel' ),
        'fab fa-contao' => __( 'contao', 'wp-travel' ),
        'fas fa-copy' => __( 'copy', 'wp-travel' ),
        'far fa-copy' => __( 'copy', 'wp-travel' ),
        'fas fa-copyright' => __( 'copyright', 'wp-travel' ),
        'far fa-copyright' => __( 'copyright', 'wp-travel' ),
        'fas fa-couch' => __( 'couch', 'wp-travel' ),
        'fab fa-cpanel' => __( 'cpanel', 'wp-travel' ),
        'fab fa-creative-commons' => __( 'creative-commons', 'wp-travel' ),
        'fas fa-credit-card' => __( 'credit-card', 'wp-travel' ),
        'far fa-credit-card' => __( 'credit-card', 'wp-travel' ),
        'fas fa-crop' => __( 'crop', 'wp-travel' ),
        'fas fa-crosshairs' => __( 'crosshairs', 'wp-travel' ),
        'fab fa-css3' => __( 'css3', 'wp-travel' ),
        'fab fa-css3-alt' => __( 'css3-alt', 'wp-travel' ),
        'fas fa-cube' => __( 'cube', 'wp-travel' ),
        'fas fa-cubes' => __( 'cubes', 'wp-travel' ),
        'fas fa-cut' => __( 'cut', 'wp-travel' ),
        'fab fa-cuttlefish' => __( 'cuttlefish', 'wp-travel' ),
        'fab fa-d-and-d' => __( 'd-and-d', 'wp-travel' ),
        'fab fa-dashcube' => __( 'dashcube', 'wp-travel' ),
        'fas fa-database' => __( 'database', 'wp-travel' ),
        'fas fa-deaf' => __( 'deaf', 'wp-travel' ),
        'fab fa-delicious' => __( 'delicious', 'wp-travel' ),
        'fab fa-deploydog' => __( 'deploydog', 'wp-travel' ),
        'fab fa-deskpro' => __( 'deskpro', 'wp-travel' ),
        'fas fa-desktop' => __( 'desktop', 'wp-travel' ),
        'fab fa-deviantart' => __( 'deviantart', 'wp-travel' ),
        'fas fa-diagnoses' => __( 'diagnoses', 'wp-travel' ),
        'fab fa-digg' => __( 'digg', 'wp-travel' ),
        'fab fa-digital-ocean' => __( 'digital-ocean', 'wp-travel' ),
        'fab fa-discord' => __( 'discord', 'wp-travel' ),
        'fab fa-discourse' => __( 'discourse', 'wp-travel' ),
        'fas fa-dna' => __( 'dna', 'wp-travel' ),
        'fab fa-dochub' => __( 'dochub', 'wp-travel' ),
        'fab fa-docker' => __( 'docker', 'wp-travel' ),
        'fas fa-dollar-sign' => __( 'dollar-sign', 'wp-travel' ),
        'fas fa-dolly' => __( 'dolly', 'wp-travel' ),
        'fas fa-dolly-flatbed' => __( 'dolly-flatbed', 'wp-travel' ),
        'fas fa-donate' => __( 'donate', 'wp-travel' ),
        'fas fa-dot-circle' => __( 'dot-circle', 'wp-travel' ),
        'far fa-dot-circle' => __( 'dot-circle', 'wp-travel' ),
        'fas fa-dove' => __( 'dove', 'wp-travel' ),
        'fas fa-download' => __( 'download', 'wp-travel' ),
        'fab fa-draft2digital' => __( 'draft2digital', 'wp-travel' ),
        'fab fa-dribbble' => __( 'dribbble', 'wp-travel' ),
        'fab fa-dribbble-square' => __( 'dribbble-square', 'wp-travel' ),
        'fab fa-dropbox' => __( 'dropbox', 'wp-travel' ),
        'fab fa-drupal' => __( 'drupal', 'wp-travel' ),
        'fab fa-dyalog' => __( 'dyalog', 'wp-travel' ),
        'fab fa-earlybirds' => __( 'earlybirds', 'wp-travel' ),
        'fab fa-edge' => __( 'edge', 'wp-travel' ),
        'fas fa-edit' => __( 'edit', 'wp-travel' ),
        'far fa-edit' => __( 'edit', 'wp-travel' ),
        'fas fa-eject' => __( 'eject', 'wp-travel' ),
        'fab fa-elementor' => __( 'elementor', 'wp-travel' ),
        'fas fa-ellipsis-h' => __( 'ellipsis-h', 'wp-travel' ),
        'fas fa-ellipsis-v' => __( 'ellipsis-v', 'wp-travel' ),
        'fab fa-ember' => __( 'ember', 'wp-travel' ),
        'fab fa-empire' => __( 'empire', 'wp-travel' ),
        'fas fa-envelope' => __( 'envelope', 'wp-travel' ),
        'far fa-envelope' => __( 'envelope', 'wp-travel' ),
        'fas fa-envelope-open' => __( 'envelope-open', 'wp-travel' ),
        'far fa-envelope-open' => __( 'envelope-open', 'wp-travel' ),
        'fas fa-envelope-square' => __( 'envelope-square', 'wp-travel' ),
        'fab fa-envira' => __( 'envira', 'wp-travel' ),
        'fas fa-eraser' => __( 'eraser', 'wp-travel' ),
        'fab fa-erlang' => __( 'erlang', 'wp-travel' ),
        'fab fa-ethereum' => __( 'ethereum', 'wp-travel' ),
        'fab fa-etsy' => __( 'etsy', 'wp-travel' ),
        'fas fa-euro-sign' => __( 'euro-sign', 'wp-travel' ),
        'fas fa-exchange-alt' => __( 'exchange-alt', 'wp-travel' ),
        'fas fa-exclamation' => __( 'exclamation', 'wp-travel' ),
        'fas fa-exclamation-circle' => __( 'exclamation-circle', 'wp-travel' ),
        'fas fa-exclamation-triangle' => __( 'exclamation-triangle', 'wp-travel' ),
        'fas fa-expand' => __( 'expand', 'wp-travel' ),
        'fas fa-expand-arrows-alt' => __( 'expand-arrows-alt', 'wp-travel' ),
        'fab fa-expeditedssl' => __( 'expeditedssl', 'wp-travel' ),
        'fas fa-external-link-alt' => __( 'external-link-alt', 'wp-travel' ),
        'fas fa-external-link-square-alt' => __( 'external-link-square-alt', 'wp-travel' ),
        'fas fa-eye' => __( 'eye', 'wp-travel' ),
        'fas fa-eye-dropper' => __( 'eye-dropper', 'wp-travel' ),
        'fas fa-eye-slash' => __( 'eye-slash', 'wp-travel' ),
        'far fa-eye-slash' => __( 'eye-slash', 'wp-travel' ),
        'fab fa-facebook' => __( 'facebook', 'wp-travel' ),
        'fab fa-facebook-f' => __( 'facebook-f', 'wp-travel' ),
        'fab fa-facebook-messenger' => __( 'facebook-messenger', 'wp-travel' ),
        'fab fa-facebook-square' => __( 'facebook-square', 'wp-travel' ),
        'fas fa-fast-backward' => __( 'fast-backward', 'wp-travel' ),
        'fas fa-fast-forward' => __( 'fast-forward', 'wp-travel' ),
        'fas fa-fax' => __( 'fax', 'wp-travel' ),
        'fas fa-female' => __( 'female', 'wp-travel' ),
        'fas fa-fighter-jet' => __( 'fighter-jet', 'wp-travel' ),
        'fas fa-file' => __( 'file', 'wp-travel' ),
        'far fa-file' => __( 'file', 'wp-travel' ),
        'fas fa-file-alt' => __( 'file-alt', 'wp-travel' ),
        'far fa-file-alt' => __( 'file-alt', 'wp-travel' ),
        'fas fa-file-archive' => __( 'file-archive', 'wp-travel' ),
        'far fa-file-archive' => __( 'file-archive', 'wp-travel' ),
        'fas fa-file-audio' => __( 'file-audio', 'wp-travel' ),
        'far fa-file-audio' => __( 'file-audio', 'wp-travel' ),
        'fas fa-file-code' => __( 'file-code', 'wp-travel' ),
        'far fa-file-code' => __( 'file-code', 'wp-travel' ),
        'fas fa-file-excel' => __( 'file-excel', 'wp-travel' ),
        'far fa-file-excel' => __( 'file-excel', 'wp-travel' ),
        'fas fa-file-image' => __( 'file-image', 'wp-travel' ),
        'far fa-file-image' => __( 'file-image', 'wp-travel' ),
        'fas fa-file-medical' => __( 'file-medical', 'wp-travel' ),
        'fas fa-file-medical-alt' => __( 'file-medical-alt', 'wp-travel' ),
        'fas fa-file-pdf' => __( 'file-pdf', 'wp-travel' ),
        'far fa-file-pdf' => __( 'file-pdf', 'wp-travel' ),
        'fas fa-file-powerpoint' => __( 'file-powerpoint', 'wp-travel' ),
        'far fa-file-powerpoint' => __( 'file-powerpoint', 'wp-travel' ),
        'fas fa-file-video' => __( 'file-video', 'wp-travel' ),
        'far fa-file-video' => __( 'file-video', 'wp-travel' ),
        'fas fa-file-word' => __( 'file-word', 'wp-travel' ),
        'far fa-file-word' => __( 'file-word', 'wp-travel' ),
        'fas fa-film' => __( 'film', 'wp-travel' ),
        'fas fa-filter' => __( 'filter', 'wp-travel' ),
        'fas fa-fire' => __( 'fire', 'wp-travel' ),
        'fas fa-fire-extinguisher' => __( 'fire-extinguisher', 'wp-travel' ),
        'fab fa-firefox' => __( 'firefox', 'wp-travel' ),
        'fas fa-first-aid' => __( 'first-aid', 'wp-travel' ),
        'fab fa-first-order' => __( 'first-order', 'wp-travel' ),
        'fab fa-firstdraft' => __( 'firstdraft', 'wp-travel' ),
        'fas fa-flag' => __( 'flag', 'wp-travel' ),
        'far fa-flag' => __( 'flag', 'wp-travel' ),
        'fas fa-flag-checkered' => __( 'flag-checkered', 'wp-travel' ),
        'fas fa-flask' => __( 'flask', 'wp-travel' ),
        'fab fa-flickr' => __( 'flickr', 'wp-travel' ),
        'fab fa-flipboard' => __( 'flipboard', 'wp-travel' ),
        'fab fa-fly' => __( 'fly', 'wp-travel' ),
        'fas fa-folder' => __( 'folder', 'wp-travel' ),
        'far fa-folder' => __( 'folder', 'wp-travel' ),
        'fas fa-folder-open' => __( 'folder-open', 'wp-travel' ),
        'far fa-folder-open' => __( 'folder-open', 'wp-travel' ),
        'fas fa-font' => __( 'font', 'wp-travel' ),
        'fab fa-font-awesome' => __( 'font-awesome', 'wp-travel' ),
        'fab fa-font-awesome-alt' => __( 'font-awesome-alt', 'wp-travel' ),
        'fab fa-font-awesome-flag' => __( 'font-awesome-flag', 'wp-travel' ),
        'fab fa-fonticons' => __( 'fonticons', 'wp-travel' ),
        'fab fa-fonticons-fi' => __( 'fonticons-fi', 'wp-travel' ),
        'fas fa-football-ball' => __( 'football-ball', 'wp-travel' ),
        'fab fa-fort-awesome' => __( 'fort-awesome', 'wp-travel' ),
        'fab fa-fort-awesome-alt' => __( 'fort-awesome-alt', 'wp-travel' ),
        'fab fa-forumbee' => __( 'forumbee', 'wp-travel' ),
        'fas fa-forward' => __( 'forward', 'wp-travel' ),
        'fab fa-foursquare' => __( 'foursquare', 'wp-travel' ),
        'fab fa-free-code-camp' => __( 'free-code-camp', 'wp-travel' ),
        'fab fa-freebsd' => __( 'freebsd', 'wp-travel' ),
        'fas fa-frown' => __( 'frown', 'wp-travel' ),
        'far fa-frown' => __( 'frown', 'wp-travel' ),
        'fas fa-futbol' => __( 'futbol', 'wp-travel' ),
        'far fa-futbol' => __( 'futbol', 'wp-travel' ),
        'fas fa-gamepad' => __( 'gamepad', 'wp-travel' ),
        'fas fa-gavel' => __( 'gavel', 'wp-travel' ),
        'fas fa-gem' => __( 'gem', 'wp-travel' ),
        'far fa-gem' => __( 'gem', 'wp-travel' ),
        'fas fa-genderless' => __( 'genderless', 'wp-travel' ),
        'fab fa-get-pocket' => __( 'get-pocket', 'wp-travel' ),
        'fab fa-gg' => __( 'gg', 'wp-travel' ),
        'fab fa-gg-circle' => __( 'gg-circle', 'wp-travel' ),
        'fas fa-gift' => __( 'gift', 'wp-travel' ),
        'fab fa-git' => __( 'git', 'wp-travel' ),
        'fab fa-git-square' => __( 'git-square', 'wp-travel' ),
        'fab fa-github' => __( 'github', 'wp-travel' ),
        'fab fa-github-alt' => __( 'github-alt', 'wp-travel' ),
        'fab fa-github-square' => __( 'github-square', 'wp-travel' ),
        'fab fa-gitkraken' => __( 'gitkraken', 'wp-travel' ),
        'fab fa-gitlab' => __( 'gitlab', 'wp-travel' ),
        'fab fa-gitter' => __( 'gitter', 'wp-travel' ),
        'fas fa-glass-martini' => __( 'glass-martini', 'wp-travel' ),
        'fab fa-glide' => __( 'glide', 'wp-travel' ),
        'fab fa-glide-g' => __( 'glide-g', 'wp-travel' ),
        'fas fa-globe' => __( 'globe', 'wp-travel' ),
        'fab fa-gofore' => __( 'gofore', 'wp-travel' ),
        'fas fa-golf-ball' => __( 'golf-ball', 'wp-travel' ),
        'fab fa-goodreads' => __( 'goodreads', 'wp-travel' ),
        'fab fa-goodreads-g' => __( 'goodreads-g', 'wp-travel' ),
        'fab fa-google' => __( 'google', 'wp-travel' ),
        'fab fa-google-drive' => __( 'google-drive', 'wp-travel' ),
        'fab fa-google-play' => __( 'google-play', 'wp-travel' ),
        'fab fa-google-plus' => __( 'google-plus', 'wp-travel' ),
        'fab fa-google-plus-g' => __( 'google-plus-g', 'wp-travel' ),
        'fab fa-google-plus-square' => __( 'google-plus-square', 'wp-travel' ),
        'fab fa-google-wallet' => __( 'google-wallet', 'wp-travel' ),
        'fas fa-graduation-cap' => __( 'graduation-cap', 'wp-travel' ),
        'fab fa-gratipay' => __( 'gratipay', 'wp-travel' ),
        'fab fa-grav' => __( 'grav', 'wp-travel' ),
        'fab fa-gripfire' => __( 'gripfire', 'wp-travel' ),
        'fab fa-grunt' => __( 'grunt', 'wp-travel' ),
        'fab fa-gulp' => __( 'gulp', 'wp-travel' ),
        'fas fa-h-square' => __( 'h-square', 'wp-travel' ),
        'fab fa-hacker-news' => __( 'hacker-news', 'wp-travel' ),
        'fab fa-hacker-news-square' => __( 'hacker-news-square', 'wp-travel' ),
        'fas fa-hand-holding' => __( 'hand-holding', 'wp-travel' ),
        'fas fa-hand-holding-heart' => __( 'hand-holding-heart', 'wp-travel' ),
        'fas fa-hand-holding-usd' => __( 'hand-holding-usd', 'wp-travel' ),
        'fas fa-hand-lizard' => __( 'hand-lizard', 'wp-travel' ),
        'far fa-hand-lizard' => __( 'hand-lizard', 'wp-travel' ),
        'fas fa-hand-paper' => __( 'hand-paper', 'wp-travel' ),
        'far fa-hand-paper' => __( 'hand-paper', 'wp-travel' ),
        'fas fa-hand-peace' => __( 'hand-peace', 'wp-travel' ),
        'far fa-hand-peace' => __( 'hand-peace', 'wp-travel' ),
        'fas fa-hand-point-down' => __( 'hand-point-down', 'wp-travel' ),
        'far fa-hand-point-down' => __( 'hand-point-down', 'wp-travel' ),
        'fas fa-hand-point-left' => __( 'hand-point-left', 'wp-travel' ),
        'far fa-hand-point-left' => __( 'hand-point-left', 'wp-travel' ),
        'fas fa-hand-point-right' => __( 'hand-point-right', 'wp-travel' ),
        'far fa-hand-point-right' => __( 'hand-point-right', 'wp-travel' ),
        'fas fa-hand-point-up' => __( 'hand-point-up', 'wp-travel' ),
        'far fa-hand-point-up' => __( 'hand-point-up', 'wp-travel' ),
        'fas fa-hand-pointer' => __( 'hand-pointer', 'wp-travel' ),
        'far fa-hand-pointer' => __( 'hand-pointer', 'wp-travel' ),
        'fas fa-hand-rock' => __( 'hand-rock', 'wp-travel' ),
        'far fa-hand-rock' => __( 'hand-rock', 'wp-travel' ),
        'fas fa-hand-scissors' => __( 'hand-scissors', 'wp-travel' ),
        'far fa-hand-scissors' => __( 'hand-scissors', 'wp-travel' ),
        'fas fa-hand-spock' => __( 'hand-spock', 'wp-travel' ),
        'far fa-hand-spock' => __( 'hand-spock', 'wp-travel' ),
        'fas fa-hands' => __( 'hands', 'wp-travel' ),
        'fas fa-hands-helping' => __( 'hands-helping', 'wp-travel' ),
        'fas fa-handshake' => __( 'handshake', 'wp-travel' ),
        'far fa-handshake' => __( 'handshake', 'wp-travel' ),
        'fas fa-hashtag' => __( 'hashtag', 'wp-travel' ),
        'fas fa-hdd' => __( 'hdd', 'wp-travel' ),
        'far fa-hdd' => __( 'hdd', 'wp-travel' ),
        'fas fa-heading' => __( 'heading', 'wp-travel' ),
        'fas fa-headphones' => __( 'headphones', 'wp-travel' ),
        'fas fa-heart' => __( 'heart', 'wp-travel' ),
        'far fa-heart' => __( 'heart', 'wp-travel' ),
        'fas fa-heartbeat' => __( 'heartbeat', 'wp-travel' ),
        'fab fa-hips' => __( 'hips', 'wp-travel' ),
        'fab fa-hire-a-helper' => __( 'hire-a-helper', 'wp-travel' ),
        'fas fa-history' => __( 'history', 'wp-travel' ),
        'fas fa-hockey-puck' => __( 'hockey-puck', 'wp-travel' ),
        'fas fa-home' => __( 'home', 'wp-travel' ),
        'fab fa-hooli' => __( 'hooli', 'wp-travel' ),
        'fas fa-hospital' => __( 'hospital', 'wp-travel' ),
        'far fa-hospital' => __( 'hospital', 'wp-travel' ),
        'fas fa-hospital-alt' => __( 'hospital-alt', 'wp-travel' ),
        'fas fa-hospital-symbol' => __( 'hospital-symbol', 'wp-travel' ),
        'fab fa-hotjar' => __( 'hotjar', 'wp-travel' ),
        'fas fa-hourglass' => __( 'hourglass', 'wp-travel' ),
        'far fa-hourglass' => __( 'hourglass', 'wp-travel' ),
        'fas fa-hourglass-end' => __( 'hourglass-end', 'wp-travel' ),
        'fas fa-hourglass-half' => __( 'hourglass-half', 'wp-travel' ),
        'fas fa-hourglass-start' => __( 'hourglass-start', 'wp-travel' ),
        'fab fa-houzz' => __( 'houzz', 'wp-travel' ),
        'fab fa-html5' => __( 'html5', 'wp-travel' ),
        'fab fa-hubspot' => __( 'hubspot', 'wp-travel' ),
        'fas fa-i-cursor' => __( 'i-cursor', 'wp-travel' ),
        'fas fa-id-badge' => __( 'id-badge', 'wp-travel' ),
        'far fa-id-badge' => __( 'id-badge', 'wp-travel' ),
        'fas fa-id-card' => __( 'id-card', 'wp-travel' ),
        'far fa-id-card' => __( 'id-card', 'wp-travel' ),
        'fas fa-id-card-alt' => __( 'id-card-alt', 'wp-travel' ),
        'fas fa-image' => __( 'image', 'wp-travel' ),
        'far fa-image' => __( 'image', 'wp-travel' ),
        'fas fa-images' => __( 'images', 'wp-travel' ),
        'far fa-images' => __( 'images', 'wp-travel' ),
        'fab fa-imdb' => __( 'imdb', 'wp-travel' ),
        'fas fa-inbox' => __( 'inbox', 'wp-travel' ),
        'fas fa-indent' => __( 'indent', 'wp-travel' ),
        'fas fa-industry' => __( 'industry', 'wp-travel' ),
        'fas fa-info' => __( 'info', 'wp-travel' ),
        'fas fa-info-circle' => __( 'info-circle', 'wp-travel' ),
        'fab fa-instagram' => __( 'instagram', 'wp-travel' ),
        'fab fa-internet-explorer' => __( 'internet-explorer', 'wp-travel' ),
        'fab fa-ioxhost' => __( 'ioxhost', 'wp-travel' ),
        'fas fa-italic' => __( 'italic', 'wp-travel' ),
        'fab fa-itunes' => __( 'itunes', 'wp-travel' ),
        'fab fa-itunes-note' => __( 'itunes-note', 'wp-travel' ),
        'fab fa-java' => __( 'java', 'wp-travel' ),
        'fab fa-jenkins' => __( 'jenkins', 'wp-travel' ),
        'fab fa-joget' => __( 'joget', 'wp-travel' ),
        'fab fa-joomla' => __( 'joomla', 'wp-travel' ),
        'fab fa-js' => __( 'js', 'wp-travel' ),
        'fab fa-js-square' => __( 'js-square', 'wp-travel' ),
        'fab fa-jsfiddle' => __( 'jsfiddle', 'wp-travel' ),
        'fas fa-key' => __( 'key', 'wp-travel' ),
        'fas fa-keyboard' => __( 'keyboard', 'wp-travel' ),
        'far fa-keyboard' => __( 'keyboard', 'wp-travel' ),
        'fab fa-keycdn' => __( 'keycdn', 'wp-travel' ),
        'fab fa-kickstarter' => __( 'kickstarter', 'wp-travel' ),
        'fab fa-kickstarter-k' => __( 'kickstarter-k', 'wp-travel' ),
        'fab fa-korvue' => __( 'korvue', 'wp-travel' ),
        'fas fa-language' => __( 'language', 'wp-travel' ),
        'fas fa-laptop' => __( 'laptop', 'wp-travel' ),
        'fab fa-laravel' => __( 'laravel', 'wp-travel' ),
        'fab fa-lastfm' => __( 'lastfm', 'wp-travel' ),
        'fab fa-lastfm-square' => __( 'lastfm-square', 'wp-travel' ),
        'fas fa-leaf' => __( 'leaf', 'wp-travel' ),
        'fab fa-leanpub' => __( 'leanpub', 'wp-travel' ),
        'fas fa-lemon' => __( 'lemon', 'wp-travel' ),
        'far fa-lemon' => __( 'lemon', 'wp-travel' ),
        'fab fa-less' => __( 'less', 'wp-travel' ),
        'fas fa-level-down-alt' => __( 'level-down-alt', 'wp-travel' ),
        'fas fa-level-up-alt' => __( 'level-up-alt', 'wp-travel' ),
        'fas fa-life-ring' => __( 'life-ring', 'wp-travel' ),
        'far fa-life-ring' => __( 'life-ring', 'wp-travel' ),
        'fas fa-lightbulb' => __( 'lightbulb', 'wp-travel' ),
        'far fa-lightbulb' => __( 'lightbulb', 'wp-travel' ),
        'fab fa-line' => __( 'line', 'wp-travel' ),
        'fas fa-link' => __( 'link', 'wp-travel' ),
        'fab fa-linkedin' => __( 'linkedin', 'wp-travel' ),
        'fab fa-linkedin-in' => __( 'linkedin-in', 'wp-travel' ),
        'fab fa-linode' => __( 'linode', 'wp-travel' ),
        'fab fa-linux' => __( 'linux', 'wp-travel' ),
        'fas fa-lira-sign' => __( 'lira-sign', 'wp-travel' ),
        'fas fa-list' => __( 'list', 'wp-travel' ),
        'fas fa-list-alt' => __( 'list-alt', 'wp-travel' ),
        'far fa-list-alt' => __( 'list-alt', 'wp-travel' ),
        'fas fa-list-ol' => __( 'list-ol', 'wp-travel' ),
        'fas fa-list-ul' => __( 'list-ul', 'wp-travel' ),
        'fas fa-location-arrow' => __( 'location-arrow', 'wp-travel' ),
        'fas fa-lock' => __( 'lock', 'wp-travel' ),
        'fas fa-lock-open' => __( 'lock-open', 'wp-travel' ),
        'fas fa-long-arrow-alt-down' => __( 'long-arrow-alt-down', 'wp-travel' ),
        'fas fa-long-arrow-alt-left' => __( 'long-arrow-alt-left', 'wp-travel' ),
        'fas fa-long-arrow-alt-right' => __( 'long-arrow-alt-right', 'wp-travel' ),
        'fas fa-long-arrow-alt-up' => __( 'long-arrow-alt-up', 'wp-travel' ),
        'fas fa-low-vision' => __( 'low-vision', 'wp-travel' ),
        'fab fa-lyft' => __( 'lyft', 'wp-travel' ),
        'fab fa-magento' => __( 'magento', 'wp-travel' ),
        'fas fa-magic' => __( 'magic', 'wp-travel' ),
        'fas fa-magnet' => __( 'magnet', 'wp-travel' ),
        'fas fa-male' => __( 'male', 'wp-travel' ),
        'fas fa-map' => __( 'map', 'wp-travel' ),
        'far fa-map' => __( 'map', 'wp-travel' ),
        'fas fa-map-marker' => __( 'map-marker', 'wp-travel' ),
        'fas fa-map-marker-alt' => __( 'map-marker-alt', 'wp-travel' ),
        'fas fa-map-pin' => __( 'map-pin', 'wp-travel' ),
        'fas fa-map-signs' => __( 'map-signs', 'wp-travel' ),
        'fas fa-mars' => __( 'mars', 'wp-travel' ),
        'fas fa-mars-double' => __( 'mars-double', 'wp-travel' ),
        'fas fa-mars-stroke' => __( 'mars-stroke', 'wp-travel' ),
        'fas fa-mars-stroke-h' => __( 'mars-stroke-h', 'wp-travel' ),
        'fas fa-mars-stroke-v' => __( 'mars-stroke-v', 'wp-travel' ),
        'fab fa-maxcdn' => __( 'maxcdn', 'wp-travel' ),
        'fab fa-medapps' => __( 'medapps', 'wp-travel' ),
        'fab fa-medium' => __( 'medium', 'wp-travel' ),
        'fab fa-medium-m' => __( 'medium-m', 'wp-travel' ),
        'fas fa-medkit' => __( 'medkit', 'wp-travel' ),
        'fab fa-medrt' => __( 'medrt', 'wp-travel' ),
        'fab fa-meetup' => __( 'meetup', 'wp-travel' ),
        'fas fa-meh' => __( 'meh', 'wp-travel' ),
        'far fa-meh' => __( 'meh', 'wp-travel' ),
        'fas fa-mercury' => __( 'mercury', 'wp-travel' ),
        'fas fa-microchip' => __( 'microchip', 'wp-travel' ),
        'fas fa-microphone' => __( 'microphone', 'wp-travel' ),
        'fas fa-microphone-slash' => __( 'microphone-slash', 'wp-travel' ),
        'fab fa-microsoft' => __( 'microsoft', 'wp-travel' ),
        'fas fa-minus' => __( 'minus', 'wp-travel' ),
        'fas fa-minus-circle' => __( 'minus-circle', 'wp-travel' ),
        'fas fa-minus-square' => __( 'minus-square', 'wp-travel' ),
        'far fa-minus-square' => __( 'minus-square', 'wp-travel' ),
        'fab fa-mix' => __( 'mix', 'wp-travel' ),
        'fab fa-mixcloud' => __( 'mixcloud', 'wp-travel' ),
        'fab fa-mizuni' => __( 'mizuni', 'wp-travel' ),
        'fas fa-mobile' => __( 'mobile', 'wp-travel' ),
        'fas fa-mobile-alt' => __( 'mobile-alt', 'wp-travel' ),
        'fab fa-modx' => __( 'modx', 'wp-travel' ),
        'fab fa-monero' => __( 'monero', 'wp-travel' ),
        'fas fa-money-bill-alt' => __( 'money-bill-alt', 'wp-travel' ),
        'far fa-money-bill-alt' => __( 'money-bill-alt', 'wp-travel' ),
        'fas fa-moon' => __( 'moon', 'wp-travel' ),
        'far fa-moon' => __( 'moon', 'wp-travel' ),
        'fas fa-motorcycle' => __( 'motorcycle', 'wp-travel' ),
        'fas fa-mouse-pointer' => __( 'mouse-pointer', 'wp-travel' ),
        'fas fa-music' => __( 'music', 'wp-travel' ),
        'fab fa-napster' => __( 'napster', 'wp-travel' ),
        'fas fa-neuter' => __( 'neuter', 'wp-travel' ),
        'fas fa-newspaper' => __( 'newspaper', 'wp-travel' ),
        'far fa-newspaper' => __( 'newspaper', 'wp-travel' ),
        'fab fa-nintendo-switch' => __( 'nintendo-switch', 'wp-travel' ),
        'fab fa-node' => __( 'node', 'wp-travel' ),
        'fab fa-node-js' => __( 'node-js', 'wp-travel' ),
        'fas fa-notes-medical' => __( 'notes-medical', 'wp-travel' ),
        'fab fa-npm' => __( 'npm', 'wp-travel' ),
        'fab fa-ns8' => __( 'ns8', 'wp-travel' ),
        'fab fa-nutritionix' => __( 'nutritionix', 'wp-travel' ),
        'fas fa-object-group' => __( 'object-group', 'wp-travel' ),
        'far fa-object-group' => __( 'object-group', 'wp-travel' ),
        'fas fa-object-ungroup' => __( 'object-ungroup', 'wp-travel' ),
        'far fa-object-ungroup' => __( 'object-ungroup', 'wp-travel' ),
        'fab fa-odnoklassniki' => __( 'odnoklassniki', 'wp-travel' ),
        'fab fa-odnoklassniki-square' => __( 'odnoklassniki-square', 'wp-travel' ),
        'fab fa-opencart' => __( 'opencart', 'wp-travel' ),
        'fab fa-openid' => __( 'openid', 'wp-travel' ),
        'fab fa-opera' => __( 'opera', 'wp-travel' ),
        'fab fa-optin-monster' => __( 'optin-monster', 'wp-travel' ),
        'fab fa-osi' => __( 'osi', 'wp-travel' ),
        'fas fa-outdent' => __( 'outdent', 'wp-travel' ),
        'fab fa-page4' => __( 'page4', 'wp-travel' ),
        'fab fa-pagelines' => __( 'pagelines', 'wp-travel' ),
        'fas fa-paint-brush' => __( 'paint-brush', 'wp-travel' ),
        'fab fa-palfed' => __( 'palfed', 'wp-travel' ),
        'fas fa-pallet' => __( 'pallet', 'wp-travel' ),
        'fas fa-paper-plane' => __( 'paper-plane', 'wp-travel' ),
        'far fa-paper-plane' => __( 'paper-plane', 'wp-travel' ),
        'fas fa-paperclip' => __( 'paperclip', 'wp-travel' ),
        'fas fa-parachute-box' => __( 'parachute-box', 'wp-travel' ),
        'fas fa-paragraph' => __( 'paragraph', 'wp-travel' ),
        'fas fa-paste' => __( 'paste', 'wp-travel' ),
        'fab fa-patreon' => __( 'patreon', 'wp-travel' ),
        'fas fa-pause' => __( 'pause', 'wp-travel' ),
        'fas fa-pause-circle' => __( 'pause-circle', 'wp-travel' ),
        'far fa-pause-circle' => __( 'pause-circle', 'wp-travel' ),
        'fas fa-paw' => __( 'paw', 'wp-travel' ),
        'fab fa-paypal' => __( 'paypal', 'wp-travel' ),
        'fas fa-pen-square' => __( 'pen-square', 'wp-travel' ),
        'fas fa-pencil-alt' => __( 'pencil-alt', 'wp-travel' ),
        'fas fa-people-carry' => __( 'people-carry', 'wp-travel' ),
        'fas fa-percent' => __( 'percent', 'wp-travel' ),
        'fab fa-periscope' => __( 'periscope', 'wp-travel' ),
        'fab fa-phabricator' => __( 'phabricator', 'wp-travel' ),
        'fab fa-phoenix-framework' => __( 'phoenix-framework', 'wp-travel' ),
        'fas fa-phone' => __( 'phone', 'wp-travel' ),
        'fas fa-phone-slash' => __( 'phone-slash', 'wp-travel' ),
        'fas fa-phone-square' => __( 'phone-square', 'wp-travel' ),
        'fas fa-phone-volume' => __( 'phone-volume', 'wp-travel' ),
        'fab fa-php' => __( 'php', 'wp-travel' ),
        'fab fa-pied-piper' => __( 'pied-piper', 'wp-travel' ),
        'fab fa-pied-piper-alt' => __( 'pied-piper-alt', 'wp-travel' ),
        'fab fa-pied-piper-hat' => __( 'pied-piper-hat', 'wp-travel' ),
        'fab fa-pied-piper-pp' => __( 'pied-piper-pp', 'wp-travel' ),
        'fas fa-piggy-bank' => __( 'piggy-bank', 'wp-travel' ),
        'fas fa-pills' => __( 'pills', 'wp-travel' ),
        'fab fa-pinterest' => __( 'pinterest', 'wp-travel' ),
        'fab fa-pinterest-p' => __( 'pinterest-p', 'wp-travel' ),
        'fab fa-pinterest-square' => __( 'pinterest-square', 'wp-travel' ),
        'fas fa-plane' => __( 'plane', 'wp-travel' ),
        'fas fa-play' => __( 'play', 'wp-travel' ),
        'fas fa-play-circle' => __( 'play-circle', 'wp-travel' ),
        'far fa-play-circle' => __( 'play-circle', 'wp-travel' ),
        'fab fa-playstation' => __( 'playstation', 'wp-travel' ),
        'fas fa-plug' => __( 'plug', 'wp-travel' ),
        'fas fa-plus' => __( 'plus', 'wp-travel' ),
        'fas fa-plus-circle' => __( 'plus-circle', 'wp-travel' ),
        'fas fa-plus-square' => __( 'plus-square', 'wp-travel' ),
        'far fa-plus-square' => __( 'plus-square', 'wp-travel' ),
        'fas fa-podcast' => __( 'podcast', 'wp-travel' ),
        'fas fa-poo' => __( 'poo', 'wp-travel' ),
        'fas fa-pound-sign' => __( 'pound-sign', 'wp-travel' ),
        'fas fa-power-off' => __( 'power-off', 'wp-travel' ),
        'fas fa-prescription-bottle' => __( 'prescription-bottle', 'wp-travel' ),
        'fas fa-prescription-bottle-alt' => __( 'prescription-bottle-alt', 'wp-travel' ),
        'fas fa-print' => __( 'print', 'wp-travel' ),
        'fas fa-procedures' => __( 'procedures', 'wp-travel' ),
        'fab fa-product-hunt' => __( 'product-hunt', 'wp-travel' ),
        'fab fa-pushed' => __( 'pushed', 'wp-travel' ),
        'fas fa-puzzle-piece' => __( 'puzzle-piece', 'wp-travel' ),
        'fab fa-python' => __( 'python', 'wp-travel' ),
        'fab fa-qq' => __( 'qq', 'wp-travel' ),
        'fas fa-qrcode' => __( 'qrcode', 'wp-travel' ),
        'fas fa-question' => __( 'question', 'wp-travel' ),
        'fas fa-question-circle' => __( 'question-circle', 'wp-travel' ),
        'far fa-question-circle' => __( 'question-circle', 'wp-travel' ),
        'fas fa-quidditch' => __( 'quidditch', 'wp-travel' ),
        'fab fa-quinscape' => __( 'quinscape', 'wp-travel' ),
        'fab fa-quora' => __( 'quora', 'wp-travel' ),
        'fas fa-quote-left' => __( 'quote-left', 'wp-travel' ),
        'fas fa-quote-right' => __( 'quote-right', 'wp-travel' ),
        'fas fa-random' => __( 'random', 'wp-travel' ),
        'fab fa-ravelry' => __( 'ravelry', 'wp-travel' ),
        'fab fa-react' => __( 'react', 'wp-travel' ),
        'fab fa-readme' => __( 'readme', 'wp-travel' ),
        'fab fa-rebel' => __( 'rebel', 'wp-travel' ),
        'fas fa-recycle' => __( 'recycle', 'wp-travel' ),
        'fab fa-red-river' => __( 'red-river', 'wp-travel' ),
        'fab fa-reddit' => __( 'reddit', 'wp-travel' ),
        'fab fa-reddit-alien' => __( 'reddit-alien', 'wp-travel' ),
        'fab fa-reddit-square' => __( 'reddit-square', 'wp-travel' ),
        'fas fa-redo' => __( 'redo', 'wp-travel' ),
        'fas fa-redo-alt' => __( 'redo-alt', 'wp-travel' ),
        'fas fa-registered' => __( 'registered', 'wp-travel' ),
        'far fa-registered' => __( 'registered', 'wp-travel' ),
        'fab fa-rendact' => __( 'rendact', 'wp-travel' ),
        'fab fa-renren' => __( 'renren', 'wp-travel' ),
        'fas fa-reply' => __( 'reply', 'wp-travel' ),
        'fas fa-reply-all' => __( 'reply-all', 'wp-travel' ),
        'fab fa-replyd' => __( 'replyd', 'wp-travel' ),
        'fab fa-resolving' => __( 'resolving', 'wp-travel' ),
        'fas fa-retweet' => __( 'retweet', 'wp-travel' ),
        'fas fa-ribbon' => __( 'ribbon', 'wp-travel' ),
        'fas fa-road' => __( 'road', 'wp-travel' ),
        'fas fa-rocket' => __( 'rocket', 'wp-travel' ),
        'fab fa-rocketchat' => __( 'rocketchat', 'wp-travel' ),
        'fab fa-rockrms' => __( 'rockrms', 'wp-travel' ),
        'fas fa-rss' => __( 'rss', 'wp-travel' ),
        'fas fa-rss-square' => __( 'rss-square', 'wp-travel' ),
        'fas fa-ruble-sign' => __( 'ruble-sign', 'wp-travel' ),
        'fas fa-rupee-sign' => __( 'rupee-sign', 'wp-travel' ),
        'fab fa-safari' => __( 'safari', 'wp-travel' ),
        'fab fa-sass' => __( 'sass', 'wp-travel' ),
        'fas fa-save' => __( 'save', 'wp-travel' ),
        'far fa-save' => __( 'save', 'wp-travel' ),
        'fab fa-schlix' => __( 'schlix', 'wp-travel' ),
        'fab fa-scribd' => __( 'scribd', 'wp-travel' ),
        'fas fa-search' => __( 'search', 'wp-travel' ),
        'fas fa-search-minus' => __( 'search-minus', 'wp-travel' ),
        'fas fa-search-plus' => __( 'search-plus', 'wp-travel' ),
        'fab fa-searchengin' => __( 'searchengin', 'wp-travel' ),
        'fas fa-seedling' => __( 'seedling', 'wp-travel' ),
        'fab fa-sellcast' => __( 'sellcast', 'wp-travel' ),
        'fab fa-sellsy' => __( 'sellsy', 'wp-travel' ),
        'fas fa-server' => __( 'server', 'wp-travel' ),
        'fab fa-servicestack' => __( 'servicestack', 'wp-travel' ),
        'fas fa-share' => __( 'share', 'wp-travel' ),
        'fas fa-share-alt' => __( 'share-alt', 'wp-travel' ),
        'fas fa-share-alt-square' => __( 'share-alt-square', 'wp-travel' ),
        'fas fa-share-square' => __( 'share-square', 'wp-travel' ),
        'far fa-share-square' => __( 'share-square', 'wp-travel' ),
        'fas fa-shekel-sign' => __( 'shekel-sign', 'wp-travel' ),
        'fas fa-shield-alt' => __( 'shield-alt', 'wp-travel' ),
        'fas fa-ship' => __( 'ship', 'wp-travel' ),
        'fas fa-shipping-fast' => __( 'shipping-fast', 'wp-travel' ),
        'fab fa-shirtsinbulk' => __( 'shirtsinbulk', 'wp-travel' ),
        'fas fa-shopping-bag' => __( 'shopping-bag', 'wp-travel' ),
        'fas fa-shopping-basket' => __( 'shopping-basket', 'wp-travel' ),
        'fas fa-shopping-cart' => __( 'shopping-cart', 'wp-travel' ),
        'fas fa-shower' => __( 'shower', 'wp-travel' ),
        'fas fa-sign' => __( 'sign', 'wp-travel' ),
        'fas fa-sign-in-alt' => __( 'sign-in-alt', 'wp-travel' ),
        'fas fa-sign-language' => __( 'sign-language', 'wp-travel' ),
        'fas fa-sign-out-alt' => __( 'sign-out-alt', 'wp-travel' ),
        'fas fa-signal' => __( 'signal', 'wp-travel' ),
        'fab fa-simplybuilt' => __( 'simplybuilt', 'wp-travel' ),
        'fab fa-sistrix' => __( 'sistrix', 'wp-travel' ),
        'fas fa-sitemap' => __( 'sitemap', 'wp-travel' ),
        'fab fa-skyatlas' => __( 'skyatlas', 'wp-travel' ),
        'fab fa-skype' => __( 'skype', 'wp-travel' ),
        'fab fa-slack' => __( 'slack', 'wp-travel' ),
        'fab fa-slack-hash' => __( 'slack-hash', 'wp-travel' ),
        'fas fa-sliders-h' => __( 'sliders-h', 'wp-travel' ),
        'fab fa-slideshare' => __( 'slideshare', 'wp-travel' ),
        'fas fa-smile' => __( 'smile', 'wp-travel' ),
        'far fa-smile' => __( 'smile', 'wp-travel' ),
        'fas fa-smoking' => __( 'smoking', 'wp-travel' ),
        'fab fa-snapchat' => __( 'snapchat', 'wp-travel' ),
        'fab fa-snapchat-ghost' => __( 'snapchat-ghost', 'wp-travel' ),
        'fab fa-snapchat-square' => __( 'snapchat-square', 'wp-travel' ),
        'fas fa-snowflake' => __( 'snowflake', 'wp-travel' ),
        'far fa-snowflake' => __( 'snowflake', 'wp-travel' ),
        'fas fa-sort' => __( 'sort', 'wp-travel' ),
        'fas fa-sort-alpha-down' => __( 'sort-alpha-down', 'wp-travel' ),
        'fas fa-sort-alpha-up' => __( 'sort-alpha-up', 'wp-travel' ),
        'fas fa-sort-amount-down' => __( 'sort-amount-down', 'wp-travel' ),
        'fas fa-sort-amount-up' => __( 'sort-amount-up', 'wp-travel' ),
        'fas fa-sort-down' => __( 'sort-down', 'wp-travel' ),
        'fas fa-sort-numeric-down' => __( 'sort-numeric-down', 'wp-travel' ),
        'fas fa-sort-numeric-up' => __( 'sort-numeric-up', 'wp-travel' ),
        'fas fa-sort-up' => __( 'sort-up', 'wp-travel' ),
        'fab fa-soundcloud' => __( 'soundcloud', 'wp-travel' ),
        'fas fa-space-shuttle' => __( 'space-shuttle', 'wp-travel' ),
        'fab fa-speakap' => __( 'speakap', 'wp-travel' ),
        'fas fa-spinner' => __( 'spinner', 'wp-travel' ),
        'fab fa-spotify' => __( 'spotify', 'wp-travel' ),
        'fas fa-square' => __( 'square', 'wp-travel' ),
        'far fa-square' => __( 'square', 'wp-travel' ),
        'fas fa-square-full' => __( 'square-full', 'wp-travel' ),
        'fab fa-stack-exchange' => __( 'stack-exchange', 'wp-travel' ),
        'fab fa-stack-overflow' => __( 'stack-overflow', 'wp-travel' ),
        'fas fa-star' => __( 'star', 'wp-travel' ),
        'far fa-star' => __( 'star', 'wp-travel' ),
        'fas fa-star-half' => __( 'star-half', 'wp-travel' ),
        'far fa-star-half' => __( 'star-half', 'wp-travel' ),
        'fab fa-staylinked' => __( 'staylinked', 'wp-travel' ),
        'fab fa-steam' => __( 'steam', 'wp-travel' ),
        'fab fa-steam-square' => __( 'steam-square', 'wp-travel' ),
        'fab fa-steam-symbol' => __( 'steam-symbol', 'wp-travel' ),
        'fas fa-step-backward' => __( 'step-backward', 'wp-travel' ),
        'fas fa-step-forward' => __( 'step-forward', 'wp-travel' ),
        'fas fa-stethoscope' => __( 'stethoscope', 'wp-travel' ),
        'fab fa-sticker-mule' => __( 'sticker-mule', 'wp-travel' ),
        'fas fa-sticky-note' => __( 'sticky-note', 'wp-travel' ),
        'far fa-sticky-note' => __( 'sticky-note', 'wp-travel' ),
        'fas fa-stop' => __( 'stop', 'wp-travel' ),
        'fas fa-stop-circle' => __( 'stop-circle', 'wp-travel' ),
        'far fa-stop-circle' => __( 'stop-circle', 'wp-travel' ),
        'fas fa-stopwatch' => __( 'stopwatch', 'wp-travel' ),
        'fab fa-strava' => __( 'strava', 'wp-travel' ),
        'fas fa-street-view' => __( 'street-view', 'wp-travel' ),
        'fas fa-strikethrough' => __( 'strikethrough', 'wp-travel' ),
        'fab fa-stripe' => __( 'stripe', 'wp-travel' ),
        'fab fa-stripe-s' => __( 'stripe-s', 'wp-travel' ),
        'fab fa-studiovinari' => __( 'studiovinari', 'wp-travel' ),
        'fab fa-stumbleupon' => __( 'stumbleupon', 'wp-travel' ),
        'fab fa-stumbleupon-circle' => __( 'stumbleupon-circle', 'wp-travel' ),
        'fas fa-subscript' => __( 'subscript', 'wp-travel' ),
        'fas fa-subway' => __( 'subway', 'wp-travel' ),
        'fas fa-suitcase' => __( 'suitcase', 'wp-travel' ),
        'fas fa-sun' => __( 'sun', 'wp-travel' ),
        'far fa-sun' => __( 'sun', 'wp-travel' ),
        'fab fa-superpowers' => __( 'superpowers', 'wp-travel' ),
        'fas fa-superscript' => __( 'superscript', 'wp-travel' ),
        'fab fa-supple' => __( 'supple', 'wp-travel' ),
        'fas fa-sync' => __( 'sync', 'wp-travel' ),
        'fas fa-sync-alt' => __( 'sync-alt', 'wp-travel' ),
        'fas fa-syringe' => __( 'syringe', 'wp-travel' ),
        'fas fa-table' => __( 'table', 'wp-travel' ),
        'fas fa-table-tennis' => __( 'table-tennis', 'wp-travel' ),
        'fas fa-tablet' => __( 'tablet', 'wp-travel' ),
        'fas fa-tablet-alt' => __( 'tablet-alt', 'wp-travel' ),
        'fas fa-tablets' => __( 'tablets', 'wp-travel' ),
        'fas fa-tachometer-alt' => __( 'tachometer-alt', 'wp-travel' ),
        'fas fa-tag' => __( 'tag', 'wp-travel' ),
        'fas fa-tags' => __( 'tags', 'wp-travel' ),
        'fas fa-tape' => __( 'tape', 'wp-travel' ),
        'fas fa-tasks' => __( 'tasks', 'wp-travel' ),
        'fas fa-taxi' => __( 'taxi', 'wp-travel' ),
        'fab fa-telegram' => __( 'telegram', 'wp-travel' ),
        'fab fa-telegram-plane' => __( 'telegram-plane', 'wp-travel' ),
        'fab fa-tencent-weibo' => __( 'tencent-weibo', 'wp-travel' ),
        'fas fa-terminal' => __( 'terminal', 'wp-travel' ),
        'fas fa-text-height' => __( 'text-height', 'wp-travel' ),
        'fas fa-text-width' => __( 'text-width', 'wp-travel' ),
        'fas fa-th' => __( 'th', 'wp-travel' ),
        'fas fa-th-large' => __( 'th-large', 'wp-travel' ),
        'fas fa-th-list' => __( 'th-list', 'wp-travel' ),
        'fab fa-themeisle' => __( 'themeisle', 'wp-travel' ),
        'fas fa-thermometer' => __( 'thermometer', 'wp-travel' ),
        'fas fa-thermometer-empty' => __( 'thermometer-empty', 'wp-travel' ),
        'fas fa-thermometer-full' => __( 'thermometer-full', 'wp-travel' ),
        'fas fa-thermometer-half' => __( 'thermometer-half', 'wp-travel' ),
        'fas fa-thermometer-quarter' => __( 'thermometer-quarter', 'wp-travel' ),
        'fas fa-thermometer-three-quarters' => __( 'thermometer-three-quarters', 'wp-travel' ),
        'fas fa-thumbs-down' => __( 'thumbs-down', 'wp-travel' ),
        'far fa-thumbs-down' => __( 'thumbs-down', 'wp-travel' ),
        'fas fa-thumbs-up' => __( 'thumbs-up', 'wp-travel' ),
        'far fa-thumbs-up' => __( 'thumbs-up', 'wp-travel' ),
        'fas fa-thumbtack' => __( 'thumbtack', 'wp-travel' ),
        'fas fa-ticket-alt' => __( 'ticket-alt', 'wp-travel' ),
        'fas fa-times' => __( 'times', 'wp-travel' ),
        'fas fa-times-circle' => __( 'times-circle', 'wp-travel' ),
        'far fa-times-circle' => __( 'times-circle', 'wp-travel' ),
        'fas fa-tint' => __( 'tint', 'wp-travel' ),
        'fas fa-toggle-off' => __( 'toggle-off', 'wp-travel' ),
        'fas fa-toggle-on' => __( 'toggle-on', 'wp-travel' ),
        'fas fa-trademark' => __( 'trademark', 'wp-travel' ),
        'fas fa-train' => __( 'train', 'wp-travel' ),
        'fas fa-transgender' => __( 'transgender', 'wp-travel' ),
        'fas fa-transgender-alt' => __( 'transgender-alt', 'wp-travel' ),
        'fas fa-trash' => __( 'trash', 'wp-travel' ),
        'fas fa-trash-alt' => __( 'trash-alt', 'wp-travel' ),
        'far fa-trash-alt' => __( 'trash-alt', 'wp-travel' ),
        'fas fa-tree' => __( 'tree', 'wp-travel' ),
        'fab fa-trello' => __( 'trello', 'wp-travel' ),
        'fab fa-tripadvisor' => __( 'tripadvisor', 'wp-travel' ),
        'fas fa-trophy' => __( 'trophy', 'wp-travel' ),
        'fas fa-truck' => __( 'truck', 'wp-travel' ),
        'fas fa-truck-loading' => __( 'truck-loading', 'wp-travel' ),
        'fas fa-truck-moving' => __( 'truck-moving', 'wp-travel' ),
        'fas fa-tty' => __( 'tty', 'wp-travel' ),
        'fab fa-tumblr' => __( 'tumblr', 'wp-travel' ),
        'fab fa-tumblr-square' => __( 'tumblr-square', 'wp-travel' ),
        'fas fa-tv' => __( 'tv', 'wp-travel' ),
        'fab fa-twitch' => __( 'twitch', 'wp-travel' ),
        'fab fa-twitter' => __( 'twitter', 'wp-travel' ),
        'fab fa-twitter-square' => __( 'twitter-square', 'wp-travel' ),
        'fab fa-typo3' => __( 'typo3', 'wp-travel' ),
        'fab fa-uber' => __( 'uber', 'wp-travel' ),
        'fab fa-uikit' => __( 'uikit', 'wp-travel' ),
        'fas fa-umbrella' => __( 'umbrella', 'wp-travel' ),
        'fas fa-underline' => __( 'underline', 'wp-travel' ),
        'fas fa-undo' => __( 'undo', 'wp-travel' ),
        'fas fa-undo-alt' => __( 'undo-alt', 'wp-travel' ),
        'fab fa-uniregistry' => __( 'uniregistry', 'wp-travel' ),
        'fas fa-universal-access' => __( 'universal-access', 'wp-travel' ),
        'fas fa-university' => __( 'university', 'wp-travel' ),
        'fas fa-unlink' => __( 'unlink', 'wp-travel' ),
        'fas fa-unlock' => __( 'unlock', 'wp-travel' ),
        'fas fa-unlock-alt' => __( 'unlock-alt', 'wp-travel' ),
        'fab fa-untappd' => __( 'untappd', 'wp-travel' ),
        'fas fa-upload' => __( 'upload', 'wp-travel' ),
        'fab fa-usb' => __( 'usb', 'wp-travel' ),
        'fas fa-user' => __( 'user', 'wp-travel' ),
        'far fa-user' => __( 'user', 'wp-travel' ),
        'fas fa-user-circle' => __( 'user-circle', 'wp-travel' ),
        'far fa-user-circle' => __( 'user-circle', 'wp-travel' ),
        'fas fa-user-md' => __( 'user-md', 'wp-travel' ),
        'fas fa-user-plus' => __( 'user-plus', 'wp-travel' ),
        'fas fa-user-secret' => __( 'user-secret', 'wp-travel' ),
        'fas fa-user-times' => __( 'user-times', 'wp-travel' ),
        'fas fa-users' => __( 'users', 'wp-travel' ),
        'fab fa-ussunnah' => __( 'ussunnah', 'wp-travel' ),
        'fas fa-utensil-spoon' => __( 'utensil-spoon', 'wp-travel' ),
        'fas fa-utensils' => __( 'utensils', 'wp-travel' ),
        'fab fa-vaadin' => __( 'vaadin', 'wp-travel' ),
        'fas fa-venus' => __( 'venus', 'wp-travel' ),
        'fas fa-venus-double' => __( 'venus-double', 'wp-travel' ),
        'fas fa-venus-mars' => __( 'venus-mars', 'wp-travel' ),
        'fab fa-viacoin' => __( 'viacoin', 'wp-travel' ),
        'fab fa-viadeo' => __( 'viadeo', 'wp-travel' ),
        'fab fa-viadeo-square' => __( 'viadeo-square', 'wp-travel' ),
        'fas fa-vial' => __( 'vial', 'wp-travel' ),
        'fas fa-vials' => __( 'vials', 'wp-travel' ),
        'fab fa-viber' => __( 'viber', 'wp-travel' ),
        'fas fa-video' => __( 'video', 'wp-travel' ),
        'fas fa-video-slash' => __( 'video-slash', 'wp-travel' ),
        'fab fa-vimeo' => __( 'vimeo', 'wp-travel' ),
        'fab fa-vimeo-square' => __( 'vimeo-square', 'wp-travel' ),
        'fab fa-vimeo-v' => __( 'vimeo-v', 'wp-travel' ),
        'fab fa-vine' => __( 'vine', 'wp-travel' ),
        'fab fa-vk' => __( 'vk', 'wp-travel' ),
        'fab fa-vnv' => __( 'vnv', 'wp-travel' ),
        'fas fa-volleyball-ball' => __( 'volleyball-ball', 'wp-travel' ),
        'fas fa-volume-down' => __( 'volume-down', 'wp-travel' ),
        'fas fa-volume-off' => __( 'volume-off', 'wp-travel' ),
        'fas fa-volume-up' => __( 'volume-up', 'wp-travel' ),
        'fab fa-vuejs' => __( 'vuejs', 'wp-travel' ),
        'fas fa-warehouse' => __( 'warehouse', 'wp-travel' ),
        'fab fa-weibo' => __( 'weibo', 'wp-travel' ),
        'fas fa-weight' => __( 'weight', 'wp-travel' ),
        'fab fa-weixin' => __( 'weixin', 'wp-travel' ),
        'fab fa-whatsapp' => __( 'whatsapp', 'wp-travel' ),
        'fab fa-whatsapp-square' => __( 'whatsapp-square', 'wp-travel' ),
        'fas fa-wheelchair' => __( 'wheelchair', 'wp-travel' ),
        'fab fa-whmcs' => __( 'whmcs', 'wp-travel' ),
        'fas fa-wifi' => __( 'wifi', 'wp-travel' ),
        'fab fa-wikipedia-w' => __( 'wikipedia-w', 'wp-travel' ),
        'fas fa-window-close' => __( 'window-close', 'wp-travel' ),
        'far fa-window-close' => __( 'window-close', 'wp-travel' ),
        'fas fa-window-maximize' => __( 'window-maximize', 'wp-travel' ),
        'far fa-window-maximize' => __( 'window-maximize', 'wp-travel' ),
        'fas fa-window-minimize' => __( 'window-minimize', 'wp-travel' ),
        'far fa-window-minimize' => __( 'window-minimize', 'wp-travel' ),
        'fas fa-window-restore' => __( 'window-restore', 'wp-travel' ),
        'far fa-window-restore' => __( 'window-restore', 'wp-travel' ),
        'fab fa-windows' => __( 'windows', 'wp-travel' ),
        'fas fa-wine-glass' => __( 'wine-glass', 'wp-travel' ),
        'fas fa-won-sign' => __( 'won-sign', 'wp-travel' ),
        'fab fa-wordpress' => __( 'wordpress', 'wp-travel' ),
        'fab fa-wordpress-simple' => __( 'wordpress-simple', 'wp-travel' ),
        'fab fa-wpbeginner' => __( 'wpbeginner', 'wp-travel' ),
        'fab fa-wpexplorer' => __( 'wpexplorer', 'wp-travel' ),
        'fab fa-wpforms' => __( 'wpforms', 'wp-travel' ),
        'fas fa-wrench' => __( 'wrench', 'wp-travel' ),
        'fas fa-x-ray' => __( 'x-ray', 'wp-travel' ),
        'fab fa-xbox' => __( 'xbox', 'wp-travel' ),
        'fab fa-xing' => __( 'xing', 'wp-travel' ),
        'fab fa-xing-square' => __( 'xing-square', 'wp-travel' ),
        'fab fa-y-combinator' => __( 'y-combinator', 'wp-travel' ),
        'fab fa-yahoo' => __( 'yahoo', 'wp-travel' ),
        'fab fa-yandex' => __( 'yandex', 'wp-travel' ),
        'fab fa-yandex-international' => __( 'yandex-international', 'wp-travel' ),
        'fab fa-yelp' => __( 'yelp', 'wp-travel' ),
        'fas fa-yen-sign' => __( 'yen-sign', 'wp-travel' ),
        'fab fa-yoast' => __( 'yoast', 'wp-travel' ),
        'fab fa-youtube' => __( 'youtube', 'wp-travel' ),
        'fab fa-youtube-square' => __( 'youtube-square', 'wp-travel' ),
    );
}

/**
 * Check if current page is WP Travel admin page.
 *
 * @param  array   $pages Pages to check.
 * @return boolean
 */
function wp_travel_is_admin_page( $pages = array() ) {
	if ( ! is_admin() ) {
		return false;
	}
	$screen = get_current_screen();
	$wp_travel_pages[] = array( 'itinerary-booking_page_settings' );
	if ( ! empty( $pages ) ) {
		foreach( $pages as $page ) {
			if ( 'settings' === $page ) {
				$settings_allowed_screens[] = 'itinerary-booking_page_settings';
				if ( in_array( $screen->id, $settings_allowed_screens, true ) ) {
					return true;
				}
			}
		}
	} else if ( in_array( $screen->id, $wp_travel_pages, true ) ) {
		return true;
	}

	// $allowed_screens[] = 'itinerary-booking_page_wp-travel-marketplace';
	return false;
}

function wp_travel_get_pricing_option_list() {
    $type = array(
        'single-price' => __( 'Single Price', 'wp-travel' ),
        'multiple-price' => __( 'Multiple Price', 'wp-travel' ),
        // 'custom-price' => __( 'Custom', 'wp-travel' ),
    );

    return apply_filters( 'wp_travel_pricing_option_list', $type );
}

function wp_travel_upsell_message( $args ) {
    $title = isset( $args['title'] ) ? $args['title'] : __( 'WP Travel', 'wp-travel' );
    $content = isset( $args['content'] ) ? $args['content'] : '';
    $link = isset( $args['link'] ) ? $args['link'] : '';
    $link_label = isset( $args['link_label'] ) ? $args['link_label'] : __( 'View WP Travel Addons', 'wp-travel' );
    $link2 = isset( $args['link2'] ) ? $args['link2'] : '';
    $link2_label = isset( $args['link2_label'] ) ? $args['link2_label'] : __( 'View WP Travel Addons', 'wp-travel' );
    ?>
    <div class="wp-travel-upsell-message">
		<div class="wp-travel-pro-feature-notice clearfix">
			<h4><?php echo esc_html( $title ); ?></h4>
			<p><?php echo esc_html( $content ); ?></p>
            <a target="_blank" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $link_label ); ?></a> 
            <?php if ( ! empty( $link2 ) ) : ?>
                <a target="_blank" href="<?php echo esc_url( $link2 ); ?>"><?php echo esc_html( $link2_label ); ?></a> 
            <?php endif; ?>       
        </div>
	</div>
    <?php 
}
