<?php
class WP_Travel_Admin_Metaboxes {
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'register_metaboxes' ), 10, 2 );
		add_filter( 'postbox_classes_itineraries_wp-travel-itinerary-detail', array( $this, 'add_clean_metabox_class' ) );
		add_filter( 'wp_travel_admin_post_tabs', array( $this, 'add_tabs' ) );
	}

	public function register_metaboxes() {
		add_meta_box(
			'wp-travel-itinerary-detail',
			__( 'Trip Detail' ),
			array( $this, 'travaldoor_trip_detail_html' ),
			'itineraries',
			'normal',
			'default'
		);
		remove_meta_box( 'itinerary_locationsdiv', 'itineraries', 'side' );
		remove_meta_box( 'itinerary_typesdiv', 'itineraries', 'side' );
	}

	function add_clean_metabox_class( $classes ) {
		array_push( $classes, 'wp-travel-clean-metabox' );
		return $classes;
	}

	function add_tabs( $tabs ) {
		$itineraries['detail'] = array(
			'tab_label' => __( 'Details', 'sell_media' ),
			'content_title' => __( 'Details', 'sell_media' ),
			'content_callback' => array( $this, 'call_back' ),
		);

		$itineraries['additional_info'] = array(
			'tab_label' => __( 'Additional Info', 'sell_media' ),
			'content_title' => __( 'Additional Info', 'sell_media' ),
			'content_callback' => array( $this, 'call_back' ),
		);

		$itineraries['images_gallery'] = array(
			'tab_label' => __( 'Images/ Gallery', 'sell_media' ),
			'content_title' => __( 'Images/ Gallery', 'sell_media' ),
			'content_callback' => array( $this, 'call_back' ),
		);

		$itineraries['locations'] = array(
			'tab_label' => __( 'Locations', 'sell_media' ),
			'content_title' => __( 'Locations', 'sell_media' ),
			'content_callback' => array( $this, 'call_back' ),
		);

		$itineraries['advanced'] = array(
			'tab_label' => __( 'Advanced', 'sell_media' ),
			'content_title' => __( 'Advanced Options', 'sell_media' ),
			'content_callback' => array( $this, 'call_back' ),
		);

		$tabs['itineraries'] = $itineraries;
		return $tabs;
	}

	function call_back(){
		echo "test";
	}

	function travaldoor_trip_detail_html( $post ) {
		$args['post_type'] = 'itineraries';
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_travel_get_template( 'admin/post/tabs.php', $args );
	}
}

new WP_Travel_Admin_Metaboxes();
