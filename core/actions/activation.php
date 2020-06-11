<?php
class WP_Travel_Actions_Activation {
	public static function init() {
		register_activation_hook( WP_TRAVEL_PLUGIN_FILE, array( __CLASS__, 'activations' ) );
	}

	public static function activations( $network_enabled ) {
		self::add_default_pricing_categories();
		self::add_db_tables( $network_enabled );
	}

	public static function add_db_tables( $network_enabled ) {
		$pricing_table_created = get_option( 'wp_travel_pricing_table_created' );

		if ( 'yes' === $pricing_table_created ) {
			return;
		}

		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		if ( is_multisite() ) {

			if ( $network_enabled ) {
				$sites = get_sites();
				if ( is_array( $sites ) && count( $sites ) > 0 ) {

					foreach ( $sites as $site ) {
						$tables        = self::wp_travel_get_db_tables( $site->blog_id );
						$create_tables = array(
							'pricings_table'            => $tables['pricings_table'],
							'dates_table'               => $tables['dates_table'],
							'excluded_dates_time_table' => $tables['excluded_dates_time_table'],
							'price_category_relation'   => $tables['price_category_relation'],
						);

						self::create_db_tables( $create_tables );
					}
				}
			} else {
				$blog_id = get_current_blog_id();

				$tables        = self::wp_travel_get_db_tables( $blog_id );
				$create_tables = array(
					'pricings_table'            => $tables['pricings_table'],
					'dates_table'               => $tables['dates_table'],
					'excluded_dates_time_table' => $tables['excluded_dates_time_table'],
					'price_category_relation'   => $tables['price_category_relation'],
				);
				self::create_db_tables( $create_tables );
			}
		} else {

			$tables        = self::wp_travel_get_db_tables();
			$create_tables = array(
				'pricings_table'            => $tables['pricings_table'],
				'dates_table'               => $tables['dates_table'],
				'excluded_dates_time_table' => $tables['excluded_dates_time_table'],
				'price_category_relation'   => $tables['price_category_relation'],
			);
			self::create_db_tables( $create_tables );
		}

		update_option( 'wp_travel_pricing_table_created', 'yes' ); // Note: not worked for multisite network enabled. [Quick fix: updated this option from data migration file 400.php]
	}

	public static function create_db_tables( $tables = array() ) {

		if ( ! is_array( $tables ) || count( $tables ) == 0 ) {
			return;
		}
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$pricings_table            = $tables['pricings_table'];
		$price_category_relation   = $tables['price_category_relation'];
		$dates_table               = $tables['dates_table'];
		$excluded_dates_time_table = $tables['excluded_dates_time_table'];

		// Pricing Table.
		$sql = "CREATE TABLE IF NOT EXISTS $pricings_table(
			id int(255) NOT NULL AUTO_INCREMENT,
			title varchar(255) DEFAULT '' NULL,
			trip_id int(11) DEFAULT '0' NULL,
			min_pax int(11) DEFAULT '0' NULL,
			max_pax int(11) DEFAULT '0' NULL,
			has_group_price varchar(11) DEFAULT '0' NULL,
			group_prices longtext DEFAULT '' NULL,
			trip_extras varchar(255) DEFAULT '' NULL,
			dates longtext DEFAULT '' NULL,
			sort_order int(11) DEFAULT '1' NULL,

			PRIMARY KEY (id)
			) $charset_collate;";
		dbDelta( $sql );

		$sql = "CREATE TABLE IF NOT EXISTS $price_category_relation(
			id int(255) NOT NULL AUTO_INCREMENT,
			pricing_id int(11) DEFAULT '0' NULL,
			pricing_category_id int(11) DEFAULT '0' NULL,
			price_per varchar(60) DEFAULT '' NULL,
			regular_price varchar(60) DEFAULT '' NULL,
			is_sale int(11) DEFAULT '0' NULL,
			sale_price varchar(60) DEFAULT '' NULL,
			has_group_price int(11) DEFAULT '0' NULL,
			group_prices longtext DEFAULT '' NULL,
			default_pax int(11) DEFAULT '0' NULL,
			PRIMARY KEY (id)
			) $charset_collate;";
		dbDelta( $sql );

		// Dates Table.
		$sql = "CREATE TABLE IF NOT EXISTS $dates_table(
			id int(255) NOT NULL AUTO_INCREMENT,
			trip_id int(11) DEFAULT NULL NULL,
			title varchar(255) DEFAULT '' NULL,
			recurring varchar(5) DEFAULT '' NULL,
			-- weekly_or_daily varchar(10) DEFAULT 'weekly' NULL,
			years varchar(255) DEFAULT '' NULL,
			months varchar(255) DEFAULT '' NULL,
			weeks varchar(255) DEFAULT '' NULL,
			days varchar(255) DEFAULT '' NULL,
			date_days varchar(255) DEFAULT '' NULL,
			start_date DATE DEFAULT NULL NULL,
			end_date DATE DEFAULT NULL NULL,
			trip_time varchar(255) DEFAULT '' NULL,
			pricing_ids varchar(255) DEFAULT '' NULL,
			PRIMARY KEY (id)
			) $charset_collate;";

		dbDelta( $sql );

		// Excluded Dates Table.
		$sql = "CREATE TABLE IF NOT EXISTS $excluded_dates_time_table(
			id int(255) NOT NULL AUTO_INCREMENT,
			trip_id int(11) DEFAULT NULL NULL,
			title varchar(255) DEFAULT '' NULL,
			recurring varchar(5) DEFAULT '' NULL,
			-- weekly_or_daily varchar(10) DEFAULT 'weekly' NULL,
			years varchar(255) DEFAULT '' NULL,
			months varchar(255) DEFAULT '' NULL,
			weeks varchar(255) DEFAULT '' NULL,
			days varchar(255) DEFAULT '' NULL,
			date_days varchar(255) DEFAULT '' NULL,
			start_date DATE DEFAULT NULL NULL,
			end_date DATE DEFAULT NULL NULL,
			time varchar(255) DEFAULT '' NULL,
			PRIMARY KEY (id)
			) $charset_collate;";
		dbDelta( $sql );

	}

	// Temp Helper Functions
	public static function wp_travel_get_db_tables( $blog_id = null ) {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// if ( is_multisite() ) {
		// if ( is_plugin_active_for_network( __FILE__ ) ) {
		// $sites = wp_sites();
		// } else {
		// $blog_id = get_current_blog_id();
		// $pricings_table      = $wpdb->base_prefix . $blog_id . '_wt_pricings';
		// $dates_table         = $wpdb->base_prefix . $blog_id . '_wt_dates';
		// $date_price_relation = $wpdb->base_prefix . $blog_id . '_wt_price_dates_relation';
		// }
		// } else {
		// $pricings_table      = $wpdb->base_prefix . 'wt_pricings';
		// $dates_table         = $wpdb->base_prefix . 'wt_dates';
		// $date_price_relation = $wpdb->base_prefix . 'wt_price_dates_relation';
		// }
		$tables = array();
		if ( $blog_id ) {

			$tables['pricings_table']            = $wpdb->base_prefix . $blog_id . '_wt_pricings';
			$tables['dates_table']               = $wpdb->base_prefix . $blog_id . '_wt_dates';
			$tables['excluded_dates_time_table'] = $wpdb->base_prefix . $blog_id . '_wt_excluded_dates_times';
			$tables['date_price_relation']       = $wpdb->base_prefix . $blog_id . '_wt_price_dates_relation';
			$tables['price_category_relation']   = $wpdb->base_prefix . $blog_id . '_wt_price_category_relation';
		} else {
			$tables['pricings_table']            = $wpdb->base_prefix . 'wt_pricings';
			$tables['dates_table']               = $wpdb->base_prefix . 'wt_dates';
			$tables['excluded_dates_time_table'] = $wpdb->base_prefix . 'wt_excluded_dates_times';
			$tables['date_price_relation']       = $wpdb->base_prefix . 'wt_price_dates_relation';
			$tables['price_category_relation']   = $wpdb->base_prefix . 'wt_price_category_relation';
		}
		return $tables;
	}

	public static function add_default_pricing_categories() {
		WP_Travel_Actions_Register_Taxonomies::create_taxonomies();
		$tax       = 'itinerary_pricing_category';
		$termExits = term_exists( 'adult', $tax );
		if ( $termExits === 0 || $termExits === null ) {
			$term = wp_insert_term(
				'Adult',   // the term
				'itinerary_pricing_category', // the taxonomy
				array(
					'slug' => 'adult',
				)
			);
			if ( ! is_wp_error( $term ) ) {
				update_term_meta( $term['term_id'], 'pax_size', 1 );
			}
		}
	}
}

WP_Travel_Actions_Activation::init();
