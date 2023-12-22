<?php

/**
 * WP Travel Data Update for above version 4.0.0
 *
 * @package WP_Travel
 */

if ( ! function_exists( 'wptravel_migrate_data_to_400' ) ) {
	function wptravel_migrate_data_to_400( $tables = array() ) {
		if ( ! is_array( $tables ) || count( $tables ) == 0 ) {
			return;
		}
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$pricings_table                = $tables['pricings_table'];
		$dates_table                   = $tables['dates_table'];
		$price_category_relation_table = $tables['price_category_relation_table'];

		$custom_post_type = WP_TRAVEL_POST_TYPE;
		$query1           = "SELECT ID from {$wpdb->posts}  where post_type='$custom_post_type' and post_status in( 'publish', 'draft' )";
		$post_ids         = $wpdb->get_results( $query1 );

		if ( is_array( $post_ids ) && count( $post_ids ) > 0 ) {
			foreach ( $post_ids as $custom_post ) {
				$trip_id                       = $custom_post->ID;
				$wp_travel_pricing_option_type = get_post_meta( $trip_id, 'wp_travel_pricing_option_type', true ) ? get_post_meta( $trip_id, 'wp_travel_pricing_option_type', true ) : 'multiple-price';
				$wp_travel_fixed_departure     = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true ) ? get_post_meta( $trip_id, 'wp_travel_fixed_departure', true ) : 'no';

				// Pax size limit migration.
				$pax_limit         = 0;
				$limit_type        = get_post_meta( $trip_id, 'wp_travel_inventory_pax_limit_type', true );
				$custom_group_size = get_post_meta( $trip_id, 'wp_travel_inventory_custom_max_pax', true );
				if ( 'custom_value' === $limit_type ) { // Return custom pax size for inventory if custom_value is set.
					if ( $custom_group_size ) {
						$pax_limit = $custom_group_size;
					}
				} else {
					$group_size = get_post_meta( $trip_id, 'wp_travel_group_size', true );
					if ( $group_size ) {

						$pax_limit = $group_size;
					}
				}
				update_post_meta( $trip_id, 'wp_travel_inventory_size', $pax_limit );
				// End of pax size limit migration.
				if ( $wp_travel_pricing_option_type == 'multiple-price' ) {
					// Migration start.
					$wp_travel_pricing_options = get_post_meta( $trip_id, 'wp_travel_pricing_options', true ) ? get_post_meta( $trip_id, 'wp_travel_pricing_options', true ) : array();
					$temp_pricing_ids          = array();

					// First need to migrate Pricings to set it in dates table.
					// Pricing Migration Start.
					if ( is_array( $wp_travel_pricing_options ) && count( $wp_travel_pricing_options ) > 0 ) {
						foreach ( $wp_travel_pricing_options as $old_pricing_id => $old_pricing ) {
							$wpdb->insert(
								$pricings_table,
								array(
									'title'       => $old_pricing['pricing_name'],
									'max_pax'     => ! empty( $old_pricing['max_pax'] ) ? absint( $old_pricing['max_pax'] ) : 0,
									'min_pax'     => ! empty( $old_pricing['min_pax'] ) ? absint( $old_pricing['min_pax'] ) : 0,
									// 'has_group_price' => 0,
									// 'group_prices'    => array(),
									'trip_id'     => $trip_id,
									'trip_extras' => ! empty( $old_pricing['tour_extras'] ) ? esc_attr( implode( ', ', $old_pricing['tour_extras'] ) ) : '',
								),
								array(
									'%s',
									'%d',
									'%d',
									// '%d',
									// '%s',
									'%d',
									'%s',
								)
							);
							$new_pricing_id = $wpdb->insert_id; // New Pricing ID.

							// if ( empty( $new_pricing_id ) ) {
							// return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_ERROR_SAVING_PRICING' );
							// }
							$temp_pricing_ids[ $old_pricing['price_key'] ] = $new_pricing_id;
							// Category Migration Start.
							$old_pricing_categories = isset( $old_pricing['categories'] ) && is_array( $old_pricing['categories'] ) ? $old_pricing['categories'] : array();
							if ( is_array( $old_pricing_categories ) && count( $old_pricing_categories ) > 0 ) { // category migration for v3.0.0 or above.

								foreach ( $old_pricing_categories as $old_category_id => $old_pricing_category ) {
									$new_category_id = 0;
									// create term if not exists and insert it into price category relation table.
									$tax               = 'itinerary_pricing_category';
									$old_category_name = $old_pricing_category['type'];
									if ( 'custom' == $old_category_name ) {
										$old_category_name = isset( $old_pricing_category['custom_label'] ) && ! empty( $old_pricing_category['custom_label'] ) ? $old_pricing_category['custom_label'] : $old_category_name;
									}
									$termExits = term_exists( $old_category_name, $tax );
									if ( ! $termExits ) {
										$term = wp_insert_term(
											ucfirst( $old_category_name ),   // the term
											$tax, // the taxonomy
											array(
												'slug' => strtolower( $old_category_name ),
											)
										);
										if ( ! is_wp_error( $term ) ) {
											update_term_meta( $term['term_id'], 'pax_size', 1 );
											$new_category_id = $term['term_id'];
										}
									} else {
										$term = get_term_by( 'slug', $old_category_name, $tax );
										update_term_meta( $term->term_id, 'pax_size', 1 );
										$new_category_id = $term->term_id;
									}

									if ( $new_category_id > 0 ) {
										$old_price_per   = $old_pricing_category['price_per'];
										$old_price       = $old_pricing_category['price'];
										$old_enable_sale = isset( $old_pricing_category['enable_sale'] ) && 'yes' === $old_pricing_category['enable_sale'] ? 1 : 0;
										$old_sale_price  = $old_pricing_category['sale_price'];

										// Group Discount.
										$group_discount = get_post_meta( $trip_id, 'wp_travel_group_discount', true );
										$group_discount = isset( $group_discount[ $old_pricing_id ] ) ? $group_discount[ $old_pricing_id ] : array(); // Legacy version data.

										if ( isset( $group_discount[ $old_category_id ] ) ) {
											$group_discount = $group_discount[ $old_category_id ];
										}
										$group_discount_enable   = isset( $group_discount['enable_group_discount'] ) ? $group_discount['enable_group_discount'] : 'no';
										$group_discount_pax_from = isset( $group_discount['pax_from'] ) ? $group_discount['pax_from'] : 1;
										$group_discount_pax_to   = isset( $group_discount['pax_to'] ) ? $group_discount['pax_to'] : 1;
										$group_discount_price    = isset( $group_discount['price'] ) ? $group_discount['price'] : '1';

										$group_prices = array();
										if ( is_array( $group_discount_pax_from ) && count( $group_discount_pax_from ) > 0 ) {
											foreach ( $group_discount_pax_from as $gd_key => $pax_from ) {
												$group_prices[] = array(
													'min_pax' => $pax_from,
													'max_pax' => $group_discount_pax_to[ $gd_key ],
													'price' => $group_discount_price[ $gd_key ],
												);
											}
										}

										// Insert new category in price category relation table.
										$wpdb->insert(
											$price_category_relation_table,
											array(
												'pricing_id' => $new_pricing_id,
												'pricing_category_id' => $new_category_id,
												'price_per' => $old_price_per,
												'regular_price' => $old_price,
												'is_sale' => $old_enable_sale,
												'sale_price' => $old_sale_price,
												'has_group_price' => 'yes' == $group_discount_enable ? 1 : 0,
												'group_prices' => maybe_serialize( $group_prices ),
											),
											array(
												'%d',
												'%d',
												'%s',
												'%s',
												'%d',
												'%s',
												'%d',
												'%s',
											)
										);
									}
								}
							} else { // Category migration for below 3.0.0.
								$new_category_id = 0;
								// create term if not exists and insert it into price category relation table.
								$tax               = 'itinerary_pricing_category';
								$old_category_name = $old_pricing['type'];
								if ( 'custom' == $old_category_name ) {
									$old_category_name = isset( $old_pricing['custom_label'] ) && ! empty( $old_pricing['custom_label'] ) ? $old_pricing['custom_label'] : $old_category_name;
								}
								$termExits = term_exists( $old_category_name, $tax );
								if ( ! $termExits ) {
									$term = wp_insert_term(
										ucfirst( $old_category_name ),   // the term
										$tax, // the taxonomy
										array(
											'slug' => strtolower( $old_category_name ),
										)
									);
									if ( ! is_wp_error( $term ) ) {
										update_term_meta( $term['term_id'], 'pax_size', 1 );
										$new_category_id = $term['term_id'];
									}
								} else {
									$term = get_term_by( 'slug', $old_category_name, $tax );
									update_term_meta( $term->term_id, 'pax_size', 1 );
									$new_category_id = $term->term_id;
								}

								if ( $new_category_id > 0 ) {
									$old_price_per   = $old_pricing['price_per'];
									$old_price       = $old_pricing['price'];
									$old_enable_sale = isset( $old_pricing['enable_sale'] ) && 'yes' === $old_pricing['enable_sale'] ? 1 : 0;
									$old_sale_price  = $old_pricing['sale_price'];

									// Group Discount.
									$group_discount = get_post_meta( $trip_id, 'wp_travel_group_discount', true );

									$group_discount = isset( $group_discount[ $old_pricing_id ] ) ? $group_discount[ $old_pricing_id ] : array(); // Legacy version data.

									// if ( isset( $group_discount[ $old_category_id ] ) ) {
									// $group_discount = $group_discount[ $old_category_id ];
									// }
									$group_discount_enable   = isset( $group_discount['enable_group_discount'] ) ? $group_discount['enable_group_discount'] : 'no';
									$group_discount_pax_from = isset( $group_discount['pax_from'] ) ? $group_discount['pax_from'] : 1;
									$group_discount_pax_to   = isset( $group_discount['pax_to'] ) ? $group_discount['pax_to'] : 1;
									$group_discount_price    = isset( $group_discount['price'] ) ? $group_discount['price'] : '1';

									$group_prices = array();
									if ( is_array( $group_discount_pax_from ) && count( $group_discount_pax_from ) > 0 ) {
										foreach ( $group_discount_pax_from as $gd_key => $pax_from ) {
											$group_prices[] = array(
												'min_pax' => $pax_from,
												'max_pax' => $group_discount_pax_to[ $gd_key ],
												'price'   => $group_discount_price[ $gd_key ],
											);
										}
									}

									// Insert new category in price category relation table.
									$wpdb->insert(
										$price_category_relation_table,
										array(
											'pricing_id'   => $new_pricing_id,
											'pricing_category_id' => $new_category_id,
											'price_per'    => $old_price_per,
											'regular_price' => $old_price,
											'is_sale'      => $old_enable_sale,
											'sale_price'   => $old_sale_price,
											'has_group_price' => 'yes' == $group_discount_enable ? 1 : 0,
											'group_prices' => maybe_serialize( $group_prices ),
										),
										array(
											'%d',
											'%d',
											'%s',
											'%s',
											'%d',
											'%s',
											'%d',
											'%s',
										)
									);
								}
							}
						}
					}

					if ( 'yes' === $wp_travel_fixed_departure ) { // Fixed Departure Migration start.
						$wp_travel_enable_multiple_fixed_departue = get_post_meta( $trip_id, 'wp_travel_enable_multiple_fixed_departue', true ) ? get_post_meta( $trip_id, 'wp_travel_enable_multiple_fixed_departue', true ) : 'no';

						if ( 'yes' === $wp_travel_enable_multiple_fixed_departue ) { // Multiple fixed departure start.
							$wp_travel_multiple_trip_dates = get_post_meta( $trip_id, 'wp_travel_multiple_trip_dates', true ) ? get_post_meta( $trip_id, 'wp_travel_multiple_trip_dates', true ) : array();
							if ( is_array( $wp_travel_multiple_trip_dates ) && count( $wp_travel_multiple_trip_dates ) > 0 ) {

								foreach ( $wp_travel_multiple_trip_dates as $old_date_id => $old_date ) {

									$times = get_post_meta( $trip_id, 'wp_travel_trip_time', true );

									$selected_times = ! empty( $times ) && isset( $times[ $old_date_id ] ) && isset( $times[ $old_date_id ]['times'] ) ? $times[ $old_date_id ]['times'] : array();

									$pricing_options          = $old_date['pricing_options'];
									$new_pricing_ids          = array();
									$selected_times_migration = array(); // quick fix.
									foreach ( $pricing_options as $pricing_option_key ) {
										// Trip Time Migration
										if ( is_array( $selected_times ) && count( $selected_times ) > 0 ) {
											foreach ( $selected_times as $selected_time ) {
												$selected_time = date( 'H:i', strtotime( $selected_time ) );
												if ( $selected_time ) {
													$selected_times_migration[] = $selected_time;
												}
											}
										}
										// End of Trip Time Migration
										if ( isset( $temp_pricing_ids[ $pricing_option_key ] ) ) {
											$new_pricing_ids[] = $temp_pricing_ids[ $pricing_option_key ];

											// Inventory Migration Start.
											$old_inventory_meta_key = 'wp_travel_inventory_booking_pax_count';
											if ( $pricing_option_key ) {
												$old_inventory_meta_key .= sprintf( '_%s', $pricing_option_key );
											}
											if ( $old_date['start_date'] ) {
												$old_inventory_meta_key .= sprintf( '_%s', $old_date['start_date'] );
											}
											$old_inventory_value = get_post_meta( $trip_id, $old_inventory_meta_key, true );

											if ( $old_inventory_value ) {
												$new_inventory_meta_key = 'wt_booked_pax';
												if ( $temp_pricing_ids[ $pricing_option_key ] ) {
													$new_inventory_meta_key .= sprintf( '-%s', $temp_pricing_ids[ $pricing_option_key ] ); // Added to work with trip pricing_id.
												}
												if ( $old_date['start_date'] ) {
													$new_inventory_meta_key .= sprintf( '-%s', str_replace( '-', '_', $old_date['start_date'] ) ); // Added to work with trip date.
												}
												if ( is_array( $selected_times ) && count( $selected_times ) > 0 ) {
													foreach ( $selected_times as $selected_time ) {
														$selected_time = date( 'H:i', strtotime( $selected_time ) );
														if ( $selected_time ) {
															$new_inventory_meta_key .= sprintf( '-%s', str_replace( ':', '_', $selected_time ) ); // Added to work with trip time.
															// update inventory with time.
															update_post_meta( $trip_id, $new_inventory_meta_key, $old_inventory_value );
														}
													}
												} else {

													// update inventory without time.
													update_post_meta( $trip_id, $new_inventory_meta_key, $old_inventory_value );
												}
											}
											// Inventory Migration ends.
										}
									}
									// Insert New Date along with new pricing ids.
									$wpdb->insert(
										$dates_table,
										array(
											'trip_id'     => $trip_id,
											'title'       => $old_date['date_label'],
											'recurring'   => '0',
											'years'       => '',
											'months'      => '',
											'weeks'       => '',
											'days'        => '',
											'date_days'   => '',
											'start_date'  => $old_date['start_date'],
											'end_date'    => $old_date['end_date'],
											'trip_time'   => implode( ',', array_unique( $selected_times_migration ) ),
											'pricing_ids' => implode( ', ', $new_pricing_ids ),
										),
										array(
											'%d',
											'%s',
											'%s',
											'%s',
											'%s',
											'%s',
											'%s',
											'%s',
											'%s',
											'%s',
											'%s',
											'%s',
										)
									);
									$inserted_id = $wpdb->insert_id;
								}
							}
						} else { // Single fixed departure date.
							// insert date.
							$trip_start_date = get_post_meta( $trip_id, 'wp_travel_start_date', true );
							$trip_end_date   = get_post_meta( $trip_id, 'wp_travel_end_date', true );

							foreach ( $temp_pricing_ids as $old_price_key => $new_pricing_id ) {
								// Inventory Migration Start.
								$old_inventory_meta_key = 'wp_travel_inventory_booking_pax_count';
								if ( $old_price_key ) {
									$old_inventory_meta_key .= sprintf( '_%s', $old_price_key );
								}
								if ( $trip_start_date ) {
									$old_inventory_meta_key .= sprintf( '_%s', $trip_start_date );
								}
								$old_inventory_value = get_post_meta( $trip_id, $old_inventory_meta_key, true );

								if ( $old_inventory_value ) {
									$new_inventory_meta_key = 'wt_booked_pax';
									if ( $new_pricing_id ) {
										$new_inventory_meta_key .= sprintf( '-%s', $new_pricing_id ); // Added to work with trip pricing_id.
									}
									if ( $trip_start_date ) {
										$new_inventory_meta_key .= sprintf( '-%s', str_replace( '-', '_', $trip_start_date ) ); // Added to work with trip date.
									}
									update_post_meta( $trip_id, $new_inventory_meta_key, $old_inventory_value );
								}
								// Inventory Migration ends.
							}

							$wpdb->insert(
								$dates_table,
								array(
									'trip_id'     => $trip_id,
									'title'       => __( 'Date', 'wp-travel' ),
									'recurring'   => '0',
									'years'       => '',
									'months'      => '',
									'weeks'       => '',
									'days'        => '',
									'date_days'   => '',
									'start_date'  => $trip_start_date,
									'end_date'    => $trip_end_date,
									'trip_time'   => '',
									'pricing_ids' => implode( ', ', array_values( $temp_pricing_ids ) ),
								),
								array(
									'%d',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
									'%s',
								)
							);
							$inserted_id = $wpdb->insert_id;
						}
					} else {
						// Trip Duration
						$wpdb->insert(
							$dates_table,
							array(
								'trip_id'     => $trip_id,
								'title'       => __( 'Date', 'wp-travel' ),
								'recurring'   => '0',
								'years'       => '',
								'months'      => '',
								'weeks'       => '',
								'days'        => '',
								'date_days'   => '',
								'start_date'  => date( 'Y-m-d' ),
								'end_date'    => date( 'Y-m-d' ),
								'trip_time'   => '',
								'pricing_ids' => implode( ', ', array_values( $temp_pricing_ids ) ),
							),
							array(
								'%d',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
								'%s',
							)
						);
					}
				} elseif ( $wp_travel_pricing_option_type == 'single-price' ) {
					// Convert it to multiple price
					// Migration Start
				}
			}
			update_option( 'wp_travel_migrate_400', 'yes' ); // Data Migration.
			update_option( 'wp_travel_pricing_table_created', 'yes' ); // quick fix for multisite network enabled.
		}

	}
}


if ( ! function_exists( 'wptravel_update_to_400' ) ) {
	function wptravel_update_to_400( $network_enabled, $force_migrate = false ) {
		global $wpdb;
		$migrate_400 = get_option( 'wp_travel_migrate_400' );

		if ( $migrate_400 && 'yes' === $migrate_400 && ! $force_migrate ) {
			return;
		}

		/**
		 * @todo WP Travel Table need to get from function/class.
		 */
		if ( is_multisite() ) {
			if ( $network_enabled ) {
				$sites = get_sites();
				if ( is_array( $sites ) && count( $sites ) > 0 ) {
					foreach ( $sites as $site ) {
						$blog_id = $site->blog_id;
						// switch to blog.
						switch_to_blog( $blog_id );
						$pricings_table                = $wpdb->base_prefix . $blog_id . '_wt_pricings';
						$dates_table                   = $wpdb->base_prefix . $blog_id . '_wt_dates';
						$price_category_relation_table = $wpdb->base_prefix . $blog_id . '_wt_price_category_relation';

						$tables = array(
							'pricings_table' => $pricings_table,
							'dates_table'    => $dates_table,
							'price_category_relation_table' => $price_category_relation_table,
						);

						wptravel_migrate_data_to_400( $tables );
						restore_current_blog();
						// restore current blog.
					}
				}
			} else {
				$blog_id                       = get_current_blog_id();
				$pricings_table                = $wpdb->base_prefix . $blog_id . '_wt_pricings';
				$dates_table                   = $wpdb->base_prefix . $blog_id . '_wt_dates';
				$price_category_relation_table = $wpdb->base_prefix . $blog_id . '_wt_price_category_relation';

				$tables = array(
					'pricings_table'                => $pricings_table,
					'dates_table'                   => $dates_table,
					'price_category_relation_table' => $price_category_relation_table,
				);
				wptravel_migrate_data_to_400( $tables );
			}
		} else {
			$pricings_table                = $wpdb->base_prefix . 'wt_pricings';
			$dates_table                   = $wpdb->base_prefix . 'wt_dates';
			$price_category_relation_table = $wpdb->base_prefix . 'wt_price_category_relation';

			$tables = array(
				'pricings_table'                => $pricings_table,
				'dates_table'                   => $dates_table,
				'price_category_relation_table' => $price_category_relation_table,
			);
			wptravel_migrate_data_to_400( $tables );
		}

		update_option( 'wp_travel_migrate_400', 'yes' ); // Data Migration.
		return WP_Travel_Helpers_Response_Codes::get_success_response(
			'MIGRATE_V4',
			array(
				'migrate' => get_option( 'wp_travel_migrate_400' ),
			)
		);

	}
}
wptravel_update_to_400( @$network_enabled );