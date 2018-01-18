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
		$wp_travel_use_global_tabs = get_post_meta( $post_id, 'wp_travel_use_global_tabs', true );

		if ( is_array( $tabs ) && count( $tabs ) > 0 ) {
			?>
				<table class="form-table">
					<tr>
						<td><label for="wp-travel-use-global-tabs"><?php esc_html_e( 'Use Global Tabs Layout', 'wp-travel' ); ?></label></td>
						<td><input type="checkbox" name="wp_travel_use_global_tabs" id="wp-travel-use-global-tabs" value="yes" <?php checked( 'yes', $wp_travel_use_global_tabs ) ?> /></td>
					</tr>
				</table>
			<?php
			echo '<ul class="wp-travel-sorting-tabs">';
			foreach ( $tabs as $key => $tab ) : ?>
				<li class="clearfix">
					<div class="wp-travel-sorting-handle">
					</div>
					<div class="wp-travel-sorting-tabs-wrap">
						<span class="wp-travel-tab-label wp-travel-accordion-title"><?php echo esc_html( $tab['label'] ); ?></span>
						<input type="text" class="wp_travel_tabs_input-field section_title" name="wp_travel_tabs[<?php echo esc_attr( $key ) ?>][label]" value="<?php echo esc_html( $tab['label'] ); ?>" placeholder="<?php echo esc_html( $tab['label'] ); ?>" />
						<input type="hidden" name="wp_travel_tabs[<?php echo esc_attr( $key ) ?>][show_in_menu]" value="no" />
						<span class="show-in-frontend"><label><input name="wp_travel_tabs[<?php echo esc_attr( $key ) ?>][show_in_menu]" type="checkbox" value="yes" <?php checked( 'yes', $tab['show_in_menu'] ) ?> /><?php esc_html_e( 'Display', 'wp-travel' ); ?></label></span>
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
		} ?>

		<?php
		$post_id = $args['post']->ID;
			$faq_questions = get_post_meta( $post_id, 'wp_travel_faq_question', true );
		?>
		<div class="wp-travel-tab-content-faq-header clearfix">
			<?php
			if ( is_array( $faq_questions ) && count( $faq_questions ) != 0  ) :
				$empty_item_style = 'display:none';
				$collapse_link_style = 'display:block';
			else :
				$empty_item_style = 'display:block';
				$collapse_link_style = 'display:none';
			endif;
			?>
			
			<div class="while-empty" style="<?php echo esc_attr( $empty_item_style ) ?>">
				<p>
					<?php esc_html_e( 'Click on add new question to add FAQ.', 'wp-travel' ); ?>
				</p>
			</div>
			<div class="wp-collapse-open" style="<?php echo esc_attr( $collapse_link_style ) ?>" >
				<a href="#" class="open-all-link"><span class="open-all" id="open-all"><?php esc_html_e( 'Open All', 'wp-travel' ) ?></span></a>
				<a style="display:none;" href="#" class="close-all-link"><span class="close-all" id="close-all"><?php esc_html_e( 'Close All', 'wp-travel' ) ?></span></a>
			</div>
		</div>
		<div id="tab-accordion" class="tab-accordion">
			<div class="panel-group wp-travel-sorting-tabs" id="accordion-faq-data" role="tablist" aria-multiselectable="true">
				<?php if ( is_array( $faq_questions ) && count( $faq_questions ) > 0 ) : ?>
					
					<?php $faq_answers = get_post_meta( $post_id, 'wp_travel_faq_answer', true ); ?>
					
					<?php foreach( $faq_questions as $key => $question ) : ?>
					
						<div class="panel panel-default">
							<div class="panel-heading" role="tab" id="heading-"<?php echo esc_attr($key); ?>>
								<h4 class="panel-title">
									<div class="wp-travel-sorting-handle"></div>
									<a role="button" data-toggle="collapse" data-parent="#accordion-faq-data" href="#collapse-<?php echo esc_attr($key); ?>" aria-expanded="true" aria-controls="collapse-<?php echo esc_attr($key); ?>">
									
										<span bind="faq_question_<?php echo esc_attr($key); ?>" class="faq-label"><?php echo esc_html( $question ); ?></span>

									<span class="collapse-icon"></span>
									</a>
									<span class="dashicons dashicons-no-alt hover-icon close-faq"></span>
								</h4>
							</div>
							<div id="collapse-<?php echo esc_attr($key); ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-"<?php echo esc_attr($key); ?>>
							<div class="panel-body">
								<div class="panel-faq-question">
									<label><?php esc_html_e( 'Enter Your Question', 'wp-travel' ); ?></label>
									<input bind="faq_question_<?php echo esc_attr($key); ?>" type="text" class="faq-question-text" name="wp_travel_faq_question[]" placeholder="FAQ Question?" value="<?php echo esc_html( $question ); ?>">
								</div>
								<textarea rows="6" name="wp_travel_faq_answer[]" placeholder="Write Your Answer."><?php echo esc_attr( $faq_answers[ $key ] ) ?></textarea>
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
						<span class="dashicons dashicons-no-alt hover-icon close-faq"></span>
					</h4>
				</div>
				<div id="collapse-{{data.random}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-{{data.random}}">
					<div class="panel-body">
						<div class="panel-faq-question">
							<label><?php esc_html_e( 'Enter Your Question', 'wp-travel' ); ?></label>
							<input bind="faq_question_{{data.random}}" type="text" name="wp_travel_faq_question[]" placeholder="FAQ Question?" value="">
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
		$use_global_tabs = 'no';
		if ( isset( $_POST['wp_travel_use_global_tabs'] ) ) {
			$use_global_tabs = sanitize_text_field( wp_unslash( $_POST['wp_travel_use_global_tabs'] ) );
		}
			update_post_meta( $post_id, 'wp_travel_use_global_tabs', $use_global_tabs );
		
		if ( isset( $_POST['wp_travel_tabs'] ) ) {
			// $wp_travel_tabs = array_map( 'esc_attr', $_POST['wp_travel_tabs'] );
			$wp_travel_tabs = ( wp_unslash( $_POST['wp_travel_tabs'] ) );
			update_post_meta( $post_id, 'wp_travel_tabs', $wp_travel_tabs );
		}
		if ( isset( $_POST['wp_travel_faq_question'] ) ) {
			// $wp_travel_tabs = array_map( 'esc_attr', $_POST['wp_travel_tabs'] );
			$wp_travel_faq_question = ( wp_unslash( $_POST['wp_travel_faq_question'] ) );
			update_post_meta( $post_id, 'wp_travel_faq_question', $wp_travel_faq_question );
		}
		if ( isset( $_POST['wp_travel_faq_answer'] ) ) {
			// $wp_travel_tabs = array_map( 'esc_attr', $_POST['wp_travel_tabs'] );
			$wp_travel_faq_answer = ( wp_unslash( $_POST['wp_travel_faq_answer'] ) );
			update_post_meta( $post_id, 'wp_travel_faq_answer', $wp_travel_faq_answer );
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
