<?php
	global $post;

	$price 		= get_post_meta( $post->ID, 'wp_travel_price', true );
	$sale_price = get_post_meta( $post->ID, 'wp_travel_sale_price', true );
	$outline 	= get_post_meta( $post->ID, 'wp_travel_outline', true );
	$trip_include = get_post_meta( $post->ID, 'wp_travel_trip_include', true );
	$trip_exclude = get_post_meta( $post->ID, 'wp_travel_trip_exclude', true );
	$start_date	= get_post_meta( $post->ID, 'wp_travel_start_date', true );
	$end_date 	= get_post_meta( $post->ID, 'wp_travel_end_date', true );

	$enable_sale = get_post_meta( $post->ID, 'wp_travel_enable_sale', true );

	echo '<div class="trip-type-wrap itineraries-tax-wrap">';
	post_categories_meta_box( $post, array( 'args' => array( 'taxonomy' => 'itinerary_types' ) ) );
	printf( '<div class="tax-edit"><a href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=itinerary_types&post_type=itineraries' ) ) . '">%s</a></div>', esc_html__( 'Edit All Trip Type' ) );
	echo '</div>';

	$settings = wp_traval_get_settings();
	$currency_code = ( isset( $settings['currency'] ) ) ? $settings['currency'] :'';
	$currency_symbol = wp_traval_get_currency_symbol( $currency_code );
?>
<table class="form-table">
	<tr>
		<td><label for="wp-travel-price"><?php esc_html_e( 'Price', 'wp-travel' ); ?></label></td>
		<td><span class="wp-travel-currency-symbol"><?php esc_html_e( $currency_symbol, 'wp-travel' ); ?></span><input type="number" min="0" step="0.01" name="wp_travel_price" id="wp-travel-price" value="<?php echo esc_attr( $price ); ?>" /></td>
	</tr>

	<tr>
		<td><label for="wp-travel-price"><?php esc_html_e( 'Enable Regular Sale', 'wp-travel' ); ?></label></td>
		<td>
			
			<label>
				<input type="checkbox" name="wp_travel_enable_sale" id="wp-travel-enable-sale" <?php checked( $enable_sale, 1 ); ?> value="1" />
				<span class="wp-travel-enable-sale"><?php esc_html_e( 'Check to enable regular sale.', 'wp-travel' ); ?></span>
			</label>
		</td>
	</tr>
	<tr>
		<td><label for="wp-travel-price"><?php esc_html_e( 'Regular Price', 'wp-travel' ); ?></label></td>
		<td><span class="wp-travel-currency-symbol"><?php esc_html_e( $currency_symbol, 'wp-travel' ); ?></span><input type="number" min="0" step="0.01" name="wp_travel_sale_price" id="wp-travel-sale-price" value="<?php echo esc_attr( $sale_price ); ?>" /></td>
	</tr>

	<tr>
		<td><label for="wp_travel_outline"><?php esc_html_e( 'Outline', 'wp-travel' ); ?></label></td>
		<td><?php wp_editor( $outline, 'wp_travel_outline' ); ?></td>
	</tr>
	<tr>
		<td><label for="wp_travel_trip_include"><?php esc_html_e( 'Trip Include', 'wp-travel' ); ?></label></td>
		<td><?php wp_editor( $trip_include, 'wp_travel_trip_include' ); ?></td>
	</tr>
	<tr>
		<td><label for="wp_travel_trip_exclude"><?php esc_html_e( 'Trip Exclude', 'wp-travel' ); ?></label></td>
		<td><?php wp_editor( $trip_exclude, 'wp_travel_trip_exclude' ); ?></td>
	</tr>
	<tr>
		<td><label for="wp_travel_outline"><?php esc_html_e( 'Starting date', 'wp-travel' ); ?></label></td>
		<td><input type="text" name="wp_travel_start_date" id="wp-travel-start-date" value="<?php echo esc_attr( $start_date ); ?>" /></td>
	</tr>
	<tr>
		<td><label for="wp_travel_end_date"><?php esc_html_e( 'Ending date', 'wp-travel' ); ?></label></td>
		<td><input type="text" name="wp_travel_end_date" id="wp-travel-end-date" value="<?php echo esc_attr( $end_date ); ?>" /></td>
	</tr>
</table>
