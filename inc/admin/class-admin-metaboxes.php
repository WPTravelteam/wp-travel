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
	private static $post_type = 'itineraries';
	/**
	 * Constructor WP_Travel_Admin_Metaboxes.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'register_metaboxes' ), 10, 2 );
		add_action( 'do_meta_boxes', array( $this, 'remove_metaboxs' ), 10, 2 );
		add_filter( 'postbox_classes_itineraries_wp-travel-itinerary-detail', array( $this, 'add_clean_metabox_class' ) );
		add_filter( 'wp_travel_admin_tabs', array( $this, 'add_tabs' ) );
		add_action( 'admin_footer', array( $this, 'gallery_images_listing' ) );
		add_action( 'save_post', array( $this, 'save_meta_data' ) );
		add_filter( 'wp_travel_localize_gallery_data', array( $this, 'localize_gallery_data' ) );
		add_action( 'wp_travel_tabs_content_itineraries', array( $this, 'detail_tab_callback' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_itineraries', array( $this, 'additional_info_tab_callback' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_itineraries', array( $this, 'gallery_tab_callback' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_itineraries', array( $this, 'location_tab_callback' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_itineraries', array( $this, 'advance_tab_callback' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_itineraries', array( $this, 'call_back' ), 10, 2 );
	}

	/**
	 * Register metabox.
	 */
	public function register_metaboxes() {
		add_meta_box( 'wp-travel-itinerary-detail', __( 'Itinerary Detail', 'wp-travel' ), array( $this, 'load_tab_template' ), 'itineraries', 'normal', 'high' );
		remove_meta_box( 'itinerary_locationsdiv', 'itineraries', 'side' );
		remove_meta_box( 'itinerary_typesdiv', 'itineraries', 'side' );
		remove_meta_box( 'travel_locationsdiv', 'itineraries', 'side' );
	}

	/**
	 * Remove metabox.
	 */
	public function remove_metaboxs() {		
		remove_meta_box( 'postimagediv','itineraries','side' );
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
		$itineraries['detail'] = array(
			'tab_label' => __( 'Details', 'wp-travel' ),
			'content_title' => __( 'Details', 'wp-travel' ),
		);

		$itineraries['additional_info'] = array(
			'tab_label' => __( 'Additional Info', 'wp-travel' ),
			'content_title' => __( 'Additional Info', 'wp-travel' ),
			'content_callback' => array( $this, 'call_back' ),
		);

		$itineraries['images_gallery'] = array(
			'tab_label' => __( 'Images/ Gallery', 'wp-travel' ),
			'content_title' => __( 'Images/ Gallery', 'wp-travel' ),
			'content_callback' => array( $this, 'gallery_tab_callback' ),
		);

		$itineraries['locations'] = array(
			'tab_label' => __( 'Locations', 'wp-travel' ),
			'content_title' => __( 'Locations', 'wp-travel' ),
			'content_callback' => array( $this, 'call_back' ),
		);

		// $itineraries['advanced'] = array(
		// 	'tab_label' => __( 'Advanced', 'wp-travel' ),
		// 	'content_title' => __( 'Advanced Options', 'wp-travel' ),
		// 	'content_callback' => array( $this, 'call_back' ),
		// );

		$tabs['itineraries'] = $itineraries;
		return apply_filters( 'wp_travel_tabs', $tabs );;
	}

	/**
	 * Callback Function for Detail Tabs.
	 *
	 * @param  string $tab tab name 'detail'.
	 * @return Mixed
	 */
	function detail_tab_callback( $tab ) {
		global $post;
		if ( 'detail' !== $tab ) {
			return;
		}
		WP_Travel()->tabs->content( 'itineraries/detail-tab.php' );
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

		// If this isn't a 'itineraries' post, don't update it.
		if ( 'itineraries' !== $post_type ) {
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

		if ( isset( $_POST['wp_traval_location'] ) ) {
			$wp_traval_location = sanitize_text_field( wp_unslash( $_POST['wp_traval_location'] ) );
			update_post_meta( $post_id, 'wp_traval_location', $wp_traval_location );
		}

		if ( isset( $_POST['wp_traval_lat'] ) ) {
			$wp_traval_lat = sanitize_text_field( wp_unslash( $_POST['wp_traval_lat'] ) );
			update_post_meta( $post_id, 'wp_traval_lat', $wp_traval_lat );
		}

		if ( isset( $_POST['wp_traval_lng'] ) ) {
			$wp_traval_lng = sanitize_text_field( wp_unslash( $_POST['wp_traval_lng'] ) );
			update_post_meta( $post_id, 'wp_traval_lng', $wp_traval_lng );
		}
		if ( isset( $_POST['wp_traval_location_id'] ) ) {
			$wp_traval_location_id = sanitize_text_field( wp_unslash( $_POST['wp_traval_location_id'] ) );
			update_post_meta( $post_id, 'wp_traval_location_id', $wp_traval_location_id );
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
