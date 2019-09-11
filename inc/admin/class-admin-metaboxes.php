<?php
/**
 * Metabox for Iteneraries fields.
 *
 * @package wp-travel\inc\admin
 */

/**
 * WP_Travel_Admin_Metaboxes Class.
 */
class WP_Travel_Admin_Metaboxes {
	/**
	 * Private var $post_type.
	 *
	 * @var string
	 */
	private static $post_type = WP_TRAVEL_POST_TYPE;
	/**
	 * Constructor WP_Travel_Admin_Metaboxes.
	 */
	public function __construct() {

		add_action( 'add_meta_boxes', array( $this, 'register_metaboxes' ), 10, 2 );
		add_action( 'do_meta_boxes', array( $this, 'remove_metaboxs' ), 10, 2 );
		add_filter( 'postbox_classes_' . WP_TRAVEL_POST_TYPE . '_wp-travel-' . WP_TRAVEL_POST_TYPE . '-detail', array( $this, 'add_clean_metabox_class' ) );
		add_filter( 'wp_travel_admin_tabs', array( $this, 'add_tabs' ) );
		add_action( 'admin_footer', array( $this, 'gallery_images_listing' ) );
		add_action( 'save_post', array( $this, 'save_meta_data' ) );
		add_filter( 'wp_travel_localize_gallery_data', array( $this, 'localize_gallery_data' ) );

	}

	/**
	 * Function to add tab.
	 *
	 * @param array $tabs Array list of all tabs.
	 * @return array
	 */
	public function add_tabs( $tabs ) {
		$trips = array(
			'detail'              => array(
				'tab_label'     => __( 'General', 'wp-travel' ),
				'content_title' => __( 'General', 'wp-travel' ),
				'priority'      => 10,
				'callback'      => 'wp_travel_trip_callback_detail',
				'icon'          => 'fa-sticky-note',
			),
			'itineraries_content' => array(
				'tab_label'     => __( 'Itinerary', 'wp-travel' ),
				'content_title' => __( 'Outline', 'wp-travel' ),
				'priority'      => 20,
				'callback'      => 'wp_travel_trip_callback_itineraries_content',
				'icon'          => 'fa-clipboard-list',
			),
			'price'               => array(
				'tab_label'     => __( 'Dates and Prices', 'wp-travel' ),
				'content_title' => __( 'Dates and Prices', 'wp-travel' ),
				'priority'      => 30,
				'callback'      => 'wp_travel_trip_callback_price',
				'icon'          => 'fa-tag',
			),
			'trip_includes'       => array(
				'tab_label'     => __( 'Includes/ Excludes', 'wp-travel' ),
				'content_title' => __( 'Trip Includes and Excludes', 'wp-travel' ),
				'priority'      => 40,
				'callback'      => 'wp_travel_trip_callback_trip_includes',
				'icon'          => 'fa-sliders-h',
			),
			'trip_facts'          => array(
				'tab_label'     => __( 'Facts', 'wp-travel' ),
				'content_title' => __( 'Trip Facts', 'wp-travel' ),
				'priority'      => 50,
				'callback'      => 'wp_travel_trip_callback_trip_facts',
				'icon'          => 'fa-industry',
			),
			'images_gallery'      => array(
				'tab_label'     => __( 'Gallery', 'wp-travel' ),
				'content_title' => __( 'Gallery', 'wp-travel' ),
				'priority'      => 60,
				'callback'      => 'wp_travel_trip_callback_images_gallery',
				'icon'          => 'fa-images',
			),
			'locations'           => array(
				'tab_label'     => __( 'Locations', 'wp-travel' ),
				'content_title' => __( 'Locations', 'wp-travel' ),
				'priority'      => 70,
				'callback'      => 'wp_travel_trip_callback_locations',
				'icon'          => 'fa-map-marked-alt',
			),
			'cart_checkout'       => array(
				'tab_label'     => __( 'Cart & Checkout', 'wp-travel' ),
				'content_title' => __( 'Cart & Checkout Options', 'wp-travel' ),
				'priority'      => 80,
				'callback'      => 'wp_travel_trip_callback_cart_checkout',
				'icon'          => 'fa-shopping-cart',
			),
			'inventory'           => array(
				'tab_label'     => __( 'Inventory Options', 'wp-travel' ),
				'content_title' => __( 'Trip Inventory', 'wp-travel' ),
				'priority'      => 90,
				'callback'      => 'wp_travel_trip_callback_inventory',
				'icon'          => 'fa-truck-moving',
			),
			'faq'                 => array(
				'tab_label'     => __( 'FAQs', 'wp-travel' ),
				'content_title' => __( 'FAQs', 'wp-travel' ),
				'priority'      => 100,
				'callback'      => 'wp_travel_trip_callback_faq',
				'icon'          => 'fa-question-circle',
			),
			'downloads'           => array(
				'tab_label'     => __( 'Downloads', 'wp-travel' ),
				'content_title' => __( 'Downloads', 'wp-travel' ),
				'priority'      => 110,
				'callback'      => 'wp_travel_trip_callback_downloads',
				'icon'          => 'fa-download',
			),
			'tabs'             => array(
				'tab_label'     => __( 'Tabs', 'wp-travel' ),
				'content_title' => __( 'Tabs', 'wp-travel' ),
				'priority'      => 120,
				'callback'      => 'wp_travel_trip_callback_tabs',
				'icon'          => 'fa-folder-open',
			),
			'misc_options'        => array(
				'tab_label'     => __( 'Misc. Options', 'wp-travel' ),
				'content_title' => __( 'Miscellanaous Options', 'wp-travel' ),
				'priority'      => 120,
				'callback'      => 'wp_travel_trip_callback_misc_options',
				// 'icon'          => 'fa-images',
			),

		);

		$tabs[ WP_TRAVEL_POST_TYPE ] = $trips;
		return apply_filters( 'wp_travel_tabs', $tabs );
	}

	/**
	 * Register metabox.
	 */
	public function register_metaboxes() {
		add_meta_box( 'wp-travel-' . WP_TRAVEL_POST_TYPE . '-detail', __( 'Trip Detail', 'wp-travel' ), array( $this, 'load_tab_template' ), WP_TRAVEL_POST_TYPE, 'normal', 'high' );
		// add_meta_box( 'wp-travel-' . WP_TRAVEL_POST_TYPE . '-info', __( 'Trip Info', 'wp-travel' ), array( $this, 'wp_travel_trip_info' ), WP_TRAVEL_POST_TYPE, 'side' );
		add_meta_box( 'wp-travel-itinerary-payment-detail', __( 'Payment Detail', 'wp-travel' ), array( $this, 'wp_travel_payment_info' ), 'itinerary-booking', 'normal', 'low' );
		add_meta_box( 'wp-travel-itinerary-single-payment-detail', __( 'Payment Info', 'wp-travel' ), array( $this, 'wp_travel_single_payment_info' ), 'itinerary-booking', 'side', 'low' );
		remove_meta_box( 'travel_locationsdiv', WP_TRAVEL_POST_TYPE, 'side' );
	}

	/**
	 * Payment Info Metabox info
	 *
	 * @param Object $post Current Post Object.
	 * @return void
	 */
	public function wp_travel_payment_info( $post ) {
		if ( ! $post ) {
			return;
		}
		$booking_id   = $post->ID;
		$payment_data = wp_travel_payment_data( $booking_id );

		if ( $payment_data && count( $payment_data ) > 0 ) {
			foreach ( $payment_data as $payment_args ) {

				if ( isset( $payment_args['data'] ) && ( is_object( $payment_args['data'] ) || is_array( $payment_args['data'] ) ) ) : ?>
					<table>
						<?php if ( isset( $payment_args['payment_id'] ) ) : ?>
							<tr>
								<th align="right"><?php esc_html_e( 'Payment ID : ', 'wp-travel' ); ?></th>
								<td><?php echo esc_html( $payment_args['payment_id'] ); ?></td>
							</tr>
						<?php endif; ?>
						<?php if ( isset( $payment_args['payment_method'] ) ) : ?>
							<tr>
								<th align="right"><?php esc_html_e( 'Payment Method : ', 'wp-travel' ); ?></th>
								<td><?php echo esc_html( $payment_args['payment_method'] ); ?></td>
							</tr>
						<?php endif; ?>	
						<?php if ( isset( $payment_args['payment_date'] ) ) : ?>
							<tr>
								<th align="right"><?php esc_html_e( 'Payment Date : ', 'wp-travel' ); ?></th>
								<td><?php echo esc_html( $payment_args['payment_date'] ); ?></td>
							</tr>
						<?php endif; ?>			
						<?php foreach ( $payment_args['data'] as $title => $description ) : ?>
							<tr>
								<th align="right"><?php echo esc_html( $title . ' : ' ); ?></th>
								<td>
									<?php
									if ( is_array( $description ) || is_object( $description ) ) {
										$description = (array) $description;
										if ( count( $description ) > 0 ) {
											echo '<pre>';
											print_r( $description );
											echo '</pre>';
										}
									} else {
										echo esc_html( $description );
									}
									?>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
					<hr>
					<?php
				endif;
			}
		}
	}

	/**
	 * Payment Info Metabox info
	 *
	 * @param Object $post Current Post Object.
	 * @return void
	 */
	public function wp_travel_single_payment_info( $post ) {
		if ( ! $post ) {
			return;
		}
		$booking_id = $post->ID;

		$payment_id = get_post_meta( $booking_id, 'wp_travel_payment_id', true );
		$payment_id = wp_travel_get_payment_id( $booking_id );
		if ( ! $payment_id ) {
			$title      = 'Payment - #' . $booking_id;
			$post_array = array(
				'post_title'   => $title,
				'post_content' => '',
				'post_status'  => 'publish',
				'post_slug'    => uniqid(),
				'post_type'    => 'wp-travel-payment',
			);
			$payment_id = wp_insert_post( $post_array );
			update_post_meta( $booking_id, 'wp_travel_payment_id', $payment_id );
		}
		$status = wp_travel_get_payment_status();

		$details        = wp_travel_booking_data( $booking_id );
		$payment_status = $details['payment_status'];
		?>
		<table>
			<tr>
				<td><strong><?php esc_html_e( 'Status', 'wp-travel' ); ?></strong></td>
				<td>
				<select id="wp_travel_payment_status" name="wp_travel_payment_status" >
				<?php foreach ( $status as $value => $st ) : ?>
					<option value="<?php echo esc_html( $value ); ?>" <?php selected( $value, $payment_status ); ?>>
						<?php echo esc_html( $status[ $value ]['text'] ); ?>
					</option>
				<?php endforeach; ?>
				</select>
				</td>

			</tr>
			<?php if ( 'paid' === $payment_status ) : ?>
				<?php
				$total_price = $details['total'];
				$paid_amount = $details['paid_amount'];
				$due_amount  = $details['due_amount'];
				?>
				<tr>
					<td><strong><?php esc_html_e( 'Payment Mode', 'wp-travel' ); ?></strong></td>
					<td><?php echo esc_html( $details['payment_mode'] ); ?></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Total Price', 'wp-travel' ); ?></strong></td>
					<td><?php echo wp_travel_get_formated_price_currency( $total_price ); ?></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Paid Amount', 'wp-travel' ); ?></strong></td>
					<td><?php echo wp_travel_get_formated_price_currency( $paid_amount ); ?></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Due Amount', 'wp-travel' ); ?></strong></td>
					<td><?php echo wp_travel_get_formated_price_currency( $due_amount ); ?></td>
				</tr>
			<?php endif; ?>
		</table>
		<?php
	}

	/**
	 * Remove metabox.
	 */
	public function remove_metaboxs() {
		remove_meta_box( 'postimagediv', WP_TRAVEL_POST_TYPE, 'side' );
		remove_meta_box( 'postexcerpt', WP_TRAVEL_POST_TYPE, 'normal' );
	}
	/**
	 * Clean Metabox Classes.
	 *
	 * @param array $classes Class list array.
	 */
	public function add_clean_metabox_class( $classes ) {
		array_push( $classes, 'wp-travel-clean-metabox wp-travel-styles' );
		return $classes;
	}

	/**
	 * HTML template for gallery list item.
	 */
	public function gallery_images_listing() {
		?>
		<script type="text/html" id="tmpl-my-template">
			<#
			if ( data.length > 0 ) {
				_.each( data, function( val ){
			#>
			<li data-attachmentid="{{val.id}}" id="wp-travel-gallery-image-list-{{val.id}}">
				<!-- <a href=""> -->
					<img src="{{val.url}}" width="100" title="<?php esc_html_e( 'Click to make featured image.', 'wp-travel' ); ?>"/>
					<span><?php esc_html_e( 'Delete', 'wp-travel' ); ?></span>
				<!-- </a> -->
			</li>
			<#
				});
			}
			#>
		</script>
		<?php
	}

	/**
	 * Load template for tab.
	 *
	 * @param  Object $post Post object.
	 */
	public function load_tab_template( $post ) {
		$args['post'] = $post;
		WP_Travel()->tabs->load( self::$post_type, $args );
	}

	/**
	 * Trip Info metabox. [ metabox is removed in utilities ]
	 *
	 * @param  Object $post Post object.
	 */
	public function wp_travel_trip_info( $post ) {
		if ( ! $post ) {
			return;
		}
		$trip_code = wp_travel_get_trip_code( $post->ID );
		?>
		<table class="form-table trip-info-sidebar">
			<tr>
				<td><label for="wp-travel-detail"><?php esc_html_e( 'Trip Code', 'wp-travel' ); ?></label></td>
				<td><input type="text" id="wp-travel-trip-code" disabled="disabled" value="<?php echo esc_attr( $trip_code ); ?>" /></td>
			</tr>
		</table>
		<?php
		if ( ! class_exists( 'WP_Travel_Utilities_Core' ) ) :
			$args = array(
				'title'      => __( 'Need Custom Trip Code ?', 'wp-travel' ),
				'content'    => __( 'By upgrading to Pro, you can get Trip Code Customization and removal features and more !', 'wp-travel' ),
				'link'       => 'https://wptravel.io/wp-travel-pro/',
        		'link_label' => __( 'Get WP Travel Pro', 'wp-travel' ),
				'link2'       => 'https://wptravel.io/downloads/wp-travel-utilities/',
				'link2_label' => __( 'Get WP Travel Utilities Addon', 'wp-travel' ),
			);
			wp_travel_upsell_message( $args );
		endif;
	}

	/**
	 * Save Post meta data.
	 *
	 * @param  int $post_id ID of current post.
	 *
	 * @return Mixed
	 */
	public function save_meta_data( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$post_type = get_post_type( $post_id );

		// If this isn't a WP_TRAVEL_POST_TYPE post, don't update it.
		if ( WP_TRAVEL_POST_TYPE !== $post_type ) {
			return;
		}

		// Return if action is quick edit.
		if ( isset( $_POST['action'] ) && 'inline-save' === $_POST['action'] ) {
			return;
		}

		remove_action( 'save_post', array( $this, 'save_meta_data' ) );
		if ( isset( $_POST['wp_travel_save_data'] ) && ! wp_verify_nonce( $_POST['wp_travel_save_data'], 'wp_travel_save_data_process' ) ) {
			return;
		}

		if ( isset( $_POST['wp_travel_editor'] ) ) {
			$new_content = $_POST['wp_travel_editor'];
			$old_content = get_post_field( 'post_content', $post_id );
			if ( ! wp_is_post_revision( $post_id ) && $old_content !== $new_content ) {
				$args = array(
					'ID'           => $post_id,
					'post_content' => $new_content,
				);

				// Unhook this function so it doesn't loop infinitely.
				remove_action( 'save_post', array( $this, 'save_meta_data' ) );
				// Update the post, which calls save_post again.
				wp_update_post( $args );
				// Re-hook this function.
				add_action( 'save_post', array( $this, 'save_meta_data' ) );
			}
		}

		$trip_meta = array();

		// Save pricing option type @since 1.7.6.
		$trip_meta['wp_travel_pricing_option_type'] = isset( $_POST['wp_travel_pricing_option_type'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_pricing_option_type'] ) ) : 'multiple-price'; // default multiple price @since 3.0.0.
		$trip_meta['wp_travel_price']               = isset( $_POST['wp_travel_price'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_price'] ) ) : 0;

		// Setting trip price.
		$trip_meta['wp_travel_trip_price'] = $trip_meta['wp_travel_price']; // This price is used to sort archive list by price so need to update accordingly [ for single and multiple pricing option ]

		$trip_meta['wp_travel_enable_sale'] = isset( $_POST['wp_travel_enable_sale'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_enable_sale'] ) ) : 0;
		$trip_meta['wp_travel_sale_price']  = isset( $_POST['wp_travel_sale_price'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_sale_price'] ) ) : 0;
		if ( $trip_meta['wp_travel_sale_price'] ) {
			// Update trip price.
			$trip_meta['wp_travel_trip_price'] = $trip_meta['wp_travel_sale_price'];
		}

		$trip_meta['wp_travel_price_per']           = isset( $_POST['wp_travel_price_per'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_price_per'] ) ) : '';
		$trip_meta['wp_travel_group_size']          = isset( $_POST['wp_travel_group_size'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_group_size'] ) ) : '';
		$trip_meta['wp_travel_trip_include']        = isset( $_POST['wp_travel_trip_include'] ) ? $_POST['wp_travel_trip_include'] : '';
		$trip_meta['wp_travel_trip_exclude']        = isset( $_POST['wp_travel_trip_exclude'] ) ? $_POST['wp_travel_trip_exclude'] : '';
		$trip_meta['wp_travel_outline']             = isset( $_POST['wp_travel_outline'] ) ? $_POST['wp_travel_outline'] : '';
		$trip_meta['wp_travel_start_date']          = isset( $_POST['wp_travel_start_date'] ) ? $_POST['wp_travel_start_date'] : '';
		$trip_meta['wp_travel_end_date']            = isset( $_POST['wp_travel_end_date'] ) ? $_POST['wp_travel_end_date'] : '';
		$trip_meta['wp_travel_trip_itinerary_data'] = isset( $_POST['wp_travel_trip_itinerary_data'] ) ? wp_unslash( $_POST['wp_travel_trip_itinerary_data'] ) : '';

		// Gallery.
		$gallery_ids = array();
		if ( isset( $_POST['wp_travel_gallery_ids'] ) && '' != $_POST['wp_travel_gallery_ids'] ) {
			$gallery_ids = explode( ',', $_POST['wp_travel_gallery_ids'] );
		}
		$trip_meta['wp_travel_itinerary_gallery_ids'] = $gallery_ids;
		$trip_meta['_thumbnail_id']                   = isset( $_POST['wp_travel_thumbnail_id'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_thumbnail_id'] ) ) : 0;

		$trip_meta['wp_travel_location']    = isset( $_POST['wp_travel_location'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_location'] ) ) : '';
		$trip_meta['wp_travel_lat']         = isset( $_POST['wp_travel_lat'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_lat'] ) ) : '';
		$trip_meta['wp_travel_lng']         = isset( $_POST['wp_travel_lng'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_lng'] ) ) : '';
		if ( empty( $trip_meta['wp_travel_location'] ) ) {
			$trip_meta['wp_travel_lat'] = '';
			$trip_meta['wp_travel_lng'] = '';
		}
		$trip_meta['wp_travel_location_id'] = isset( $_POST['wp_travel_location_id'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_location_id'] ) ) : '';

		$trip_meta['wp_travel_fixed_departure']                = isset( $_POST['wp_travel_fixed_departure'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_fixed_departure'] ) ) : 'no';
		$trip_meta['wp_travel_enable_pricing_options']         = isset( $_POST['wp_travel_enable_pricing_options'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_enable_pricing_options'] ) ) : 'no';
		$trip_meta['wp_travel_enable_multiple_fixed_departue'] = isset( $_POST['wp_travel_enable_multiple_fixed_departue'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_enable_multiple_fixed_departue'] ) ) : 'no';
		$trip_meta['wp_travel_trip_duration']                  = isset( $_POST['wp_travel_trip_duration'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_trip_duration'] ) ) : '';
		$trip_meta['wp_travel_trip_duration_night']            = isset( $_POST['wp_travel_trip_duration_night'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_trip_duration_night'] ) ) : '';

		// Saving Tabs Settings.
		$trip_meta['wp_travel_use_global_tabs'] = isset( $_POST['wp_travel_use_global_tabs'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_use_global_tabs'] ) ) : 'yes';
		$trip_meta['wp_travel_tabs']            = isset( $_POST['wp_travel_tabs'] ) ? ( wp_unslash( $_POST['wp_travel_tabs'] ) ) : array();
		// Trip enquiry Global.
		$trip_meta['wp_travel_use_global_trip_enquiry_option'] = isset( $_POST['wp_travel_use_global_trip_enquiry_option'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_use_global_trip_enquiry_option'] ) ) : 'yes';
		// Trip Specific Enquiry Option.
		$trip_meta['wp_travel_enable_trip_enquiry_option'] = isset( $_POST['wp_travel_enable_trip_enquiry_option'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_enable_trip_enquiry_option'] ) ) : 'no';

		$trip_meta['wp_travel_faq_question'] = isset( $_POST['wp_travel_faq_question'] ) ? ( wp_unslash( $_POST['wp_travel_faq_question'] ) ) : array();
		$trip_meta['wp_travel_faq_answer']   = isset( $_POST['wp_travel_faq_answer'] ) ? ( wp_unslash( $_POST['wp_travel_faq_answer'] ) ) : array();
		$trip_meta['wp_travel_is_global_faq']   = isset( $_POST['wp_travel_is_global_faq'] ) ? ( wp_unslash( $_POST['wp_travel_is_global_faq'] ) ) : array(); // fixes issue with is global faq values not updating when disabling pro. 

		// WP Travel Standard Paypal Merged. @since 1.2.1.
		$trip_meta['wp_travel_minimum_partial_payout']            = isset( $_POST['wp_travel_minimum_partial_payout'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_minimum_partial_payout'] ) ) : 0;
		$trip_meta['wp_travel_minimum_partial_payout_percent']    = isset( $_POST['wp_travel_minimum_partial_payout_percent'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_minimum_partial_payout_percent'] ) ) : 0;
		$trip_meta['wp_travel_minimum_partial_payout_use_global'] = isset( $_POST['wp_travel_minimum_partial_payout_use_global'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_minimum_partial_payout_use_global'] ) ) : '';

		// Update Pricing Options Metas. [ Multiple Pricing data ].
		$trip_meta['wp_travel_pricing_options'] = isset( $_POST['wp_travel_pricing_options'] ) ? ( wp_unslash( $_POST['wp_travel_pricing_options'] ) ) : '';

		if ( 'multiple-price' === $trip_meta['wp_travel_pricing_option_type'] && is_array( $trip_meta['wp_travel_pricing_options'] ) && count( $trip_meta['wp_travel_pricing_options'] ) > 0 ) {
			$pricing_options = $trip_meta['wp_travel_pricing_options'];
			// Need to update wp_travel_trip_price which is used to filter by price in archive page.
			$price_key = wp_travel_get_min_price_key( $pricing_options );
			$price     = wp_travel_get_actual_trip_price( $post_id, $price_key );
			if ( $price ) {
				$trip_meta['wp_travel_trip_price'] = $price;
			}
		}

		// Update multiple trip dates options.
		$wp_travel_multiple_trip_dates = array();
		if ( isset( $_POST['wp_travel_multiple_trip_dates'] ) ) {
			$wp_travel_multiple_trip_dates = ( wp_unslash( $_POST['wp_travel_multiple_trip_dates'] ) );

			foreach ( $wp_travel_multiple_trip_dates as $date_key => $date_value ) {

				if ( isset( $date_value['start_date'] ) && '' !== $date_value['start_date'] ) {
					$start_date = $date_value['start_date'];
					if ( ! wp_travel_is_ymd_date( $start_date ) ) {
						$start_date = wp_travel_format_ymd_date( $start_date, $date_format );
					}

					$wp_travel_multiple_trip_dates[ $date_key ]['start_date'] = $start_date;
				}

				if ( isset( $date_value['end_date'] ) && '' !== $date_value['end_date'] ) {
					$end_date = $date_value['end_date'];
					if ( ! wp_travel_is_ymd_date( $end_date ) ) {
						$end_date = wp_travel_format_ymd_date( $end_date, $date_format );
					}
					$wp_travel_multiple_trip_dates[ $date_key ]['end_date'] = $end_date;
				}
			}
			$wp_travel_multiple_trip_dates = ( wp_unslash( $wp_travel_multiple_trip_dates ) );
		}
		$trip_meta['wp_travel_multiple_trip_dates'] = $wp_travel_multiple_trip_dates;

		$wp_travel_trip_facts = array();
		if ( isset( $_POST['wp_travel_trip_facts'] ) ) {
			$wp_travel_trip_facts = array_filter( array_filter( array_values( $_POST['wp_travel_trip_facts'] ), 'array_filter' ), 'count' );
		}
		$trip_meta['wp_travel_trip_facts'] = $wp_travel_trip_facts;

		$wp_travel_tour_extras = array();
		if ( isset( $_POST['wp_travel_tour_extras'] ) ) {
			$wp_travel_tour_extras = stripslashes_deep( $_POST['wp_travel_tour_extras'] );
		}
		$trip_meta['wp_travel_tour_extras'] = $wp_travel_tour_extras;

		// @since 1.8.4
		$trip_meta = apply_filters( 'wp_travel_save_trip_metas', $trip_meta, $post_id );

		foreach ( $trip_meta as $meta_key => $meta_value ) {
			update_post_meta( $post_id, $meta_key, $meta_value );
		}

		// Ends WP Travel Standard Paypal Merged. @since 1.2.1.
		do_action( 'wp_travel_itinerary_extra_meta_save', $post_id );
	}

	/**
	 * Localize variable for Gallery.
	 *
	 * @param  array $data Values.
	 * @return array.
	 */
	public function localize_gallery_data( $data ) {
		global $post;
		if ( ! $post ) {
			return;
		}
		$gallery_ids = get_post_meta( $post->ID, 'wp_travel_itinerary_gallery_ids', true );
		if ( false !== $gallery_ids && ! empty( $gallery_ids ) ) {
			$gallery_data  = array();
			$i             = 0;
			$_thumbnail_id = get_post_meta( $post->ID, '_thumbnail_id', true );
			foreach ( $gallery_ids as $id ) {
				if ( 0 === $i && '' === $_thumbnail_id ) {
					$_thumbnail_id = $id;
				}
				$gallery_data[ $i ]['id']  = $id;
				$gallery_data[ $i ]['url'] = wp_get_attachment_thumb_url( $id );
				$i++;
			}
			$data['gallery_data']  = $gallery_data;
			$data['_thumbnail_id'] = $_thumbnail_id;
		}
		return $data;
	}
}

new WP_Travel_Admin_Metaboxes();
