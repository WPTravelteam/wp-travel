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
	add_action( 'untrash_post', 'wp_travel_clear_booking_count_transient_untrash', 10 ); // @since 2.0.3
	if ( version_compare( WP_TRAVEL_VERSION, '1.0.6', '>' ) ) {
		wp_travel_upgrade_to_110();
	}
	if ( version_compare( WP_TRAVEL_VERSION, '1.2.0', '>' ) ) {
		include_once sprintf( '%s/upgrade/update-121.php', WP_TRAVEL_ABSPATH );
	}
	if ( version_compare( WP_TRAVEL_VERSION, '1.3.6', '>' ) ) {
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
		'travel-base-pro'     => array(
			'name'       => __( 'Travel Base Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://wptravel.io/wp-content/themes/wptravel/images/travel-base-pro.png',
			'demo_url'   => 'https://wptravel.io/demo/?demo=travel-base-pro',
			'detail_url' => 'https://themepalace.com/downloads/travel-base-pro/',
		),
		'travel-base'         => array(
			'name'       => __( 'Travel Base', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://wptravel.io/wp-content/themes/wptravel/images/travel-base-free.png',
			'demo_url'   => 'https://wptravel.io/demo/?demo=travel-base-pro',
			'detail_url' => 'https://themepalace.com/downloads/travel-base/',
		),
		'travel-ultimate-pro' => array(
			'name'       => __( 'Travel Ultimate Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://wptravel.io/wp-content/themes/wptravel/images/travel-ultimate-pro.png',
			'demo_url'   => 'https://wptravel.io/demo/?demo=travel-ultimate-pro',
			'detail_url' => 'https://themepalace.com/downloads/travel-ultimate-pro/',
		),
		'travel-ultimate'     => array(
			'name'       => __( 'Travel Ultimate', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://wptravel.io/wp-content/themes/wptravel/images/travel-ultimate-free.png',
			'demo_url'   => 'https://wptravel.io/demo/?demo=travel-ultimate-pro',
			'detail_url' => 'https://themepalace.com/downloads/travel-ultimate/',
		),
		'pleased-pro'         => array(
			'name'       => __( 'Pleased Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://wptravel.io/wp-content/themes/wptravel/images/pleased-pro.png',
			'demo_url'   => 'https://wptravel.io/demo/?demo=pleased-pro',
			'detail_url' => 'https://themepalace.com/downloads/pleased-pro/',
		),
		'pleased'             => array(
			'name'       => __( 'Pleased', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://wptravel.io/wp-content/themes/wptravel/images/pleased-free.png',
			'demo_url'   => 'https://wptravel.io/demo/?demo=pleased-pro',
			'detail_url' => 'https://themepalace.com/downloads/pleased/',
		),
		'travel-gem-pro'      => array(
			'name'       => __( 'Travel Gem Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://wptravel.io/wp-content/themes/wptravel/images/travel-gem-pro.png',
			'demo_url'   => 'https://wptravel.io/demo/?demo=travel-gem-pro',
			'detail_url' => 'https://themepalace.com/downloads/travel-gem-pro/',
		),
		'travel-gem'          => array(
			'name'       => __( 'Travel Gem', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://wptravel.io/wp-content/themes/wptravel/images/travel-gem-free.png',
			'demo_url'   => 'https://wptravel.io/demo/?demo=travel-gem-pro',
			'detail_url' => 'https://themepalace.com/downloads/travel-gem/',
		),
		'tourable-pro'        => array(
			'name'       => __( 'Tourable Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://wptravel.io/wp-content/themes/wptravel/images/tourable-pro.png',
			'demo_url'   => 'https://wptravel.io/demo/?demo=tourable-pro',
			'detail_url' => 'https://themepalace.com/downloads/tourable-pro/',
		),
		'tourable'            => array(
			'name'       => __( 'Tourable', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://wptravel.io/wp-content/themes/wptravel/images/tourable-free.png',
			'demo_url'   => 'https://wptravel.io/demo/?demo=tourable-pro',
			'detail_url' => 'https://themepalace.com/downloads/tourable/',
		),
		'travel-log'          => array(
			'name'       => __( 'Travel Log', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => plugins_url( '/wp-travel/assets/images/devices_web.png' ),
			'demo_url'   => 'https://wptravel.io/demo/?demo=travel-log',
			'detail_url' => 'http://wensolutions.com/themes/travel-log/',
		),
	);

	$info_btn_text     = __( 'View Demo', 'wp-travel' );
	$download_btn_text = __( 'View Detail', 'wp-travel' );

	?>
	<div class="wrap">
		<div id="poststuff">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Marketplace', 'wp-travel' ); ?></h1>
			<div id="post-body">
				<div class="wp-travel-marketplace-tab-wrap">
					<ul>

						<li class=""><a href="#tabs-1"><?php esc_html_e( 'Addons', 'wp-travel' ); ?></a></li>
						<?php if ( $addons_data ) : ?>
							<li class=""><a href="#tabs-2"><?php esc_html_e( 'Themes', 'wp-travel' ); ?></a></li>
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
								<div class="single-module full-pro-section">
									<div class="single-module-image">
										<a href="http://wptravel.io/?post_type=download&amp;p=12906" target="_blank">
										<img width="423" height="237" src="https://wptravel.io/wp-content/themes/wptravel/images/wp-travel-pro-banner.png" class="" alt="">
										</a>
									</div>
									<div class="single-module-content clearfix">
										<h4 class="text-title">
											<a href="https://wptravel.io/wp-travel-pro/" target="_blank">
											<span class="dashicons-wp-travel">
											</span>WP Travel PRO</a>
										</h4>

										<p>With WP Travel Pro you can get all premium feature of WP Travel in a single package. No hassle of installing separate add-ons, no hassle of managing different license and above all have hundreds of dollars.</p>
										<a class="btn-default pull-left" href="https://wptravel.io/wp-travel-pro/" target="_blank">View Detail</a>
										<a class="btn-default buy-btn" href="https://themepalace.com/download-checkout/?edd_action=add_to_cart&amp;download_id=95078" target="_blank">Buy Now</a>
									</div>
								</div>
							<?php
							foreach ( $addons_data as $key => $product ) :
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
										<a class="btn-default pull-left" href="<?php echo esc_url( $prod_info->link ); ?>" target="_blank"><?php esc_html_e( 'View Detail', 'wp-travel' ); ?></a>
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
				<span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Support', 'wp-travel' ); ?></span>
				<span class="toggle-indicator-acc" aria-hidden="true"></span>
			</button>
			<h2 class="hndle ui-sortable-handle">
				<span><?php esc_html_e( 'Support', 'wp-travel' ); ?></span>
			</h2>
			<div class="inside">

			<div class="thumbnail">
				<img src="<?php echo plugins_url( '/wp-travel/assets/images/support-image.png' ); ?>">
					<p class="text-justify"><?php esc_html_e( 'Click Below for support.', 'wp-travel' ); ?> </p>
					<p class="text-center"><a href="http://wptravel.io/support/" target="_blank" class="button button-primary"><?php esc_html_e( 'Get Support Here', 'wp-travel' ); ?></a></p>
			</div>

			</div>
		</div>

		<div id="wp_travel_doc_block_id" class="postbox ">
			<button type="button" class="handlediv" aria-expanded="true">
				<span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Documentation', 'wp-travel' ); ?></span>
				<span class="toggle-indicator" aria-hidden="true"></span>
			</button>
			<h2 class="hndle ui-sortable-handle">
				<span><?php esc_html_e( 'Documentation', 'wp-travel' ); ?></span>
			</h2>
			<div class="inside">

				<div class="thumbnail">
					<img src="<?php echo plugins_url( '/wp-travel/assets/images/docico.png' ); ?>">
						<p class="text-justify"><?php esc_html_e( 'Click Below for our full Documentation about logo slider.', 'wp-travel' ); ?> </p>
						<p class="text-center"><a href="http://wptravel.io/documentations/" target="_blank" class="button button-primary"><?php esc_html_e( 'Get Documentation Here', 'wp-travel' ); ?></a></p>
				</div>

			</div>
		</div>

		<div id="wp_travel_review_block_id" class="postbox ">
			<button type="button" class="handlediv" aria-expanded="true">
				<span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Reviews', 'wp-travel' ); ?></span>
				<span class="toggle-indicator" aria-hidden="true"></span>
			</button>
			<h2 class="hndle ui-sortable-handle">
				<span><?php esc_html_e( 'Reviews', 'wp-travel' ); ?></span>
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
					<h5>
					<?php
					esc_html_e(
						'"The plugin is very intuitive and fresh.
The layout fits well into theme with flexibility to different shortcodes.
Its great plugin for travel or tour agent websites."',
						'wp-travel'
					)
					?>
						</h5>
					<span class="by"><strong> <a href="https://profiles.wordpress.org/muzdat" target="_blank"><?php esc_html_e( 'muzdat', 'wp-travel' ); ?></a></strong></span>

				</div>
				<div class="thumbnail last">
					<h5><?php esc_html_e( '"Please fill free to leave us a review, if you found this plugin helpful."', 'wp-travel' ); ?></h5>
					<p class="text-center"><a href="https://wordpress.org/plugins/wp-travel/#reviews" target="_blank" class="button button-primary"><?php esc_html_e( 'Leave a Review', 'wp-travel' ); ?></a></p>
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
			'title'       => __( 'Need to add your downloads ?', 'wp-travel' ),
			'content'     => __( 'By upgrading to Pro, you can add your downloads in all of your trips !', 'wp-travel' ),
			'link'        => 'https://wptravel.io/wp-travel-pro/',
			'link_label'  => __( 'Get WP Travel Pro', 'wp-travel' ),
			'link2'       => 'https://wptravel.io/downloads/wp-travel-downloads/',
			'link2_label' => __( 'Get WP Travel Downloads Addon', 'wp-travel' ),
		);
		wp_travel_upsell_message( $args );
	endif;
}

// Upsell Message Callback for Custom Filters submenu. WP Travel > Custom Filters.
function wp_travel_custom_filters_upsell() {
	?>
	<h2><?php echo esc_html( 'Custom Filters' ); ?></h2>
	<?php
	if ( ! class_exists( 'WP_Travel_Custom_Filters_Core' ) ) :
		$args = array(
			'title'       => __( 'Need custom search filters?', 'wp-travel' ),
			'content'     => __( 'By upgrading to Pro, you can add your custom search filter fields to search trips !', 'wp-travel' ),
			'link'        => 'https://wptravel.io/wp-travel-pro/',
			'link_label'  => __( 'Get WP Travel Pro', 'wp-travel' ),
			'link2'       => 'https://wptravel.io/downloads/wp-travel-custom-filters/',
			'link2_label' => __( 'Get WP Travel Custom Filters Addon', 'wp-travel' ),
		);
		wp_travel_upsell_message( $args );
	endif;
}

/**
 * Modify Admin Footer Message.
 */
function wp_travel_modify_admin_footer_admin_settings_page() {

	printf( __( 'Love %1$1s, Consider leaving us a %2$2s rating, also checkout %3$3s . A huge thanks in advance!', 'wp-travel' ), '<strong>WP Travel ?</strong>', '<a target="_blank" href="https://wordpress.org/support/plugin/wp-travel/reviews/">★★★★★</a>', '<a target="_blank" href="https://wptravel.io/downloads/">WP Travel add-ons</a>' );
}
/**
 * Modify Admin Footer Message.
 */
function wp_travel_modify_admin_footer_version() {

	printf( __( 'WP Travel version: %s', 'wp-travel' ), '<strong>' . WP_TRAVEL_VERSION . '</strong>' );

}
/**
 * Add Footer Custom Text Hook.
 */
function wp_travel_doc_support_footer_custom_text() {

	$screen = get_current_screen();

	if ( WP_TRAVEL_POST_TYPE === $screen->post_type ) {

		add_filter( 'admin_footer_text', 'wp_travel_modify_admin_footer_admin_settings_page' );
		add_filter( 'update_footer', 'wp_travel_modify_admin_footer_version', 11 );
	}
}

add_action( 'current_screen', 'wp_travel_doc_support_footer_custom_text' );

function wp_travel_clear_booking_count_transient( $booking_id ) {
	if ( ! $booking_id ) {
		return;
	}
	global $post_type;
	if ( 'itinerary-booking' !== $post_type ) {
		return;
	}
	$trip_id = get_post_meta( $booking_id, 'wp_travel_post_id', true );
	delete_site_transient( "_transient_wt_booking_count_{$trip_id}" );
	do_action( 'wp_travel_action_after_trash_booking', $booking_id ); // @since 2.0.3 to update current booking inventory data.
}

/**
 * Restore Booking on untrash booking.
 *
 * @param Number $booking_id
 */
function wp_travel_clear_booking_count_transient_untrash( $booking_id ) {
	if ( ! $booking_id ) {
		return;
	}
	global $post_type;
	if ( 'itinerary-booking' !== $post_type ) {
		return;
	}
	$trip_id = get_post_meta( $booking_id, 'wp_travel_post_id', true );
	delete_site_transient( "_transient_wt_booking_count_{$trip_id}" );
	do_action( 'wp_travel_action_after_untrash_booking', $booking_id ); // @since 2.0.3 to update current booking inventory data.
}

function wp_travel_get_booking_count( $itinerary_id ) {
	if ( ! $itinerary_id ) {
		return 0;
	}
	global $wpdb;
	$booking_count = get_site_transient( "_transient_wt_booking_count_{$itinerary_id}" );
	if ( ! $booking_count ) {
		$booking_count = 0;
		$query         = "SELECT count( itinerary_id ) as booking_count FROM {$wpdb->posts} P
		JOIN ( Select distinct( post_id ), meta_value as itinerary_id from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_post_id' and meta_value > 0 ) I on P.ID = I.post_id  where post_type='itinerary-booking' and post_status='publish' and itinerary_id={$itinerary_id} group by itinerary_id";
		$results       = $wpdb->get_row( $query );
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
	$comment = isset( $itinerary_columns['comments'] ) ? $itinerary_columns['comments'] : '';
	$date    = $itinerary_columns['date'];
	unset( $itinerary_columns['date'] );
	unset( $itinerary_columns['comments'] );

	$itinerary_columns['booking_count'] = __( 'Booking', 'wp-travel' );
	$itinerary_columns['featured']      = __( 'Featured', 'wp-travel' );
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
 * @param  int    $id          Post ID.
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
		'booking_count' => 'booking_count',
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
		$vars = array_merge(
			$vars,
			array(
				'meta_key' => 'wp_travel_booking_count',
				'orderby'  => 'meta_value',
			)
		);
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
	$post_id         = intval( $_POST['post_id'] );
	$featured_status = esc_attr( get_post_meta( $post_id, 'wp_travel_featured', true ) );
	$new_status      = $featured_status == 'yes' ? 'no' : 'yes';
	update_post_meta( $post_id, 'wp_travel_featured', $new_status );
	echo json_encode(
		array(
			'ID'         => $post_id,
			'new_status' => $new_status,
		)
	);
	die();
}
add_action( 'wp_ajax_wp_travel_featured_post', 'wp_travel_featured_admin_ajax' );

function wp_travel_publish_metabox() {
	global $post;
	if ( get_post_type( $post ) === 'itinerary-booking' ) {
		?>
		<div class="misc-pub-section misc-pub-booking-status">
			<?php
			$status    = wp_travel_get_booking_status();
			$label_key = get_post_meta( $post->ID, 'wp_travel_booking_status', true );
			?>

			<label for="wp-travel-post-id"><?php esc_html_e( 'Booking Status', 'wp-travel' ); ?></label>
			<select id="wp_travel_booking_status" name="wp_travel_booking_status" >
			<?php foreach ( $status as $value => $st ) : ?>
				<option value="<?php echo esc_html( $value ); ?>" <?php selected( $value, $label_key ); ?>>
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
	$itineraries        = get_posts(
		array(
			'post_type'   => 'itineraries',
			'post_status' => 'publish',
		)
	);
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

	$booking_columns['payment_mode']   = __( 'Payment Mode', 'wp-travel' );
	$booking_columns['payment_status'] = __( 'Payment Status', 'wp-travel' );
	$booking_columns['date']           = $date;
	return $booking_columns;
}



/**
 * Add data to custom column.
 *
 * @param  String $column_name Custom column name.
 * @param  int    $id          Post ID.
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
			$booking_option = get_post_meta( $payment_id, 'wp_travel_booking_option', true );

			$label_key = get_post_meta( $payment_id, 'wp_travel_payment_status', true );
			if ( ! $label_key ) {
				$label_key = 'N/A';
				update_post_meta( $payment_id, 'wp_travel_payment_status', $label_key );
			}
			$status = wp_travel_get_payment_status();
			echo '<span class="wp-travel-status wp-travel-payment-status" style="background: ' . esc_attr( $status[ $label_key ]['color'], 'wp-travel' ) . ' ">' . esc_attr( $status[ $label_key ]['text'], 'wp-travel' ) . '</span>';
			break;
		case 'payment_mode':
			$mode       = wp_travel_get_payment_mode();
			$payment_id = get_post_meta( $id, 'wp_travel_payment_id', true );
			$label_key  = get_post_meta( $payment_id, 'wp_travel_payment_mode', true );

			if ( ! $label_key ) {
				$label_key          = 'N/A';
				$is_partial_enabled = get_post_meta( $payment_id, 'wp_travel_is_partial_payment', true );
				if ( ! $is_partial_enabled ) {
					$label_key = 'full';
				}
				update_post_meta( $payment_id, 'wp_travel_payment_mode', $label_key );
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
		$vars = array_merge(
			$vars,
			array(
				'meta_key' => 'wp_travel_payment_status',
				'orderby'  => 'meta_value',
			)
		);
	}
	if ( isset( $vars['orderby'] ) && 'payment_mode' == $vars['orderby'] ) {
		$vars = array_merge(
			$vars,
			array(
				'meta_key' => 'wp_travel_payment_mode',
				'orderby'  => 'meta_value',
			)
		);
	}
	return $vars;
}
add_filter( 'request', 'wp_travel_booking_payment_column_orderby' );

/**
 * Create a page and store the ID in an option.
 *
 * @param mixed  $slug Slug for the new page
 * @param string $option Option name to store the page's ID
 * @param string $page_title (default: '') Title for the new page
 * @param string $page_content (default: '') Content for the new page
 * @param int    $post_parent (default: 0) Parent for the new page
 * @return int page ID
 */
function wp_travel_create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0 ) {
	global $wpdb;

	$option_value = get_option( $option );

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
			'ID'          => $page_id,
			'post_status' => 'publish',
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
		$page_id   = wp_insert_post( $page_data );
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
	if ( 0 == $tour_extras->publish ) {
		ob_start();

		if ( $table_row ) :
			?>
			<td>
			<?php
		else :
			?>
			<div class="one-third">
			<?php
		endif;
		?>
		<label for=""><?php echo esc_html( 'Trip Extras', 'wp-travel-coupon-pro' ); ?></label>
		<?php
		if ( $table_row ) :
			?>
			</td>
			<td>
			<?php
		else :
			?>
			</div>
			<div class="two-third">
			<?php
		endif;
		?>
		<p class="wp-travel-trip-extra-notice good" id="pass-strength-result"><span class="dashicons dashicons-info"></span> Please <a href="post-new.php?post_type=tour-extras">Click here </a> to add Trip Extra first.</p>
		<?php
		if ( $table_row ) :
			?>
			</td>
			<?php
		else :
			?>
			</div>
			<?php
		endif;

		$data = ob_get_clean();
		return $data;
	}

	if ( empty( $post_id ) || empty( $fetch_key ) ) {
		return;
	}

	$name = 'wp_travel_tour_extras[]';
	if ( $context && 'pricing_options' === $context ) {
		$pricing_options = get_post_meta( $post_id, 'wp_travel_pricing_options', true );
		$trip_extras     = isset( $pricing_options[ $fetch_key ]['tour_extras'] ) && ! empty( $pricing_options[ $fetch_key ]['tour_extras'] ) ? $pricing_options[ $fetch_key ]['tour_extras'] : false;
		$name            = 'wp_travel_pricing_options[' . $fetch_key . '][tour_extras][]';
	} elseif ( ! $context && 'wp_travel_tour_extras' === $fetch_key ) {
		$trip_extras = get_post_meta( $post_id, 'wp_travel_tour_extras', true );
	}

	$restricted_trips = ( $trip_extras ) ? $trip_extras : array();

	$itineraries = wp_travel_get_tour_extras_array();
	ob_start();

	if ( $table_row ) :
		?>
		<td>
		<?php
	else :
		?>
		<div><div class="one-third">
		<?php
	endif;
	?>
		<label for=""><?php echo esc_html__( 'Trip Extras', 'wp-travel' ); ?></label>
	<?php
	if ( $table_row ) :
		?>
		</td><td>
		<?php
	else :
		?>
		</div><div class="two-third">
		<?php
	endif;
	?>
	
	<div class="custom-multi-select">
		<?php
		$count_options_data   = count( $restricted_trips );
		$count_itineraries    = count( $itineraries );
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

					$checked            = '';
					$selecte_list_class = '';

					if ( in_array( $key, $restricted_trips ) ) {
						$checked            = 'checked=checked';
						$selecte_list_class = 'selected';
					}
					?>
					<li class="wp-travel-multi-inner <?php echo esc_attr( $selecte_list_class ); ?>">
						<label class="checkbox wp-travel-multi-inner ">
							<input <?php echo esc_attr( $checked ); ?>  name="<?php echo esc_attr( $name ); ?>" type="checkbox" id="wp-travel-multi-input-<?php echo esc_attr( $key ); ?>" class="wp-travel-multi-inner multiselect-value" value="<?php echo esc_attr( $key ); ?>">  <?php echo esc_html( $iti ); ?>
						</label>
					</li>
				<?php } ?>
			</ul>
		</span>
		<?php if ( ! class_exists( 'WP_Travel_Tour_Extras_Core' ) ) : ?>
			<p class="description">
				<?php printf( __( 'Need advance Trip Extras options? %1$s GET PRO%2$s', 'wp-travel' ), '<a href="https://wptravel.io/wp-travel-pro/" target="_blank" class="wp-travel-upsell-badge">', '</a>' ); ?>
			</p>
		<?php endif; ?>

	</div>
	<?php
	if ( $table_row ) :
		?>
		</td>
		<?php
	else :
		?>
		</div></div>
		<?php
	endif;
	// @since 2.0.3
	do_action( 'wp_travel_trip_extras_fields', $post_id, $context, $fetch_key, $table_row );

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
			<?php
			if ( ! class_exists( 'WP_Travel_Tour_Extras_Core' ) ) :
				$args = array(
					'title'       => __( 'Want to use above pro features?', 'wp-travel' ),
					'content'     => __( 'By upgrading to Pro, you can get features with gallery, detail extras page in Front-End and more !', 'wp-travel' ),
					'link'        => 'https://wptravel.io/wp-travel-pro/',
					'link_label'  => __( 'Get WP Travel Pro', 'wp-travel' ),
					'link2'       => 'https://themepalace.com/downloads/wp-travel-tour-extras/',
					'link2_label' => __( 'Get Tour Extras Addon', 'wp-travel' ),
				);
				wp_travel_upsell_message( $args );
				endif;
			?>
		</td>
	</tr>

	<?php
}

/**
 * Check if current page is WP Travel admin page.
 *
 * @param  array $pages Pages to check.
 * @return boolean
 */
function wp_travel_is_admin_page( $pages = array() ) {
	if ( ! is_admin() ) {
		return false;
	}
	$screen            = get_current_screen();
	$wp_travel_pages[] = array( 'itinerary-booking_page_settings' );
	if ( ! empty( $pages ) ) {
		foreach ( $pages as $page ) {
			if ( 'settings' === $page ) {
				$settings_allowed_screens[] = 'itinerary-booking_page_settings';
				if ( in_array( $screen->id, $settings_allowed_screens, true ) ) {
					return true;
				}
			}
		}
	} elseif ( in_array( $screen->id, $wp_travel_pages, true ) ) {
		return true;
	}

	// $allowed_screens[] = 'itinerary-booking_page_wp-travel-marketplace';
	return false;
}

function wp_travel_get_pricing_option_list() {
	$type = array(
		'multiple-price' => __( 'Multiple Price', 'wp-travel' ),
	);

	$hide_single_for_new_user = get_option( 'wp_travel_user_after_multiple_pricing_category' );  // @since 3.0.0
	
	if ( 'yes' !== $hide_single_for_new_user ) { // Single pricing is only available for old user who is using it.
		$type['single-price'] = __( 'Single Price', 'wp-travel' );
	}

	return apply_filters( 'wp_travel_pricing_option_list', $type );
}

function wp_travel_upsell_message( $args ) {
	$defaults   = array(
		'type'               => array( 'wp-travel-pro' ),
		'title'              => __( 'Get WP Travel PRO', 'wp-travel' ),
		'content'            => __( 'Get addon for Payment, Trip Extras, Inventory Management, Field Editor and other premium features.', 'wp-travel' ),
		'link'               => 'https://wptravel.io/wp-travel-pro/',
		'link_label'         => __( 'Get WP Travel Pro', 'wp-travel' ),
		'link2'              => 'https://wptravel.io/downloads/',
		'link2_label'        => __( 'Check all Add-ons', 'wp-travel' ),
		'link3'              => '',
		'link3_label'        => __( 'View WP Travel Addons', 'wp-travel' ),
		'main_wrapper_class' => array( 'wp-travel-upsell-message-wide' ),
	);
	$args       = wp_parse_args( $args, $defaults );
	$add_groups = array(
		'maps'     => array( 'wp-travel-here-map' ),
		'payments' => array( 'wp-travel-paypal-express-checkout' ),
	);
	$types      = $args['type'];
	if ( is_string( $types ) ) {
		$types = isset( $add_groups[ $args['type'] ] ) ? $add_groups[ $args['type'] ] : $types;
	}

	$types[]     = 'wp-travel-pro';
	$show_upsell = apply_filters( 'wp_travel_show_upsell_message', true, $types );

	if ( ! $show_upsell ) {
		return;
	}
	?>
	<div class="wp-travel-upsell-message <?php echo esc_attr( implode( ' ', $args['main_wrapper_class'] ) ); ?>">
		<div class="wp-travel-pro-feature-notice clearfix">
			<div class="section-one">
				<h4><?php echo esc_html( $args['title'] ); ?></h4>
				<p><?php echo $args['content']; ?></p>
			</div>
			<div class="section-two">
			<div class="buy-pro-action buy-pro">
				<a target="_blank" href="<?php echo esc_url( $args['link'] ); ?>" class="action-btn" ><?php echo esc_html( $args['link_label'] ); ?></a>
				<?php if ( ! empty( $args['link2'] ) ) : ?>
				<p>
					<?php esc_html_e( 'or', 'wp-travel' ); ?> <a target="_blank" class="link-default" href="<?php echo esc_url( $args['link2'] ); ?>"><?php echo esc_html( $args['link2_label'] ); ?></a>
				</p> 
				<?php endif; ?>  
				</div>
				<?php if ( ! empty( $args['link3'] ) ) : ?>
				<div class="buy-pro-action action2">
					<a target="_blank" href="<?php echo esc_url( $args['link3'] ); ?>" class="action-btn" ><?php echo esc_html( $args['link3_label'] ); ?></a> 
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
}
