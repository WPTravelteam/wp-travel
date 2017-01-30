<?php
class WP_Travel_Ajax {

	public function __construct() {
		add_action( 'wp_ajax_envira_gallery_load_image', array( $this, 'post_gallery_ajax_load_image' ) );
	}
	function post_gallery_ajax_load_image() {
		// Run a security check first.
		check_ajax_referer( 'wp-travel-drag-drop-nonce', 'nonce' );
		// Prepare variables.
		$id  = absint( $_POST['id'] );
		echo wp_json_encode( array(
			'id' => $id,
			'url' => wp_get_attachment_thumb_url( $id ),
		) );
		exit;
	}
}
new WP_Travel_Ajax();
