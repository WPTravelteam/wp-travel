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
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'description_tab_callback' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'itineraries_content_call_back' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'gallery_tab_callback' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'location_tab_callback' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'advance_tab_callback' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'call_back' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'price_tab_call_back' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'trip_includes_callback' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'trip_excludes_callback' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'trip_facts_callback' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'frontend_tabs_content_call_back' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'wp_travel_faq_callback' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'wp_travel_misc_options_callback' ), 10, 2 );

	}

	/**
	 * Register metabox.
	 */
	public function register_metaboxes() {
		add_meta_box( 'wp-travel-' . WP_TRAVEL_POST_TYPE . '-detail', __( 'Trip Detail', 'wp-travel' ), array( $this, 'load_tab_template' ), WP_TRAVEL_POST_TYPE, 'normal', 'high' );
		add_meta_box( 'wp-travel-' . WP_TRAVEL_POST_TYPE . '-info', __( 'Trip Info', 'wp-travel' ), array( $this, 'wp_travel_trip_info' ), WP_TRAVEL_POST_TYPE, 'side' );
		remove_meta_box( 'travel_locationsdiv', WP_TRAVEL_POST_TYPE, 'side' );
		add_meta_box( 'wp-travel-itinerary-payment-detail', __( 'Payment Detail', 'wp-travel' ), array( $this, 'wp_travel_payment_info' ), 'itinerary-booking', 'normal', 'low' );
		add_meta_box( 'wp-travel-itinerary-single-payment-detail', __( 'Payment Info', 'wp-travel' ), array( $this, 'wp_travel_single_payment_info' ), 'itinerary-booking', 'side', 'low' );
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
					<td><?php echo esc_html( wp_travel_get_currency_symbol() . ' ' . $total_price ); ?></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Paid Amount', 'wp-travel' ); ?></strong></td>
					<td><?php echo esc_html( wp_travel_get_currency_symbol() . ' ' . $paid_amount ); ?></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Due Amount', 'wp-travel' ); ?></strong></td>
					<td><?php echo esc_html( wp_travel_get_currency_symbol() . ' ' . $due_amount ); ?></td>
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
	}
	/**
	 * Clean Metabox Classes.
	 *
	 * @param array $classes Class list array.
	 */
	public function add_clean_metabox_class( $classes ) {
		array_push( $classes, 'wp-travel-clean-metabox' );
		return $classes;
	}

	/**
	 * Function to add tab.
	 *
	 * @param array $tabs Array list of all tabs.
	 * @return array
	 */
	public function add_tabs( $tabs ) {
		$trips['detail']              = array(
			'tab_label'     => __( 'Description', 'wp-travel' ),
			'content_title' => __( 'Description', 'wp-travel' ),
		);
		$trips['itineraries_content'] = array(
			'tab_label'     => __( 'Itinerary', 'wp-travel' ),
			'content_title' => __( 'Outline', 'wp-travel' ),
		);
		$trips['price']               = array(
			'tab_label'     => __( 'Dates and Prices', 'wp-travel' ),
			'content_title' => __( 'Dates and Prices', 'wp-travel' ),
		);
		$trips['trip_includes']       = array(
			'tab_label'     => __( 'Includes/ Excludes', 'wp-travel' ),
			'content_title' => __( 'Trip Includes and Excludes', 'wp-travel' ),
		);
		$trips['trip_facts']          = array(
			'tab_label'     => __( 'Facts', 'wp-travel' ),
			'content_title' => __( 'Trip Facts', 'wp-travel' ),
		);

		$trips['images_gallery'] = array(
			'tab_label'     => __( 'Gallery', 'wp-travel' ),
			'content_title' => __( 'Gallery', 'wp-travel' ),
		);

		$trips['locations'] = array(
			'tab_label'     => __( 'Locations', 'wp-travel' ),
			'content_title' => __( 'Locations', 'wp-travel' ),
		);

		$trips['faq']     = array(
			'tab_label'     => __( 'FAQs', 'wp-travel' ),
			'content_title' => __( 'FAQs', 'wp-travel' ),
		);
		$trips['setting'] = array(
			'tab_label'     => __( 'Tabs', 'wp-travel' ),
			'content_title' => __( 'Tabs', 'wp-travel' ),
		);

		$trips['misc_options'] = array(
			'tab_label'     => __( 'Misc. Options', 'wp-travel' ),
			'content_title' => __( 'Miscellanaous Options', 'wp-travel' ),
		);

		$tabs[ WP_TRAVEL_POST_TYPE ] = $trips;
		return apply_filters( 'wp_travel_tabs', $tabs );
	}

	/**
	 * Callback Function for Description Tabs.
	 *
	 * @param  string $tab tab name 'Description'.
	 * @return Mixed
	 */
	public function description_tab_callback( $tab ) {
		global $post;
		if ( 'detail' !== $tab ) {
			return;
		}
		WP_Travel()->tabs->content( 'itineraries/detail-tab.php' );
	}

	/**
	 * Callback Function for Price Tabs.
	 *
	 * @param  string $tab tab name 'price'.
	 * @since 1.0.7
	 * @return Mixed
	 */
	public function price_tab_call_back( $tab ) {
		global $post;
		if ( 'price' !== $tab ) {
			return;
		}
		WP_Travel()->tabs->content( 'itineraries/price-tab.php' );
	}
	/**
	 * Callback Function for additional_info Tabs.
	 *
	 * @param  string $tab tab name 'additional_info'.
	 * @return Mixed
	 */
	public function additional_info_tab_callback( $tab ) {
		global $post;
		if ( 'additional_info' !== $tab ) {
			return;
		}
		WP_Travel()->tabs->content( 'itineraries/additional-info-tab.php' );
	}

	/**
	 * Callback Function for images_gallery Tabs.
	 *
	 * @param  string $tab tab name 'images_gallery'.
	 * @return Mixed
	 */
	public function gallery_tab_callback( $tab ) {
		global $post;
		if ( 'images_gallery' !== $tab ) {
			return;
		}
		WP_Travel()->tabs->content( 'itineraries/gallery-tab.php' );
	}

	/**
	 * Callback Function for locations Tabs.
	 *
	 * @param  string $tab tab name 'locations'.
	 * @return Mixed
	 */
	public function location_tab_callback( $tab ) {
		global $post;
		if ( 'locations' !== $tab ) {
			return;
		}
		WP_Travel()->tabs->content( 'itineraries/location-tab.php' );
	}
	/**
	 * Callback Function for advanced Tabs.
	 *
	 * @param  string $tab tab name 'advanced'.
	 * @return Mixed
	 */
	public function advance_tab_callback( $tab ) {
		global $post;
		if ( 'advanced' !== $tab ) {
			return;
		}
		WP_Travel()->tabs->content( 'itineraries/advance-tab.php' );
	}
	/**
	 * Callback Function for Trip includes Tabs.
	 *
	 * @param  string $tab tab name 'advanced'.
	 * @return Mixed
	 */
	public function trip_includes_callback( $tab ) {
		global $post;
		if ( 'trip_includes' !== $tab ) {
			return;
		}
		WP_Travel()->tabs->content( 'itineraries/trip-includes.php' );
	}
	/**
	 * Callback Function for Trip excludes Tabs.
	 *
	 * @param  string $tab tab name 'advanced'.
	 * @return Mixed
	 */
	public function trip_excludes_callback( $tab ) {
		global $post;
		if ( 'trip_excludes' !== $tab ) {
			return;
		}
		WP_Travel()->tabs->content( 'itineraries/trip-excludes.php' );
	}

	/**
	 * Callback Function for advanced Tabs.
	 *
	 * @param  string $tab tab name 'advanced'.
	 * @return Mixed
	 */
	public function call_back( $tab ) {
		global $post;
		if ( 'advanced' !== $tab ) {
			return;
		}
		$thumbnail_id = get_post_meta( $post->ID, '_thumbnail_id', true );
	}
	/**
	 * Callback Function For Itineraries Content Tabs
	 *
	 * @param string $tab tab name 'itineraries_content'.
	 * @return Mixed
	 */
	public function itineraries_content_call_back( $tab ) {

		global $post;

		if ( 'itineraries_content' !== $tab ) {
			return;
		}

		WP_Travel()->tabs->content( 'itineraries/itineraries-content.php' );

	}

	/**
	 * Callback Function For Itineraries Content Tabs
	 *
	 * @param string $tab  tab name 'itineraries_content'.
	 * @param array  $args arguments function arugments.
	 * @return Mixed
	 */
	public function trip_facts_callback( $tab, $args ) {
		if ( 'trip_facts' !== $tab ) {
			return;
		}
		WP_Travel()->tabs->content( 'itineraries/fact-tab.php' );
	}

	/**
	 * Callback Function For Itineraries Content Tabs
	 *
	 * @param string $tab  tab name 'itineraries_content'.
	 * @param array  $args arguments function arugments.
	 * @return Mixed
	 */
	public function frontend_tabs_content_call_back( $tab, $args ) {
		if ( 'setting' !== $tab ) {
			return;
		}
		$post_id                   = $args['post']->ID;
		$tabs                      = wp_travel_get_frontend_tabs( false, false );
		$wp_travel_use_global_tabs = get_post_meta( $post_id, 'wp_travel_use_global_tabs', true );

		$enable_custom_itinerary_tabs = apply_filters( 'wp_travel_custom_itinerary_tabs', false );

		if ( is_array( $tabs ) && count( $tabs ) > 0 && ! $enable_custom_itinerary_tabs ) {
			?>
			<?php if ( ! class_exists( 'WP_Travel_Utilities' ) ) : ?>
				<div class="wp-travel-upsell-message">
					<div class="wp-travel-pro-feature-notice">
						<h4><?php esc_html_e( 'Need Additional Tabs ?', 'wp-travel' ); ?></h4>
						<p><?php esc_html_e( 'By upgrading to Pro, you can trip specific custom tabs addition options with customized content and sorting !', 'wp-travel' ); ?></p>
						<a target="_blank" href="https://themepalace.com/downloads/wp-travel-utilites/"><?php esc_html_e( 'Get WP Travel Utilities Addon', 'wp-travel' ); ?></a>
					</div>
				</div>
			<?php endif; ?>
				<table class="form-table">
					<tr>
						<td>
							<label for="wp-travel-use-global-tabs" class="show-in-frontend-label"><?php esc_html_e( 'Use Global Tabs Layout', 'wp-travel' ); ?></label>
							<input name="wp_travel_use_global_tabs" type="hidden"  value="no">
						<span class="show-in-frontend checkbox-default-design">
							<label data-on="ON" data-off="OFF">
							<input type="checkbox" name="wp_travel_use_global_tabs" id="wp-travel-use-global-tabs" value="yes" <?php checked( 'yes', $wp_travel_use_global_tabs ); ?> />
								<span class="switch">
								</span>
							</label>
						</span>
						</td>
					</tr>
					<tr>
						<td>
							<p class="description wp-travel-custom-tabs-message"><?php _e( 'Uncheck above checkbox to add custom tab settings for this trip.', 'wp-travel' ); ?> </p>
						</td>
					</tr>
				</table>
			<?php
			echo '<table class="wp-travel-sorting-tabs form-table">';
			?>
				<thead>
					<th width="50px"><?php esc_html_e( 'Sorting', 'wp-travel' ); ?></th>
					<th width="35%"><?php esc_html_e( 'Global Trip Title', 'wp-travel' ); ?></th>
					<th width="35%"><?php esc_html_e( 'Custom Trip Title', 'wp-travel' ); ?></th>
					<th width="20%"><?php esc_html_e( 'Display', 'wp-travel' ); ?></th>
				</thead>
				<tbody>
			<?php foreach ( $tabs as $key => $tab ) : ?>
				<tr>
					<td width="50px">
						<div class="wp-travel-sorting-handle">
						</div>
					</td>
					<td width="35%">
					<div class="wp-travel-sorting-tabs-wrap">
						<span class="wp-travel-tab-label wp-travel-accordion-title"><?php echo esc_html( $tab['label'] ); ?></span>
					</div>
					</td>
					<td>
						<div class="wp-travel-sorting-tabs-wrap">
						<input type="text" class="wp_travel_tabs_input-field section_title" name="wp_travel_tabs[<?php echo esc_attr( $key ); ?>][label]" value="<?php echo esc_html( $tab['label'] ); ?>" placeholder="<?php echo esc_html( $tab['label'] ); ?>" />
						<input type="hidden" name="wp_travel_tabs[<?php echo esc_attr( $key ); ?>][show_in_menu]" value="no" />
					</div>
					</td>
					<td width="20%">
						<span class="show-in-frontend checkbox-default-design">
							<label data-on="ON" data-off="OFF"><input name="wp_travel_tabs[<?php echo esc_attr( $key ); ?>][show_in_menu]" type="checkbox" value="yes" <?php checked( 'yes', $tab['show_in_menu'] ); ?> /><?php // esc_html_e( 'Display', 'wp-travel' ); ?>
							<span class="switch">
								</span>
							</label>
						</span>
						<span class="check-handeller"></span>
					</td>
				</tr>
				<?php
			endforeach;

			echo '</tbody></table>';
		}

		// Custom itinerary tabs support.
		do_action( 'wp_travel_itinerary_custom_tabs' );

	}

	/**
	 * Callback Function For Itineraries Content Tabs
	 *
	 * @param string $tab  tab name 'itineraries_content'.
	 * @param array  $args arguments function arugments.
	 * @return Mixed
	 */
	public function wp_travel_misc_options_callback( $tab, $args ) {

		if ( 'misc_options' !== $tab ) {
			return;
		}

		WP_Travel()->tabs->content( 'itineraries/misc-tab.php' );

	}

	/**
	 * Callback Function For Itineraries Content Tabs
	 *
	 * @param string $tab  tab name 'itineraries_content'.
	 * @param array  $args arguments function arugments.
	 * @return Mixed
	 */
	public function wp_travel_faq_callback( $tab, $args ) {
		if ( 'faq' !== $tab ) {
			return;
		}

		do_action( 'wp_travel_utils_itinerary_global_faq_settings' );

		$post_id           = $args['post']->ID;
			$faq_questions = get_post_meta( $post_id, 'wp_travel_faq_question', true );
		?>
		<?php if ( ! class_exists( 'WP_Travel_Utilities' ) ) : ?>
			<div class="wp-travel-upsell-message">
				<div class="wp-travel-pro-feature-notice">
					<h4><?php esc_html_e( 'Tired of updating repitative FAQs ?', 'wp-travel' ); ?></h4>
					<p><?php esc_html_e( 'By upgrading to Pro, you can create and use Global FAQs in all of your trips !', 'wp-travel' ); ?></p>
					<a target="_blank" href="https://themepalace.com/downloads/wp-travel-utilites/"><?php esc_html_e( 'Get WP Travel Utilities Addon', 'wp-travel' ); ?></a>
				</div>
			</div>
		<?php endif; ?>
		<div class="wp-travel-tab-content-faq-header clearfix">
			<?php
			if ( is_array( $faq_questions ) && count( $faq_questions ) != 0 ) :
				$empty_item_style    = 'display:none';
				$collapse_link_style = 'display:block';
			else :
				$empty_item_style    = 'display:block';
				$collapse_link_style = 'display:none';
			endif;
			?>

			<div class="while-empty" style="<?php echo esc_attr( $empty_item_style ); ?>">
				<p>
					<?php esc_html_e( 'Click on add new question to add FAQ.', 'wp-travel' ); ?>
				</p>
			</div>
			<div class="wp-collapse-open" style="<?php echo esc_attr( $collapse_link_style ); ?>" >
				<a href="#" data-parent="wp-travel-tab-content-faq" class="open-all-link"><span class="open-all" id="open-all"><?php esc_html_e( 'Open All', 'wp-travel' ); ?></span></a>
				<a data-parent="wp-travel-tab-content-faq" style="display:none;" href="#" class="close-all-link"><span class="close-all" id="close-all"><?php esc_html_e( 'Close All', 'wp-travel' ); ?></span></a>
			</div>
		</div>
		<div id="tab-accordion" class="tab-accordion">
			<div class="panel-group wp-travel-sorting-tabs" id="accordion-faq-data" role="tablist" aria-multiselectable="true">
				<?php if ( is_array( $faq_questions ) && count( $faq_questions ) > 0 ) : ?>

					<?php $faq_answers = get_post_meta( $post_id, 'wp_travel_faq_answer', true ); ?>

					<?php foreach ( $faq_questions as $key => $question ) : ?>
						<?php $question = ( '' !== $question ) ? $question : __( 'Untitled', 'wp-travel' ); ?>
						<div class="panel panel-default">
							<div class="panel-heading" role="tab" id="heading-<?php echo esc_attr( $key ); ?>">
								<h4 class="panel-title">
									<div class="wp-travel-sorting-handle"></div>
									<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion-faq-data" href="#collapse-<?php echo esc_attr( $key ); ?>" aria-expanded="true" aria-controls="collapse-<?php echo esc_attr( $key ); ?>">

										<span bind="faq_question_<?php echo esc_attr( $key ); ?>" class="faq-label"><?php echo esc_html( $question ); ?></span>

									<span class="collapse-icon"></span>
									</a>
									<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
								</h4>
							</div>
							<div id="collapse-<?php echo esc_attr( $key ); ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-"<?php echo esc_attr( $key ); ?>>
							<div class="panel-body">
								<div class="panel-wrap">
									<label><?php esc_html_e( 'Enter Your Question', 'wp-travel' ); ?></label>
									<input bind="faq_question_<?php echo esc_attr( $key ); ?>" type="text" class="faq-question-text" name="wp_travel_faq_question[]" placeholder="FAQ Question?" value="<?php echo esc_html( $question ); ?>">
								</div>
								<textarea rows="6" name="wp_travel_faq_answer[]" placeholder="Write Your Answer."><?php echo esc_attr( $faq_answers[ $key ] ); ?></textarea>
							</div>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
		<div class="wp-travel-faq-quest-button clearfix">
			<input type="button" value="Add New Question" class="button button-primary wp-travel-faq-add-new">
		</div>
		<script type="text/html" id="tmpl-wp-travel-faq">

			<div class="panel panel-default">
				<div class="panel-heading" role="tab" id="heading-{{data.random}}">
					<h4 class="panel-title">
						<div class="wp-travel-sorting-handle"></div>
						<a role="button" data-toggle="collapse" data-parent="#accordion-faq-data" href="#collapse-{{data.random}}" aria-expanded="true" aria-controls="collapse-{{data.random}}">

							<span bind="faq_question_{{data.random}}"><?php echo esc_html( 'FAQ?', 'wp-travel' ); ?></span>

						<span class="collapse-icon"></span>
						</a>
						<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
					</h4>
				</div>
				<div id="collapse-{{data.random}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-{{data.random}}">
					<div class="panel-body">
						<div class="panel-wrap">
							<label><?php esc_html_e( 'Enter Your Question', 'wp-travel' ); ?></label>
							<input bind="faq_question_{{data.random}}" type="text" name="wp_travel_faq_question[]" placeholder="FAQ Question?" class="faq-question-text" value="">
						</div>
						<textarea rows="6" name="wp_travel_faq_answer[]" placeholder="Write Your Answer."></textarea>
					</div>
				</div>
			</div>
		</script>
		<?php
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
	 * Trip Info metabox.
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
		<?php if ( ! class_exists( 'WP_Travel_Utilities' ) ) : ?>
			<div class="wp-travel-upsell-message">
				<div class="wp-travel-pro-feature-notice">
					<h4><?php esc_html_e( 'Need Custom Trip Code ?', 'wp-travel' ); ?></h4>
					<p><?php esc_html_e( 'By upgrading to Pro, you can get Trip Code Customization and removal features and more !', 'wp-travel' ); ?></p>
					<a target="_blank" href="https://themepalace.com/downloads/wp-travel-utilites/"><?php esc_html_e( 'Get WP Travel Utilities Addon', 'wp-travel' ); ?></a>
				</div>
			</div>
		<?php endif; ?>
		<?php
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
		$trip_meta['wp_travel_pricing_option_type'] = isset( $_POST['wp_travel_pricing_option_type'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_pricing_option_type'] ) ) : '';
		$trip_meta['wp_travel_price']               = isset( $_POST['wp_travel_price'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_price'] ) ) : 0;

		// Setting trip price.
		$trip_meta['wp_travel_trip_price'] = $trip_meta['wp_travel_price'];

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
		$trip_meta['wp_travel_location_id'] = isset( $_POST['wp_travel_location_id'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_location_id'] ) ) : '';

		$trip_meta['wp_travel_fixed_departure']                = isset( $_POST['wp_travel_fixed_departure'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_fixed_departure'] ) ) : 'no';
		$trip_meta['wp_travel_enable_pricing_options']         = isset( $_POST['wp_travel_enable_pricing_options'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_enable_pricing_options'] ) ) : 'no';
		$trip_meta['wp_travel_enable_multiple_fixed_departue'] = isset( $_POST['wp_travel_enable_multiple_fixed_departue'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_enable_multiple_fixed_departue'] ) ) : 'no';
		$trip_meta['wp_travel_trip_duration']                  = isset( $_POST['wp_travel_trip_duration'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_trip_duration'] ) ) : '';
		$trip_meta['wp_travel_trip_duration_night']            = isset( $_POST['wp_travel_trip_duration_night'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_trip_duration_night'] ) ) : '';

		// Saving Tabs Settings.
		$trip_meta['wp_travel_use_global_tabs'] = isset( $_POST['wp_travel_use_global_tabs'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_use_global_tabs'] ) ) : 'yes';
		$trip_meta['wp_travel_tabs']            = isset( $_POST['wp_travel_tabs'] ) ? ( wp_unslash( $_POST['wp_travel_tabs'] ) ) : array();
// error_log( print_r( $_POST, true ) );
		// Trip enquiry Global.
		$trip_meta['wp_travel_use_global_trip_enquiry_option'] = isset( $_POST['wp_travel_use_global_trip_enquiry_option'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_use_global_trip_enquiry_option'] ) ) : 'yes';
		// Trip Specific Enquiry Option.
		$trip_meta['wp_travel_enable_trip_enquiry_option'] = isset( $_POST['wp_travel_enable_trip_enquiry_option'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_enable_trip_enquiry_option'] ) ) : 'no';

		$trip_meta['wp_travel_faq_question'] = isset( $_POST['wp_travel_faq_question'] ) ? ( wp_unslash( $_POST['wp_travel_faq_question'] ) ) : '';
		$trip_meta['wp_travel_faq_answer']   = isset( $_POST['wp_travel_faq_answer'] ) ? ( wp_unslash( $_POST['wp_travel_faq_answer'] ) ) : '';

		// WP Travel Standard Paypal Merged. @since 1.2.1.
		$trip_meta['wp_travel_minimum_partial_payout']            = isset( $_POST['wp_travel_minimum_partial_payout'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_minimum_partial_payout'] ) ) : 0;
		$trip_meta['wp_travel_minimum_partial_payout_percent']    = isset( $_POST['wp_travel_minimum_partial_payout_percent'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_minimum_partial_payout_percent'] ) ) : 0;
		$trip_meta['wp_travel_minimum_partial_payout_use_global'] = isset( $_POST['wp_travel_minimum_partial_payout_use_global'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_minimum_partial_payout_use_global'] ) ) : '';

		// Update Pricing Options Metas.
		$trip_meta['wp_travel_pricing_options'] = isset( $_POST['wp_travel_pricing_options'] ) ? ( wp_unslash( $_POST['wp_travel_pricing_options'] ) ) : '';

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
