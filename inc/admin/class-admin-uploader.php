<?php
/**
 * Admin uploader.
 *
 * @package WP Travel
 * @author WEN Solutions
 */

/**
 * Admin uploader class.
 */
class WP_Travel_Admin_Uploader {

	public function load() {
		add_action( 'post-plupload-upload-ui', array( $this, 'append_media_upload_form' ) , 1 );
		add_action( 'post-html-upload-ui', array( $this, 'append_media_upload_form' ) , 1 );
		?>
		<div class="wp-travel-open-upload-area">
			<div id="wp-travel-upload-error"></div>
			<?php
			media_upload_form();
			?>
		</div>
		<?php
	}

	public function append_media_upload_form() {

			?>
			<!-- Add from Media Library -->
			<a href="#" class="wp-travel-open-uploader button" title="<?php esc_html_e( 'Click Here to Insert from Media Library', 'wp-travel' ); ?>" style="vertical-align: baseline;">
					<?php _e( 'Select Files from Media Library', 'wp-travel' ); ?>
			</a>

			<!-- Progress Bar -->
			<div class="wp-travel-upload-progress-bar">
					<div class="wp-travel-upload-progress-bar-inner"></div>
					<div class="wp-travel-upload-progress-bar-status">
							<span class="uploading">
									<?php _e( 'Uploading Image', 'wp-travel' ); ?>
									<span class="current">1</span>
									<?php _e( 'of', 'wp-travel' ); ?>
									<span class="total">3</span>
							</span>

							<span class="done"><?php _e( 'All images uploaded.', 'wp-travel' ); ?></span>
					</div>
			</div>
			<?php

	}
}
