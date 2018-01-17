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
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'additional_info_tab_callback' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'itineraries_content_call_back' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'gallery_tab_callback' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'location_tab_callback' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'advance_tab_callback' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'call_back' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'price_tab_call_back' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'trip_includes_callback' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'trip_excludes_callback' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'frontend_tabs_content_call_back' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_' . WP_TRAVEL_POST_TYPE, array( $this, 'wp_travel_faq_callback' ), 10, 2 );
		
	}

	/**
	 * Register metabox.
	 */
	public function register_metaboxes() {
		add_meta_box( 'wp-travel-' . WP_TRAVEL_POST_TYPE . '-detail', __( 'Trip Detail', 'wp-travel' ), array( $this, 'load_tab_template' ), WP_TRAVEL_POST_TYPE, 'normal', 'high' );
		add_meta_box( 'wp-travel-' . WP_TRAVEL_POST_TYPE . '-info', __( 'Trip Info', 'wp-travel' ), array( $this, 'wp_travel_trip_info' ), WP_TRAVEL_POST_TYPE, 'side' );
		// remove_meta_box( 'itinerary_locationsdiv', WP_TRAVEL_POST_TYPE, 'side' );
		// remove_meta_box( 'itinerary_typesdiv', WP_TRAVEL_POST_TYPE, 'side' );
		remove_meta_box( 'travel_locationsdiv', WP_TRAVEL_POST_TYPE, 'side' );
		// remove_meta_box( 'tagsdiv-travel_keywords', WP_TRAVEL_POST_TYPE, 'side' );
	}

	/**
	 * Remove metabox.
	 */
	public function remove_metaboxs() {		
		remove_meta_box( 'postimagediv', WP_TRAVEL_POST_TYPE,'side' );
	}
	/**
	 * Clean Metabox Classes.
	 *
	 * @param array $classes Class list array.
	 */
	function add_clean_metabox_class( $classes ) {
		array_push( $classes, 'wp-travel-clean-metabox' );
		return $classes;
	}

	/**
	 * Function to add tab.
	 *
	 * @param array $tabs Array list of all tabs.
	 * @return array
	 */
	function add_tabs( $tabs ) {
		$trips['detail'] = array(
			'tab_label' => __( 'Description', 'wp-travel' ),
			'content_title' => __( 'Description', 'wp-travel' ),
		);
		$trips['itineraries_content'] = array(
			'tab_label' => __( 'Itinerary', 'wp-travel' ),
			'content_title' => __( 'Outline', 'wp-travel' ),
		);
		$trips['price'] = array(
			'tab_label' => __( 'Price', 'wp-travel' ),
			'content_title' => __( 'Price', 'wp-travel' ),
		);
		$trips['trip_includes'] = array(
			'tab_label' => __( 'Includes/ Excludes', 'wp-travel' ),
			'content_title' => __( 'Trip Includes and Excludes', 'wp-travel' ),
		);
		$trips['additional_info'] = array(
			'tab_label' => __( 'Additional Info', 'wp-travel' ),
			'content_title' => __( 'Additional Info', 'wp-travel' ),
		);
		$trips['images_gallery'] = array(
			'tab_label' => __( 'Gallery', 'wp-travel' ),
			'content_title' => __( 'Gallery', 'wp-travel' ),
			// 'content_callback' => array( $this, 'gallery_tab_callback' ),
		);

		$trips['locations'] = array(
			'tab_label' => __( 'Locations', 'wp-travel' ),
			'content_title' => __( 'Locations', 'wp-travel' ),
			// 'content_callback' => array( $this, 'call_back' ),
		);
		
		$trips['faq'] = array(
			'tab_label' => __( 'FAQs', 'wp-travel' ),
			'content_title' => __( 'FAQs', 'wp-travel' ),
			// 'content_callback' => array( $this, 'call_back' ),
		);
		$trips['setting'] = array(
			'tab_label' => __( 'Tabs', 'wp-travel' ),
			'content_title' => __( 'Tabs', 'wp-travel' ),
			// 'content_callback' => array( $this, 'call_back' ),
		);

		// $trips['advanced'] = array(
		// 	'tab_label' => __( 'Advanced', 'wp-travel' ),
		// 	'content_title' => __( 'Advanced Options', 'wp-travel' ),
		// 	'content_callback' => array( $this, 'call_back' ),
		// );

		$tabs[ WP_TRAVEL_POST_TYPE ] = $trips;
		return apply_filters( 'wp_travel_tabs', $tabs );
	}

	/**
	 * Callback Function for Description Tabs.
	 *
	 * @param  string $tab tab name 'Description'.
	 * @return Mixed
	 */
	function description_tab_callback( $tab ) {
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
	function price_tab_call_back( $tab ) {		
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
	function additional_info_tab_callback( $tab ) {
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
	function gallery_tab_callback( $tab ) {
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
	function location_tab_callback( $tab ) {
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
	function advance_tab_callback( $tab ) {
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
	function trip_includes_callback( $tab ) {
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
	function trip_excludes_callback( $tab ) {
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
	function call_back( $tab ) {
		global $post;
		if ( 'advanced' !== $tab ) {
			return;
		}
		$thumbnail_id = get_post_meta( $post->ID, '_thumbnail_id', true );
		// echo _wp_post_thumbnail_html( $thumbnail_id, $post->ID );
	}
	/**
	 * Callback Function For Itineraries Content Tabs
	 * 	
	 * @param string $tab tab name 'itineraries_content'
	 * @return Mixed
	 */
	function itineraries_content_call_back( $tab ) {
		
		global $post;

		if( 'itineraries_content' !== $tab ) {
			return;
		}

		WP_Travel()->tabs->content( 'itineraries/itineraries-content.php' );

	 }

	 /**
	 * Callback Function For Itineraries Content Tabs
	 * 	
	 * @param string $tab tab name 'itineraries_content'
	 * @return Mixed
	 */
	function frontend_tabs_content_call_back( $tab, $args ) {
		if( 'setting' !== $tab ) {
			return;
		}
		$post_id = $args['post']->ID;
		$tabs = wp_travel_get_frontend_tabs();
		
		if ( is_array( $tabs ) && count( $tabs ) > 0 ) {
			echo '<ul class="wp-travel-sorting-tabs">';
			foreach ( $tabs as $key => $tab ) : ?>
				<li class="clearfix">
					<div class="wp-travel-sorting-handle">
					</div>
					<div class="wp-travel-sorting-tabs-wrap">
						<span class="wp-travel-tab-label"><?php echo esc_html( $tab['label'] ); ?></span>
						<input type="text" class="wp_travel_tabs_input-field" name="wp_travel_tabs[<?php echo esc_attr( $key ) ?>][label]" value="<?php echo esc_html( $tab['label'] ); ?>" placeholder="<?php echo esc_html( $tab['global_label'] ); ?>" />
						
						<input type="hidden" name="wp_travel_tabs[<?php echo esc_attr( $key ) ?>][use_global_label]" value="no" />
						<input type="hidden" name="wp_travel_tabs[<?php echo esc_attr( $key ) ?>][show_in_menu]" value="no" />
						<span class="use-global-tab"><label><input name="wp_travel_tabs[<?php echo esc_attr( $key ) ?>][use_global_label]" type="checkbox" value="yes" <?php checked( 'yes', $tab['use_global_label'] ) ?> />Use global Label</label></span>
						<span class="show-in-frontend"><label><input name="wp_travel_tabs[<?php echo esc_attr( $key ) ?>][show_in_menu]" type="checkbox" value="yes" <?php checked( 'yes', $tab['show_in_menu'] ) ?> />Show in tab</label></span>
					</div>
				</li>
			<?php
			endforeach;
			echo '</ul>';
		}
	 }

	function wp_travel_faq_callback( $tab, $args ) {
		if ( 'faq' !== $tab ) {
			return;
		}  ?>

		<div class="wp-travel-tab-content-faq-header">
			<div class="wp-collapse-open">
				<a href="#"><span class="open-all" id="open-all">Open All</span></a>
				<a href="#"><span class="close-all" id="close-all">Close All</span></a>
			</div>
		</div>



		<ul id="tab-accordion" class="tab-accordion" style="margin-top:80px">
			<li>
				<h3 class="heading-accordion">
				<div class="wp-travel-sorting-handle">
				</div>
				<span class="wp-travel-accordion-title">
					How to sort menu item?
				</span>

					<input class="section_title" id="title-1"  type="text" name="faq-question[1]" placeholder="(add question)" value="How to sort menu item?">
					
					<span class="dashicons dashicons-no-alt hover-icon"></span>
					<span class="toggle-indicator"></span>
				</h3>
				<div>
					<textarea rows="6">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</textarea>
				</div>
			</li>
			<li>
				<h3 class="heading-accordion">
				<div class="wp-travel-sorting-handle">
				</div>
				<span class="wp-travel-accordion-title">
					How to sort menu item?
				</span>

					<input class="section_title" id="title-1"  type="text" name="faq-question[1]" placeholder="(add question)" value="How to sort menu item?">
					
					<span class="dashicons dashicons-no-alt hover-icon"></span>
					<span class="toggle-indicator"></span>
				</h3>
				<div>
					<textarea rows="6">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</textarea>
				</div>
			</li>
		</ul>
		<div class="wp-travel-faq-quest-button clearfix">		
			<input type="button" value="Add New Question" class="button button-primary wp-travel-faq-add-new">		
		</div>
		<?php
	} 

	/**
	 * HTML template for gallery list item.
	 */
	function gallery_images_listing() {
		?>
		<script type="text/html" id="tmpl-my-template">
			<# console.log( data ); #>
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
	function load_tab_template( $post ) {
		$args['post'] = $post;
		WP_Travel()->tabs->load( self::$post_type, $args );
	}

	/**
	 * Trip Info metabox.
	 *
	 * @param  Object $post Post object.
	 */
	function wp_travel_trip_info( $post ) {
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
	}

	/**
	 * Save Post meta data.
	 *
	 * @param  int $post_id ID of current post.
	 *
	 * @return Mixed
	 */
	function save_meta_data( $post_id ) {

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
		$wp_travel_trip_price = 0;
		// Additional Info section.
		if ( isset( $_POST['wp_travel_price'] ) ) {
			$wp_travel_price = sanitize_text_field( wp_unslash( $_POST['wp_travel_price'] ) );
			update_post_meta( $post_id, 'wp_travel_price', $wp_travel_price );
			$wp_travel_trip_price = $wp_travel_price;
		}

		if ( isset( $_POST['wp_travel_price_per'] ) ) {
			$wp_travel_price_per = sanitize_text_field( wp_unslash( $_POST['wp_travel_price_per'] ) );
			update_post_meta( $post_id, 'wp_travel_price_per', $wp_travel_price_per );
		}

		$wp_travel_enable_sale = 0;
		if ( isset( $_POST['wp_travel_enable_sale'] ) ) {
			$wp_travel_enable_sale = sanitize_text_field( wp_unslash( $_POST['wp_travel_enable_sale'] ) );	
		}
		update_post_meta( $post_id, 'wp_travel_enable_sale', $wp_travel_enable_sale );
		if ( isset( $_POST['wp_travel_sale_price'] ) ) {
			$wp_travel_sale_price = sanitize_text_field( wp_unslash( $_POST['wp_travel_sale_price'] ) );
			update_post_meta( $post_id, 'wp_travel_sale_price', $wp_travel_sale_price );
			$wp_travel_trip_price = $wp_travel_sale_price;
		}
		update_post_meta( $post_id, 'wp_travel_trip_price', $wp_travel_trip_price );

		if ( isset( $_POST['wp_travel_group_size'] ) ) {
			$wp_travel_group_size = sanitize_text_field( wp_unslash( $_POST['wp_travel_group_size'] ) );
			update_post_meta( $post_id, 'wp_travel_group_size', $wp_travel_group_size );
		}

		if ( isset( $_POST['wp_travel_trip_include'] ) ) {
			$wp_travel_trip_include = $_POST['wp_travel_trip_include'];
			update_post_meta( $post_id, 'wp_travel_trip_include', $wp_travel_trip_include );
		}
		if ( isset( $_POST['wp_travel_trip_exclude'] ) ) {
			$wp_travel_trip_exclude = $_POST['wp_travel_trip_exclude'];
			update_post_meta( $post_id, 'wp_travel_trip_exclude', $wp_travel_trip_exclude );
		}
		if ( isset( $_POST['wp_travel_outline'] ) ) {
			$wp_travel_outline = $_POST['wp_travel_outline'];
			update_post_meta( $post_id, 'wp_travel_outline', $wp_travel_outline );
		}

		if ( isset( $_POST['wp_travel_start_date'] ) ) {
			$wp_travel_start_date = sanitize_text_field( wp_unslash( $_POST['wp_travel_start_date'] ) );
			update_post_meta( $post_id, 'wp_travel_start_date', $wp_travel_start_date );
		}

		if ( isset( $_POST['wp_travel_end_date'] ) ) {
			$wp_travel_end_date = sanitize_text_field( wp_unslash( $_POST['wp_travel_end_date'] ) );
			update_post_meta( $post_id, 'wp_travel_end_date', $wp_travel_end_date );
		}

		// Itinerary Details Data.

		if( isset( $_POST['wp_travel_trip_itinerary_data'] ) ) {

			$wp_travel_trip_itinerary_data =  wp_unslash( $_POST['wp_travel_trip_itinerary_data'] );
			update_post_meta( $post_id, 'wp_travel_trip_itinerary_data', $wp_travel_trip_itinerary_data );

		}

		// Gallery.
		$gallery_ids = array();
		if ( isset( $_POST['wp_travel_gallery_ids'] ) && '' != $_POST['wp_travel_gallery_ids'] ) {
			$gallery_ids = explode( ',', $_POST['wp_travel_gallery_ids'] );
		}
		update_post_meta( $post_id, 'wp_travel_itinerary_gallery_ids', $gallery_ids );

		if ( isset( $_POST['wp_travel_thumbnail_id'] ) ) {
			$wp_travel_thumbnail_id = (int) $_POST['wp_travel_thumbnail_id'];
			update_post_meta( $post_id, '_thumbnail_id', $wp_travel_thumbnail_id );
		}

		if ( isset( $_POST['wp_travel_location'] ) ) {
			$wp_travel_location = sanitize_text_field( wp_unslash( $_POST['wp_travel_location'] ) );
			update_post_meta( $post_id, 'wp_travel_location', $wp_travel_location );
		}

		if ( isset( $_POST['wp_travel_lat'] ) ) {
			$wp_travel_lat = sanitize_text_field( wp_unslash( $_POST['wp_travel_lat'] ) );
			update_post_meta( $post_id, 'wp_travel_lat', $wp_travel_lat );
		}

		if ( isset( $_POST['wp_travel_lng'] ) ) {
			$wp_travel_lng = sanitize_text_field( wp_unslash( $_POST['wp_travel_lng'] ) );
			update_post_meta( $post_id, 'wp_travel_lng', $wp_travel_lng );
		}
		if ( isset( $_POST['wp_travel_location_id'] ) ) {
			$wp_travel_location_id = sanitize_text_field( wp_unslash( $_POST['wp_travel_location_id'] ) );
			update_post_meta( $post_id, 'wp_travel_location_id', $wp_travel_location_id );
		}
	
		$fixed_departure = 'no';
		if ( isset( $_POST['wp_travel_fixed_departure'] ) ) {
			$fixed_departure = sanitize_text_field( wp_unslash( $_POST['wp_travel_fixed_departure'] ) );
		}
		update_post_meta( $post_id, 'wp_travel_fixed_departure', $fixed_departure );

		
		if ( isset( $_POST['wp_travel_trip_duration'] ) ) {
			$trip_duration = sanitize_text_field( wp_unslash( $_POST['wp_travel_trip_duration'] ) );
			update_post_meta( $post_id, 'wp_travel_trip_duration', $trip_duration );
		}
		if ( isset( $_POST['wp_travel_trip_duration_night'] ) ) {
			$trip_duration_night = sanitize_text_field( wp_unslash( $_POST['wp_travel_trip_duration_night'] ) );
			update_post_meta( $post_id, 'wp_travel_trip_duration_night', $trip_duration_night );
		}

		// Saving Tabs Settings
		
		if ( isset( $_POST['wp_travel_tabs'] ) ) {
			// $wp_travel_tabs = array_map( 'esc_attr', $_POST['wp_travel_tabs'] );
			$wp_travel_tabs = ( wp_unslash( $_POST['wp_travel_tabs'] ) );
			update_post_meta( $post_id, 'wp_travel_tabs', $wp_travel_tabs );
		}
		if ( isset( $_POST['wp_travel_editor'] ) && ! empty( $_POST['wp_travel_editor'] ) ) {
			$new_content = $_POST['wp_travel_editor'];
			$old_content = get_post_field( 'post_content', $post_id );
			if ( ! wp_is_post_revision( $post_id ) && $old_content !== $new_content ) {
				$args = array(
					'ID' => $post_id,
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

		do_action( 'wp_travel_itinerary_extra_meta_save', $post_id );
	}

	/**
	 * Localize variable for Gallery.
	 *
	 * @param  array $data Values.
	 * @return array.
	 */
	function localize_gallery_data( $data ) {
		global $post;
		if ( ! $post ) {
			return;
		}
		$gallery_ids = get_post_meta( $post->ID, 'wp_travel_itinerary_gallery_ids', true );
		if ( false !== $gallery_ids && ! empty( $gallery_ids ) ) {
			$gallery_data = array();
			$i = 0;
			$_thumbnail_id = get_post_meta( $post->ID, '_thumbnail_id', true );
			foreach ( $gallery_ids as $id ) {
				if ( 0 === $i && '' === $_thumbnail_id ) {
					$_thumbnail_id = $id;
				}
				$gallery_data[ $i ]['id'] = $id;
				$gallery_data[ $i ]['url'] = wp_get_attachment_thumb_url( $id );
				$i++;
			}
			$data['gallery_data'] = $gallery_data;
			$data['_thumbnail_id'] = $_thumbnail_id;
		}
		return $data;
	}
}

new WP_Travel_Admin_Metaboxes();
