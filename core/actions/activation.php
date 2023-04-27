<?php
/**
 * WP Travel Activation hooks.
 *
 * @package WP_Travel
 */

/**
 * Activation class.
 */
class WP_Travel_Actions_Activation {
 // @phpcs:ignore

	/**
	 * Minimum required PHP version.
	 *
	 * @var string
	 */
	public static $min_php_version = '5.6.20';

	/**
	 * Minimum required WP version.
	 *
	 * @var string
	 */
	public static $min_wp_version = '5.4.1';

	/**
	 * Init.
	 *
	 * @param bool $network_enabled Whether network enabled or not.
	 */
	public static function init( $network_enabled ) {

		self::compatibility();
		self::add_default_pricing_categories();
		self::add_db_tables( $network_enabled );

		WP_Travel_Post_Types::init();
		Wp_Travel_Taxonomies::init();
		self::create_pages();
		self::migrations();
		WP_Travel::create_roles(); // @since 1.3.7
		self::update_db_version();

		self::wp_travel_check_for_plugin_activation();

		// Flush Rewrite rule.
		flush_rewrite_rules();

	}

	/**
	 * Check for multile plugin activation.
	 */
	public static function wp_travel_check_for_plugin_activation() {
		// Don't do redirects when multiple plugins are bulk activated
		if (
			( isset( $_REQUEST['action'] ) && 'activate-selected' === $_REQUEST['action'] ) &&
			( isset( $_POST['checked'] ) && count( $_POST['checked'] ) > 1 ) ) {
			return;
		}
		add_option( 'wp_travel_setup_page_redirect', wp_get_current_user()->ID );

	}

	/**
	 * Check compatibility before activate.
	 *
	 * @since 4.5.8
	 */
	public static function compatibility() {
		// Check for PHP Compatibility.
		global $wp_version;
		if ( version_compare( PHP_VERSION, self::$min_php_version, '<' ) ) {

			$flag = __( 'PHP', 'wp-travel' );

			// translators: placeholder for PHP minimum version.
			$version = sprintf( __( '%s or higher', 'wp-travel' ), self::$min_php_version );
			deactivate_plugins( basename( WP_TRAVEL_PLUGIN_FILE ) );
			// translators: placeholder for PHP word & PHP minimum version.
			$message = sprintf( __( 'WP Travel plugin requires %1$s version %2$s to work.', 'wp-travel' ), $flag, $version );
			wp_die(
				esc_attr( $message ),
				esc_attr( __( 'Plugin Activation Error', 'wp-travel' ) ),
				array(
					'response'  => 200,
					'back_link' => true,
				)
			);
		}
	}

	/**
	 * Create WP Travel Pages.
	 *
	 * @since 4.5.8
	 */
	public static function create_pages() {
		/**
		 * Insert cart and checkout pages
		 *
		 * @since 1.2.3
		 */
		include_once sprintf( '%s/inc/admin/admin-helper.php', WP_TRAVEL_ABSPATH );

		// Shortcode filters.
		$cart_shortcode_tag = apply_filters( 'wp_travel_cart_shortcode_tag', 'wp_travel_cart' ); // phpcs:ignore
		$cart_shortcode_tag = apply_filters( 'wptravel_cart_shortcode_tag', $cart_shortcode_tag );

		$checkout_shortcode_tag = apply_filters( 'wp_travel_checkout_shortcode_tag', 'wp_travel_checkout' ); // phpcs:ignore
		$checkout_shortcode_tag = apply_filters( 'wptravel_checkout_shortcode_tag', $checkout_shortcode_tag );

		$account_shortcode_tag = apply_filters( 'wp_travel_account_shortcode_tag', 'wp_travel_user_account' ); // phpcs:ignore
		$account_shortcode_tag = apply_filters( 'wptravel_account_shortcode_tag', $account_shortcode_tag );

		$pages = apply_filters(
			'wp_travel_create_pages', // phpcs:ignore
			array(
				'wp-travel-checkout'  => array(
					'name'    => _x( 'wp-travel-checkout', 'Page slug', 'wp-travel' ),
					'title'   => _x( 'WP Travel Checkout', 'Page title', 'wp-travel' ),
					'content' => '[' . $checkout_shortcode_tag . ']',
				),
				'wp-travel-dashboard' => array(
					'name'    => _x( 'wp-travel-dashboard', 'Page slug', 'wp-travel' ),
					'title'   => _x( 'WP Travel Dashboard', 'Page title', 'wp-travel' ),
					'content' => '[' . $account_shortcode_tag . ']',
				),
			)
		);

		$pages = apply_filters( 'wptravel_create_pages', $pages );

		foreach ( $pages as $key => $page ) {
			wptravel_create_page( esc_sql( $page['name'] ), 'wp_travel_' . $key . '_page_id', $page['title'], $page['content'], ! empty( $page['parent'] ) ? wptravel_get_page_id( $page['parent'] ) : '' );
		}
	}

	/**
	 * Migrations.
	 *
	 * @since 4.5.8
	 */
	public static function migrations() {
		$migrations = array(
			/**
			 * 'name' : 'name of file'.
			 * 'version':  'Migrate if current version is greater than this'.
			 */
			array(
				'name'    => '103-104',
				'version' => '1.0.3',
			),
			array(
				'name'    => '104-105',
				'version' => '1.0.4',
			),
			array(
				'name'    => 'update-121',
				'version' => '1.2.0',
			),
			array(
				'name'    => '175-176',
				'version' => '1.7.5',
			),
			array(
				'name'    => '193-194',
				'version' => '1.9.3',
			),
			array(
				'name'    => '303-304',
				'version' => '3.0.3',
			),
			array(
				'name'    => '322-323',
				'version' => '3.2.2',
			),
			array(
				'name'    => '400',
				'version' => '4.0.0',
			),
			array(
				'name'    => '404',
				'version' => '4.0.4',
			),
			array(
				'name'    => '505',
				'version' => '5.0.5',
			),
			array(
				'name'    => '523',
				'version' => '5.2.3',
			),
		);
		self::migration_includes( $migrations );
	}

	/**
	 * Include all Migration files.
	 *
	 * @param array $files List of migration files.
	 * @since 4.4.0
	 * @since 5.0.2 Optimized migration script. run/include only required migration file on activation.
	 * @return void
	 */
	public static function migration_includes( $files ) {

		$current_db_version = get_option( 'wp_travel_version' );
		if ( empty( $current_db_version ) ) {
			return; // No need to run migration in case of new user.
		}

		$user_since   = get_option( 'wp_travel_user_since', '1.0.0' );
		$include_path = sprintf( '%s/upgrade', untrailingslashit( WP_TRAVEL_ABSPATH ) );
		foreach ( $files as $file ) {
			if ( version_compare( $user_since, $file['version'], '<' ) ) {
				include_once sprintf( '%s/%s.php', $include_path, $file['name'] );
			}
		}
	}

	/**
	 * Update DB Version.
	 *
	 * @since 4.5.8
	 */
	public static function update_db_version() {
		$current_db_version = get_option( 'wp_travel_version' );
		if ( WP_TRAVEL_VERSION !== $current_db_version ) {
			if ( empty( $current_db_version ) ) {
				/**
				 * Update wp travel version.
				 *
				 * @since 3.0.0
				 */
				update_option( 'wp_travel_user_since', WP_TRAVEL_VERSION );

				/**
				 * Option is used to hide option 'Enable multiple category on pricing' and single pricng option.
				 *
				 * @since 3.0.0
				 */
				update_option( 'wp_travel_user_after_multiple_pricing_category', 'yes' );
			}
			update_option( 'wp_travel_version', WP_TRAVEL_VERSION );
		}
		// Update marketplace data transient.
		delete_transient( 'wp_travel_marketplace_addons_list' );
	}

	/**
	 * Add WP Travel custom tables.
	 *
	 * @param bool $network_enabled Is network enabled or not.
	 * @param int  $blog_id Blog id of the site, only have value if add new site. In case of network activate it will loop through the sites to get blog id.
	 */
	public static function add_db_tables( $network_enabled, $blog_id = null ) {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			// Multisite but network activate. [This will not for add new site].
			if ( $network_enabled ) {
				if ( false === is_super_admin() ) {
					return;
				}
				$sites = get_sites();
				if ( is_array( $sites ) && count( $sites ) > 0 ) {
					foreach ( $sites as $site ) {
						switch_to_blog( $site->blog_id );
						$tables        = self::get_db_tables( $site->blog_id );
						$create_tables = array(
							'pricings_table'            => $tables['pricings_table'],
							'dates_table'               => $tables['dates_table'],
							'excluded_dates_time_table' => $tables['excluded_dates_time_table'],
							'price_category_relation'   => $tables['price_category_relation'],
						);
						self::create_db_tables( $create_tables );
						restore_current_blog();
					}
				}
			} else {
				// Multisite, single site activate. or adding new site from network. [blog id from param have value only in case of add new site.].
				$blog_id = $blog_id ? $blog_id : get_current_blog_id();

				$tables        = self::get_db_tables( $blog_id );
				$create_tables = array(
					'pricings_table'            => $tables['pricings_table'],
					'dates_table'               => $tables['dates_table'],
					'excluded_dates_time_table' => $tables['excluded_dates_time_table'],
					'price_category_relation'   => $tables['price_category_relation'],
				);
				self::create_db_tables( $create_tables );
			}
		} else {
			$tables        = self::get_db_tables();
			$create_tables = array(
				'pricings_table'            => $tables['pricings_table'],
				'dates_table'               => $tables['dates_table'],
				'excluded_dates_time_table' => $tables['excluded_dates_time_table'],
				'price_category_relation'   => $tables['price_category_relation'],
			);
			self::create_db_tables( $create_tables );
		}

		update_option( 'wp_travel_pricing_table_created', 'yes' ); // Note: not worked for multisite network enabled. [Quick fix: updated this option from data migration file 400.php].
	}

	/**
	 * Create DB Tables.
	 *
	 * @param array $tables Name of tables.
	 */
	public static function create_db_tables( $tables = array() ) {

		if ( ! is_array( $tables ) || 0 === count( $tables ) ) {
			return;
		}
		$pricing_table_created = get_option( 'wp_travel_pricing_table_created' );

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
			group_prices longtext NULL,
			trip_extras varchar(255) DEFAULT '' NULL,
			dates longtext NULL,
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
			group_prices longtext NULL,
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

	/**
	 * Delete DB Tables.
	 *
	 * @param int $blog_id Id of blog/site.
	 */
	public static function remove_db_tables( $blog_id = array() ) {
		if ( ! $blog_id ) {
			return;
		}

		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$tables          = self::get_db_tables( $blog_id );
		foreach ( $tables as $table ) {
			$sql = "DROP TABLE IF EXISTS $table";
			$wpdb->query( $sql ); // @phpcs:ignore
		}
	}

	/**
	 * Temp Helper Functions.
	 *
	 * @param number $blog_id         Blog id.
	 */
	public static function get_db_tables( $blog_id = null ) {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$tables = array();
		// Multisite. [Not used $wpdb->prefix].
		if ( function_exists( 'is_multisite' ) && is_multisite() && $blog_id ) {
			if ( is_main_site() ) {
				$blog_id = ''; // No Blog id required for main site.
			}
		}
		$blog_prefix = $blog_id ? $blog_id . '_' : '';

		$tables['pricings_table']            = $wpdb->base_prefix . $blog_prefix . 'wt_pricings'; // @since 4.0.0
		$tables['dates_table']               = $wpdb->base_prefix . $blog_prefix . 'wt_dates'; // @since 4.0.0
		$tables['excluded_dates_time_table'] = $wpdb->base_prefix . $blog_prefix . 'wt_excluded_dates_times'; // @since 4.0.0
		$tables['price_category_relation']   = $wpdb->base_prefix . $blog_prefix . 'wt_price_category_relation'; // @since 4.0.0
		return $tables;
	}

	/**
	 * Add Default Pricing Category.
	 */
	public static function add_default_pricing_categories() {
		WP_Travel_Actions_Register_Taxonomies::create_taxonomies();
		$tax        = 'itinerary_pricing_category';
		$term_exits = term_exists( 'adult', $tax );
		if ( 0 === $term_exits || null === $term_exits ) {
			$term = wp_insert_term(
				'Adult',   // the term.
				'itinerary_pricing_category', // the taxonomy.
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
