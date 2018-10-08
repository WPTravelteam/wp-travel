<?php 
/**
 * WP_TRAVEL_TERM_META
 * @package WP_Travel
 **/
if ( ! class_exists( 'WP_TRAVEL_TERM_META' ) ) {

class WP_TRAVEL_TERM_META {

	public function __construct() {
	//
	}
	/*
	* Initialize the class and start calling our hooks and filters
	* @since 1.6.3
	*/
	public function init() {
		
		// Trip Type Fields.
		add_action( 'itinerary_types_add_form_fields', array ( $this, 'add_category_image' ), 10, 2 );
		add_action( 'created_itinerary_types', array ( $this, 'save_category_image' ), 10, 2 );
		add_action( 'itinerary_types_edit_form_fields', array ( $this, 'update_category_image' ), 10, 2 );
		add_action( 'edited_itinerary_types', array ( $this, 'updated_category_image' ), 10, 2 );

		// Destinations Fields.
		add_action( 'travel_locations_add_form_fields', array ( $this, 'add_category_image' ), 10, 2 );
		add_action( 'created_travel_locations', array ( $this, 'save_category_image' ), 10, 2 );
		add_action( 'travel_locations_edit_form_fields', array ( $this, 'update_category_image' ), 10, 2 );
		add_action( 'edited_travel_locations', array ( $this, 'updated_category_image' ), 10, 2 );

		// Activity Fields.
		add_action( 'activity_add_form_fields', array ( $this, 'add_category_image' ), 10, 2 );
		add_action( 'created_activity', array ( $this, 'save_category_image' ), 10, 2 );
		add_action( 'activity_edit_form_fields', array ( $this, 'update_category_image' ), 10, 2 );
		add_action( 'edited_activity', array ( $this, 'updated_category_image' ), 10, 2 );

		// Keyword Fields.
		add_action( 'travel_keywords_add_form_fields', array ( $this, 'add_category_image' ), 10, 2 );
		add_action( 'created_travel_keywords', array ( $this, 'save_category_image' ), 10, 2 );
		add_action( 'travel_keywords_edit_form_fields', array ( $this, 'update_category_image' ), 10, 2 );
		add_action( 'edited_travel_keywords', array ( $this, 'updated_category_image' ), 10, 2 );
		
		add_action( 'admin_enqueue_scripts', array( $this, 'load_media' ) );
		add_action( 'admin_footer', array ( $this, 'add_script' ) );
	}

	public function load_media() {
		$current_screen_id = get_current_screen()->id;
		$allowed_screens   = array( 'edit-activity', 'edit-itinerary_types', 'edit-travel_locations', 'edit-travel_keywords' );

		if ( ! in_array( $current_screen_id, $allowed_screens ) )
			return;
		
		wp_enqueue_media();
	}

	/*
	* Add a form field in the new category page
	* @since 1.6.3
	*/
	public function add_category_image ( $taxonomy ) { ?>
	<div class="form-field term-group">
		<label for="category-image-id"><?php _e('Image', 'wp-travel'); ?></label>
		<input type="hidden" id="category-image-id" name="wp_travel_trip_type_image_id" class="custom_media_url" value="">
		<div id="category-image-wrapper"></div>
		<p>
		<input type="button" class="button button-secondary wp_travel_tax_media_button" id="wp_travel_tax_media_button" name="wp_travel_tax_media_button" value="<?php _e( 'Add Image', 'wp-travel' ); ?>" />
		<input type="button" class="button button-secondary wp_travel_tax_media_remove" id="wp_travel_tax_media_remove" name="wp_travel_tax_media_remove" value="<?php _e( 'Remove Image', 'wp-travel' ); ?>" />
		</p>
	</div>
	<?php
	}

	/*
	* Save the form field
	* @since 1.6.3
	*/
	public function save_category_image ( $term_id, $tt_id ) {
		if ( isset( $_POST['wp_travel_trip_type_image_id'] ) && '' !== $_POST['wp_travel_trip_type_image_id'] ) {
			$image = $_POST['wp_travel_trip_type_image_id'];
			add_term_meta( $term_id, 'wp_travel_trip_type_image_id', $image );
		}
	}

	/*
	* Edit the form field
	* @since 1.6.3
	*/
	public function update_category_image ( $term, $taxonomy ) { ?>
	<tr class="form-field term-group-wrap">
		<th scope="row">
		<label for="category-image-id"><?php _e( 'Image', 'wp-travel' ); ?></label>
		</th>
		<td>
		<?php $image_id = get_term_meta ( $term ->term_id, 'wp_travel_trip_type_image_id', true ); ?>
		<input type="hidden" id="category-image-id" name="wp_travel_trip_type_image_id" value="<?php echo $image_id; ?>">
		<div id="category-image-wrapper">
			<?php if ( $image_id ) { ?>
			<?php echo wp_get_attachment_image ( $image_id, 'thumbnail' ); ?>
			<?php } ?>
		</div>
		<p>
			<input type="button" class="button button-secondary wp_travel_tax_media_button" id="wp_travel_tax_media_button" name="wp_travel_tax_media_button" value="<?php _e( 'Add Image', 'wp-travel' ); ?>" />
			<input type="button" class="button button-secondary wp_travel_tax_media_remove" id="wp_travel_tax_media_remove" name="wp_travel_tax_media_remove" value="<?php _e( 'Remove Image', 'wp-travel' ); ?>" />
		</p>
		</td>
	</tr>
	<?php
	}

	/*
	* Update the form field value
	* @since 1.6.3
	*/
	public function updated_category_image ( $term_id, $tt_id ) {
		if ( isset( $_POST['wp_travel_trip_type_image_id'] ) && '' !== $_POST['wp_travel_trip_type_image_id'] ) {
			$image = $_POST['wp_travel_trip_type_image_id'];
			update_term_meta ( $term_id, 'wp_travel_trip_type_image_id', $image );
		} else {
			update_term_meta ( $term_id, 'wp_travel_trip_type_image_id', '' );
		}
	}

	/*
	* Add script
	* @since 1.6.3
	*/
	public function add_script() { 

		$current_screen_id = get_current_screen()->id;
		$allowed_screens   = array( 'edit-activity', 'edit-itinerary_types', 'edit-travel_locations', 'edit-travel_keywords' );

		if ( ! in_array( $current_screen_id, $allowed_screens ) )
			return;
		
		?>
		<script>
			jQuery(document).ready( function($) {
			function wp_travel_media_upload(button_class) {
				var _custom_media = true,
				_orig_send_attachment = wp.media.editor.send.attachment;
				$('body').on('click', button_class, function(e) {
				var button_id = '#'+$(this).attr('id');
				var send_attachment_bkp = wp.media.editor.send.attachment;
				var button = $(button_id);
				_custom_media = true;
				wp.media.editor.send.attachment = function(props, attachment){
					if ( _custom_media ) {
					$('#category-image-id').val(attachment.id);
					$('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
					$('#category-image-wrapper .custom_media_image').attr('src',attachment.url).css('display','block');
					} else {
					return _orig_send_attachment.apply( button_id, [props, attachment] );
					}
					}
				wp.media.editor.open(button);
				return false;
			});
			}
			wp_travel_media_upload('.wp_travel_tax_media_button.button'); 
			$('body').on('click','.wp_travel_tax_media_remove',function(){
			$('#category-image-id').val('');
			$('#category-image-wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
			});
			// Thanks: http://stackoverflow.com/questions/15281995/wordpress-create-category-ajax-response
			$(document).ajaxComplete(function(event, xhr, settings) {
			var queryStringArr = settings.data.split('&');
			if( $.inArray('action=add-tag', queryStringArr) !== -1 ){
				var xml = xhr.responseXML;
				$response = $(xml).find('term_id').text();
				if($response!=""){
				// Clear the thumb image
				$('#category-image-wrapper').html('');
				}
			}
			});
		});
		</script>
	<?php }
}

	$WP_TRAVEL_TERM_META = new WP_TRAVEL_TERM_META();
	$WP_TRAVEL_TERM_META->init();

}