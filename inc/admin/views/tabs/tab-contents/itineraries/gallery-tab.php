<?php
/**
 * Template file for WP Travel gallery tab.
 *
 * @package WP Travel
 */

global $post;
?>

<div class="wp-travel-post-tab-content-section">
	<?php
	WP_Travel()->uploader->load(); ?>
	<script type="text/javascript">
			var post_id = <?php echo $post->ID; ?>, shortform = 3;
	</script>
	<div class="wp-travel-open-uploaded-images">
		<h3 class="wp-travel-post-tab-content-section-title"><?php esc_html_e( 'Gallery images', 'wp-travel' ); ?></h3>
		<ul>
		</ul>
	</div>
	<input type="hidden" name="wp_travel_gallery_ids" id="wp_travel_gallery_ids" value="" />
	<input type="hidden" name="wp_travel_thumbnail_id" id="wp_travel_thumbnail_id" value="" />
</div>
