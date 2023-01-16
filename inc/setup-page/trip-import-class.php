<?php

class WP_Travel_Import_Dummy_Trip {

	/**
	 * Post Type to be imported.
	 *
	 * @var [type]
	 */
	private $post_type;

	/**
	 * Undocumented variable
	 *
	 * @var [type]
	 */
	private $data;

	/**
	 * Post ID.
	 *
	 * @var [type]
	 */
	private $post_id;

	/**
	 * Current importing post.
	 *
	 * @var array
	 */
	private $new_post;

	/**
	 * Newly created Post.
	 *
	 * @var object
	 */
	private $post;


	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Handle the CSV Import action
	 *
	 * @return void
	 */
	public function init() {
		$data                       = $this->prepare_data();
		$this->insert_posts( $data );
	}

	/**
	 * Insert posts.
	 *
	 * @param [type] $data
	 * @return void
	 */
	public function insert_posts( $data ) {

		foreach ( $data as $key => $post ) {
			$this->new_post = $post;
			if ( empty( $post ) || ! is_array( $post ) ) {
				continue;
			}
			$this->insert_post( $post );
		}

	}

	public function insert_post( $post ) {

		$title        = $post['post_title'];
		$slug         = $post['post_name'];
		$post_status  = $post['post_status'];
		$post_content = $post['post_content'];
		$post_date    = $post['post_date'];
		$post_excerpt = $post['post_excerpt'];
		$post_type    = $post['post_type'];

		$post_array = array(
			'post_title'  => wp_strip_all_tags( $title ),
			'post_status' => $post_status,
			'post_slug'   => $slug,
			'post_type'   => $post_type,
			// 'post_date'   => $post_date, // issue in import if date format mismatch.
		);

		if ( ! empty( $post_content ) ) {
			$post_array['post_content'] = $post_content;
		}

		if ( ! empty( $post_excerpt ) ) {
			$post_array['post_excerpt'] = $post_excerpt;
		}

		$post_id = wp_insert_post( $post_array );
		if ( $post_id && ! is_wp_error( $post_id ) ) {
			$this->post_id = $post_id;
			$this->update_metas( $post_id, $post );
		}
	}

	/**
	 * Undocumented function
	 *
	 * @param integer $post_id
	 * @param array   $post
	 * @return void
	 */
	public function update_metas( $post_id, $post ) {
		$skip_array = array( 'post_title', 'post_content', 'post_excerpt', 'post_date', 'post_status', 'post_name', 'post_name' );
		$meta_keys  = array_diff( array_keys( $post ), $skip_array );

		foreach ( $meta_keys as $meta_key ) {
			if ( ! empty( $meta_key ) ) {
				$this->update_meta_data( $post_id, $meta_key, $post[ $meta_key ], $post );
			}
		}
	}

	/**
	 * Update Post Values.
	 *
	 * @param [type] $post_id
	 * @param [type] $key
	 * @param [type] $data
	 * @return void
	 */
	public function update_meta_data( $post_id, $key, $data, $post_data = null ) {
		switch ( $key ) {
			case 'taxonomies':
				$this->set_terms( $data, $post_id );
				break;

			case 'wp_travel_itinerary_gallery_ids':
				if ( ! empty( $data ) ) {
					$gal_ids = unserialize( $data );
					if ( is_array( $gal_ids ) && ! empty( $gal_ids ) ) {
						foreach ( $gal_ids as $gk => $gv ) {
							$thumb          = $this->new_post['_thumbnail_id'];
							$gal_ids[ $gk ] = $this->insert_attachment_from_url( $gv );
							if ( $thumb === $gv ) {
								update_post_meta( $post_id, '_thumbnail_id', maybe_unserialize( $gal_ids[ $gk ] ) );
							}
						}
					}
					update_post_meta( $post_id, $key, maybe_unserialize( $gal_ids ) );
				}
				break;

			case '_thumbnail_id':
				if ( WP_TRAVEL_POST_TYPE !== $this->new_post['_thumbnail_id'] ) {
					$thumb_id = $this->insert_attachment_from_url( $data );
					update_post_meta( $post_id, $key, maybe_unserialize( $thumb_id ) );
				}
				break;

			case 'dates_v4':
				$pricings_data = $post_data['pricing_v4'];

				if ( ! empty( $pricings_data ) ) {
					global $wpdb;
					$pricings_table                = $wpdb->base_prefix . 'wt_pricings';
					$price_category_relation_table = $wpdb->base_prefix . 'wt_price_category_relation';
					$wp_travel_pricing_option_type = get_post_meta( $post_id, 'wp_travel_pricing_option_type', true ) ? get_post_meta( $post_id, 'wp_travel_pricing_option_type', true ) : 'multiple-price';

					if ( 'multiple-price' === $wp_travel_pricing_option_type ) {
						$temp_pricing_ids = array();
						$pricings         = unserialize( $pricings_data );

						if ( is_array( $pricings ) && count( $pricings ) > 0 ) {
							foreach ( $pricings as $key => $pricing ) {
								$wpdb->insert(
									$pricings_table,
									array(
										'title'       => $pricing['title'],
										'max_pax'     => ! empty( $pricing['max_pax'] ) ? absint( $pricing['max_pax'] ) : 0,
										'min_pax'     => ! empty( $pricing['min_pax'] ) ? absint( $pricing['min_pax'] ) : 0,
										// 'has_group_price' => 0,
										// 'group_prices'    => array(),
										'trip_id'     => $post_id,
										'trip_extras' => ! empty( $pricing['tour_extras'] ) ? esc_attr( implode( ', ', $pricing['tour_extras'] ) ) : '',
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

								$temp_pricing_ids[ $pricing['id'] ] = $new_pricing_id;

								$pricing_categories = isset( $pricing['categories'] ) && is_array( $pricing['categories'] ) ? $pricing['categories'] : array();

								if ( is_array( $pricing_categories ) && count( $pricing_categories ) > 0 ) {
									foreach ( $pricing_categories as $key => $pricing_category ) {
										$new_category_id = ! empty( $pricing_category['term_info']['id'] ) ? ( $pricing_category['term_info']['id'] ) : 0;

										if ( $new_category_id > 0 ) {
											// Insert new category in price category relation table.
											$wpdb->insert(
												$price_category_relation_table,
												array(
													'pricing_id' => $new_pricing_id,
													'pricing_category_id' => $new_category_id,
													'price_per' => $pricing_category['price_per'],
													'regular_price' => $pricing_category['regular_price'],
													'is_sale' => $pricing_category['is_sale'],
													'sale_price' => $pricing_category['sale_price'],
													'has_group_price' => $pricing_category['has_group_price'],
													'group_prices' => maybe_serialize( $pricing_category['group_prices'] ),
													'default_pax' => $pricing_category['default_pax'],
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
													'%s',
												)
											);
										}
									}
								}
							}
						}
					}
				}
				if ( ! empty( $data ) ) {
					$wp_travel_fixed_departure = get_post_meta( $post_id, 'wp_travel_fixed_departure', true ) ? get_post_meta( $post_id, 'wp_travel_fixed_departure', true ) : 'no';

					if ( 'yes' === $wp_travel_fixed_departure ) {

							global $wpdb;
							$dates_table = $wpdb->base_prefix . 'wt_dates';

							$dates = unserialize( $data );

							$new_pricing_ids = array();

						if ( is_array( $dates ) && count( $dates ) > 0 ) {
							foreach ( $dates as $key => $date ) {
								$new_pricing_ids = array();
								$pricing_ids     = explode( ',', $date['pricing_ids'] );
								if ( is_array( $pricing_ids ) && count( $pricing_ids ) > 0 ) {
									foreach ( $pricing_ids as $pricing_id ) {
										if ( ! empty( $temp_pricing_ids[ $pricing_id ] ) ) {
											$new_pricing_ids[] = $temp_pricing_ids[ $pricing_id ];
										}
									}
								}

								// Insert New Date along with new pricing ids.
								$wpdb->insert(
									$dates_table,
									array(
										'trip_id'     => $post_id,
										'title'       => $date['title'],
										'recurring'   => '0',
										'years'       => '',
										'months'      => '',
										'weeks'       => '',
										'days'        => '',
										'date_days'   => '',
										'start_date'  => $date['start_date'],
										'end_date'    => $date['end_date'],
										'trip_time'   => '',
										'pricing_ids' => implode( ',', $new_pricing_ids ),
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
						}
					}
				}
				break;
			default:
				update_post_meta( $post_id, $key, maybe_unserialize( $data ) );
		}
	}

	/**
	 * Set Terms for new Post.
	 *
	 * @param mixed   $taxonomies
	 * @param integer $post_id
	 * @return void
	 */
	public function set_terms( $taxonomies, $post_id = 0 ) {
		$taxs = unserialize( $taxonomies );
		if ( is_array( $taxs ) && count( $taxs ) >= 1 ) {
			foreach ( $taxs as $tax => $terms ) {
				$term_ids = $this->get_terms_ids( $terms, $tax );
				if ( $post_id ) {
					wp_set_post_terms( $post_id, $term_ids, $tax );
				}
			}
		}

	}

	/**
	 * Get Terms IDs.
	 *
	 * @param array  $terms Terms.
	 * @param string $tax Taxonomy Terms.
	 * @return array
	 */
	public function get_terms_ids( $terms, $tax ) {
		$term_ids = array();
		if ( is_array( $terms ) && count( $terms ) >= 0 ) {
			foreach ( $terms as $term ) {
				$term_exists = term_exists( $term->slug, $tax );
				$term_id     = is_array( $term_exists ) ? $term_exists['term_id'] : $term_exists;
				// if ( 'travel_keywords' === $tax ) {
				// 	$term_obj = is_array( $term_exists ) ? get_term( $term_exists['term_id'], $tax ) : get_term( $term_exists, $tax );

				// 	$term_id = $term_obj->name;
				// }
				if ( ! $term_id ) {
					$t = wp_insert_term( $term->name, $tax, array( 'slug' => $term->slug ) );
					if ( ! is_wp_error( $t ) ) {
						$term_id = $t['term_id'];
						if ( 'travel_keywords' === $tax ) {
							$t_oj    = get_term( $t['term_id'], $tax );
							$term_id = $t_oj->name;
						}
					}
				}
				$term_ids[] = $term_id;
			}
		}
		return $term_ids;
	}

	/**
	 * Prepares Data.
	 *
	 * @return array
	 */
	public function prepare_data() {
		$header = null;
		$data   = array();
		$file   = plugin_dir_url( WP_TRAVEL_PLUGIN_FILE )."assets/demo-trip.csv";
		if ( ( $handle = fopen( $file, 'r' ) ) !== false ) {
			while ( ( $row = fgetcsv( $handle, 0, ',' ) ) !== false ) {
				if ( ! $header ) {
					$header = $row;
				} else {
					$data[] = array_combine( $header, $row );
				}
			}
			fclose( $handle );
		}
		if ( ! is_array( $data ) || empty( $data ) ) {
			$this->send_json_error( __( 'Invalid CSV file or bad format.', 'wp-travel' ) );
		}

		$this->data = $data;
		return $data;
	}

	/**
	 * Sends Error in Response, If Any.
	 *
	 * @param string $error
	 * @return void
	 */
	public function send_json_error( $error ) {
		wp_send_json_error( array( 'msg' => $error ) );
	}

	/**
	 * Insert an attachment from an URL address.
	 *
	 * @param  string $url URL.
	 * @param  int    $parent_post_id Parent Post ID.
	 * @return int    Attachment ID
	 */
	function insert_attachment_from_url( $url, $parent_post_id = null ) {
		if ( ! class_exists( 'WP_Http' ) ) {
			include_once ABSPATH . WPINC . '/class-http.php';
		}
		$http     = new WP_Http();
		$response = $http->request( $url );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		if ( $response['response']['code'] != 200 ) {
			return false;
		}

		$upload = wp_upload_bits( basename( $url ), null, $response['body'] );
		if ( ! empty( $upload['error'] ) ) {
			return false;
		}

		$file_path        = $upload['file'];
		$file_name        = basename( $file_path );
		$file_type        = wp_check_filetype( $file_name, null );
		$attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );
		$wp_upload_dir    = wp_upload_dir();
		$post_info        = array(
			'guid'           => $wp_upload_dir['url'] . '/' . $file_name,
			'post_mime_type' => $file_type['type'],
			'post_title'     => $attachment_title,
			'post_content'   => '',
			'post_status'    => 'inherit',
		);
		// Create the attachment
		$attach_id = wp_insert_attachment( $post_info, $file_path, $parent_post_id );
		// Include image.php
		require_once ABSPATH . 'wp-admin/includes/image.php';
		// Define attachment metadata
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );
		// Assign metadata to attachment
		wp_update_attachment_metadata( $attach_id, $attach_data );
		return $attach_id;
	}

}