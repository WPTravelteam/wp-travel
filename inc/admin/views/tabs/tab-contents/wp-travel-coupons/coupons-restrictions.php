<?php
/**
 * Tab Contents
 *
 * @package WP Travel
 */

function wp_travel_coupons_restrictions_tab_callback() {
	global $post;
	// Get Restrictions Tab Data.
	$coupon_metas     = get_post_meta( $post->ID, 'wp_travel_coupon_metas', true );
	$restrictions_tab = isset( $coupon_metas['restriction'] ) ? $coupon_metas['restriction'] : array();

	// Field Values.
	$restricted_trips    = isset( $restrictions_tab['restricted_trips'] ) ? $restrictions_tab['restricted_trips'] : array();
	$coupon_limit_number = isset( $restrictions_tab['coupon_limit_number'] ) ? $restrictions_tab['coupon_limit_number'] : '';
	?>
   
   <table class="form-table">
	   <tbody>
   
		   <tr>
			   <td>
				   <label for=""><?php echo esc_html( 'Restrict Coupon to Trips', 'wp-travel-coupon-pro' ); ?></label>
					   <span class="tooltip-area" title="<?php esc_html_e( 'Choose to apply coupons to certain trips only. Deselect all to apply to all trips', 'wp-travel' ); ?>">
							  <i class="wt-icon wt-icon-question-circle" aria-hidden="true"></i>
						  </span>
			   </td>
			   <td>
				   <?php $itineraries = wp_travel_get_itineraries_array(); ?>
   
				   <div class="custom-multi-select">
				   <?php

					   $count_options_data   = count( $restricted_trips );
					   $count_itineraries    = count( $itineraries );
					   $multiple_checked_all = '';
					if ( $count_options_data == $count_itineraries ) {
						$multiple_checked_all = 'checked=checked';
					}

					   $multiple_checked_text = __( 'Select multiple', 'wp-travel' );
					if ( $count_itineraries > 0 ) {
						$multiple_checked_text = $count_options_data . __( ' item selected', 'wp-travel' );
					}

					?>
					   <span class="select-main">
						   <span class="selected-item"><?php echo esc_html( $multiple_checked_text ); ?></span> 
						   <span class="carret"></span> 
						   <span class="close"></span>
						   <ul class="wp-travel-multi-inner">
							   <li class="wp-travel-multi-inner">
								   <label class="checkbox wp-travel-multi-inner">
									   <input <?php echo esc_attr( $multiple_checked_all ); ?> type="checkbox"  id="wp-travel-multi-input-1" class="wp-travel-multi-inner multiselect-all" value="multiselect-all"><?php esc_html_e( 'Select all', 'wp-travel' ); ?>
								   </label>
							   </li>
							   <?php
								foreach ( $itineraries as $key => $iti ) {

									$checked            = '';
									$selecte_list_class = '';

									if ( in_array( $key, $restricted_trips ) ) {

										$checked            = 'checked=checked';
										$selecte_list_class = 'selected';

									}

									?>
								   <li class="wp-travel-multi-inner <?php echo esc_attr( $selecte_list_class ); ?>">
									   <label class="checkbox wp-travel-multi-inner ">
										   <input <?php echo esc_attr( $checked ); ?>  name="wp_travel_coupon[restriction][restricted_trips][]" type="checkbox" id="wp-travel-multi-input-<?php echo esc_attr( $key ); ?>" class="wp-travel-multi-inner multiselect-value" value="<?php echo esc_attr( $key ); ?>">  <?php echo esc_html( $iti ); ?>
									   </label>
								   </li>
								<?php } ?>
						   </ul>
					   </span>
   
				   </div>
			   </td>
		   </tr>
   
		   <tr>
			   <td>
				   <label for="coupon-limit"><?php esc_html_e( 'Coupon Usage Limit', 'wp-travel' ); ?></label>
				   <span class="tooltip-area" title="<?php echo esc_attr( 'No. of times coupon can be used before being obsolute.', 'wp-travel' ); ?>">
						  <i class="wt-icon wt-icon-question-circle" aria-hidden="true"></i>
					  </span>
			   </td>
			   <td>
				   <input type="number" step="1" min="0" id="coupon-limit" name="wp_travel_coupon[restriction][coupon_limit_number]"  value="<?php echo esc_attr( $coupon_limit_number ); ?>">
			   </td>
		   </tr>
		   
		   
	   </tbody>
   </table>
	<?php
}
