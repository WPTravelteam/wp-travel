<?php
/**
 * Helper Functions.
 *
 * @package wp-travel/inc
 */

/**
 * Get All Data Needed for booking stat.
 *
 * @since 1.0.5
 * @return Array
 */
function wp_travel_get_booking_data() {
	global $wpdb;
	$data = array();

	$initial_load = true;

	// Default variables.
	$query_limit         = apply_filters( 'wp_travel_stat_default_query_limit', 10 );
	$limit               = "limit {$query_limit}";
	$where               = '';
	$top_country_where   = '';
	$top_itinerary_where = '';
	$groupby             = '';

	$from_date = '';
	if ( isset( $_REQUEST['booking_stat_from'] ) && '' !== $_REQUEST['booking_stat_from'] ) {
		$from_date = $_REQUEST['booking_stat_from'];
	}
	$to_date = '';
	if ( isset( $_REQUEST['booking_stat_to'] ) && '' !== $_REQUEST['booking_stat_to'] ) {
		$to_date = $_REQUEST['booking_stat_to'] . ' 23:59:59';
	}
	$country = '';
	if ( isset( $_REQUEST['booking_country'] ) && '' !== $_REQUEST['booking_country'] ) {
		$country = $_REQUEST['booking_country'];
	}

	$itinerary = '';
	if ( isset( $_REQUEST['booking_itinerary'] ) && '' !== $_REQUEST['booking_itinerary'] ) {
		$itinerary = $_REQUEST['booking_itinerary'];
	}

	// Stat Data Array
	// Setting conditions.
	if ( '' !== $from_date || '' !== $to_date || '' !== $country || '' !== $itinerary ) {
		// Set initial load to false if there is extra get variables.
		$initial_load = false;

		if ( '' !== $itinerary ) {
			$where             .= " and itinerary_id={$itinerary} ";
			$top_country_where .= $where;
			$groupby           .= ' itinerary_id,';
		}
		if ( '' !== $country ) {
			$where               .= " and country='{$country}'";
			$top_itinerary_where .= " and country='{$country}'";
			$groupby             .= ' country,';
		}

		if ( '' !== $from_date && '' !== $to_date ) {

			$date_format = 'Y-m-d H:i:s';

			$booking_from = date( $date_format, strtotime( $from_date ) );
			$booking_to   = date( $date_format, strtotime( $to_date ) );

			$where               .= " and post_date >= '{$booking_from}' and post_date <= '{$booking_to}' ";
			$top_country_where   .= " and post_date >= '{$booking_from}' and post_date <= '{$booking_to}' ";
			$top_itinerary_where .= " and post_date >= '{$booking_from}' and post_date <= '{$booking_to}' ";
		}
		$limit = '';
	}

	$stat_data         = array();
	$date_format       = 'm/d/Y';
	$booking_stat_from = $booking_stat_to = date( $date_format );
	$temp_stat_data    = array();
	$max_bookings      = 0;
	$max_pax           = 0;

	if ( ! isset( $_REQUEST['chart_type'] ) || ( isset( $_REQUEST['chart_type'] ) && 'booking' === $_REQUEST['chart_type'] ) ) {
		// Booking Data Default Query.
		$initial_transient = $results = get_site_transient( '_transient_wt_booking_stat_data' );
		if ( ( ! $initial_load ) || ( $initial_load && ! $results ) ) {
			$query   = "SELECT count(ID) as wt_total, YEAR(post_date) as wt_year, MONTH(post_date) as wt_month, DAY(post_date) as wt_day, sum(no_of_pax) as no_of_pax
			from (
				Select P.ID, P.post_date, P.post_type, P.post_status, C.country, I.itinerary_id, PAX.no_of_pax from {$wpdb->posts} P
				join ( Select distinct( post_id ), meta_value as country from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_country' ) C on P.ID = C.post_id
				join ( Select distinct( post_id ), meta_value as itinerary_id from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_post_id' ) I on P.ID = I.post_id
				join ( Select distinct( post_id ), meta_value as no_of_pax from  {$wpdb->postmeta} WHERE meta_key = 'wp_travel_pax' ) PAX on P.ID = PAX.post_id
				group by P.ID, C.country, I.itinerary_id, PAX.no_of_pax
			) Booking
			where post_type='itinerary-booking' AND post_status='publish' {$where} group by {$groupby} YEAR(post_date), MONTH(post_date), DAY(post_date) {$limit}";
			$results = $wpdb->get_results( $query );
			// set initial load transient for stat data.
			if ( $initial_load && ! $initial_transient ) {
				set_site_transient( '_transient_wt_booking_stat_data', $results );
			}
		}

		$temp_stat_data['data_label'] = __( 'Bookings', 'wp-travel' );
		if ( isset( $_REQUEST['compare_stat'] ) && 'yes' === $_REQUEST['compare_stat'] ) {
			$temp_stat_data['data_label'] = __( 'Booking 1', 'wp-travel' );
		}
		$temp_stat_data['data_bg_color']     = __( '#00f', 'wp-travel' );
		$temp_stat_data['data_border_color'] = __( '#00f', 'wp-travel' );
	} else {
		// Payment Data Default Query.
		$query = "Select count( BOOKING.ID ) as wt_total, YEAR( payment_date ) as wt_year, Month( payment_date ) as wt_month, DAY( payment_date ) as wt_day, sum( AMT.payment_amount ) as payment_amount from {$wpdb->posts} BOOKING
		join (
			Select distinct( PaymentMeta.post_id ), meta_value as payment_id, PaymentPost.post_date as payment_date from {$wpdb->posts} PaymentPost
			join {$wpdb->postmeta} PaymentMeta on PaymentMeta.meta_value = PaymentPost.ID
			WHERE PaymentMeta.meta_key = 'wp_travel_payment_id'
		) PMT on BOOKING.ID = PMT.post_id
		join ( Select distinct( post_id ), meta_value as country from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_country' ) C on BOOKING.ID = C.post_id
		join ( Select distinct( post_id ), meta_value as itinerary_id from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_post_id' ) I on BOOKING.ID = I.post_id
		join ( Select distinct( post_id ), meta_value as payment_status from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_payment_status' and meta_value = 'paid' ) PSt on PMT.payment_id = PSt.post_id
		join ( Select distinct( post_id ), case when meta_value IS NULL or meta_value = '' then '0' else meta_value
       end as payment_amount from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_payment_amount'  ) AMT on PMT.payment_id = AMT.post_id
		where post_status='publish' and post_type = 'itinerary-booking' {$where}
		group by YEAR( payment_date ), Month( payment_date ), DAY( payment_date ) order by YEAR( payment_date ), Month( payment_date ), DAY( payment_date ) asc {$limit}";

		$results = $wpdb->get_results( $query );

		$temp_stat_data['data_label'] = __( 'Payment', 'wp-travel' );
		if ( isset( $_REQUEST['compare_stat'] ) && 'yes' === $_REQUEST['compare_stat'] ) {
			$temp_stat_data['data_label'] = __( 'Payment 1', 'wp-travel' );
		}
		$temp_stat_data['data_bg_color']     = __( '#1DFE0E', 'wp-travel' );
		$temp_stat_data['data_border_color'] = __( '#1DFE0E', 'wp-travel' );
	}

	if ( is_array( $results ) && count( $results ) > 0 ) {
		foreach ( $results as $result ) {
			$label_date = $result->wt_year . '-' . $result->wt_month . '-' . $result->wt_day;
			$label_date = date( $date_format, strtotime( $label_date ) );

			$temp_stat_data['data'][ $label_date ] = $result->wt_total;

			$max_bookings += (int) $result->wt_total;
			$max_pax      += (int) $result->no_of_pax;

			if ( strtotime( $booking_stat_from ) > strtotime( $label_date ) ) {

				$booking_stat_from = date( 'm/d/Y', strtotime( $label_date ) );
			}

			if ( strtotime( $booking_stat_to ) < strtotime( $label_date ) ) {
				$booking_stat_to = date( 'm/d/Y', strtotime( $label_date ) );
			}
		}
	}

	// Booking Calculation ends here.
	if ( '' !== $from_date ) {
		$booking_stat_from = date( 'm/d/Y', strtotime( $from_date ) );
	}

	if ( '' !== $to_date ) {
		$booking_stat_to = date( 'm/d/Y', strtotime( $to_date ) );
	}

	// Query for top country.
	$initial_transient = $results = get_site_transient( '_transient_wt_booking_top_country' );
	if ( ( ! $initial_load ) || ( $initial_load && ! $results ) ) {
		$top_country_query = "SELECT count(ID) as wt_total, country
		from (
			Select P.ID, P.post_date, P.post_type, P.post_status, C.country, I.itinerary_id from  {$wpdb->posts} P
			join ( Select distinct( post_id ), meta_value as country from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_country' and meta_value != '' ) C on P.ID = C.post_id
			join ( Select distinct( post_id ), meta_value as itinerary_id from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_post_id' ) I on P.ID = I.post_id
			group by P.ID, C.country, I.itinerary_id
		) Booking
		where post_type='itinerary-booking' AND post_status='publish' {$where}  group by country order by wt_total desc";

		$top_countries = array();
		$results       = $wpdb->get_results( $top_country_query );
		// set initial load transient for stat data.
		if ( $initial_load && ! $initial_transient ) {
			set_site_transient( '_transient_wt_booking_top_country', $results );
		}
	}

	if ( is_array( $results ) && count( $results ) > 0 ) {
		foreach ( $results as $result ) {
			$top_countries[] = $result->country;
		}
	}
	// End of query for top country.
	// Query for top Itinerary.
	$initial_transient = $results = get_site_transient( '_transient_wt_booking_top_itinerary' );
	if ( ( ! $initial_load ) || ( $initial_load && ! $results ) ) {
		$top_itinerary_query = "SELECT count(ID) as wt_total, itinerary_id
		from (
			Select P.ID, P.post_date, P.post_type, P.post_status, C.country, I.itinerary_id from  {$wpdb->posts} P
			join ( Select distinct( post_id ), meta_value as country from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_country' and meta_value != '' ) C on P.ID = C.post_id
			join ( Select distinct( post_id ), meta_value as itinerary_id from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_post_id' ) I on P.ID = I.post_id
			group by P.ID, C.country, I.itinerary_id
		) Booking
		where post_type='itinerary-booking' AND post_status='publish' {$where}  group by itinerary_id order by wt_total desc";

		$results = $wpdb->get_results( $top_itinerary_query );
		// set initial load transient for stat data.
		if ( $initial_load && ! $initial_transient ) {
			set_site_transient( '_transient_wt_booking_top_itinerary', $results );
		}
	}
	$top_itinerary = array(
		'name' => esc_html__( 'N/A', 'wp-travel' ),
		'url'  => '',
	);
	if ( is_array( $results ) && count( $results ) > 0 ) {
		$itinerary_id = $results['0']->itinerary_id;

		if ( $itinerary_id ) {
			$top_itinerary['name'] = get_the_title( $itinerary_id );
			$top_itinerary['id']   = $itinerary_id;
		}
	}

	$booking_additional_data = array(
		'from'          => $booking_stat_from,
		'to'            => $booking_stat_to,
		'max_bookings'  => $max_bookings,
		'max_pax'       => $max_pax,
		'top_countries' => $top_countries,
		'top_itinerary' => $top_itinerary,
	);

	$data[] = $temp_stat_data;

	// End of Booking Data Default Query.
	$where               = '';
	$top_country_where   = '';
	$top_itinerary_where = '';
	$groupby             = '';
	if ( isset( $_REQUEST['compare_stat'] ) && 'yes' === $_REQUEST['compare_stat'] ) {

		$compare_from_date = '';
		if ( isset( $_REQUEST['compare_stat_from'] ) && '' !== $_REQUEST['compare_stat_from'] ) {
			$compare_from_date = $_REQUEST['compare_stat_from'];
		}
		$compare_to_date = '';
		if ( isset( $_REQUEST['compare_stat_to'] ) && '' !== $_REQUEST['compare_stat_to'] ) {
			$compare_to_date = $_REQUEST['compare_stat_to'] . ' 23:59:59';
		}
		$compare_country = '';
		if ( isset( $_REQUEST['compare_country'] ) && '' !== $_REQUEST['compare_country'] ) {
			$compare_country = $_REQUEST['compare_country'];
		}

		$compare_itinerary = '';
		if ( isset( $_REQUEST['compare_itinerary'] ) && '' !== $_REQUEST['compare_itinerary'] ) {
			$compare_itinerary = $_REQUEST['compare_itinerary'];
		}

		// Setting conditions.
		if ( '' !== $compare_from_date || '' !== $compare_to_date || '' !== $compare_country || '' !== $compare_itinerary ) {
			// Set initial load to false if there is extra get variables.
			$initial_load = false;

			if ( '' !== $compare_itinerary ) {
				$where             .= " and itinerary_id={$compare_itinerary} ";
				$top_country_where .= $where;
				$groupby           .= ' itinerary_id,';
			}
			if ( '' !== $compare_country ) {
				$where               .= " and country='{$compare_country}'";
				$top_itinerary_where .= " and country='{$compare_country}'";
				$groupby             .= ' country,';
			}

			if ( '' !== $compare_from_date && '' !== $compare_to_date ) {

				$date_format = 'Y-m-d H:i:s';

				$booking_from = date( $date_format, strtotime( $compare_from_date ) );
				$booking_to   = date( $date_format, strtotime( $compare_to_date ) );

				$where               .= " and post_date >= '{$booking_from}' and post_date <= '{$booking_to}' ";
				$top_country_where   .= " and post_date >= '{$booking_from}' and post_date <= '{$booking_to}' ";
				$top_itinerary_where .= " and post_date >= '{$booking_from}' and post_date <= '{$booking_to}' ";
			}
			$limit = '';
		}

		$temp_compare_data = array();
		if ( ! isset( $_REQUEST['chart_type'] ) || ( isset( $_REQUEST['chart_type'] ) && 'booking' === $_REQUEST['chart_type'] ) ) {

			// Compare Data Default Query.
			$query   = "SELECT count(ID) as wt_total, YEAR(post_date) as wt_year, MONTH(post_date) as wt_month, DAY(post_date) as wt_day, sum(no_of_pax) as no_of_pax
			from (
				Select P.ID, P.post_date, P.post_type, P.post_status, C.country, I.itinerary_id, PAX.no_of_pax from {$wpdb->posts} P
				join ( Select distinct( post_id ), meta_value as country from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_country' ) C on P.ID = C.post_id
				join ( Select distinct( post_id ), meta_value as itinerary_id from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_post_id' ) I on P.ID = I.post_id
				join ( Select distinct( post_id ), meta_value as no_of_pax from  {$wpdb->postmeta} WHERE meta_key = 'wp_travel_pax' ) PAX on P.ID = PAX.post_id
				group by P.ID, C.country, I.itinerary_id, PAX.no_of_pax
			) Booking
			where post_type='itinerary-booking' AND post_status='publish' {$where} group by {$groupby} YEAR(post_date), MONTH(post_date), DAY(post_date) {$limit}";
			$results = $wpdb->get_results( $query );

			$temp_compare_data['data_label']        = __( 'Booking 2', 'wp-travel' );
			$temp_compare_data['data_bg_color']     = __( '#3c0', 'wp-travel' );
			$temp_compare_data['data_border_color'] = __( '#3c0', 'wp-travel' );
		} else {
			// Payment Data Default Query.
			$query = "Select count( BOOKING.ID ) as wt_total, YEAR( payment_date ) as wt_year, Month( payment_date ) as wt_month, DAY( payment_date ) as wt_day, sum( AMT.payment_amount ) as payment_amount from {$wpdb->posts} BOOKING
			join (
				Select distinct( PaymentMeta.post_id ), meta_value as payment_id, PaymentPost.post_date as payment_date from {$wpdb->posts} PaymentPost
				join {$wpdb->postmeta} PaymentMeta on PaymentMeta.meta_value = PaymentPost.ID
				WHERE PaymentMeta.meta_key = 'wp_travel_payment_id'
			) PMT on BOOKING.ID = PMT.post_id
			join ( Select distinct( post_id ), meta_value as country from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_country' ) C on BOOKING.ID = C.post_id
			join ( Select distinct( post_id ), meta_value as itinerary_id from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_post_id' ) I on BOOKING.ID = I.post_id
			join ( Select distinct( post_id ), meta_value as payment_status from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_payment_status' and meta_value = 'paid' ) PSt on PMT.payment_id = PSt.post_id
			join ( Select distinct( post_id ), case when meta_value IS NULL or meta_value = '' then '0' else meta_value
		end as payment_amount from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_payment_amount'  ) AMT on PMT.payment_id = AMT.post_id
			where post_status='publish' and post_type = 'itinerary-booking' {$where}
			group by YEAR( payment_date ), Month( payment_date ), DAY( payment_date ) order by YEAR( payment_date ), Month( payment_date ), DAY( payment_date ) asc {$limit}";

			$results = $wpdb->get_results( $query );

			$temp_compare_data['data_label'] = __( 'Payment', 'wp-travel' );
			if ( isset( $_REQUEST['compare_stat'] ) && 'yes' === $_REQUEST['compare_stat'] ) {
				$temp_compare_data['data_label'] = __( 'Payment 2', 'wp-travel' );
			}
			$temp_compare_data['data_bg_color']     = __( '#000', 'wp-travel' );
			$temp_compare_data['data_border_color'] = __( '#000', 'wp-travel' );
		}

		$date_format = 'm/d/Y';

		$max_bookings = 0;
		$max_pax      = 0;
		if ( is_array( $results ) && count( $results ) > 0 ) {
			foreach ( $results as $result ) {
				$label_date                               = $result->wt_year . '-' . $result->wt_month . '-' . $result->wt_day;
				$label_date                               = date( $date_format, strtotime( $label_date ) );
				$temp_compare_data['data'][ $label_date ] = $result->wt_total;

				$max_bookings += (int) $result->wt_total;
				$max_pax      += (int) $result->no_of_pax;
			}
		}

		// Query for top country.
		$top_country_query = "SELECT count(ID) as wt_total, country
		from (
			Select P.ID, P.post_date, P.post_type, P.post_status, C.country, I.itinerary_id from  {$wpdb->posts} P
			join ( Select distinct( post_id ), meta_value as country from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_country' and meta_value != '' ) C on P.ID = C.post_id
			join ( Select distinct( post_id ), meta_value as itinerary_id from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_post_id' ) I on P.ID = I.post_id
			group by P.ID, C.country, I.itinerary_id
		) Booking
		where post_type='itinerary-booking' AND post_status='publish' {$where}  group by country order by wt_total desc";

		$top_countries = array();
		$results       = $wpdb->get_results( $top_country_query );

		if ( is_array( $results ) && count( $results ) > 0 ) {
			foreach ( $results as $result ) {
				$top_countries[] = $result->country;
			}
		}
		// End of query for top country.
		// Query for top Itinerary.
		$top_itinerary_query = "SELECT count(ID) as wt_total, itinerary_id
		from (
			Select P.ID, P.post_date, P.post_type, P.post_status, C.country, I.itinerary_id from  {$wpdb->posts} P
			join ( Select distinct( post_id ), meta_value as country from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_country' and meta_value != '' ) C on P.ID = C.post_id
			join ( Select distinct( post_id ), meta_value as itinerary_id from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_post_id' ) I on P.ID = I.post_id
			group by P.ID, C.country, I.itinerary_id
		) Booking
		where post_type='itinerary-booking' AND post_status='publish' {$where}  group by itinerary_id order by wt_total desc";

		$results = $wpdb->get_results( $top_itinerary_query );
		// set initial load transient for stat data.
		if ( $initial_load && ! $initial_transient ) {
			set_site_transient( '_transient_wt_booking_top_itinerary', $results );
		}
		$top_itinerary = array(
			'name' => esc_html__( 'N/A', 'wp-travel' ),
			'url'  => '',
		);
		if ( is_array( $results ) && count( $results ) > 0 ) {
			$itinerary_id = $results['0']->itinerary_id;

			if ( $itinerary_id ) {
				$top_itinerary['name'] = get_the_title( $itinerary_id );
				$top_itinerary['id']   = $itinerary_id;
			}
		}
		// Compare Calculation ends here.
		if ( '' !== $compare_from_date ) {
			$compare_from_date = date( 'm/d/Y', strtotime( $compare_from_date ) );
		}

		if ( '' !== $compare_to_date ) {
			$compare_to_date = date( 'm/d/Y', strtotime( $compare_to_date ) );
		}

		$compare_additional_data = array(
			'from'          => $compare_from_date,
			'to'            => $compare_to_date,
			'max_bookings'  => $max_bookings,
			'max_pax'       => $max_pax,
			'top_countries' => $top_countries,
			'top_itinerary' => $top_itinerary,
		);
		// Compare Calculation ends here.
		$data[] = $temp_compare_data;
	}
	$data          = apply_filters( 'wp_travel_stat_data', $data, $_REQUEST );
	$new_stat_data = wp_travel_make_stat_data( $data );

	// End of query for top Itinerary.
	$stat_data['stat_data'] = $new_stat_data;

	$stat_data['booking_stat_from'] = $booking_additional_data['from'];
	$stat_data['booking_stat_to']   = $booking_additional_data['to'];
	$stat_data['max_bookings']      = $booking_additional_data['max_bookings'];
	$stat_data['max_pax']           = $booking_additional_data['max_pax'];
	$stat_data['top_countries']     = wp_travel_get_country_by_code( $booking_additional_data['top_countries'] );
	$stat_data['top_itinerary']     = $booking_additional_data['top_itinerary'];

	if ( isset( $_REQUEST['compare_stat'] ) && 'yes' === $_REQUEST['compare_stat'] ) {
		$stat_data['compare_stat_from']     = $compare_additional_data['from'];
		$stat_data['compare_stat_to']       = $compare_additional_data['to'];
		$stat_data['compare_max_bookings']  = $compare_additional_data['max_bookings'];
		$stat_data['compare_max_pax']       = $compare_additional_data['max_pax'];
		$stat_data['compare_top_countries'] = wp_travel_get_country_by_code( $compare_additional_data['top_countries'] );
		$stat_data['compare_top_itinerary'] = $compare_additional_data['top_itinerary'];

		// Query for total 2 in compare stat.
		// if ( class_exists( 'WP_travel_paypal' ) ) :
			$query   = "Select count( BOOKING.ID ) as no_of_payment, YEAR( payment_date ) as payment_year, Month( payment_date ) as payment_month, DAY( payment_date ) as payment_day, sum( AMT.payment_amount ) as payment_amount from {$wpdb->posts} BOOKING
			join (
				Select distinct( PaymentMeta.post_id ), meta_value as payment_id, PaymentPost.post_date as payment_date from {$wpdb->posts} PaymentPost
				join {$wpdb->postmeta} PaymentMeta on PaymentMeta.meta_value = PaymentPost.ID
				WHERE PaymentMeta.meta_key = 'wp_travel_payment_id'
			) PMT on BOOKING.ID = PMT.post_id
			join ( Select distinct( post_id ), meta_value as country from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_country' ) C on BOOKING.ID = C.post_id
			join ( Select distinct( post_id ), meta_value as itinerary_id from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_post_id' ) I on BOOKING.ID = I.post_id
			join ( Select distinct( post_id ), meta_value as payment_status from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_payment_status' and meta_value = 'paid' ) PSt on PMT.payment_id = PSt.post_id
			join ( Select distinct( post_id ), case when meta_value IS NULL or meta_value = '' then '0' else meta_value
		end as payment_amount from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_payment_amount'  ) AMT on PMT.payment_id = AMT.post_id
			where post_status='publish' and post_type = 'itinerary-booking' {$where}
			group by YEAR( payment_date ), Month( payment_date ), DAY( payment_date ) order by YEAR( payment_date ), Month( payment_date ), DAY( payment_date ) asc {$limit}";
			$results = $wpdb->get_results( $query );

			$total_sales_compare = 0;
		if ( $results ) {
			foreach ( $results as $result ) {
				$total_sales_compare += $result->payment_amount;
			}
		}
			$stat_data['total_sales_compare'] = number_format( $total_sales_compare, 2, '.', '' );
		// endif;
	}

	return $stat_data;
}

/**
 * Get Booking Status List.
 *
 * @since 1.0.5
 */
function wp_travel_get_booking_status() {
	$status = array(
		'pending'  => array(
			'color' => '#FF9800',
			'text'  => __( 'Pending', 'wp-travel' ),
		),
		'booked'   => array(
			'color' => '#008600',
			'text'  => __( 'Booked', 'wp-travel' ),
		),
		'canceled' => array(
			'color' => '#FE450E',
			'text'  => __( 'Canceled', 'wp-travel' ),
		),
		'N/A'      => array(
			'color' => '#892E2C',
			'text'  => __( 'N/A', 'wp-travel' ),
		),
	);

	return apply_filters( 'wp_travel_booking_status_list', $status );
}

function wp_travel_make_stat_data( $stat_datas, $show_empty = false ) {
	if ( ! $stat_datas ) {
		return;
	}
	// Split stat data.
	if ( is_array( $stat_datas ) && count( $stat_datas ) > 0 ) {
		$data              = array();
		$data_label        = array();
		$data_bg_color     = array();
		$data_border_color = array();
		foreach ( $stat_datas as $stat_data ) {
			$data[]              = isset( $stat_data['data'] ) ? $stat_data['data'] : array();
			$data_label[]        = isset( $stat_data['data_label'] ) ? $stat_data['data_label'] : array();
			$data_bg_color[]     = isset( $stat_data['data_bg_color'] ) ? $stat_data['data_bg_color'] : array();
			$data_border_color[] = isset( $stat_data['data_border_color'] ) ? $stat_data['data_border_color'] : array();
		}
	}

	if ( is_array( $data ) ) {
		if ( count( $data ) == 1 ) {
			$default_array_key = array_keys( $data[0] );
			$new_data[]        = array_values( $data[0] );

		} elseif ( count( $data ) > 1 ) {
			if ( count( $data ) > 1 ) {
				$array_with_all_keys = $data[0];
				for ( $i = 0; $i < count( $data ) - 1; $i++ ) {
					$next_array_key         = array_keys( $data[ $i + 1 ] );
					$next_array_default_val = array_fill_keys( $next_array_key, 0 );

					$array_with_all_keys = array_merge( $next_array_default_val, $array_with_all_keys );
					uksort(
						$array_with_all_keys,
						function( $a, $b ) {
							return strtotime( $a ) > strtotime( $b );
						}
					);
				}
				$default_array_key = array_keys( $array_with_all_keys );
				$default_stat_val  = null;
				if ( $show_empty ) {
					$default_stat_val = 0;
				}
				$array_key_default_val = array_fill_keys( $default_array_key, $default_stat_val );

				$new_data = array();
				for ( $i = 0; $i < count( $data ); $i++ ) {
					$new_array = array_merge( $array_key_default_val, $data[ $i ] );
					uksort(
						$new_array,
						function( $a, $b ) {
							return strtotime( $a ) > strtotime( $b );
						}
					);
					$new_data[] = array_values( $new_array );
				}
			}
		}
		$new_return_data['stat_label']        = $default_array_key;
		$new_return_data['data']              = $new_data;
		$new_return_data['data_label']        = $data_label;
		$new_return_data['data_bg_color']     = $data_bg_color;
		$new_return_data['data_border_color'] = $data_border_color;

		return $new_return_data;
	}
}
