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

	$addons_data = get_transient( 'wp_travel_marketplace_addons_list' );

	if ( ! $addons_data ) {

		$addons_data = file_get_contents( 'https://wptravel.io/edd-api/products/' );
		set_transient( 'wp_travel_marketplace_addons_list', $addons_data );

	}

	if ( ! empty( $addons_data ) ) :

		$addons_data = json_decode( $addons_data );
		$addons_data = $addons_data->products;

	endif;
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
							<div class="single-module">
								<div class="single-module-image">
									<a href="http://wensolutions.com/themes/travel-log/" target="_blank">
									<img width="423" height="237" src="<?php echo plugins_url( '/wp-travel/assets/images/devices_web.png' ) ?>" class="" alt="" >
									</a>
								</div>
								<div class="single-module-content clearfix">
									<h4 class="text-title"><a href="http://wensolutions.com/themes/travel-log/" target="_blank">
									<span class="dashicons-wp-travel">
									</span><?php esc_html_e( 'Travel Log', 'wp-travel' ); ?></a></h4>
									<a class="btn-default pull-left" href="http://wensolutions.com/themes/travel-log/" target="_blank"><?php esc_html_e( 'View Detail', 'wp-travel' ); ?></a>
									<a class="btn-default pull-right" href="https://downloads.wordpress.org/theme/travel-log.zip" target="_blank"><?php esc_html_e( 'Download', 'wp-travel' ); ?></a>
								</div>
							</div>
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

function docs_support_page_callback(){
	?>

	<div class="wrap">
		<div id="poststuff">
			<div id="docs-and-support-page">
				<div class="about-header">
	                <div class="about-text WP-Travel-about-text">
		                <div class="left-side-section">
		                	<strong><?php esc_html_e( 'Welcome to WP Travel.', 'wp-travel' ); ?></strong>
		                		<p><?php esc_html_e( 'Thanks for installing and we hope you will enjoy using WP Travel.', 'wp-travel' ); ?> </p>
		                		<p><?php esc_html_e( 'We strongly recommend you to install', 'wp-travel' ); ?> <a class="link-simple" href="https://wordpress.org/themes/travel-log/" target="_blank"><?php esc_html_e( 'Travel Log', 'wp-travel' ) ?></a> <?php esc_html_e( 'theme for best Front End experiences.', 'wp-travel' ); ?></p>
		                        <p class="WP-Travel-actions">
		                        <a class="button button-primary button-large" href="<?php echo home_url();?>/wp-admin/post-new.php?post_type=<?php echo WP_TRAVEL_POST_TYPE; ?>" target="_blank"><?php esc_html_e( 'Add New Trips For You Site', 'wp-travel' ); ?></a>
		                        <span><?php esc_html_e( 'OR', 'wp-travel' ); ?></span>
		                        <a href="http://wptravel.io/demo" class="link-simple" target="_blank"> <strong><?php esc_html_e( 'Visit Demo', 'wp-travel' ); ?></strong></a>
		                    </p>
	                    </div>
	                    <div class="WP-Travel-badge">
		                	<span class="dashicons-wp-travel">

							</span>
		                	<p>
		        				<?php esc_html_e( 'Version:', 'wp-travel' ); ?> <?php echo WP_TRAVEL_VERSION ?>
		                	</p>
		                </div>
	                </div>

	                <div class="wrap-footer">
	                    <table class="form-table">
			                <tbody>
			                	<tr>
			                    	<th scope="row"><?php esc_html_e( 'Get add-ons and tips...', 'wp-travel' ); ?></th>
				                    <td>
				                        <form action="https://wensolutions.us13.list-manage.com/subscribe/post?u=5924e7bef96519e3827fa3024&amp;id=a40eebcccf" method="POST" class="validate" target="_blank" name="mc-embedded-subscribe-form">
				                            <input class="regular-text ltr" type="email" name="EMAIL" id="mce-EMAIL" placeholder="Email address" required>

				                            <input type="submit"  name="subscribe" id="mc-embedded-subscribe" class="button button-primary" value="Subscribe">
				                            <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
											<div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_5924e7bef96519e3827fa3024_a40eebcccf" tabindex="-1" value=""></div>
				                        </form>
				                    </td>
				                </tr>
				            </tbody>
			            </table>
			            <div class="WP-Travel-support">
		                    <?php esc_html_e( 'Questions? Need Help?', 'wp-travel' ); ?>
			                <div id="WP-Travel-contact-us" class="WP-Travel-contact-us">
			                	<a class="thickbox-contact-us" href="http://wptravel.io/contact/" target="_blank"><?php esc_html_e( 'Contact Us', 'wp-travel' ); ?></a>
			                </div>
		                </div>
			        </div>
	        	</div>

				<div class="feature-section col two-col">
		<div class="col">
			<h3><?php esc_html_e( 'Description', 'wp-travel' ); ?></h3>
			<p class="wp-travel-summary">
				<?php esc_html_e( 'WP Travel is an easy to use and awesome plugin that you can use with any travel site. With WP travel you can simply add the post type to display the packages and WP Travel comes with booking feature as well.  Beside this  plugin provides various kind of feature, setting which makes this plugin more attractive.

				The compatibility of the plugin is also one of the features. It can also be modified very easily through custom templates.', 'wp-travel' ); ?>
			</p>
			<h3><?php esc_html_e( 'Feature Overview', 'wp-travel' ); ?></h3>
			<ul class="wp-travel-feature_list">
				<li class="wp-travel-feature">
					<?php esc_html_e( 'Get your travel site ready just on few clicks. With our user-friendly system &amp; complete documentation, you wont have any trouble while using the system.', 'wp-travel' ); ?>				</li>
				<li class="wp-travel-feature">
					<?php esc_html_e( 'WP Travel includes in-build booking system for your travel site. Users can easily book itineraries from your site and you can track all bookings from the backend.', 'wp-travel' ); ?>				</li>
				<li class="wp-travel-feature">
					<?php esc_html_e( 'Data are very important for all business. WP Travel has in-build booking stat that helps you to generate the report from different date range, types and locations.', 'wp-travel' ); ?>				</li>
				<li class="wp-travel-feature">
					<?php esc_html_e( 'With our payment processing features, you can get partial or full payment for each booking. All that payment will be tracked in the backend and also you can view stat of payments.', 'wp-travel' ); ?>			</li>
				<li class="wp-travel-feature">
					<?php esc_html_e( 'WP travel plugin is translation ready in order to fulfill customer needs from all around the world. You can translate WP Travel to any language with the help of WPML Translation Plugin and for the translation of the string, you can use Loco Translate.', 'wp-travel' ); ?>		</li>
				<li class="wp-travel-feature">
					<a href="http://wptravel.io/faq/"  class="link-simple" target="_blank"><?php esc_html_e( 'FAQs', 'wp-travel' ); ?></a><?php esc_html_e( ' provide the opportunity to group all those questions that customers ask over and over again related to trips. Also, the itinerary timeline is the new feature added to WP travel plugin which will display the timeline of the trips in tree-like structure.', 'wp-travel' ); ?></li>
				<li class="wp-travel-feature">
					<?php esc_html_e( 'Our team is dedicated to continuous development of the plugin. We will be continuously adding new features to the plugin.', 'wp-travel' ); ?>
				</li>
				<li class="wp-travel-feature">
					<?php esc_html_e( 'If you found any issues in the plugin, you can directly', 'wp-travel' ); ?> <a href="http://wptravel.io/contact/" class="link-simple" target="_blank"> <?php esc_html_e( 'Contact Us', 'wp-travel' ); ?></a> <?php esc_html_e( 'or add your issues or problems on', 'wp-travel' ); ?> <a href="http://wptravel.io/support-forum/" class="link-simple" target="_blank"><?php esc_html_e( 'Support Forum', 'wp-travel' ); ?></a>.</li>
			</ul>
		</div>

		<div class="col last-feature">
			<div class="es-form-setup">
				<h3><?php esc_html_e( 'Add Trip', 'wp-travel' ); ?></h3>
				<p class="wp-travel-faq">
					<a href="http://wptravel.io/documentations/user-documentation/how-to-create-your-very-first-trip/" target="_blank"><?php esc_html_e( 'How to Add Trip to the website?', 'wp-travel' ); ?></a>				</p>
				<p>
					<?php esc_html_e( 'After the activation of the required plugin, you will find the Trips menu on the dashboard.
					Now  to create the trip of your choice go to Admin Panel > Trips > Add New and begin entering the required content as per your wish.
					Furthermore, to help you and make you clear we have explained each and every field below in the Overview section.', 'wp-travel' ); ?>
				</p>
				<h2> <?php esc_html_e( 'Additional Trip settings', 'wp-travel' ); ?></h2>
				<ul class="wp-travel-faq_list">
					<li class="wp-travel-faq">
						<a href="http://wptravel.io/documentations/user-documentation/how-to/how-to-globally-set-the-tabs-format/" target="_blank"><?php esc_html_e( 'How to Globally Set the Tabs Format Shown on Single Trip Page?', 'wp-travel' ); ?></a>
					</li>
					<li class="wp-travel-faq">
						<a href="http://wptravel.io/documentations/user-documentation/how-to/how-to-arrange-and-change-tabs-in-single-trip/" target="_blank"><?php esc_html_e( 'How to change the label of tabs shown on Single Trip Page?', 'wp-travel' ); ?></a>
					</li>
					<li class="wp-travel-faq">
						<?php esc_html_e( 'See all the Frequently Asked Question Solution For WP Travel Plugin', 'wp-travel' ); ?>
						<a href="http://wptravel.io/faq/" target="_blank"><?php esc_html_e( 'Frequently Asked Question', 'wp-travel' ); ?></a>
					</li>
					<li class="wp-travel-faq">
					<?php esc_html_e( 'You can find your solution about the problem of WP Travel from our', 'wp-travel' ); ?> <a href="http://wptravel.io/support-forum/forum/wp-travel/" class="link-simple" target="_blank"><?php esc_html_e( 'Support Page', 'wp-travel' ); ?></a> <?php esc_html_e( 'or you can create a', 'wp-travel' ); ?> <a href="http://wptravel.io/support-forum/forum/wp-travel/" class="link-simple" target="_blank"><?php esc_html_e( 'Support', 'wp-travel' ); ?></a> <?php esc_html_e( 'for free. Feel free to ask a question about the problem, this will eventually help the growth of the plugin furthermore', 'wp-travel' ); ?>
						<a href="http://wptravel.io/support-forum/" target="_blank"><?php esc_html_e( 'Support Forum', 'wp-travel' ); ?></a>.
					</li>
				</ul>
			</div>
			<br>
			<div class="es-setting">
				<h2><?php esc_html_e( 'General Plugin Configuration', 'wp-travel' ); ?></h2>
				<ul class="wp-travel-faq_list">
					<li class="wp-travel-faq">
						<a href="http://wptravel.io/documentations/user-documentation/" target="_blank">
							<?php esc_html_e( 'See User Documentation to know the plugin and how it works', 'wp-travel' ); ?>
						</a>

						</li>

						<li class="wp-travel-faq">
							<a href="http://wptravel.io/documentations/developer-documentation/" target="_blank">
								<?php esc_html_e( 'See Developer Documentation to know the plugin in depth.', 'wp-travel' ); ?>
							</a>
						</li>
				</ul>
			</div>
		</div>
	</div>
</div>
</div>
	</div>
<?php

}
/**
 * Modify Admin Footer Message.
 */
function wp_travel_modify_admin_footer_admin_settings_page(){

	printf(__('If you like %1s, please consider leaving us a %2s rating. A huge thank you from WEN Solutions in advance!', 'wp-travel' ), '<strong>WP Travel</strong>','<a target="_blank" href="https://wordpress.org/support/plugin/wp-travel/reviews/">★★★★★</a>' );

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
	//delete_site_transient( "_transient_wt_booking_count_{$itinerary_id}" );
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
        'fab fa-500px' => __( '500px' ),
        'fab fa-accessible-icon' => __( 'accessible-icon' ),
        'fab fa-accusoft' => __( 'accusoft' ),
        'fas fa-address-book' => __( 'address-book' ),
        'far fa-address-book' => __( 'address-book' ),
        'fas fa-address-card' => __( 'address-card' ),
        'far fa-address-card' => __( 'address-card' ),
        'fas fa-adjust' => __( 'adjust' ),
        'fab fa-adn' => __( 'adn' ),
        'fab fa-adversal' => __( 'adversal' ),
        'fab fa-affiliatetheme' => __( 'affiliatetheme' ),
        'fab fa-algolia' => __( 'algolia' ),
        'fas fa-align-center' => __( 'align-center' ),
        'fas fa-align-justify' => __( 'align-justify' ),
        'fas fa-align-left' => __( 'align-left' ),
        'fas fa-align-right' => __( 'align-right' ),
        'fas fa-allergies' => __( 'allergies' ),
        'fab fa-amazon' => __( 'amazon' ),
        'fab fa-amazon-pay' => __( 'amazon-pay' ),
        'fas fa-ambulance' => __( 'ambulance' ),
        'fas fa-american-sign-language-interpreting' => __( 'american-sign-language-interpreting' ),
        'fab fa-amilia' => __( 'amilia' ),
        'fas fa-anchor' => __( 'anchor' ),
        'fab fa-android' => __( 'android' ),
        'fab fa-angellist' => __( 'angellist' ),
        'fas fa-angle-double-down' => __( 'angle-double-down' ),
        'fas fa-angle-double-left' => __( 'angle-double-left' ),
        'fas fa-angle-double-right' => __( 'angle-double-right' ),
        'fas fa-angle-double-up' => __( 'angle-double-up' ),
        'fas fa-angle-down' => __( 'angle-down' ),
        'fas fa-angle-left' => __( 'angle-left' ),
        'fas fa-angle-right' => __( 'angle-right' ),
        'fas fa-angle-up' => __( 'angle-up' ),
        'fab fa-angrycreative' => __( 'angrycreative' ),
        'fab fa-angular' => __( 'angular' ),
        'fab fa-app-store' => __( 'app-store' ),
        'fab fa-app-store-ios' => __( 'app-store-ios' ),
        'fab fa-apper' => __( 'apper' ),
        'fab fa-apple' => __( 'apple' ),
        'fab fa-apple-pay' => __( 'apple-pay' ),
        'fas fa-archive' => __( 'archive' ),
        'fas fa-arrow-alt-circle-down' => __( 'arrow-alt-circle-down' ),
        'far fa-arrow-alt-circle-down' => __( 'arrow-alt-circle-down' ),
        'fas fa-arrow-alt-circle-left' => __( 'arrow-alt-circle-left' ),
        'far fa-arrow-alt-circle-left' => __( 'arrow-alt-circle-left' ),
        'fas fa-arrow-alt-circle-right' => __( 'arrow-alt-circle-right' ),
        'far fa-arrow-alt-circle-right' => __( 'arrow-alt-circle-right' ),
        'fas fa-arrow-alt-circle-up' => __( 'arrow-alt-circle-up' ),
        'far fa-arrow-alt-circle-up' => __( 'arrow-alt-circle-up' ),
        'fas fa-arrow-circle-down' => __( 'arrow-circle-down' ),
        'fas fa-arrow-circle-left' => __( 'arrow-circle-left' ),
        'fas fa-arrow-circle-right' => __( 'arrow-circle-right' ),
        'fas fa-arrow-circle-up' => __( 'arrow-circle-up' ),
        'fas fa-arrow-down' => __( 'arrow-down' ),
        'fas fa-arrow-left' => __( 'arrow-left' ),
        'fas fa-arrow-right' => __( 'arrow-right' ),
        'fas fa-arrow-up' => __( 'arrow-up' ),
        'fas fa-arrows-alt' => __( 'arrows-alt' ),
        'fas fa-arrows-alt-h' => __( 'arrows-alt-h' ),
        'fas fa-arrows-alt-v' => __( 'arrows-alt-v' ),
        'fas fa-assistive-listening-systems' => __( 'assistive-listening-systems' ),
        'fas fa-asterisk' => __( 'asterisk' ),
        'fab fa-asymmetrik' => __( 'asymmetrik' ),
        'fas fa-at' => __( 'at' ),
        'fab fa-audible' => __( 'audible' ),
        'fas fa-audio-description' => __( 'audio-description' ),
        'fab fa-autoprefixer' => __( 'autoprefixer' ),
        'fab fa-avianex' => __( 'avianex' ),
        'fab fa-aviato' => __( 'aviato' ),
        'fab fa-aws' => __( 'aws' ),
        'fas fa-backward' => __( 'backward' ),
        'fas fa-balance-scale' => __( 'balance-scale' ),
        'fas fa-ban' => __( 'ban' ),
        'fas fa-band-aid' => __( 'band-aid' ),
        'fab fa-bandcamp' => __( 'bandcamp' ),
        'fas fa-barcode' => __( 'barcode' ),
        'fas fa-bars' => __( 'bars' ),
        'fas fa-baseball-ball' => __( 'baseball-ball' ),
        'fas fa-basketball-ball' => __( 'basketball-ball' ),
        'fas fa-bath' => __( 'bath' ),
        'fas fa-battery-empty' => __( 'battery-empty' ),
        'fas fa-battery-full' => __( 'battery-full' ),
        'fas fa-battery-half' => __( 'battery-half' ),
        'fas fa-battery-quarter' => __( 'battery-quarter' ),
        'fas fa-battery-three-quarters' => __( 'battery-three-quarters' ),
        'fas fa-bed' => __( 'bed' ),
        'fas fa-beer' => __( 'beer' ),
        'fab fa-behance' => __( 'behance' ),
        'fab fa-behance-square' => __( 'behance-square' ),
        'fas fa-bell' => __( 'bell' ),
        'far fa-bell' => __( 'bell' ),
        'fas fa-bell-slash' => __( 'bell-slash' ),
        'far fa-bell-slash' => __( 'bell-slash' ),
        'fas fa-bicycle' => __( 'bicycle' ),
        'fab fa-bimobject' => __( 'bimobject' ),
        'fas fa-binoculars' => __( 'binoculars' ),
        'fas fa-birthday-cake' => __( 'birthday-cake' ),
        'fab fa-bitbucket' => __( 'bitbucket' ),
        'fab fa-bitcoin' => __( 'bitcoin' ),
        'fab fa-bity' => __( 'bity' ),
        'fab fa-black-tie' => __( 'black-tie' ),
        'fab fa-blackberry' => __( 'blackberry' ),
        'fas fa-blind' => __( 'blind' ),
        'fab fa-blogger' => __( 'blogger' ),
        'fab fa-blogger-b' => __( 'blogger-b' ),
        'fab fa-bluetooth' => __( 'bluetooth' ),
        'fab fa-bluetooth-b' => __( 'bluetooth-b' ),
        'fas fa-bold' => __( 'bold' ),
        'fas fa-bolt' => __( 'bolt' ),
        'fas fa-bomb' => __( 'bomb' ),
        'fas fa-book' => __( 'book' ),
        'fas fa-bookmark' => __( 'bookmark' ),
        'far fa-bookmark' => __( 'bookmark' ),
        'fas fa-bowling-ball' => __( 'bowling-ball' ),
        'fas fa-box' => __( 'box' ),
        'fas fa-box-open' => __( 'box-open' ),
        'fas fa-boxes' => __( 'boxes' ),
        'fas fa-braille' => __( 'braille' ),
        'fas fa-briefcase' => __( 'briefcase' ),
        'fas fa-briefcase-medical' => __( 'briefcase-medical' ),
        'fab fa-btc' => __( 'btc' ),
        'fas fa-bug' => __( 'bug' ),
        'fas fa-building' => __( 'building' ),
        'far fa-building' => __( 'building' ),
        'fas fa-bullhorn' => __( 'bullhorn' ),
        'fas fa-bullseye' => __( 'bullseye' ),
        'fas fa-burn' => __( 'burn' ),
        'fab fa-buromobelexperte' => __( 'buromobelexperte' ),
        'fas fa-bus' => __( 'bus' ),
        'fab fa-buysellads' => __( 'buysellads' ),
        'fas fa-calculator' => __( 'calculator' ),
        'fas fa-calendar' => __( 'calendar' ),
        'far fa-calendar' => __( 'calendar' ),
        'fas fa-calendar-alt' => __( 'calendar-alt' ),
        'far fa-calendar-alt' => __( 'calendar-alt' ),
        'fas fa-calendar-check' => __( 'calendar-check' ),
        'far fa-calendar-check' => __( 'calendar-check' ),
        'fas fa-calendar-minus' => __( 'calendar-minus' ),
        'far fa-calendar-minus' => __( 'calendar-minus' ),
        'fas fa-calendar-plus' => __( 'calendar-plus' ),
        'far fa-calendar-plus' => __( 'calendar-plus' ),
        'fas fa-calendar-times' => __( 'calendar-times' ),
        'far fa-calendar-times' => __( 'calendar-times' ),
        'fas fa-camera' => __( 'camera' ),
        'fas fa-camera-retro' => __( 'camera-retro' ),
        'fas fa-capsules' => __( 'capsules' ),
        'fas fa-car' => __( 'car' ),
        'fas fa-caret-down' => __( 'caret-down' ),
        'fas fa-caret-left' => __( 'caret-left' ),
        'fas fa-caret-right' => __( 'caret-right' ),
        'fas fa-caret-square-down' => __( 'caret-square-down' ),
        'far fa-caret-square-down' => __( 'caret-square-down' ),
        'fas fa-caret-square-left' => __( 'caret-square-left' ),
        'far fa-caret-square-left' => __( 'caret-square-left' ),
        'fas fa-caret-square-right' => __( 'caret-square-right' ),
        'far fa-caret-square-right' => __( 'caret-square-right' ),
        'fas fa-caret-square-up' => __( 'caret-square-up' ),
        'far fa-caret-square-up' => __( 'caret-square-up' ),
        'fas fa-caret-up' => __( 'caret-up' ),
        'fas fa-cart-arrow-down' => __( 'cart-arrow-down' ),
        'fas fa-cart-plus' => __( 'cart-plus' ),
        'fab fa-cc-amazon-pay' => __( 'cc-amazon-pay' ),
        'fab fa-cc-amex' => __( 'cc-amex' ),
        'fab fa-cc-apple-pay' => __( 'cc-apple-pay' ),
        'fab fa-cc-diners-club' => __( 'cc-diners-club' ),
        'fab fa-cc-discover' => __( 'cc-discover' ),
        'fab fa-cc-jcb' => __( 'cc-jcb' ),
        'fab fa-cc-mastercard' => __( 'cc-mastercard' ),
        'fab fa-cc-paypal' => __( 'cc-paypal' ),
        'fab fa-cc-stripe' => __( 'cc-stripe' ),
        'fab fa-cc-visa' => __( 'cc-visa' ),
        'fab fa-centercode' => __( 'centercode' ),
        'fas fa-certificate' => __( 'certificate' ),
        'fas fa-chart-area' => __( 'chart-area' ),
        'fas fa-chart-bar' => __( 'chart-bar' ),
        'far fa-chart-bar' => __( 'chart-bar' ),
        'fas fa-chart-line' => __( 'chart-line' ),
        'fas fa-chart-pie' => __( 'chart-pie' ),
        'fas fa-check' => __( 'check' ),
        'fas fa-check-circle' => __( 'check-circle' ),
        'far fa-check-circle' => __( 'check-circle' ),
        'fas fa-check-square' => __( 'check-square' ),
        'far fa-check-square' => __( 'check-square' ),
        'fas fa-chess' => __( 'chess' ),
        'fas fa-chess-bishop' => __( 'chess-bishop' ),
        'fas fa-chess-board' => __( 'chess-board' ),
        'fas fa-chess-king' => __( 'chess-king' ),
        'fas fa-chess-knight' => __( 'chess-knight' ),
        'fas fa-chess-pawn' => __( 'chess-pawn' ),
        'fas fa-chess-queen' => __( 'chess-queen' ),
        'fas fa-chess-rook' => __( 'chess-rook' ),
        'fas fa-chevron-circle-down' => __( 'chevron-circle-down' ),
        'fas fa-chevron-circle-left' => __( 'chevron-circle-left' ),
        'fas fa-chevron-circle-right' => __( 'chevron-circle-right' ),
        'fas fa-chevron-circle-up' => __( 'chevron-circle-up' ),
        'fas fa-chevron-down' => __( 'chevron-down' ),
        'fas fa-chevron-left' => __( 'chevron-left' ),
        'fas fa-chevron-right' => __( 'chevron-right' ),
        'fas fa-chevron-up' => __( 'chevron-up' ),
        'fas fa-child' => __( 'child' ),
        'fab fa-chrome' => __( 'chrome' ),
        'fas fa-circle' => __( 'circle' ),
        'far fa-circle' => __( 'circle' ),
        'fas fa-circle-notch' => __( 'circle-notch' ),
        'fas fa-clipboard' => __( 'clipboard' ),
        'far fa-clipboard' => __( 'clipboard' ),
        'fas fa-clipboard-check' => __( 'clipboard-check' ),
        'fas fa-clipboard-list' => __( 'clipboard-list' ),
        'fas fa-clock' => __( 'clock' ),
        'far fa-clock' => __( 'clock' ),
        'fas fa-clone' => __( 'clone' ),
        'far fa-clone' => __( 'clone' ),
        'fas fa-closed-captioning' => __( 'closed-captioning' ),
        'far fa-closed-captioning' => __( 'closed-captioning' ),
        'fas fa-cloud' => __( 'cloud' ),
        'fas fa-cloud-download-alt' => __( 'cloud-download-alt' ),
        'fas fa-cloud-upload-alt' => __( 'cloud-upload-alt' ),
        'fab fa-cloudscale' => __( 'cloudscale' ),
        'fab fa-cloudsmith' => __( 'cloudsmith' ),
        'fab fa-cloudversify' => __( 'cloudversify' ),
        'fas fa-code' => __( 'code' ),
        'fas fa-code-branch' => __( 'code-branch' ),
        'fab fa-codepen' => __( 'codepen' ),
        'fab fa-codiepie' => __( 'codiepie' ),
        'fas fa-coffee' => __( 'coffee' ),
        'fas fa-cog' => __( 'cog' ),
        'fas fa-cogs' => __( 'cogs' ),
        'fas fa-columns' => __( 'columns' ),
        'fas fa-comment' => __( 'comment' ),
        'far fa-comment' => __( 'comment' ),
        'fas fa-comment-alt' => __( 'comment-alt' ),
        'far fa-comment-alt' => __( 'comment-alt' ),
        'fas fa-comment-dots' => __( 'comment-dots' ),
        'fas fa-comment-slash' => __( 'comment-slash' ),
        'fas fa-comments' => __( 'comments' ),
        'far fa-comments' => __( 'comments' ),
        'fas fa-compass' => __( 'compass' ),
        'far fa-compass' => __( 'compass' ),
        'fas fa-compress' => __( 'compress' ),
        'fab fa-connectdevelop' => __( 'connectdevelop' ),
        'fab fa-contao' => __( 'contao' ),
        'fas fa-copy' => __( 'copy' ),
        'far fa-copy' => __( 'copy' ),
        'fas fa-copyright' => __( 'copyright' ),
        'far fa-copyright' => __( 'copyright' ),
        'fas fa-couch' => __( 'couch' ),
        'fab fa-cpanel' => __( 'cpanel' ),
        'fab fa-creative-commons' => __( 'creative-commons' ),
        'fas fa-credit-card' => __( 'credit-card' ),
        'far fa-credit-card' => __( 'credit-card' ),
        'fas fa-crop' => __( 'crop' ),
        'fas fa-crosshairs' => __( 'crosshairs' ),
        'fab fa-css3' => __( 'css3' ),
        'fab fa-css3-alt' => __( 'css3-alt' ),
        'fas fa-cube' => __( 'cube' ),
        'fas fa-cubes' => __( 'cubes' ),
        'fas fa-cut' => __( 'cut' ),
        'fab fa-cuttlefish' => __( 'cuttlefish' ),
        'fab fa-d-and-d' => __( 'd-and-d' ),
        'fab fa-dashcube' => __( 'dashcube' ),
        'fas fa-database' => __( 'database' ),
        'fas fa-deaf' => __( 'deaf' ),
        'fab fa-delicious' => __( 'delicious' ),
        'fab fa-deploydog' => __( 'deploydog' ),
        'fab fa-deskpro' => __( 'deskpro' ),
        'fas fa-desktop' => __( 'desktop' ),
        'fab fa-deviantart' => __( 'deviantart' ),
        'fas fa-diagnoses' => __( 'diagnoses' ),
        'fab fa-digg' => __( 'digg' ),
        'fab fa-digital-ocean' => __( 'digital-ocean' ),
        'fab fa-discord' => __( 'discord' ),
        'fab fa-discourse' => __( 'discourse' ),
        'fas fa-dna' => __( 'dna' ),
        'fab fa-dochub' => __( 'dochub' ),
        'fab fa-docker' => __( 'docker' ),
        'fas fa-dollar-sign' => __( 'dollar-sign' ),
        'fas fa-dolly' => __( 'dolly' ),
        'fas fa-dolly-flatbed' => __( 'dolly-flatbed' ),
        'fas fa-donate' => __( 'donate' ),
        'fas fa-dot-circle' => __( 'dot-circle' ),
        'far fa-dot-circle' => __( 'dot-circle' ),
        'fas fa-dove' => __( 'dove' ),
        'fas fa-download' => __( 'download' ),
        'fab fa-draft2digital' => __( 'draft2digital' ),
        'fab fa-dribbble' => __( 'dribbble' ),
        'fab fa-dribbble-square' => __( 'dribbble-square' ),
        'fab fa-dropbox' => __( 'dropbox' ),
        'fab fa-drupal' => __( 'drupal' ),
        'fab fa-dyalog' => __( 'dyalog' ),
        'fab fa-earlybirds' => __( 'earlybirds' ),
        'fab fa-edge' => __( 'edge' ),
        'fas fa-edit' => __( 'edit' ),
        'far fa-edit' => __( 'edit' ),
        'fas fa-eject' => __( 'eject' ),
        'fab fa-elementor' => __( 'elementor' ),
        'fas fa-ellipsis-h' => __( 'ellipsis-h' ),
        'fas fa-ellipsis-v' => __( 'ellipsis-v' ),
        'fab fa-ember' => __( 'ember' ),
        'fab fa-empire' => __( 'empire' ),
        'fas fa-envelope' => __( 'envelope' ),
        'far fa-envelope' => __( 'envelope' ),
        'fas fa-envelope-open' => __( 'envelope-open' ),
        'far fa-envelope-open' => __( 'envelope-open' ),
        'fas fa-envelope-square' => __( 'envelope-square' ),
        'fab fa-envira' => __( 'envira' ),
        'fas fa-eraser' => __( 'eraser' ),
        'fab fa-erlang' => __( 'erlang' ),
        'fab fa-ethereum' => __( 'ethereum' ),
        'fab fa-etsy' => __( 'etsy' ),
        'fas fa-euro-sign' => __( 'euro-sign' ),
        'fas fa-exchange-alt' => __( 'exchange-alt' ),
        'fas fa-exclamation' => __( 'exclamation' ),
        'fas fa-exclamation-circle' => __( 'exclamation-circle' ),
        'fas fa-exclamation-triangle' => __( 'exclamation-triangle' ),
        'fas fa-expand' => __( 'expand' ),
        'fas fa-expand-arrows-alt' => __( 'expand-arrows-alt' ),
        'fab fa-expeditedssl' => __( 'expeditedssl' ),
        'fas fa-external-link-alt' => __( 'external-link-alt' ),
        'fas fa-external-link-square-alt' => __( 'external-link-square-alt' ),
        'fas fa-eye' => __( 'eye' ),
        'fas fa-eye-dropper' => __( 'eye-dropper' ),
        'fas fa-eye-slash' => __( 'eye-slash' ),
        'far fa-eye-slash' => __( 'eye-slash' ),
        'fab fa-facebook' => __( 'facebook' ),
        'fab fa-facebook-f' => __( 'facebook-f' ),
        'fab fa-facebook-messenger' => __( 'facebook-messenger' ),
        'fab fa-facebook-square' => __( 'facebook-square' ),
        'fas fa-fast-backward' => __( 'fast-backward' ),
        'fas fa-fast-forward' => __( 'fast-forward' ),
        'fas fa-fax' => __( 'fax' ),
        'fas fa-female' => __( 'female' ),
        'fas fa-fighter-jet' => __( 'fighter-jet' ),
        'fas fa-file' => __( 'file' ),
        'far fa-file' => __( 'file' ),
        'fas fa-file-alt' => __( 'file-alt' ),
        'far fa-file-alt' => __( 'file-alt' ),
        'fas fa-file-archive' => __( 'file-archive' ),
        'far fa-file-archive' => __( 'file-archive' ),
        'fas fa-file-audio' => __( 'file-audio' ),
        'far fa-file-audio' => __( 'file-audio' ),
        'fas fa-file-code' => __( 'file-code' ),
        'far fa-file-code' => __( 'file-code' ),
        'fas fa-file-excel' => __( 'file-excel' ),
        'far fa-file-excel' => __( 'file-excel' ),
        'fas fa-file-image' => __( 'file-image' ),
        'far fa-file-image' => __( 'file-image' ),
        'fas fa-file-medical' => __( 'file-medical' ),
        'fas fa-file-medical-alt' => __( 'file-medical-alt' ),
        'fas fa-file-pdf' => __( 'file-pdf' ),
        'far fa-file-pdf' => __( 'file-pdf' ),
        'fas fa-file-powerpoint' => __( 'file-powerpoint' ),
        'far fa-file-powerpoint' => __( 'file-powerpoint' ),
        'fas fa-file-video' => __( 'file-video' ),
        'far fa-file-video' => __( 'file-video' ),
        'fas fa-file-word' => __( 'file-word' ),
        'far fa-file-word' => __( 'file-word' ),
        'fas fa-film' => __( 'film' ),
        'fas fa-filter' => __( 'filter' ),
        'fas fa-fire' => __( 'fire' ),
        'fas fa-fire-extinguisher' => __( 'fire-extinguisher' ),
        'fab fa-firefox' => __( 'firefox' ),
        'fas fa-first-aid' => __( 'first-aid' ),
        'fab fa-first-order' => __( 'first-order' ),
        'fab fa-firstdraft' => __( 'firstdraft' ),
        'fas fa-flag' => __( 'flag' ),
        'far fa-flag' => __( 'flag' ),
        'fas fa-flag-checkered' => __( 'flag-checkered' ),
        'fas fa-flask' => __( 'flask' ),
        'fab fa-flickr' => __( 'flickr' ),
        'fab fa-flipboard' => __( 'flipboard' ),
        'fab fa-fly' => __( 'fly' ),
        'fas fa-folder' => __( 'folder' ),
        'far fa-folder' => __( 'folder' ),
        'fas fa-folder-open' => __( 'folder-open' ),
        'far fa-folder-open' => __( 'folder-open' ),
        'fas fa-font' => __( 'font' ),
        'fab fa-font-awesome' => __( 'font-awesome' ),
        'fab fa-font-awesome-alt' => __( 'font-awesome-alt' ),
        'fab fa-font-awesome-flag' => __( 'font-awesome-flag' ),
        'fab fa-fonticons' => __( 'fonticons' ),
        'fab fa-fonticons-fi' => __( 'fonticons-fi' ),
        'fas fa-football-ball' => __( 'football-ball' ),
        'fab fa-fort-awesome' => __( 'fort-awesome' ),
        'fab fa-fort-awesome-alt' => __( 'fort-awesome-alt' ),
        'fab fa-forumbee' => __( 'forumbee' ),
        'fas fa-forward' => __( 'forward' ),
        'fab fa-foursquare' => __( 'foursquare' ),
        'fab fa-free-code-camp' => __( 'free-code-camp' ),
        'fab fa-freebsd' => __( 'freebsd' ),
        'fas fa-frown' => __( 'frown' ),
        'far fa-frown' => __( 'frown' ),
        'fas fa-futbol' => __( 'futbol' ),
        'far fa-futbol' => __( 'futbol' ),
        'fas fa-gamepad' => __( 'gamepad' ),
        'fas fa-gavel' => __( 'gavel' ),
        'fas fa-gem' => __( 'gem' ),
        'far fa-gem' => __( 'gem' ),
        'fas fa-genderless' => __( 'genderless' ),
        'fab fa-get-pocket' => __( 'get-pocket' ),
        'fab fa-gg' => __( 'gg' ),
        'fab fa-gg-circle' => __( 'gg-circle' ),
        'fas fa-gift' => __( 'gift' ),
        'fab fa-git' => __( 'git' ),
        'fab fa-git-square' => __( 'git-square' ),
        'fab fa-github' => __( 'github' ),
        'fab fa-github-alt' => __( 'github-alt' ),
        'fab fa-github-square' => __( 'github-square' ),
        'fab fa-gitkraken' => __( 'gitkraken' ),
        'fab fa-gitlab' => __( 'gitlab' ),
        'fab fa-gitter' => __( 'gitter' ),
        'fas fa-glass-martini' => __( 'glass-martini' ),
        'fab fa-glide' => __( 'glide' ),
        'fab fa-glide-g' => __( 'glide-g' ),
        'fas fa-globe' => __( 'globe' ),
        'fab fa-gofore' => __( 'gofore' ),
        'fas fa-golf-ball' => __( 'golf-ball' ),
        'fab fa-goodreads' => __( 'goodreads' ),
        'fab fa-goodreads-g' => __( 'goodreads-g' ),
        'fab fa-google' => __( 'google' ),
        'fab fa-google-drive' => __( 'google-drive' ),
        'fab fa-google-play' => __( 'google-play' ),
        'fab fa-google-plus' => __( 'google-plus' ),
        'fab fa-google-plus-g' => __( 'google-plus-g' ),
        'fab fa-google-plus-square' => __( 'google-plus-square' ),
        'fab fa-google-wallet' => __( 'google-wallet' ),
        'fas fa-graduation-cap' => __( 'graduation-cap' ),
        'fab fa-gratipay' => __( 'gratipay' ),
        'fab fa-grav' => __( 'grav' ),
        'fab fa-gripfire' => __( 'gripfire' ),
        'fab fa-grunt' => __( 'grunt' ),
        'fab fa-gulp' => __( 'gulp' ),
        'fas fa-h-square' => __( 'h-square' ),
        'fab fa-hacker-news' => __( 'hacker-news' ),
        'fab fa-hacker-news-square' => __( 'hacker-news-square' ),
        'fas fa-hand-holding' => __( 'hand-holding' ),
        'fas fa-hand-holding-heart' => __( 'hand-holding-heart' ),
        'fas fa-hand-holding-usd' => __( 'hand-holding-usd' ),
        'fas fa-hand-lizard' => __( 'hand-lizard' ),
        'far fa-hand-lizard' => __( 'hand-lizard' ),
        'fas fa-hand-paper' => __( 'hand-paper' ),
        'far fa-hand-paper' => __( 'hand-paper' ),
        'fas fa-hand-peace' => __( 'hand-peace' ),
        'far fa-hand-peace' => __( 'hand-peace' ),
        'fas fa-hand-point-down' => __( 'hand-point-down' ),
        'far fa-hand-point-down' => __( 'hand-point-down' ),
        'fas fa-hand-point-left' => __( 'hand-point-left' ),
        'far fa-hand-point-left' => __( 'hand-point-left' ),
        'fas fa-hand-point-right' => __( 'hand-point-right' ),
        'far fa-hand-point-right' => __( 'hand-point-right' ),
        'fas fa-hand-point-up' => __( 'hand-point-up' ),
        'far fa-hand-point-up' => __( 'hand-point-up' ),
        'fas fa-hand-pointer' => __( 'hand-pointer' ),
        'far fa-hand-pointer' => __( 'hand-pointer' ),
        'fas fa-hand-rock' => __( 'hand-rock' ),
        'far fa-hand-rock' => __( 'hand-rock' ),
        'fas fa-hand-scissors' => __( 'hand-scissors' ),
        'far fa-hand-scissors' => __( 'hand-scissors' ),
        'fas fa-hand-spock' => __( 'hand-spock' ),
        'far fa-hand-spock' => __( 'hand-spock' ),
        'fas fa-hands' => __( 'hands' ),
        'fas fa-hands-helping' => __( 'hands-helping' ),
        'fas fa-handshake' => __( 'handshake' ),
        'far fa-handshake' => __( 'handshake' ),
        'fas fa-hashtag' => __( 'hashtag' ),
        'fas fa-hdd' => __( 'hdd' ),
        'far fa-hdd' => __( 'hdd' ),
        'fas fa-heading' => __( 'heading' ),
        'fas fa-headphones' => __( 'headphones' ),
        'fas fa-heart' => __( 'heart' ),
        'far fa-heart' => __( 'heart' ),
        'fas fa-heartbeat' => __( 'heartbeat' ),
        'fab fa-hips' => __( 'hips' ),
        'fab fa-hire-a-helper' => __( 'hire-a-helper' ),
        'fas fa-history' => __( 'history' ),
        'fas fa-hockey-puck' => __( 'hockey-puck' ),
        'fas fa-home' => __( 'home' ),
        'fab fa-hooli' => __( 'hooli' ),
        'fas fa-hospital' => __( 'hospital' ),
        'far fa-hospital' => __( 'hospital' ),
        'fas fa-hospital-alt' => __( 'hospital-alt' ),
        'fas fa-hospital-symbol' => __( 'hospital-symbol' ),
        'fab fa-hotjar' => __( 'hotjar' ),
        'fas fa-hourglass' => __( 'hourglass' ),
        'far fa-hourglass' => __( 'hourglass' ),
        'fas fa-hourglass-end' => __( 'hourglass-end' ),
        'fas fa-hourglass-half' => __( 'hourglass-half' ),
        'fas fa-hourglass-start' => __( 'hourglass-start' ),
        'fab fa-houzz' => __( 'houzz' ),
        'fab fa-html5' => __( 'html5' ),
        'fab fa-hubspot' => __( 'hubspot' ),
        'fas fa-i-cursor' => __( 'i-cursor' ),
        'fas fa-id-badge' => __( 'id-badge' ),
        'far fa-id-badge' => __( 'id-badge' ),
        'fas fa-id-card' => __( 'id-card' ),
        'far fa-id-card' => __( 'id-card' ),
        'fas fa-id-card-alt' => __( 'id-card-alt' ),
        'fas fa-image' => __( 'image' ),
        'far fa-image' => __( 'image' ),
        'fas fa-images' => __( 'images' ),
        'far fa-images' => __( 'images' ),
        'fab fa-imdb' => __( 'imdb' ),
        'fas fa-inbox' => __( 'inbox' ),
        'fas fa-indent' => __( 'indent' ),
        'fas fa-industry' => __( 'industry' ),
        'fas fa-info' => __( 'info' ),
        'fas fa-info-circle' => __( 'info-circle' ),
        'fab fa-instagram' => __( 'instagram' ),
        'fab fa-internet-explorer' => __( 'internet-explorer' ),
        'fab fa-ioxhost' => __( 'ioxhost' ),
        'fas fa-italic' => __( 'italic' ),
        'fab fa-itunes' => __( 'itunes' ),
        'fab fa-itunes-note' => __( 'itunes-note' ),
        'fab fa-java' => __( 'java' ),
        'fab fa-jenkins' => __( 'jenkins' ),
        'fab fa-joget' => __( 'joget' ),
        'fab fa-joomla' => __( 'joomla' ),
        'fab fa-js' => __( 'js' ),
        'fab fa-js-square' => __( 'js-square' ),
        'fab fa-jsfiddle' => __( 'jsfiddle' ),
        'fas fa-key' => __( 'key' ),
        'fas fa-keyboard' => __( 'keyboard' ),
        'far fa-keyboard' => __( 'keyboard' ),
        'fab fa-keycdn' => __( 'keycdn' ),
        'fab fa-kickstarter' => __( 'kickstarter' ),
        'fab fa-kickstarter-k' => __( 'kickstarter-k' ),
        'fab fa-korvue' => __( 'korvue' ),
        'fas fa-language' => __( 'language' ),
        'fas fa-laptop' => __( 'laptop' ),
        'fab fa-laravel' => __( 'laravel' ),
        'fab fa-lastfm' => __( 'lastfm' ),
        'fab fa-lastfm-square' => __( 'lastfm-square' ),
        'fas fa-leaf' => __( 'leaf' ),
        'fab fa-leanpub' => __( 'leanpub' ),
        'fas fa-lemon' => __( 'lemon' ),
        'far fa-lemon' => __( 'lemon' ),
        'fab fa-less' => __( 'less' ),
        'fas fa-level-down-alt' => __( 'level-down-alt' ),
        'fas fa-level-up-alt' => __( 'level-up-alt' ),
        'fas fa-life-ring' => __( 'life-ring' ),
        'far fa-life-ring' => __( 'life-ring' ),
        'fas fa-lightbulb' => __( 'lightbulb' ),
        'far fa-lightbulb' => __( 'lightbulb' ),
        'fab fa-line' => __( 'line' ),
        'fas fa-link' => __( 'link' ),
        'fab fa-linkedin' => __( 'linkedin' ),
        'fab fa-linkedin-in' => __( 'linkedin-in' ),
        'fab fa-linode' => __( 'linode' ),
        'fab fa-linux' => __( 'linux' ),
        'fas fa-lira-sign' => __( 'lira-sign' ),
        'fas fa-list' => __( 'list' ),
        'fas fa-list-alt' => __( 'list-alt' ),
        'far fa-list-alt' => __( 'list-alt' ),
        'fas fa-list-ol' => __( 'list-ol' ),
        'fas fa-list-ul' => __( 'list-ul' ),
        'fas fa-location-arrow' => __( 'location-arrow' ),
        'fas fa-lock' => __( 'lock' ),
        'fas fa-lock-open' => __( 'lock-open' ),
        'fas fa-long-arrow-alt-down' => __( 'long-arrow-alt-down' ),
        'fas fa-long-arrow-alt-left' => __( 'long-arrow-alt-left' ),
        'fas fa-long-arrow-alt-right' => __( 'long-arrow-alt-right' ),
        'fas fa-long-arrow-alt-up' => __( 'long-arrow-alt-up' ),
        'fas fa-low-vision' => __( 'low-vision' ),
        'fab fa-lyft' => __( 'lyft' ),
        'fab fa-magento' => __( 'magento' ),
        'fas fa-magic' => __( 'magic' ),
        'fas fa-magnet' => __( 'magnet' ),
        'fas fa-male' => __( 'male' ),
        'fas fa-map' => __( 'map' ),
        'far fa-map' => __( 'map' ),
        'fas fa-map-marker' => __( 'map-marker' ),
        'fas fa-map-marker-alt' => __( 'map-marker-alt' ),
        'fas fa-map-pin' => __( 'map-pin' ),
        'fas fa-map-signs' => __( 'map-signs' ),
        'fas fa-mars' => __( 'mars' ),
        'fas fa-mars-double' => __( 'mars-double' ),
        'fas fa-mars-stroke' => __( 'mars-stroke' ),
        'fas fa-mars-stroke-h' => __( 'mars-stroke-h' ),
        'fas fa-mars-stroke-v' => __( 'mars-stroke-v' ),
        'fab fa-maxcdn' => __( 'maxcdn' ),
        'fab fa-medapps' => __( 'medapps' ),
        'fab fa-medium' => __( 'medium' ),
        'fab fa-medium-m' => __( 'medium-m' ),
        'fas fa-medkit' => __( 'medkit' ),
        'fab fa-medrt' => __( 'medrt' ),
        'fab fa-meetup' => __( 'meetup' ),
        'fas fa-meh' => __( 'meh' ),
        'far fa-meh' => __( 'meh' ),
        'fas fa-mercury' => __( 'mercury' ),
        'fas fa-microchip' => __( 'microchip' ),
        'fas fa-microphone' => __( 'microphone' ),
        'fas fa-microphone-slash' => __( 'microphone-slash' ),
        'fab fa-microsoft' => __( 'microsoft' ),
        'fas fa-minus' => __( 'minus' ),
        'fas fa-minus-circle' => __( 'minus-circle' ),
        'fas fa-minus-square' => __( 'minus-square' ),
        'far fa-minus-square' => __( 'minus-square' ),
        'fab fa-mix' => __( 'mix' ),
        'fab fa-mixcloud' => __( 'mixcloud' ),
        'fab fa-mizuni' => __( 'mizuni' ),
        'fas fa-mobile' => __( 'mobile' ),
        'fas fa-mobile-alt' => __( 'mobile-alt' ),
        'fab fa-modx' => __( 'modx' ),
        'fab fa-monero' => __( 'monero' ),
        'fas fa-money-bill-alt' => __( 'money-bill-alt' ),
        'far fa-money-bill-alt' => __( 'money-bill-alt' ),
        'fas fa-moon' => __( 'moon' ),
        'far fa-moon' => __( 'moon' ),
        'fas fa-motorcycle' => __( 'motorcycle' ),
        'fas fa-mouse-pointer' => __( 'mouse-pointer' ),
        'fas fa-music' => __( 'music' ),
        'fab fa-napster' => __( 'napster' ),
        'fas fa-neuter' => __( 'neuter' ),
        'fas fa-newspaper' => __( 'newspaper' ),
        'far fa-newspaper' => __( 'newspaper' ),
        'fab fa-nintendo-switch' => __( 'nintendo-switch' ),
        'fab fa-node' => __( 'node' ),
        'fab fa-node-js' => __( 'node-js' ),
        'fas fa-notes-medical' => __( 'notes-medical' ),
        'fab fa-npm' => __( 'npm' ),
        'fab fa-ns8' => __( 'ns8' ),
        'fab fa-nutritionix' => __( 'nutritionix' ),
        'fas fa-object-group' => __( 'object-group' ),
        'far fa-object-group' => __( 'object-group' ),
        'fas fa-object-ungroup' => __( 'object-ungroup' ),
        'far fa-object-ungroup' => __( 'object-ungroup' ),
        'fab fa-odnoklassniki' => __( 'odnoklassniki' ),
        'fab fa-odnoklassniki-square' => __( 'odnoklassniki-square' ),
        'fab fa-opencart' => __( 'opencart' ),
        'fab fa-openid' => __( 'openid' ),
        'fab fa-opera' => __( 'opera' ),
        'fab fa-optin-monster' => __( 'optin-monster' ),
        'fab fa-osi' => __( 'osi' ),
        'fas fa-outdent' => __( 'outdent' ),
        'fab fa-page4' => __( 'page4' ),
        'fab fa-pagelines' => __( 'pagelines' ),
        'fas fa-paint-brush' => __( 'paint-brush' ),
        'fab fa-palfed' => __( 'palfed' ),
        'fas fa-pallet' => __( 'pallet' ),
        'fas fa-paper-plane' => __( 'paper-plane' ),
        'far fa-paper-plane' => __( 'paper-plane' ),
        'fas fa-paperclip' => __( 'paperclip' ),
        'fas fa-parachute-box' => __( 'parachute-box' ),
        'fas fa-paragraph' => __( 'paragraph' ),
        'fas fa-paste' => __( 'paste' ),
        'fab fa-patreon' => __( 'patreon' ),
        'fas fa-pause' => __( 'pause' ),
        'fas fa-pause-circle' => __( 'pause-circle' ),
        'far fa-pause-circle' => __( 'pause-circle' ),
        'fas fa-paw' => __( 'paw' ),
        'fab fa-paypal' => __( 'paypal' ),
        'fas fa-pen-square' => __( 'pen-square' ),
        'fas fa-pencil-alt' => __( 'pencil-alt' ),
        'fas fa-people-carry' => __( 'people-carry' ),
        'fas fa-percent' => __( 'percent' ),
        'fab fa-periscope' => __( 'periscope' ),
        'fab fa-phabricator' => __( 'phabricator' ),
        'fab fa-phoenix-framework' => __( 'phoenix-framework' ),
        'fas fa-phone' => __( 'phone' ),
        'fas fa-phone-slash' => __( 'phone-slash' ),
        'fas fa-phone-square' => __( 'phone-square' ),
        'fas fa-phone-volume' => __( 'phone-volume' ),
        'fab fa-php' => __( 'php' ),
        'fab fa-pied-piper' => __( 'pied-piper' ),
        'fab fa-pied-piper-alt' => __( 'pied-piper-alt' ),
        'fab fa-pied-piper-hat' => __( 'pied-piper-hat' ),
        'fab fa-pied-piper-pp' => __( 'pied-piper-pp' ),
        'fas fa-piggy-bank' => __( 'piggy-bank' ),
        'fas fa-pills' => __( 'pills' ),
        'fab fa-pinterest' => __( 'pinterest' ),
        'fab fa-pinterest-p' => __( 'pinterest-p' ),
        'fab fa-pinterest-square' => __( 'pinterest-square' ),
        'fas fa-plane' => __( 'plane' ),
        'fas fa-play' => __( 'play' ),
        'fas fa-play-circle' => __( 'play-circle' ),
        'far fa-play-circle' => __( 'play-circle' ),
        'fab fa-playstation' => __( 'playstation' ),
        'fas fa-plug' => __( 'plug' ),
        'fas fa-plus' => __( 'plus' ),
        'fas fa-plus-circle' => __( 'plus-circle' ),
        'fas fa-plus-square' => __( 'plus-square' ),
        'far fa-plus-square' => __( 'plus-square' ),
        'fas fa-podcast' => __( 'podcast' ),
        'fas fa-poo' => __( 'poo' ),
        'fas fa-pound-sign' => __( 'pound-sign' ),
        'fas fa-power-off' => __( 'power-off' ),
        'fas fa-prescription-bottle' => __( 'prescription-bottle' ),
        'fas fa-prescription-bottle-alt' => __( 'prescription-bottle-alt' ),
        'fas fa-print' => __( 'print' ),
        'fas fa-procedures' => __( 'procedures' ),
        'fab fa-product-hunt' => __( 'product-hunt' ),
        'fab fa-pushed' => __( 'pushed' ),
        'fas fa-puzzle-piece' => __( 'puzzle-piece' ),
        'fab fa-python' => __( 'python' ),
        'fab fa-qq' => __( 'qq' ),
        'fas fa-qrcode' => __( 'qrcode' ),
        'fas fa-question' => __( 'question' ),
        'fas fa-question-circle' => __( 'question-circle' ),
        'far fa-question-circle' => __( 'question-circle' ),
        'fas fa-quidditch' => __( 'quidditch' ),
        'fab fa-quinscape' => __( 'quinscape' ),
        'fab fa-quora' => __( 'quora' ),
        'fas fa-quote-left' => __( 'quote-left' ),
        'fas fa-quote-right' => __( 'quote-right' ),
        'fas fa-random' => __( 'random' ),
        'fab fa-ravelry' => __( 'ravelry' ),
        'fab fa-react' => __( 'react' ),
        'fab fa-readme' => __( 'readme' ),
        'fab fa-rebel' => __( 'rebel' ),
        'fas fa-recycle' => __( 'recycle' ),
        'fab fa-red-river' => __( 'red-river' ),
        'fab fa-reddit' => __( 'reddit' ),
        'fab fa-reddit-alien' => __( 'reddit-alien' ),
        'fab fa-reddit-square' => __( 'reddit-square' ),
        'fas fa-redo' => __( 'redo' ),
        'fas fa-redo-alt' => __( 'redo-alt' ),
        'fas fa-registered' => __( 'registered' ),
        'far fa-registered' => __( 'registered' ),
        'fab fa-rendact' => __( 'rendact' ),
        'fab fa-renren' => __( 'renren' ),
        'fas fa-reply' => __( 'reply' ),
        'fas fa-reply-all' => __( 'reply-all' ),
        'fab fa-replyd' => __( 'replyd' ),
        'fab fa-resolving' => __( 'resolving' ),
        'fas fa-retweet' => __( 'retweet' ),
        'fas fa-ribbon' => __( 'ribbon' ),
        'fas fa-road' => __( 'road' ),
        'fas fa-rocket' => __( 'rocket' ),
        'fab fa-rocketchat' => __( 'rocketchat' ),
        'fab fa-rockrms' => __( 'rockrms' ),
        'fas fa-rss' => __( 'rss' ),
        'fas fa-rss-square' => __( 'rss-square' ),
        'fas fa-ruble-sign' => __( 'ruble-sign' ),
        'fas fa-rupee-sign' => __( 'rupee-sign' ),
        'fab fa-safari' => __( 'safari' ),
        'fab fa-sass' => __( 'sass' ),
        'fas fa-save' => __( 'save' ),
        'far fa-save' => __( 'save' ),
        'fab fa-schlix' => __( 'schlix' ),
        'fab fa-scribd' => __( 'scribd' ),
        'fas fa-search' => __( 'search' ),
        'fas fa-search-minus' => __( 'search-minus' ),
        'fas fa-search-plus' => __( 'search-plus' ),
        'fab fa-searchengin' => __( 'searchengin' ),
        'fas fa-seedling' => __( 'seedling' ),
        'fab fa-sellcast' => __( 'sellcast' ),
        'fab fa-sellsy' => __( 'sellsy' ),
        'fas fa-server' => __( 'server' ),
        'fab fa-servicestack' => __( 'servicestack' ),
        'fas fa-share' => __( 'share' ),
        'fas fa-share-alt' => __( 'share-alt' ),
        'fas fa-share-alt-square' => __( 'share-alt-square' ),
        'fas fa-share-square' => __( 'share-square' ),
        'far fa-share-square' => __( 'share-square' ),
        'fas fa-shekel-sign' => __( 'shekel-sign' ),
        'fas fa-shield-alt' => __( 'shield-alt' ),
        'fas fa-ship' => __( 'ship' ),
        'fas fa-shipping-fast' => __( 'shipping-fast' ),
        'fab fa-shirtsinbulk' => __( 'shirtsinbulk' ),
        'fas fa-shopping-bag' => __( 'shopping-bag' ),
        'fas fa-shopping-basket' => __( 'shopping-basket' ),
        'fas fa-shopping-cart' => __( 'shopping-cart' ),
        'fas fa-shower' => __( 'shower' ),
        'fas fa-sign' => __( 'sign' ),
        'fas fa-sign-in-alt' => __( 'sign-in-alt' ),
        'fas fa-sign-language' => __( 'sign-language' ),
        'fas fa-sign-out-alt' => __( 'sign-out-alt' ),
        'fas fa-signal' => __( 'signal' ),
        'fab fa-simplybuilt' => __( 'simplybuilt' ),
        'fab fa-sistrix' => __( 'sistrix' ),
        'fas fa-sitemap' => __( 'sitemap' ),
        'fab fa-skyatlas' => __( 'skyatlas' ),
        'fab fa-skype' => __( 'skype' ),
        'fab fa-slack' => __( 'slack' ),
        'fab fa-slack-hash' => __( 'slack-hash' ),
        'fas fa-sliders-h' => __( 'sliders-h' ),
        'fab fa-slideshare' => __( 'slideshare' ),
        'fas fa-smile' => __( 'smile' ),
        'far fa-smile' => __( 'smile' ),
        'fas fa-smoking' => __( 'smoking' ),
        'fab fa-snapchat' => __( 'snapchat' ),
        'fab fa-snapchat-ghost' => __( 'snapchat-ghost' ),
        'fab fa-snapchat-square' => __( 'snapchat-square' ),
        'fas fa-snowflake' => __( 'snowflake' ),
        'far fa-snowflake' => __( 'snowflake' ),
        'fas fa-sort' => __( 'sort' ),
        'fas fa-sort-alpha-down' => __( 'sort-alpha-down' ),
        'fas fa-sort-alpha-up' => __( 'sort-alpha-up' ),
        'fas fa-sort-amount-down' => __( 'sort-amount-down' ),
        'fas fa-sort-amount-up' => __( 'sort-amount-up' ),
        'fas fa-sort-down' => __( 'sort-down' ),
        'fas fa-sort-numeric-down' => __( 'sort-numeric-down' ),
        'fas fa-sort-numeric-up' => __( 'sort-numeric-up' ),
        'fas fa-sort-up' => __( 'sort-up' ),
        'fab fa-soundcloud' => __( 'soundcloud' ),
        'fas fa-space-shuttle' => __( 'space-shuttle' ),
        'fab fa-speakap' => __( 'speakap' ),
        'fas fa-spinner' => __( 'spinner' ),
        'fab fa-spotify' => __( 'spotify' ),
        'fas fa-square' => __( 'square' ),
        'far fa-square' => __( 'square' ),
        'fas fa-square-full' => __( 'square-full' ),
        'fab fa-stack-exchange' => __( 'stack-exchange' ),
        'fab fa-stack-overflow' => __( 'stack-overflow' ),
        'fas fa-star' => __( 'star' ),
        'far fa-star' => __( 'star' ),
        'fas fa-star-half' => __( 'star-half' ),
        'far fa-star-half' => __( 'star-half' ),
        'fab fa-staylinked' => __( 'staylinked' ),
        'fab fa-steam' => __( 'steam' ),
        'fab fa-steam-square' => __( 'steam-square' ),
        'fab fa-steam-symbol' => __( 'steam-symbol' ),
        'fas fa-step-backward' => __( 'step-backward' ),
        'fas fa-step-forward' => __( 'step-forward' ),
        'fas fa-stethoscope' => __( 'stethoscope' ),
        'fab fa-sticker-mule' => __( 'sticker-mule' ),
        'fas fa-sticky-note' => __( 'sticky-note' ),
        'far fa-sticky-note' => __( 'sticky-note' ),
        'fas fa-stop' => __( 'stop' ),
        'fas fa-stop-circle' => __( 'stop-circle' ),
        'far fa-stop-circle' => __( 'stop-circle' ),
        'fas fa-stopwatch' => __( 'stopwatch' ),
        'fab fa-strava' => __( 'strava' ),
        'fas fa-street-view' => __( 'street-view' ),
        'fas fa-strikethrough' => __( 'strikethrough' ),
        'fab fa-stripe' => __( 'stripe' ),
        'fab fa-stripe-s' => __( 'stripe-s' ),
        'fab fa-studiovinari' => __( 'studiovinari' ),
        'fab fa-stumbleupon' => __( 'stumbleupon' ),
        'fab fa-stumbleupon-circle' => __( 'stumbleupon-circle' ),
        'fas fa-subscript' => __( 'subscript' ),
        'fas fa-subway' => __( 'subway' ),
        'fas fa-suitcase' => __( 'suitcase' ),
        'fas fa-sun' => __( 'sun' ),
        'far fa-sun' => __( 'sun' ),
        'fab fa-superpowers' => __( 'superpowers' ),
        'fas fa-superscript' => __( 'superscript' ),
        'fab fa-supple' => __( 'supple' ),
        'fas fa-sync' => __( 'sync' ),
        'fas fa-sync-alt' => __( 'sync-alt' ),
        'fas fa-syringe' => __( 'syringe' ),
        'fas fa-table' => __( 'table' ),
        'fas fa-table-tennis' => __( 'table-tennis' ),
        'fas fa-tablet' => __( 'tablet' ),
        'fas fa-tablet-alt' => __( 'tablet-alt' ),
        'fas fa-tablets' => __( 'tablets' ),
        'fas fa-tachometer-alt' => __( 'tachometer-alt' ),
        'fas fa-tag' => __( 'tag' ),
        'fas fa-tags' => __( 'tags' ),
        'fas fa-tape' => __( 'tape' ),
        'fas fa-tasks' => __( 'tasks' ),
        'fas fa-taxi' => __( 'taxi' ),
        'fab fa-telegram' => __( 'telegram' ),
        'fab fa-telegram-plane' => __( 'telegram-plane' ),
        'fab fa-tencent-weibo' => __( 'tencent-weibo' ),
        'fas fa-terminal' => __( 'terminal' ),
        'fas fa-text-height' => __( 'text-height' ),
        'fas fa-text-width' => __( 'text-width' ),
        'fas fa-th' => __( 'th' ),
        'fas fa-th-large' => __( 'th-large' ),
        'fas fa-th-list' => __( 'th-list' ),
        'fab fa-themeisle' => __( 'themeisle' ),
        'fas fa-thermometer' => __( 'thermometer' ),
        'fas fa-thermometer-empty' => __( 'thermometer-empty' ),
        'fas fa-thermometer-full' => __( 'thermometer-full' ),
        'fas fa-thermometer-half' => __( 'thermometer-half' ),
        'fas fa-thermometer-quarter' => __( 'thermometer-quarter' ),
        'fas fa-thermometer-three-quarters' => __( 'thermometer-three-quarters' ),
        'fas fa-thumbs-down' => __( 'thumbs-down' ),
        'far fa-thumbs-down' => __( 'thumbs-down' ),
        'fas fa-thumbs-up' => __( 'thumbs-up' ),
        'far fa-thumbs-up' => __( 'thumbs-up' ),
        'fas fa-thumbtack' => __( 'thumbtack' ),
        'fas fa-ticket-alt' => __( 'ticket-alt' ),
        'fas fa-times' => __( 'times' ),
        'fas fa-times-circle' => __( 'times-circle' ),
        'far fa-times-circle' => __( 'times-circle' ),
        'fas fa-tint' => __( 'tint' ),
        'fas fa-toggle-off' => __( 'toggle-off' ),
        'fas fa-toggle-on' => __( 'toggle-on' ),
        'fas fa-trademark' => __( 'trademark' ),
        'fas fa-train' => __( 'train' ),
        'fas fa-transgender' => __( 'transgender' ),
        'fas fa-transgender-alt' => __( 'transgender-alt' ),
        'fas fa-trash' => __( 'trash' ),
        'fas fa-trash-alt' => __( 'trash-alt' ),
        'far fa-trash-alt' => __( 'trash-alt' ),
        'fas fa-tree' => __( 'tree' ),
        'fab fa-trello' => __( 'trello' ),
        'fab fa-tripadvisor' => __( 'tripadvisor' ),
        'fas fa-trophy' => __( 'trophy' ),
        'fas fa-truck' => __( 'truck' ),
        'fas fa-truck-loading' => __( 'truck-loading' ),
        'fas fa-truck-moving' => __( 'truck-moving' ),
        'fas fa-tty' => __( 'tty' ),
        'fab fa-tumblr' => __( 'tumblr' ),
        'fab fa-tumblr-square' => __( 'tumblr-square' ),
        'fas fa-tv' => __( 'tv' ),
        'fab fa-twitch' => __( 'twitch' ),
        'fab fa-twitter' => __( 'twitter' ),
        'fab fa-twitter-square' => __( 'twitter-square' ),
        'fab fa-typo3' => __( 'typo3' ),
        'fab fa-uber' => __( 'uber' ),
        'fab fa-uikit' => __( 'uikit' ),
        'fas fa-umbrella' => __( 'umbrella' ),
        'fas fa-underline' => __( 'underline' ),
        'fas fa-undo' => __( 'undo' ),
        'fas fa-undo-alt' => __( 'undo-alt' ),
        'fab fa-uniregistry' => __( 'uniregistry' ),
        'fas fa-universal-access' => __( 'universal-access' ),
        'fas fa-university' => __( 'university' ),
        'fas fa-unlink' => __( 'unlink' ),
        'fas fa-unlock' => __( 'unlock' ),
        'fas fa-unlock-alt' => __( 'unlock-alt' ),
        'fab fa-untappd' => __( 'untappd' ),
        'fas fa-upload' => __( 'upload' ),
        'fab fa-usb' => __( 'usb' ),
        'fas fa-user' => __( 'user' ),
        'far fa-user' => __( 'user' ),
        'fas fa-user-circle' => __( 'user-circle' ),
        'far fa-user-circle' => __( 'user-circle' ),
        'fas fa-user-md' => __( 'user-md' ),
        'fas fa-user-plus' => __( 'user-plus' ),
        'fas fa-user-secret' => __( 'user-secret' ),
        'fas fa-user-times' => __( 'user-times' ),
        'fas fa-users' => __( 'users' ),
        'fab fa-ussunnah' => __( 'ussunnah' ),
        'fas fa-utensil-spoon' => __( 'utensil-spoon' ),
        'fas fa-utensils' => __( 'utensils' ),
        'fab fa-vaadin' => __( 'vaadin' ),
        'fas fa-venus' => __( 'venus' ),
        'fas fa-venus-double' => __( 'venus-double' ),
        'fas fa-venus-mars' => __( 'venus-mars' ),
        'fab fa-viacoin' => __( 'viacoin' ),
        'fab fa-viadeo' => __( 'viadeo' ),
        'fab fa-viadeo-square' => __( 'viadeo-square' ),
        'fas fa-vial' => __( 'vial' ),
        'fas fa-vials' => __( 'vials' ),
        'fab fa-viber' => __( 'viber' ),
        'fas fa-video' => __( 'video' ),
        'fas fa-video-slash' => __( 'video-slash' ),
        'fab fa-vimeo' => __( 'vimeo' ),
        'fab fa-vimeo-square' => __( 'vimeo-square' ),
        'fab fa-vimeo-v' => __( 'vimeo-v' ),
        'fab fa-vine' => __( 'vine' ),
        'fab fa-vk' => __( 'vk' ),
        'fab fa-vnv' => __( 'vnv' ),
        'fas fa-volleyball-ball' => __( 'volleyball-ball' ),
        'fas fa-volume-down' => __( 'volume-down' ),
        'fas fa-volume-off' => __( 'volume-off' ),
        'fas fa-volume-up' => __( 'volume-up' ),
        'fab fa-vuejs' => __( 'vuejs' ),
        'fas fa-warehouse' => __( 'warehouse' ),
        'fab fa-weibo' => __( 'weibo' ),
        'fas fa-weight' => __( 'weight' ),
        'fab fa-weixin' => __( 'weixin' ),
        'fab fa-whatsapp' => __( 'whatsapp' ),
        'fab fa-whatsapp-square' => __( 'whatsapp-square' ),
        'fas fa-wheelchair' => __( 'wheelchair' ),
        'fab fa-whmcs' => __( 'whmcs' ),
        'fas fa-wifi' => __( 'wifi' ),
        'fab fa-wikipedia-w' => __( 'wikipedia-w' ),
        'fas fa-window-close' => __( 'window-close' ),
        'far fa-window-close' => __( 'window-close' ),
        'fas fa-window-maximize' => __( 'window-maximize' ),
        'far fa-window-maximize' => __( 'window-maximize' ),
        'fas fa-window-minimize' => __( 'window-minimize' ),
        'far fa-window-minimize' => __( 'window-minimize' ),
        'fas fa-window-restore' => __( 'window-restore' ),
        'far fa-window-restore' => __( 'window-restore' ),
        'fab fa-windows' => __( 'windows' ),
        'fas fa-wine-glass' => __( 'wine-glass' ),
        'fas fa-won-sign' => __( 'won-sign' ),
        'fab fa-wordpress' => __( 'wordpress' ),
        'fab fa-wordpress-simple' => __( 'wordpress-simple' ),
        'fab fa-wpbeginner' => __( 'wpbeginner' ),
        'fab fa-wpexplorer' => __( 'wpexplorer' ),
        'fab fa-wpforms' => __( 'wpforms' ),
        'fas fa-wrench' => __( 'wrench' ),
        'fas fa-x-ray' => __( 'x-ray' ),
        'fab fa-xbox' => __( 'xbox' ),
        'fab fa-xing' => __( 'xing' ),
        'fab fa-xing-square' => __( 'xing-square' ),
        'fab fa-y-combinator' => __( 'y-combinator' ),
        'fab fa-yahoo' => __( 'yahoo' ),
        'fab fa-yandex' => __( 'yandex' ),
        'fab fa-yandex-international' => __( 'yandex-international' ),
        'fab fa-yelp' => __( 'yelp' ),
        'fas fa-yen-sign' => __( 'yen-sign' ),
        'fab fa-yoast' => __( 'yoast' ),
        'fab fa-youtube' => __( 'youtube' ),
        'fab fa-youtube-square' => __( 'youtube-square' ),
    );
}
