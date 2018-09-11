<?php
/**
 * Tour extras General Tab Contents
 * 
 * @package WP Travel
 */
global $post;
$post_id = $post->ID;

$trip_extras_data = get_post_meta( $post_id, 'wp_travel_tour_extras_metas', true );

if ( ! $trip_extras_data )
    $trip_extras_data = array();

/**
 * Set Vars.
 */
$item_price      = isset( $trip_extras_data['extras_item_price'] ) && ! empty( $trip_extras_data['extras_item_price'] ) ? $trip_extras_data['extras_item_price']  : '';
$item_sale_price = isset( $trip_extras_data['extras_item_sale_price'] ) && ! empty( $trip_extras_data['extras_item_sale_price'] ) ? $trip_extras_data['extras_item_sale_price']  : '';
$item_unit       = isset( $trip_extras_data['extras_item_unit'] ) && ! empty( $trip_extras_data['extras_item_unit'] ) ? $trip_extras_data['extras_item_unit']  : '';
$item_desc       = isset( $trip_extras_data['extras_item_description'] ) && ! empty( $trip_extras_data['extras_item_description'] ) ? $trip_extras_data['extras_item_description']  : '';

?>
<table class="form-table">
	<tbody>
		<tr>
			<td><label for="coupon-value"><?php esc_html_e( 'Price', 'wp-travel' ); ?></label>
				<span class="tooltip-area" title="<?php esc_html_e( 'Item Price', 'wp-travel' ); ?>">
					<i class="fa fa-question-circle" aria-hidden="true"></i>
				</span>
			</td>
			<td>
                <span id="coupon-currency-symbol" class="wp-travel-currency-symbol">
						<?php echo wp_travel_get_currency_symbol(); ?>
				</span>
				<input required="required" type="number" min="1" step="0.01" id="coupon-value" name="wp_travel_extras[extras_item_price]" placeholder="<?php echo esc_attr__( 'Price', 'wp-travel' ); ?>" value="<?php echo esc_attr( $item_price ); ?>">
			</td>
		</tr>
        <tr>
			<td><label for="coupon-value"><?php esc_html_e( 'Sale Price', 'wp-travel' ); ?></label>
				<span class="tooltip-area" title="<?php esc_html_e( 'Sale Price(Leave Blank to disable sale)', 'wp-travel' ); ?>">
					<i class="fa fa-question-circle" aria-hidden="true"></i>
				</span>
			</td>
			<td>
                <span id="coupon-currency-symbol" class="wp-travel-currency-symbol">
					<?php echo wp_travel_get_currency_symbol(); ?>
				</span>
				<input type="number" min="1" step="0.01" id="coupon-value" name="wp_travel_extras[extras_item_sale_price]" placeholder="<?php echo esc_attr__( 'Sale Price', 'wp-travel' ); ?>" value="<?php echo esc_attr( $item_sale_price ); ?>">
			</td>
		</tr>
        <tr>
			<td><label for="coupon-value"><?php esc_html_e( 'Price Per', 'wp-travel' ); ?></label>
			</td>
			<td>
                <select name="wp_travel_extras[extras_item_unit]" id="">
                    <option <?php selected( $item_unit, 'unit' ); ?> value="unit"><?php esc_html_e( 'Unit', 'wp-travel' ); ?></option>
                    <option <?php selected( $item_unit, 'person' ); ?> value="person"><?php esc_html_e( 'Person', 'wp-travel' ); ?></option>
                </select>
			</td>
		</tr>
        <tr>
			<td><label for="coupon-value"><?php esc_html_e( 'Description', 'wp-travel' ); ?></label>
				<span class="tooltip-area" title="<?php esc_html_e( 'Description for the service/item', 'wp-travel' ); ?>">
					<i class="fa fa-question-circle" aria-hidden="true"></i>
				</span>
			</td>
			<td>
                <textarea name="wp_travel_extras[extras_item_description]" id="" cols="50" rows="5"><?php echo esc_html( $item_desc ); ?></textarea>
			</td>
		</tr>
	</tbody>
</table>