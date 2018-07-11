<?php
/**
 * Price Functions.
 *
 * @package wp-travel/inc
 */

// Migrated functions from inc/helpers.php.
/**
 * Return Trip Price.
 *
 * @param  int $post_id Post id of the post.
 * @return int Trip Price.
 */
function wp_travel_get_trip_price( $post_id = 0 ) {
	if ( ! $post_id ) {
		return 0;
	}
	$trip_price 	= get_post_meta( $post_id, 'wp_travel_price', true );
	if ( $trip_price ) {
		return $trip_price;
	}
	return 0;
}

/**
 * Return Trip Sale Price.
 *
 * @param  int $post_id Post id of the post.
 * @return int Trip Price.
 */
function wp_travel_get_trip_sale_price( $post_id = 0 ) {
	if ( ! $post_id ) {
		return 0;
	}
	$trip_sale_price 	= get_post_meta( $post_id, 'wp_travel_sale_price', true );
	if ( $trip_sale_price ) {
		return $trip_sale_price;
	}
	return 0;
}

/**
 * Return Trip Price.
 *
 * @param  int $post_id Post id of the post.
 *
 * @since 1.0.5
 * @return int Trip Price.
 */
function wp_travel_get_actual_trip_price( $trip_id = 0 ) {
	if ( ! $trip_id ) {
		return 0;
	}

	$trip_price = get_post_meta( $trip_id, 'wp_travel_trip_price', true );
	if ( ! $trip_price ) {
		$enable_sale 	= get_post_meta( $trip_id, 'wp_travel_enable_sale', true );

		if ( $enable_sale ) {
			$trip_price = wp_travel_get_trip_sale_price( $trip_id );
		} else {
			$trip_price = wp_travel_get_trip_price( $trip_id );
		}
	}
	return $trip_price;
}
// End of Migrated functions from inc/helpers.php / These prices are only for display.


function wp_travel_get_cart_attrs( $trip_id, $pax = 0, $price_key = '', $return_price = false  ) {
	if ( ! $trip_id ) {
		return 0;
	}
	// Default Pricings.	
	$trip_price = wp_travel_get_actual_trip_price( $trip_id ); // Default Trip Price.
	// $price = $trip_price; // Price for single qty.
	// Price Person Text.
	$per_person_text = wp_travel_get_price_per_text( $trip_id );
	// if ( 'person' === strtolower( $per_person_text ) && $pax > 0 ) {
	// 	$trip_price *= $pax;
	// }
	
	$enable_pricing_options = get_post_meta( $trip_id, 'wp_travel_enable_pricing_options', true );

	$pax_label = ! empty( $per_person_text ) ? $per_person_text : __( 'Person', 'wp-travel' );

	if ( '' != $price_key && 'yes' === $enable_pricing_options ) {
		$valid_price_key = wp_travel_is_price_key_valid( $trip_id, $price_key );
		$enable_pricing_options = wp_travel_is_enable_pricing_options( $trip_id );

		if ( $valid_price_key && 'yes' === $enable_pricing_options ) {
			
			$pricing_data = wp_travel_get_pricing_variation( $trip_id, $price_key );
			
			if ( is_array( $pricing_data ) && '' !== $pricing_data ) {

				foreach ( $pricing_data as $p_ky => $pricing ) :

					$trip_price  = $pricing['price'];
					$enable_sale = isset( $pricing['enable_sale'] ) && 'yes' === $pricing['enable_sale'] ? true : false;

					if ( $enable_sale && isset( $pricing['sale_price'] ) && '' !== $pricing['sale_price'] ) {
						$trip_price = $pricing['sale_price'];
					}
					
					// Product Metas.
					$trip_start_date = isset( $_REQUEST['trip_date'] ) && '' !== $_REQUEST['trip_date'] ? $_REQUEST['trip_date'] : '';
					$pricing_default_types = wp_travel_get_pricing_variation_options();
					$pax_label       = isset( $pricing['type'] ) && 'custom' === $pricing['type'] && '' !== $pricing['custom_label'] ? $pricing['custom_label'] : $pricing_default_types[ $pricing['type'] ];
					$max_available   = isset( $pricing['max_pax'] ) && '' !== $pricing['max_pax'] ? true : false;
					$min_available = isset( $pricing['min_pax'] ) && '' !== $pricing['min_pax'] ? true : false;

					if ( $max_available ) {
						$max_available = $pricing['max_pax'];
						// $max_attr = 'max=' . $pricing['max_pax'];
					}
					if ( $min_available ) {
						$min_available = $pricing['min_pax'];
					}
				endforeach;
			}
		}
	} else {
		// Product Metas.
		$trip_start_date = get_post_meta( $trip_id, 'wp_travel_start_date', true );
		$max_available   = get_post_meta( $trip_id, 'wp_travel_group_size', true );
		$min_available   = 1;
	}

	if ( class_exists( 'WP_Travel_Util_Inventory' ) ){

		$inventory = new WP_Travel_Util_Inventory();

		$inventory_enabled = $inventory->is_inventory_enabled( $trip_id, $price_key );
		$available_pax = $inventory->get_available_pax( $trip_id, false );

		if ( isset( $price_key ) && '' !== $price_key  ) {

			$inventory_enabled = $inventory->is_inventory_enabled( $trip_id, $price_key );
			$available_pax = $inventory->get_available_pax( $trip_id, $price_key );

		}

		if ( $inventory_enabled && $available_pax ) {

			$max_available = $available_pax;

		}

	}

	
	$trip_price = wp_travel_get_formated_price( $trip_price );
	// $price = wp_travel_get_formated_price( $price );
	
	if ( $return_price ) {
		return $trip_price;
	}
	
	$attrs = array(
		'pax_label' 		=> $pax_label,
		'max_available' 	=> $max_available,
		'min_available'     => $min_available,
		'trip_start_date' 	=> $trip_start_date,
	);
	
	$attrs['enable_partial'] = wp_travel_is_partial_payment_enabled();
	$trip_price_partial = $trip_price;
	
	if ( $attrs['enable_partial'] ) {
		$payout_percent = wp_travel_get_payout_percent( $trip_id );
		if ( $payout_percent > 0 ) {
			$trip_price_partial = ( $trip_price * $payout_percent ) / 100;
			$trip_price_partial = wp_travel_get_formated_price( $trip_price_partial );
		}
	}
	$attrs['trip_price_partial'] = $trip_price_partial;
	
	return $attrs;
}

/**
 * Validate pricing Key
 *
 * @return bool true | false.
 */
function wp_travel_is_price_key_valid( $trip_id, $price_key ) {

	if ( '' === $trip_id || '' === $price_key ) {
		return false;
	}
	//Get Pricing variations.
	$pricing_variations = get_post_meta( $trip_id, 'wp_travel_pricing_options', true );

	if ( is_array( $pricing_variations ) && '' !== $pricing_variations ) {

		$result = array_filter($pricing_variations, function( $single ) use ( $price_key ) {
			return in_array( $price_key, $single, true );
		});
		return ( '' !== $result && count( $result ) > 0 ) ? true : false;
	}
	return false;
}

function wp_travel_is_enable_pricing_options( $trip_id ) {
	if ( ! $trip_id ) {
		return false;
	}

	return get_post_meta( $trip_id, 'wp_travel_enable_pricing_options', true );
}

function wp_travel_get_formated_price( $price, $thausand_sep = false, $round = 2 ) {
	if ( ! $price ) {
		return;
	}

	$sep = '';
	if ( $thausand_sep ) {
		$sep = apply_filters( 'wp_travel_price_thousand_seperator', ',' );
	}

	return number_format( $price, $round, '.', $sep );
}

function wp_travel_is_taxable() {

	$settings = wp_travel_get_settings();
	$trip_tax_enable = isset( $settings['trip_tax_enable'] ) ? $settings['trip_tax_enable'] : 'no';

	if ( 'yes' == $trip_tax_enable ) {
		$tax_inclusive_price = $settings['trip_tax_price_inclusive'];
		$tax_percentage = isset( $settings['trip_tax_percentage'] ) ? $settings['trip_tax_percentage'] : '';
		
		if ( '' == $tax_percentage ) {
			return false;
		}
		if ( 'yes' == $tax_inclusive_price ) {
			return false;
		}
		return $tax_percentage;
	}
	return false;
}