<?php
/**
 * Detail Tab HTML.
 *
 * @package wp-travel\inc\admin\views\tabs\tab-contents\itineraries
 */

global $post;
$trip_code = wp_travel_get_trip_code( $post->ID );
$trip_code_disabled = '';
$trip_code_input_name = 'name=wp_travel_trip_code';
if ( ! class_exists( 'WP_Travel_Utilities_Core' ) ) :
	$trip_code_disabled = 'disabled=disabled';
	$trip_code_input_name = '';

	$args = array(
		'title' => __( 'Need Custom Trip Code ?', 'wp-travel' ),
		'content' => __( 'By upgrading to Pro, you can get Trip Code Customization and removal features and more !', 'wp-travel' ),
		'link' => 'https://wptravel.io/downloads/wp-travel-utilities/',
		'link_label' => __( 'Get WP Travel Utilities Addon', 'wp-travel' ),
	);
	wp_travel_upsell_message( $args );
endif;
?>
<table class="form-table">
	<tr>
		<td><label for="wp-travel-detail"><?php esc_html_e( 'Trip Code', 'wp-travel' ); ?></label></td>
		<td><input type="text" id="wp-travel-trip-code" <?php echo esc_html( $trip_code_input_name ); ?> <?php echo esc_html( $trip_code_disabled ); ?> value="<?php echo esc_attr( $trip_code ); ?>" /></td>
	</tr>
	<tr>
		<td colspan="2">
			<h4><?php esc_html_e( 'Overview' ); ?></h4>
			<?php wp_editor( $post->post_content, 'content' ); ?>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<h4><?php esc_html_e( 'Excerpt' ); ?></h4>
			<textarea name="excerpt" id="excerpt" cols="30" rows="10"><?php echo $post->post_excerpt ?></textarea>
		</td>
	</tr>	
</table>
<?php
wp_nonce_field( 'wp_travel_save_data_process', 'wp_travel_save_data' );
