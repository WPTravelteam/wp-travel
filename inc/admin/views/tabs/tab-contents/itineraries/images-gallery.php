<?php
/**
 * Template file for WP Travel gallery tab.
 *
 * @package WP Travel
 */

if ( ! function_exists( 'wptravel_trip_callback_images_gallery' ) ) {

	/**
	 * Callback image gallery tab.
	 */
	function wptravel_trip_callback_images_gallery() {
		global $post;
		?>
		<div class="wp-travel-post-tab-content-section">
			<?php
			WPTravel()->uploader->load();
			?>
			<script type="text/javascript">
				var post_id = <?php echo esc_html( $post->ID ); ?>, shortform = 3;
			</script>
			<div class="wp-travel-open-uploaded-images">
				<h3 class="wp-travel-post-tab-content-section-title"><?php esc_html_e( 'Gallery images', 'wp-travel' ); ?></h3>
				<ul>
				</ul>
				<p class="description"><?php esc_html_e( 'Click images to set featured image. You can also drag images to sort position.', 'wp-travel' ); ?></p>
			</div>
			<input type="hidden" name="wp_travel_gallery_ids" id="wp_travel_gallery_ids" value="" />
			<input type="hidden" name="wp_travel_thumbnail_id" id="wp_travel_thumbnail_id" value="" />
		</div>
		<?php
	}
}

