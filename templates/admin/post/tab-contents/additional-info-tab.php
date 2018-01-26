<table class="form-table">
	<tr>
		<td><label for="wp-travel-price"><?php esc_html_e( 'Price', 'wp-travel' ); ?></label></td>
		<td><input type="number" min="0" step="0.01" name="wp_travel_price" id="wp-travel-price" /></td>
	</tr>
	<tr>
		<td><label for="wp_travel_outline"><?php esc_html_e( 'Outline', 'wp-travel' ); ?></label></td>
		<td><?php wp_editor( '', 'wp_travel_outline' ); ?></td>
	</tr>
	<tr>
		<td><label for="wp_travel_outline"><?php esc_html_e( 'Starting Date', 'wp-travel' ); ?></label></td>
		<td><input type="text" name="wp_travel_start_date" id="wp-travel-start-date" /></td>
	</tr>
	<tr>
		<td><label for="wp_travel_end_date"><?php esc_html_e( 'Ending Date', 'wp-travel' ); ?></label></td>
		<td><input type="text" name="wp_travel_end_date" id="wp-travel-end-date" /></td>
	</tr>
</table>
