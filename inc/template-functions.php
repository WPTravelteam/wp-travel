<?php
/**
 * Template Functions.
 *
 * @package WP_Travel
 */

// Hooks.
add_action( 'after_setup_theme', 'wptravel_load_single_itinerary_hooks' );
add_action( 'wp_travel_single_trip_after_booknow', 'wptravel_single_keywords', 1 );
add_action( 'wp_travel_single_trip_meta_list', 'wptravel_single_location', 1 );
/**
 * Hook to display trip rating in trip single page besides price.
 *
 * @since 5.0.6
 */
add_action( 'wp_travel_single_trip_after_price', 'wptravel_single_trip_rating', 10, 2 );
add_filter( 'the_content', 'wptravel_content_filter' );
add_filter( 'wp_travel_trip_tabs_output_raw', 'wptravel_raw_output_on_tab_content', 10, 2 ); // @since 2.0.6. Need true to hide trip detail.
add_action( 'wp_travel_before_single_itinerary', 'wptravel_wrapper_start' );
add_action( 'wp_travel_after_single_itinerary', 'wptravel_wrapper_end' );

add_action( 'comment_post', 'wptravel_add_comment_rating', 10, 3 );
add_filter( 'preprocess_comment', 'wptravel_verify_comment_meta_data' );

// Clear transients.
add_action( 'wp_update_comment_count', 'wptravel_clear_transients' );

add_filter( 'comments_template', 'wptravel_comments_template_loader' );

add_filter( 'template_include', 'wptravel_template_loader' );

add_filter( 'excerpt_length', 'wptravel_excerpt_length', 999 );
add_filter( 'body_class', 'wptravel_body_class', 100, 2 );

add_action( 'wp_travel_before_content_start', 'wptravel_booking_message' );

add_action( 'the_post', 'wptravel_setup_itinerary_data' );

add_action( 'save_post', 'wptravel_clear_booking_transient' );
add_filter( 'excerpt_more', 'wptravel_excerpt_more' );
add_filter( 'wp_kses_allowed_html', 'wptravel_wpkses_post_iframe', 10, 2 );
add_action( 'template_redirect', 'wptravel_prevent_endpoint_indexing' );

add_filter( 'get_header_image_tag', 'wptravel_get_header_image_tag', 10 );
add_filter( 'jetpack_relatedposts_filter_options', 'wptravel_remove_jetpack_related_posts' );

add_action( 'pre_get_posts', 'wptravel_posts_filter', 20 );
add_filter( 'posts_clauses', 'wptravel_posts_clauses_filter', 11, 2 );

add_action( 'wptravel_single_itinerary_main_content', 'wptravel_single_itinerary_trip_content' );

/**
 * Load single itinerary hooks according to layout selection.
 *
 * @since v5.0.0
 */
function wptravel_load_single_itinerary_hooks() {

	$itinerary_v2_enable = wptravel_use_itinerary_v2_layout();

	// Hooks for old itinerary layout.
	if ( ! $itinerary_v2_enable ) {
		add_action( 'wp_travel_single_trip_after_title', 'wptravel_trip_price', 1 );
		add_action( 'wp_travel_single_trip_after_title', 'wptravel_after_trip_price', 1 ); // Just quick fix of review snippet displaying in archive and related trips sections. Moved review hook from callback wptravel_trip_price and added that hook into this callback.
		add_action( 'wp_travel_single_trip_after_title', 'wptravel_single_excerpt', 1 );
		add_action( 'wp_travel_single_trip_after_header', 'wptravel_frontend_trip_facts' );
		add_action( 'wp_travel_single_trip_after_header', 'wptravel_frontend_contents', 15 );
		add_action( 'wp_travel_single_trip_after_header', 'wptravel_trip_map', 20 );
		add_action( 'wp_travel_single_trip_after_header', 'wptravel_related_itineraries', 25 );
		// Filters HTML.
		add_action( 'wp_travel_before_main_content', 'wptravel_archive_toolbar' );
		add_action( 'wp_travel_after_main_content', 'wptravel_archive_wrapper_close' );
		add_action( 'wp_travel_archive_listing_sidebar', 'wptravel_archive_listing_sidebar' );
	} else { // For new layout.
		add_action( 'wp_travel_single_trip_after_header', 'wptravel_single_trip_tabs_and_price' );
		add_action( 'wp_travel_single_trip_after_header', 'wptravel_single_trip_contents', 15 );
		add_action( 'wp_travel_before_main_content', 'wptravel_archive_before_content' );
		add_action( 'wp_travel_after_main_content', 'wptravel_archive_v2_wrapper_close' );
	}
	$settings                       = wptravel_get_settings();
	$hide_plugin_archive_page_title = $settings['hide_plugin_archive_page_title'];
	if ( 'yes' !== $hide_plugin_archive_page_title ) {
		add_action( 'wp_travel_before_main_content', 'wptravel_archive_title', 9 );
	}

}

/**
 * Add Page title and description on the archive page.
 *
 * @since 5.0.6
 */
function wptravel_archive_title() {
	if ( ( WP_Travel::is_page( 'archive' ) ) && ! is_admin() ) :
		?>
		<header class="page-header">
			<?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
			<?php the_archive_description( '<div class="taxonomy-description">', '</div>' ); ?>
		</header>
		<?php
	endif;
}

/**
 * Filters post clause to filter trips after 4.0.0.
 *
 * @param string $post_clauses Post clauses.
 * @param object $object       WP Query object.
 *
 * @since 4.0.4
 */
function wptravel_posts_clauses_filter( $post_clauses, $object ) {

	if ( ! WP_Travel::verify_nonce( true ) ) {
		return $post_clauses;
	}

	global $wpdb;
	if ( WP_TRAVEL_POST_TYPE !== $object->query_vars['post_type'] ) {
		return $post_clauses;
	}

	if ( ! WP_Travel::is_page( 'archive' ) || ( WP_Travel::is_page( 'archive' ) && is_admin() ) || ! wptravel_is_react_version_enabled() ) {
		return $post_clauses;
	}

	// Tables.
	$dates_table          = $wpdb->prefix . 'wt_dates';
	$pricings_table       = $wpdb->prefix . 'wt_pricings';
	$price_category_table = $wpdb->prefix . 'wt_price_category_relation';

	// Join Tables.
	$join  = ''; // JOIN clause.
	$join .= "
		INNER JOIN {$dates_table} ON ( {$wpdb->posts}.ID = {$dates_table}.trip_id )
	";

	/**
	 * ALready checking nonce above using WP_Travel::verify_nonce;
	 */
	// Where clause.
	$where      = '';
	$start_date = isset( $_GET['trip_start'] ) ? sanitize_text_field( wp_unslash( $_GET['trip_start'] ) ) : ''; // @phpcs:ignore
	$end_date   = isset( $_GET['trip_end'] ) ? sanitize_text_field( wp_unslash( $_GET['trip_end'] ) ) : ''; // @phpcs:ignore

		// Filter by date clause.
	if ( ! empty( $start_date ) || ! empty( $end_date ) ) { // For search filter Widgets.
		$where .= ' AND ( '; // <1
		$where .= ' ( '; // <2
		if ( ! empty( $start_date ) ) {
			$where .= " CAST({$dates_table}.start_date AS DATE) >= '{$start_date}'";
			$where .= ! empty( $end_date ) ? " AND CAST({$dates_table}.end_date AS DATE) <= '{$end_date}'" : '';
		} else {
			$where .= ! empty( $end_date ) ? " CAST({$dates_table}.end_date AS DATE) <= '{$end_date}'" : '';
		}
		$where .= ' ) ';
		if ( ! empty( $start_date ) ) {
			$year  = gmdate( 'Y', strtotime( $start_date ) );
			$month = gmdate( 'n', strtotime( $start_date ) );

			$where .= ' OR (';
			if ( ! empty( $start_date ) ) {
				$where .= " CAST({$dates_table}.start_date AS DATE) >= '{$start_date}' AND";
			}
			$where .= "
				{$dates_table}.recurring = 1
				AND (
					( FIND_IN_SET( {$year}, years) || 'every_year' = years )
					 AND
					( FIND_IN_SET( {$month}, months) || 'every_month' = months )
				 )
				";
			$where .= ' ) ';

		}
		$where .= ' ) ';

		$post_clauses['join']     = $post_clauses['join'] . $join;
		$post_clauses['fields']   = $post_clauses['fields'];
		$post_clauses['where']    = $post_clauses['where'] . $where;
		$post_clauses['distinct'] = 'DISTINCT';
	}

	/**
	 * ALready checking nonce above using WP_Travel::verify_nonce;
	 * Do not enter here if search filter widget is in trips/archive page. to prevent adding duplicate join clause.
	 */
	if ( isset( $_GET['trip_date'] ) && in_array( $_GET['trip_date'], array( 'asc', 'desc' ) ) ) { // @phpcs:ignore
		if ( ( empty( $start_date ) && empty( $end_date ) ) ) {
			$post_clauses['join'] = $post_clauses['join'] . $join;
		}
		$post_clauses['orderby'] = 'asc' === sanitize_text_field( wp_unslash( $_GET['trip_date'] ) ) ? "{$dates_table}.start_date ASC" : "{$dates_table}.start_date DESC"; // @phpcs:ignore
	}

	return $post_clauses;
}

function wptravel_get_for_block_template($template_name){

	$template_path = apply_filters( 'wp_travel_template_path', 'wp-travel/' ); // @phpcs:ignore
	$template_path = apply_filters( 'wptravel_template_path', $template_path );
	$default_path  = sprintf( '%s/templates/', plugin_dir_path( dirname( __FILE__ ) ) );

	// Look templates in theme first.
	$template       = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name,
		)
	);
	$layout_version = wptravel_layout_version();
	preg_match( '!\d+!', $layout_version, $version_number );
	// Legacy Templates for themes.
	if ( ! $template ) {
		if ( isset( $version_number[0] ) && 1 !== (int) $version_number[0] ) {
			$version = (int) $version_number[0];
			for ( $i = $version; $i >= 1; $i-- ) {
				$template_ver         = 'v' . $i . '/';
				$replace_with         = 2 >= $i ? '' : 'v' . ( $i - 1 ) . '/';
				$legacy_template_name = str_replace( $template_ver, $replace_with, $template_name );
				$legacy_template      = locate_template(
					array(
						trailingslashit( $template_path ) . $legacy_template_name,
						$legacy_template_name,
					)
				);
				if ( $legacy_template ) {
					add_filter(
						'wptravel_layout_version',
						function( $v ) {
							return 'v1';
							return $v;
						}
					);
					return $legacy_template;
				}
			}
		}
	}
	// End of Legacy Templates for themes.

	if ( ! $template ) { // Load From Plugin if file not found in theme.
		$template = $default_path . $template_name;
	}
	if ( file_exists( $template ) ) {
		return $template;
	}
	return false;

}

/**
 * Return template.
 *
 * @param  String $template_name Path of template.
 * @return Mixed
 */
function wptravel_get_template( $template_name ) {
	if ( count( get_block_templates() ) > 0 ) {
		foreach ( get_block_templates() as $value ) {
			if ( is_single() && $value->slug == 'single-itineraries' ) {
				return;
			}
			if( is_archive() && $value->slug == 'archive-itineraries' ){
				return;
			}
		}
	}
	$template_path = apply_filters( 'wp_travel_template_path', 'wp-travel/' ); // @phpcs:ignore
	$template_path = apply_filters( 'wptravel_template_path', $template_path );
	$default_path  = sprintf( '%s/templates/', plugin_dir_path( dirname( __FILE__ ) ) );

	// Look templates in theme first.
	$template       = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name,
		)
	);
	$layout_version = wptravel_layout_version();
	preg_match( '!\d+!', $layout_version, $version_number );
	// Legacy Templates for themes.
	if ( ! $template ) {
		if ( isset( $version_number[0] ) && 1 !== (int) $version_number[0] ) {
			$version = (int) $version_number[0];
			for ( $i = $version; $i >= 1; $i-- ) {
				$template_ver         = 'v' . $i . '/';
				$replace_with         = 2 >= $i ? '' : 'v' . ( $i - 1 ) . '/';
				$legacy_template_name = str_replace( $template_ver, $replace_with, $template_name );
				$legacy_template      = locate_template(
					array(
						trailingslashit( $template_path ) . $legacy_template_name,
						$legacy_template_name,
					)
				);
				if ( $legacy_template ) {
					add_filter(
						'wptravel_layout_version',
						function( $v ) {
							return 'v1';
							return $v;
						}
					);
					return $legacy_template;
				}
			}
		}
	}
	// End of Legacy Templates for themes.

	if ( ! $template ) { // Load From Plugin if file not found in theme.
		$template = $default_path . $template_name;
	}
	if ( file_exists( $template ) ) {
		return $template;
	}
	return false;
}

/**
 * Like wptravel_get_template, but returns the HTML instead of outputting.
 *
 * @see wptravel_get_template
 * @since 1.3.7
 * @param string $template_name Template name.
 * @param array  $args          Arguments. (default: array).
 *
 * @return string
 */
function wptravel_get_template_html( $template_name, $args = array() ) {
	ob_start();
	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args ); // @codingStandardsIgnoreLine
	}
	include wptravel_get_template( $template_name );
	return ob_get_clean();
}

function wptravel_get_block_template_part( $slug, $name = '' ){
	$template  = '';
	$file_name = ( $name ) ? "{$slug}-{$name}.php" : "{$slug}.php";
	if ( $name ) {
		$template = wptravel_get_for_block_template( $file_name );
	}
	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Get Template Part.
 *
 * @param  String $slug Name of slug.
 * @param  string $name Name of file / template.
 */
function wptravel_get_template_part( $slug, $name = '' ) {
	$template  = '';
	$file_name = ( $name ) ? "{$slug}-{$name}.php" : "{$slug}.php";
	if ( $name ) {
		$template = wptravel_get_template( $file_name );
	}
	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Load Template
 *
 * @param  String $path Path of template.
 * @param  array  $args Template arguments.
 */
function wptravel_load_template( $path, $args = array() ) {
	$template = wptravel_get_template( $path, $args );
	if ( $template ) {
		include $template;
	}
}

/**
 * WP Travel Single Page Content.
 *
 * @param  String $content HTML content.
 * @return String
 */
function wptravel_content_filter( $content ) {

	if ( ! is_singular( WP_TRAVEL_POST_TYPE ) ) {
		return $content;
	}
	global $post;

	$settings = wptravel_get_settings();

	ob_start();
	do_action( 'wp_travel_before_trip_details', $post, $settings ); // @phpcs:ignore
	?>
	<div class="wp-travel-trip-details">
		<?php do_action( 'wp_travel_trip_details', $post, $settings ); // @phpcs:ignore ?>
	</div>
	<?php
	do_action( 'wp_travel_after_trip_details', $post, $settings ); // @phpcs:ignore
	$content .= ob_get_contents();
	ob_end_clean();
	return $content;
}

/**
 * Wrapper Start.
 */
function wptravel_wrapper_start() {
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

	$template = get_option( 'template' );

	switch ( $template ) {
		case 'twentyeleven':
			echo '<div id="primary"><div id="content" role="main" class="twentyeleven">';
			break;
		case 'twentytwelve':
			echo '<div id="primary" class="site-content"><div id="content" role="main" class="twentytwelve">';
			break;
		case 'twentythirteen':
			echo '<div id="primary" class="site-content"><div id="content" role="main" class="entry-content twentythirteen">';
			break;
		case 'twentyfourteen':
			echo '<div id="primary" class="content-area"><div id="content" role="main" class="site-content twentyfourteen"><div class="tfWSC">';
			break;
		case 'twentyfifteen':
			echo '<div id="primary" role="main" class="content-area twentyfifteen"><div id="main" class="site-main t15WSC">';
			break;
		case 'twentysixteen':
			echo '<div id="primary" class="content-area twentysixteen"><main id="main" class="site-main" role="main">';
			break;
		case 'twentyseventeen':
			echo '<div class="wrap"><div id="primary" class="content-area twentyseventeen"><div id="main" class="site-main">';
			break;
		default:
			echo '<div id="wp-travel-content" class="wp-travel-content container clearfix" role="main">';
			break;
	}
}

/**
 * Theme specific wrapper class.
 */
function wptravel_get_theme_wrapper_class() {
	$wrapper_class = '';
	$template      = get_option( 'template' );

	switch ( $template ) {
		case 'twentytwenty':
			$wrapper_class = 'alignwide';
			break;
	}
	return apply_filters( 'wp_travel_theme_wrapper_class', $wrapper_class, $template ); // @phpcs:ignore
}

/**
 * Wrapper Ends.
 */
function wptravel_wrapper_end() {
	$template = get_option( 'template' );

	switch ( $template ) {
		case 'twentyeleven':
			echo '</div></div>';
			break;
		case 'twentytwelve':
			echo '</div></div>';
			break;
		case 'twentythirteen':
			echo '</div></div>';
			break;
		case 'twentyfourteen':
			echo '</div></div></div>';
			get_sidebar( 'content' );
			break;
		case 'twentyfifteen':
			echo '</div></div>';
			break;
		case 'twentysixteen':
			echo '</div></main>';
			break;
		case 'twentyseventeen':
			echo '</div></div></div>';
			break;
		default:
			echo '</div>';
			break;
	}
}

/**
 * Add html of trip price.
 *
 * @param int  $trip_id ID for current trip.
 * @param bool $hide_rating Boolean value to show/hide rating.
 */
function wptravel_trip_price( $trip_id, $hide_rating = false ) {

	$args                             = array( 'trip_id' => $trip_id );
	$args_regular                     = $args;
	$args_regular['is_regular_price'] = true;
	$trip_price                       = WP_Travel_Helpers_Pricings::get_price( $args );
	$regular_price                    = WP_Travel_Helpers_Pricings::get_price( $args_regular );
	$enable_sale                      = WP_Travel_Helpers_Trips::is_sale_enabled(
		array(
			'trip_id'                => $trip_id,
			'from_price_sale_enable' => true,
		)
	);

	$strings = WpTravel_Helpers_Strings::get();

	do_action( 'wp_travel_single_before_trip_price', $trip_id, $hide_rating ); // @phpcs:ignore
	if ( ! $trip_price ) {
		return;
	}
	?>
	<div class="wptravel-price-wrap">
		<!-- <div class="wp-travel-trip-detail"> -->
			<div class="trip-price" >
				<span class="price-from">
					<?php echo esc_html( $strings['from'] ); ?>
				</span>
				<?php if ( $enable_sale && $regular_price !== $trip_price ) : ?>
				<del>
					<span><?php echo wptravel_get_formated_price_currency( $regular_price, true ); // @phpcs:ignore ?></span>
				</del>
				<?php endif; ?>
				<span class="person-count">
					<ins>
						<span><?php echo wptravel_get_formated_price_currency( $trip_price ); // @phpcs:ignore ?></span>
					</ins>
				</span>
			</div>
		<!-- </div> -->
	</div>

	<?php
}

/**
 * Displays content after Price in the trip single page. like ratings.
 *
 * @since 5.0.6
 * @param int  $trip_id ID for current trip.
 * @param bool $hide_rating Boolean value to show/hide rating.
 */
function wptravel_after_trip_price( $trip_id, $hide_rating = false ) {
	wptravel_do_deprecated_action( 'wp_travel_single_after_trip_price', array( $trip_id, $hide_rating ), '2.0.4', 'wp_travel_single_trip_after_price' );
	do_action( 'wp_travel_single_trip_after_price', $trip_id, $hide_rating ); // @phpcs:ignore
}

/**
 * Add html of Rating.
 *
 * @param int  $trip_id ID for current post.
 * @param bool $hide_rating Flag to sho hide rating.
 */
function wptravel_single_trip_rating( $trip_id, $hide_rating = false ) {
	// if ( ! is_singular( WP_TRAVEL_POST_TYPE ) ) {
	// return; // This function also called from archive list. so need to return here. Need to use this in blocks as well.
	// }
	if ( ! $trip_id ) {
		return;
	}
	if ( $hide_rating ) {
		return;
	}
	if ( ! wptravel_tab_show_in_menu( 'reviews' ) ) {
		return;
	}
	$average_rating = wptravel_get_average_rating( $trip_id );

	?>
	<div class="wp-travel-average-review" title="<?php printf( __( 'Rated %s out of 5', 'wp-travel' ), esc_attr( $average_rating ) ); // @phpcs:ignore ?>">
		<a href="#">
			<span style="width:<?php echo esc_attr( ( $average_rating / 5 ) * 100 ); ?>%">
				<strong itemprop="ratingValue" class="rating"><?php echo esc_html( $average_rating ); ?></strong> <?php printf( esc_html__( 'out of %1$s5%2$s', 'wp-travel' ), '<span itemprop="bestRating">', '</span>' ); // @phpcs:ignore ?>
			</span>
		</a>

	</div>
	<?php
}

/**
 * Add html of Rating.
 *
 * @param int $trip_id ID for current post.
 */
function wptravel_trip_rating( $trip_id ) {
	if ( ! $trip_id ) {
		return;
	}
	$average_rating = wptravel_get_average_rating( $trip_id );
	?>
	<div class="wp-travel-average-review" title="<?php printf( __( 'Rated %s out of 5', 'wp-travel' ), esc_attr( $average_rating ) ); // @phpcs:ignore ?>">
		<a>
			<span style="width:<?php echo esc_attr( ( $average_rating / 5 ) * 100 ); ?>%">
				<strong itemprop="ratingValue" class="rating"><?php echo esc_html( $average_rating ); ?></strong> <?php printf( esc_html__( 'out of %1$s5%2$s', 'wp-travel' ), '<span itemprop="bestRating">', '</span>' ); // @phpcs:ignore ?>
			</span>
		</a>

	</div>
	<?php
}

/**
 * Add shortcode for Trip Type, Activities, Group Size and Reviews. Modified in 5.3.2.
 *
 * @param int $text_list array data of current trip.
 *
 * @since 5.3.4.
 */

/**
 * Shortcode create for trip type in single trip page
 */
if ( ! shortcode_exists( 'wptravel_trip_type' ) ) {
	add_shortcode( 'wptravel_trip_type', 'wptravel_trip_type' );
}
function wptravel_trip_type( $attr ) {
	$strings = WpTravel_Helpers_Strings::get();
	// Strings.
	$trip_type_text = isset( $strings['trip_type'] ) ? $strings['trip_type'] : __( 'Trip Type', 'wp-travel' );
	// Empty string
	$empty_trip_type_text = isset( $strings['empty_results']['trip_type'] ) ? $strings['empty_results']['trip_type'] : __( 'No Trip Type', 'wp-travel' );
	// get trip_Id from user
	$trip_id = shortcode_atts( array( 'trip_id' => null ), $attr );

	if ( ! is_null( $trip_id['trip_id'] ) ) {
		$wp_travel_itinerary = new WP_Travel_Itinerary( get_post( $trip_id['trip_id'] ) );
	} else {
		$wp_travel_itinerary = new WP_Travel_Itinerary( get_post( get_the_ID() ) );
	}

	$trip_types_list = $wp_travel_itinerary->get_trip_types_list();
	$trip_type_data  = ( '<div class="travel-info">
								<strong class="title">' . esc_html( $trip_type_text ) . '</strong>
							</div>
							<div class="travel-info">
								<span class="value">' . ( ( $trip_types_list ) ? wp_kses( $trip_types_list, wptravel_allowed_html( array( 'a' ) ) ) : ( esc_html( apply_filters( 'wp_travel_default_no_trip_type_text', $empty_trip_type_text ) ) ) ) .
								'</span>
							</div>' );
	return $trip_type_data;
}

/**
 * Shortcode create for activities in single trip page
 */
if ( ! shortcode_exists( 'wptravel_activities' ) ) {
	add_shortcode( 'wptravel_activities', 'wptravel_activities' );
}
function wptravel_activities( $attr ) {
	$strings = WpTravel_Helpers_Strings::get();
	// Strings.
	$activities_text = isset( $strings['activities'] ) ? $strings['activities'] : __( 'Activities', 'wp-travel' );
	// Empty string
	$empty_activities_text = isset( $strings['empty_results']['activities'] ) ? $strings['empty_results']['activities'] : __( 'No Activities', 'wp-travel' );
	// get trip_Id from user
	$trip_id = shortcode_atts( array( 'trip_id' => null ), $attr );

	if ( ! is_null( $trip_id['trip_id'] ) ) {
		$wp_travel_itinerary = new WP_Travel_Itinerary( get_post( $trip_id['trip_id'] ) );
	} else {
		$wp_travel_itinerary = new WP_Travel_Itinerary( get_post( get_the_ID() ) );
	}

	$activity_list = $wp_travel_itinerary->get_activities_list();
	$activity_data = ( '<div class="travel-info">
								<strong class="title">' . esc_html( $activities_text ) . '</strong>
							</div>
							<div class="travel-info">
								<span class="value">' . ( ( $activity_list ) ? wp_kses( $activity_list, wptravel_allowed_html( array( 'a' ) ) ) : ( esc_html( apply_filters( 'wp_travel_default_no_trip_type_text', $empty_activities_text ) ) ) ) .
								'</span>
							</div>' );
	return $activity_data;
}

/**
 * Shortcode create for group size in single trip page
 */
if ( ! shortcode_exists( 'wptravel_group_size' ) ) {
	add_shortcode( 'wptravel_group_size', 'wptravel_group_size' );
}
function wptravel_group_size( $attr ) {
	$wptravel_enable_group_size_text = apply_filters( 'wptravel_show_group_size_text_single_itinerary', true );
	$strings                         = WpTravel_Helpers_Strings::get();
	// Strings.
	$group_size_text = isset( $strings['group_size'] ) ? $strings['group_size'] : __( 'Group size', 'wp-travel' );
	$pax_text        = isset( $strings['bookings']['pax'] ) ? $strings['bookings']['pax'] : __( 'Pax', 'wp-travel' );
	// Empty string
	$empty_group_size_text = isset( $strings['empty_results']['group_size'] ) ? $strings['empty_results']['group_size'] : __( 'No Size Limit', 'wp-travel' );
	// get trip_Id from user
	$trip_id         = shortcode_atts( array( 'trip_id' => null ), $attr );
	$group_size      = wptravel_get_group_size( $trip_id['trip_id'] );
	$group_size_data = ( ( $wptravel_enable_group_size_text ) ? (
								'<div class="travel-info">
										<strong class="title">' . esc_html( $group_size_text ) . '</strong>
									</div>
									<div class="travel-info">
										<span class="value">' . apply_filters( 'wp_travel_frontend_group_sized_show_min_max_in', ( ( (int) $group_size && $group_size < 999 ) ? ( sprintf( apply_filters( 'wp_travel_template_group_size_text', __( '%1$d %2$s', 'wp-travel' ) ), esc_html( $group_size ), esc_html( ( $pax_text ) ) ) ) : ( esc_html( apply_filters( 'wp_travel_default_no_trip_type_text', $empty_group_size_text ) ) ) ), $trip_id ) .
										'</span>
									</div>' ) : '<div style=" display:none; " ></div>' );
	return $group_size_data;
}

/**
 * Shortcode create for reviews in single trip page
 */
if ( ! shortcode_exists( 'wptravel_reviews' ) ) {
	add_shortcode( 'wptravel_reviews', 'wptravel_reviews' );
}
function wptravel_reviews( $attr ) {

	$strings = WpTravel_Helpers_Strings::get();
	// Strings.
	$reviews_text = isset( $strings['reviews'] ) ? $strings['reviews'] : __( 'Reviews', 'wp-travel' );
	// get trip_Id from user
	$trip_id = shortcode_atts( array( 'trip_id' => null ), $attr );

	if ( ! is_null( $trip_id['trip_id'] ) ) {
		$count = (int) get_comments_number( $trip_id['trip_id'] );
	} else {
		$count = (int) get_comments_number();
	}
	$review_data = ( '<div class="travel-info">
							<strong class="title">' . esc_html( $reviews_text ) . '</strong>
						</div>
						<div class="travel-info">
							<span class="value"> <a href="javascript:void(0)" class="wp-travel-count-info">' . ( sprintf( _n( '%s Review', '%s Reviews', $count, 'wp-travel' ), esc_html( $count ) ) ) .
							'</a></span>
						</div> ' );
	return $review_data;
}

/**
 * Add html for excerpt and booking button. Modified in 2.0.7.
 *
 * @param int $trip_id ID of current post.
 *
 * @since 2.0.0
 */
function wptravel_single_excerpt( $trip_id ) {

	if ( ! $trip_id ) {
		return;
	}
	$strings = WpTravel_Helpers_Strings::get();
	// Get Settings.
	$settings = wptravel_get_settings();
	$enable_one_page = isset( $settings['enable_one_page_booking'] ) &&  ( $settings['enable_one_page_booking'] == true || $settings['enable_one_page_booking'] == 1 ) ? true : false;
	$hook_for_double_enable = apply_filters( 'wp_travel_enable_double_booking_button', true );
	$enquery_global_setting = isset( $settings['enable_trip_enquiry_option'] ) ? $settings['enable_trip_enquiry_option'] : 'yes';

	$global_enquiry_option = get_post_meta( $trip_id, 'wp_travel_use_global_trip_enquiry_option', true );

	if ( '' === $global_enquiry_option ) {
		$global_enquiry_option = 'yes';
	}
	if ( 'yes' === $global_enquiry_option ) {

		$enable_enquiry = $enquery_global_setting;

	} else {
		$enable_enquiry = get_post_meta( $trip_id, 'wp_travel_enable_trip_enquiry_option', true );
	}

	// Strings.
	$trip_type_text  = isset( $strings['trip_type'] ) ? $strings['trip_type'] : __( 'Trip Type', 'wp-travel' );
	$activities_text = isset( $strings['activities'] ) ? $strings['activities'] : __( 'Activities', 'wp-travel' );
	$group_size_text = isset( $strings['group_size'] ) ? $strings['group_size'] : __( 'Group size', 'wp-travel' );
	$pax_text        = isset( $strings['bookings']['pax'] ) ? $strings['bookings']['pax'] : __( 'Pax', 'wp-travel' );
	$reviews_text    = isset( $strings['reviews'] ) ? $strings['reviews'] : __( 'Reviews', 'wp-travel' );

	$empty_trip_type_text  = isset( $strings['empty_results']['trip_type'] ) ? $strings['empty_results']['trip_type'] : __( 'No Trip Type', 'wp-travel' );
	$empty_activities_text = isset( $strings['empty_results']['activities'] ) ? $strings['empty_results']['activities'] : __( 'No Activities', 'wp-travel' );
	$empty_group_size_text = isset( $strings['empty_results']['group_size'] ) ? $strings['empty_results']['group_size'] : __( 'No Size Limit', 'wp-travel' );

	$wp_travel_itinerary = new WP_Travel_Itinerary();

	// Additoinal trip data.
	$pricing_type    = 'multiple-price'; // default.
	$booking_type    = get_post_meta( $trip_id, 'wp_travel_custom_booking_type', true );
	$custom_link     = get_post_meta( $trip_id, 'wp_travel_custom_booking_link', true );
	$open_in_new_tab = get_post_meta( $trip_id, 'wp_travel_custom_booking_link_open_in_new_tab', true );
	if ( class_exists( 'WP_Travel_Utilities_Core' ) ) {
		$pricing_type = get_post_meta( $trip_id, 'wp_travel_pricing_option_type', true );
	}
	?>
	<div class="trip-short-desc">
		<?php the_excerpt(); ?>
	</div>
	<div class="wp-travel-trip-meta-info">
		<ul>
			<?php
				wptravel_do_deprecated_action( 'wp_travel_single_itinerary_before_trip_meta_list', array( $trip_id ), '2.0.4', 'wp_travel_single_trip_meta_list' );  // @since 1.0.4 and deprecated in 2.0.4
				/**
				 * Variable declear for Hooks parameter
				 */
				$trip_types_list                 = $wp_travel_itinerary->get_trip_types_list();
				$activity_list                   = $wp_travel_itinerary->get_activities_list();
				$wptravel_enable_group_size_text = apply_filters( 'wptravel_show_group_size_text_single_itinerary', true );
				$group_size                      = wptravel_get_group_size( $trip_id );
				$count                           = (int) get_comments_number();

				/**
				 * Hooks parameter array varible declear
				 */
				$wptravel_after_excerpt_single_trip_page = array(
					'trip_type'  => apply_filters( 'wp_travel_single_archive_trip_types', '<li>
										<div class="travel-info">
											<strong class="title">' . esc_html( $trip_type_text ) . '</strong>
										</div>
										<div class="travel-info">
											<span class="value">' . ( ( $trip_types_list ) ? wp_kses( $trip_types_list, wptravel_allowed_html( array( 'a' ) ) ) : ( esc_html( apply_filters( 'wp_travel_default_no_trip_type_text', $empty_trip_type_text ) ) ) ) .
											'</span>
										</div>
									</li>', $trip_id ),
					'activity'   => apply_filters( 'wp_travel_single_archive_activities', '<li>
										<div class="travel-info">
											<strong class="title">' . esc_html( $activities_text ) . '</strong>
										</div>
										<div class="travel-info">
											<span class="value">' . ( ( $activity_list ) ? wp_kses( $activity_list, wptravel_allowed_html( array( 'a' ) ) ) : ( esc_html( apply_filters( 'wp_travel_default_no_trip_type_text', $empty_activities_text ) ) ) ) .
											'</span>
										</div>
									</li>', $trip_id ),
					'group_size' => ( ( $wptravel_enable_group_size_text ) ? (
									apply_filters( 'wp_travel_single_archive_group_size', '<li>
										<div class="travel-info">
											<strong class="title">' . esc_html( $group_size_text ) . '</strong>
										</div>
										<div class="travel-info">
											<span class="value">' . apply_filters( 'wp_travel_frontend_group_sized_show_min_max', ( ( (int) $group_size && $group_size < 999 ) ? ( sprintf( apply_filters( 'wp_travel_template_group_size_text', __( '%1$d %2$s', 'wp-travel' ) ), esc_html( $group_size ), esc_html( ( $pax_text ) ) ) ) : ( esc_html( apply_filters( 'wp_travel_default_no_trip_type_text', $empty_group_size_text ) ) ) ), $trip_id ) .
											'</span>
										</div>
									</li>', $trip_id ) ) : '' ),
					'reviews'    => apply_filters( 'wp_travel_single_archive_review', '<li>
									<div class="travel-info">
										<strong class="title">' . esc_html( $reviews_text ) . '</strong>
									</div>
									<div class="travel-info">
										<span class="value"> <a href="javascript:void(0)" class="wp-travel-count-info">' . ( sprintf( _n( '%s Review', '%s Reviews', $count, 'wp-travel' ), esc_html( $count ) ) ) .
										'</a></span>
									</div>
								</li>', $trip_id ),
				);

				$wptravel_after_excerpt_single_trip_page = apply_filters( 'wptravel_after_excerpt_single_trip_page', $wptravel_after_excerpt_single_trip_page, $trip_id );

				foreach ( $wptravel_after_excerpt_single_trip_page as $key => $value ) {
					echo $value;
				}

				wptravel_do_deprecated_action( 'wp_travel_single_itinerary_after_trip_meta_list', array( $trip_id ), '2.0.4', 'wp_travel_single_trip_meta_list' );  // @since 1.0.4 and deprecated in 2.0.4
				do_action( 'wp_travel_single_trip_meta_list', $trip_id ); // @phpcs:ignore
				?>
		</ul>
	</div>

	<div class="booking-form">
		<div class="wp-travel-booking-wrapper">
			<?php
			$trip_enquiry_text = isset( $strings['trip_enquiry'] ) ? $strings['trip_enquiry'] : __( 'Trip Enquiry', 'wp-travel' );
			$book_now_text     = isset( $strings['featured_book_now'] ) ? $strings['featured_book_now'] : __( 'Book Now', 'wp-travel' );
			if ( wp_travel_add_to_cart_system() ) {
				$book_now_text = isset( $strings['set_add_to_cart'] ) ? $strings['set_add_to_cart'] : __( 'Add to Cart', 'wp-travel' );
			}
			if ( 'custom-booking' === $pricing_type && 'custom-link' === $booking_type && $custom_link ) :
				?>
				<a href="<?php echo esc_url( $custom_link ); ?>" target="<?php echo $open_in_new_tab ? esc_attr( 'new' ) : ''; ?>" class="wptravel-book-your-trip"><?php echo esc_html( apply_filters( 'wp_travel_template_book_now_text', $book_now_text ) ); // @phpcs:ignore ?></a>

				<?php
			elseif ( wptravel_tab_show_in_menu( 'booking' ) || $enable_one_page ) :
				if ( $enable_one_page == true && $hook_for_double_enable == true ) {
				?>
				<div id='wp-travel-one-page-checkout-enables'><?php __('Book Now', 'wp-travel' ); ?></div>
				<?php } else { ?>
				<button class="wptravel-book-your-trip wp-travel-booknow-btn"><?php echo esc_html( apply_filters( 'wp_travel_template_book_now_text', $book_now_text ) ); // @phpcs:ignore ?></button>
			<?php } endif; ?>
			<?php if ( 'yes' === $enable_enquiry ) : ?>
				<a id="wp-travel-send-enquiries" class="wp-travel-send-enquiries" data-effect="mfp-move-from-top" href="#wp-travel-enquiries">
					<span class="wp-travel-booking-enquiry">
						<span class="dashicons dashicons-editor-help"></span>
						<span>
							<?php echo esc_attr( apply_filters( 'wp_travel_trip_enquiry_popup_link_text', $trip_enquiry_text ) ); // @phpcs:ignore ?>
						</span>
					</span>
				</a>
				<?php
			endif;
			?>
		</div>
	</div>
		<?php
		if ( 'yes' === $enable_enquiry ) :
			wptravel_get_enquiries_form();
			endif;
		?>
	<?php
	wptravel_do_deprecated_action( 'wp_travel_single_after_booknow', array( $trip_id ), '2.0.4', 'wp_travel_single_trip_after_booknow' );  // @since 1.0.4 and deprecated in 2.0.4
	/**
	 * Content after Top Right Booknow button.
	 *
	 * @since 2.0.4
	 */
	do_action( 'wp_travel_single_trip_after_booknow', $trip_id ); // @phpcs:ignore

}

/**
 * Add html for Keywords.
 *
 * @param int $trip_id ID of current post.
 */
function wptravel_single_keywords( $trip_id ) {
	if ( ! $trip_id ) {
		return;
	}
	$strings         = WpTravel_Helpers_Strings::get();
	$keywords = isset( $strings['single_archive'] ) && isset( $strings['single_archive']['keywords'] ) ? $strings['single_archive']['keywords'] : __( 'Keywordss', 'wp-travel' );
	$trip_code_enable = apply_filters( 'wp_travel_single_archive_trip_code', true, $trip_id );
	$trip_keyword_enable = apply_filters( 'wp_travel_single_archive_trip_keyword', true, $trip_id );
	$terms = get_the_terms( $trip_id, 'travel_keywords' );
	if ( is_array( $terms ) && count( $terms ) > 0 && $trip_keyword_enable == true ) :
		?>
		<div class="wp-travel-keywords">
			<span class="label"><?php echo esc_html( $keywords ); ?></span>
			<?php
			$i = 0;
			foreach ( $terms as $term ) :
				if ( $i > 0 ) :
					?>
					,
					<?php
				endif;
				?>
				<span class="wp-travel-keyword"><a href="<?php echo esc_url( get_term_link( $term->term_id ) ); ?>"><?php echo esc_html( $term->name ); ?></a></span>
				<?php
				$i++;
			endforeach;
			?>
		</div>
		<?php
	endif;
	global $wp_travel_itinerary;
	if ( is_singular( WP_TRAVEL_POST_TYPE ) && $trip_code_enable == true ) :
		$trip_code_label = isset( $strings['trip_code'] ) ? $strings['trip_code'] : __( 'Trip codes', 'wp-travel' );
		?>
		<div class="wp-travel-trip-code"><span><?php echo esc_html( $trip_code_label ); ?> </span><code><?php echo esc_html( $wp_travel_itinerary->get_trip_code() ); ?></code></div>
		<?php
	endif;

}
/**
 * Add html for Keywords.
 *
 * @param int $trip_id ID of current post.
 */
function wptravel_single_location( $trip_id ) {
	if ( ! $trip_id ) {
		return;
	}
	// Get Strings.
	$strings = WpTravel_Helpers_Strings::get();

	$terms = get_the_terms( $trip_id, 'travel_locations' );

	$fixed_departure = WP_Travel_Helpers_Trip_Dates::is_fixed_departure( $trip_id );

	$trip_duration       = get_post_meta( $trip_id, 'wp_travel_trip_duration', true );
	$trip_duration       = ( $trip_duration ) ? $trip_duration : 0;
	$trip_duration_night = get_post_meta( $trip_id, 'wp_travel_trip_duration_night', true );
	$trip_duration_night = ( $trip_duration_night ) ? $trip_duration_night : 0;

	// Strings.
	$locations_text       = isset( $strings['locations'] ) ? $strings['locations'] : __( 'Locations', 'wp-travel' );
	$fixed_departure_text = isset( $strings['fixed_departure'] ) ? $strings['fixed_departure'] : __( 'Fixed departure', 'wp-travel' );
	$trip_duration_text   = isset( $strings['trip_duration'] ) ? $strings['trip_duration'] : __( 'Trip duration', 'wp-travel' );
	$days_text            = isset( $strings['days'] ) ? $strings['days'] : __( 'Day(s)', 'wp-travel' );
	$nights_text          = isset( $strings['nights'] ) ? $strings['nights'] : __( 'Night(s)', 'wp-travel' );
	$trip_locations_enable = apply_filters( 'wp_travel_single_archive_page_trip_location', true, $trip_id );
	$trip_duration_enable = apply_filters( 'wp_travel_single_archive_page_trip_duration', true, $trip_id );

	if ( is_array( $terms ) && count( $terms ) > 0 && $trip_locations_enable == true ) :
		?>
		<li class="no-border">
			<div class="travel-info">
				<strong class="title"><?php echo esc_html( $locations_text ); ?></strong>
			</div>
			<div class="travel-info">
				<span class="value">
					<?php
					$i = 0;
					foreach ( $terms as $term ) :
						if ( $i > 0 ) :
							?>
							,
							<?php
						endif;
						?>
						<span class="wp-travel-locations"><a href="<?php echo esc_url( get_term_link( $term->term_id ) ); ?>"><?php echo esc_html( $term->name ); ?></a><?php do_action( 'wp_travel_single_after_location_data' ); ?></span>
						<?php
						$i++;
					endforeach;
					?>
				</span>
			</div>
		</li>
	<?php endif; ?>
	<?php if ( $trip_duration_enable == true ) {
	if ( $fixed_departure ) :
		$dates = wptravel_get_fixed_departure_date( $trip_id );
		if ( $dates ) {
			?>
			<li class="wp-travel-fixed-departure">
				<div class="travel-info">
					<strong class="title"><?php echo esc_html( $fixed_departure_text ); ?></strong>
				</div>
				<div class="travel-info fixed-date-options">
					<?php echo $dates; // @phpcs:ignore ?>
				</div>

			</li>
			<?php
		}
		?>

	<?php else : ?>
		<?php $new_trip_duration = wp_travel_get_trip_durations( $trip_id ); ?>
		<?php if ( ! empty( $new_trip_duration ) ) : ?>
			<li class="wp-travel-trip-duration">
				<div class="travel-info">
					<strong class="title"><?php echo esc_html( $trip_duration_text ); ?></strong>
				</div>
				<div class="travel-info">
					<span class="value">
						<?php
							printf( '%1$s', esc_html( $new_trip_duration ) ); // @phpcs:ignore
						?>
					</span>
				</div>
			</li>
		<?php endif; ?>
		<?php
	endif;
	}
}

/**
 * Frontend facts content.
 *
 * @param number $trip_id Current Trip id.
 * @since 1.3.2
 */
function wptravel_frontend_trip_facts( $trip_id ) {

	if ( ! $trip_id ) {
		return;
	}
	$settings = wptravel_get_settings();

	if ( empty( $settings['wp_travel_trip_facts_settings'] ) ) {
		return '';
	}

	$wp_travel_trip_facts_enable = isset( $settings['wp_travel_trip_facts_enable'] ) ? $settings['wp_travel_trip_facts_enable'] : 'yes';

	if ( 'no' === $wp_travel_trip_facts_enable ) {
		return;
	}

	$wp_travel_trip_facts = get_post_meta( $trip_id, 'wp_travel_trip_facts', true );

	if ( is_string( $wp_travel_trip_facts ) && $wp_travel_trip_facts ) {
		$wp_travel_trip_facts = json_decode( $wp_travel_trip_facts, true );
	}

	$i = 0;

	$settings_facts = $settings['wp_travel_trip_facts_settings'];
	$new_trip_facts = array();

	// don't display those facts which have been removed from global setting
	if ( is_array( $wp_travel_trip_facts ) && count( $wp_travel_trip_facts ) > 0 ) {
		foreach ( $wp_travel_trip_facts as $f ) {
			$name = strtolower( $f['label'] );
			foreach ( $settings_facts as $s ) {
				$s_name = strtolower( $s['name'] );
				if ( $name == $s_name ) {
					$new_trip_facts[] = $f;
				}
			}
		}
	}

	$wp_travel_trip_facts = $new_trip_facts;
	if ( is_array( $wp_travel_trip_facts ) && count( $wp_travel_trip_facts ) > 0 ) {
		?>
		<!-- TRIP FACTS -->
		<div class="tour-info">
			<div class="tour-info-box clearfix">
				<div class="tour-info-column ">
					<?php
					/**
					 * To fix fact not showing on frontend since v4.0 or greater.
					 *
					 * Modified @since v4.4.1
					 */
					foreach ( $wp_travel_trip_facts as $key => $trip_fact ) :
						?>
						<?php
						$trip_fact_id = $trip_fact['fact_id'];
						if ( isset( $settings_facts[ $trip_fact_id ] ) ) { // To check if current trip facts id matches the settings trip facts id. If matches then get icon and label.

							$icon  = $settings_facts[ $trip_fact_id ]['icon'];
							$label = $settings_facts[ $trip_fact_id ]['name'];

							$icon_args = $settings_facts[ $trip_fact_id ];

						} else { // If fact id doesn't matches or if trip fact doesn't have fact id then matching the trip fact label with fact setting label. ( For e.g Transports ( fact in trip ) === Transports ( Setting fact option ) ).
							$trip_fact_setting = array_filter(
								$settings_facts,
								function( $setting ) use ( $trip_fact ) {

									return $setting['name'] === $trip_fact['label'];
								}
							); // Gives an array for matches label with its other detail as well.

							if ( empty( $trip_fact_setting ) ) { // If there is empty array that means label doesn't matches. Hence skip that and continue.
								continue;
							}
							foreach ( $trip_fact_setting as $set ) {
								$icon      = $set['icon'];
								$label     = $set['name'];
								$icon_args = $set;
							}
						}

						if ( isset( $trip_fact['value'] ) && ! empty( $trip_fact['value'] ) ) :
							?>
							<span class="tour-info-item tour-info-type">
								<?php WpTravel_Helpers_Icon::get( $icon_args ); ?>
								<strong><?php echo esc_html( $label ); ?></strong>:
								<?php
								if ( 'multiple' === $trip_fact['type'] ) {
									$count = count( $trip_fact['value'] );
									$i     = 1;
									foreach ( $trip_fact['value'] as $key => $val ) {
										if ( isset( $trip_fact['fact_id'] ) ) {
											if ( $settings['wp_travel_trip_facts_settings'] && isset( $trip_fact['fact_id'] ) && $settings['wp_travel_trip_facts_settings'][ $trip_fact['fact_id'] ] ) {
												if ( isset( $settings['wp_travel_trip_facts_settings'][ $trip_fact['fact_id'] ]['options'] ) && isset( $settings['wp_travel_trip_facts_settings'][ $trip_fact['fact_id'] ]['options'][ $val ] ) ) {
													echo esc_html( $settings['wp_travel_trip_facts_settings'][ $trip_fact['fact_id'] ]['options'][ $val ] );
												}
											}
										} else {
											echo esc_html( $val );
										}
										if ( $count > 1 && $i !== $count ) {
											esc_html_e( ',', 'wp-travel' );
										}
										$i++;
									}
								} elseif ( isset( $trip_fact['fact_id'] ) && 'single' === $trip_fact['type'] ) {
									if ( isset( $settings['wp_travel_trip_facts_settings'] ) && isset( $settings['wp_travel_trip_facts_settings'][ $trip_fact['fact_id'] ] ) && isset( $settings['wp_travel_trip_facts_settings'][ $trip_fact['fact_id'] ]['options'] ) && $settings['wp_travel_trip_facts_settings'][ $trip_fact['fact_id'] ]['options'][ $trip_fact['value'] ] ) {
										echo esc_html( $settings['wp_travel_trip_facts_settings'][ $trip_fact['fact_id'] ]['options'][ $trip_fact['value'] ] );
									}
								} else {
									echo esc_html( $trip_fact['value'] );
								}
								?>
							</span>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<!-- TRIP FACTS END -->
		<?php
	}
}

/**
 * Single Page Details
 *
 * @param Number $trip_id Current trip id.
 * @return void
 */
function wptravel_frontend_contents( $trip_id ) {
	$wp_travel_itinerary      = new WP_Travel_Itinerary( get_post( $trip_id ) );
	$no_details_found_message = '<p class="wp-travel-no-detail-found-msg">' . __( 'No details found.', 'wp-travel' ) . '</p>';
	$trip_content             = $wp_travel_itinerary->get_content() ? $wp_travel_itinerary->get_content() : $no_details_found_message;
	$trip_outline             = $wp_travel_itinerary->get_outline() ? $wp_travel_itinerary->get_outline() : $no_details_found_message;
	$trip_include             = $wp_travel_itinerary->get_trip_include() ? $wp_travel_itinerary->get_trip_include() : $no_details_found_message;
	$trip_exclude             = $wp_travel_itinerary->get_trip_exclude() ? $wp_travel_itinerary->get_trip_exclude() : $no_details_found_message;
	$gallery_ids              = $wp_travel_itinerary->get_gallery_ids();

	$wp_travel_itinerary_tabs = wptravel_get_frontend_tabs();

	$fixed_departure = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );

	$trip_start_date = get_post_meta( $trip_id, 'wp_travel_start_date', true );
	$trip_end_date   = get_post_meta( $trip_id, 'wp_travel_end_date', true );
	$enable_sale     = WP_Travel_Helpers_Trips::is_sale_enabled( array( 'trip_id' => $trip_id ) );

	$trip_duration       = get_post_meta( $trip_id, 'wp_travel_trip_duration', true );
	$trip_duration       = ( $trip_duration ) ? $trip_duration : 0;
	$trip_duration_night = get_post_meta( $trip_id, 'wp_travel_trip_duration_night', true );
	$trip_duration_night = ( $trip_duration_night ) ? $trip_duration_night : 0;

	$settings      = wptravel_get_settings();
	$currency_code = ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';

	$currency_symbol = wptravel_get_currency_symbol( $currency_code );
	$price_per_text  = wptravel_get_price_per_text( $trip_id );

	$wrapper_class = wptravel_get_theme_wrapper_class();
	?>
	<div id="wp-travel-tab-wrapper" class="wp-travel-tab-wrapper <?php echo esc_attr( $wrapper_class ); ?>">
		<?php if ( is_array( $wp_travel_itinerary_tabs ) && count( $wp_travel_itinerary_tabs ) > 0 ) : ?>
			<ul class="wp-travel tab-list resp-tabs-list ">
				<?php
				$index = 1;
				foreach ( $wp_travel_itinerary_tabs as $tab_key => $tab_info ) :
					$tab_info['show_in_menu'] = $tab_info['show_in_menu'] === 'yes' ? 'yes' : ( $tab_info['show_in_menu'] === 'no' || empty( $tab_info['show_in_menu'] ) ? 'no' : 'yes' );
					if ( 'reviews' === $tab_key && ! comments_open() ) :
						continue;
					endif;
					if ( 'yes' !== $tab_info['show_in_menu'] ) :
						continue;
					endif;
					$tab_label = $tab_info['label'];
					?>
					<li class="wp-travel-ert <?php echo esc_attr( $tab_key ); ?> <?php echo esc_attr( $tab_info['label_class'] ); ?> tab-<?php echo esc_attr( $index ); ?>" data-tab="tab-<?php echo esc_attr( $index ); ?>-cont"><?php echo esc_attr( $tab_label ); ?></li>
					<?php
					$index++;
				endforeach;
				?>
			</ul>
			<div class="resp-tabs-container">
				<?php
				if ( is_array( $wp_travel_itinerary_tabs ) && count( $wp_travel_itinerary_tabs ) > 0 ) :
					$index = 1;
					foreach ( $wp_travel_itinerary_tabs as $tab_key => $tab_info ) :
						$tab_info['show_in_menu'] = $tab_info['show_in_menu'] === 'yes' ? 'yes' : ( $tab_info['show_in_menu'] === 'no' || empty( $tab_info['show_in_menu'] ) ? 'no' : 'yes' );
						if ( 'reviews' === $tab_key && ! comments_open() ) :
							continue;
						endif;
						if ( 'yes' !== $tab_info['show_in_menu'] ) :
							continue;
						endif;

						switch ( $tab_key ) {

							case 'reviews':
								?>
								<div id="<?php echo esc_attr( $tab_key ); ?>" class="tab-list-content">
									<?php comments_template(); ?>
								</div>
								<?php
								break;
							case 'booking':
								$booking_template = wptravel_get_template( 'content-pricing-options.php' );
								load_template( $booking_template );

								break;
							case 'faq':
								?>
								<div id="<?php echo esc_attr( $tab_key ); ?>" class="tab-list-content et_smooth_scroll_disabled"> <!-- class et_smooth_scroll_disabled to fix faq accordion issue with divi theme. -->
									<?php
									// $template = wptravel_get_template( 'content-faqs.php' );
									// load_template( $template );
									wptravel_get_template_part( 'content', 'faqs' );
									?>
								</div>
								<?php
								break;
							case 'trip_outline':
								?>
								<div id="<?php echo esc_attr( $tab_key ); ?>" class="tab-list-content">
									<?php
										echo do_shortcode( $tab_info['content'] );
										wptravel_get_template_part( 'itineraries', 'list' );
										// $itinerary_list_template = wptravel_get_template( 'itineraries-list.php' );
										// load_template( $itinerary_list_template );
									?>
								</div>
								<?php
								break;
							default:
								?>
								<div id="<?php echo esc_attr( $tab_key ); ?>" class="tab-list-content">
									<?php echo do_shortcode( $tab_info['content'] ); // @phpcs:ignore ?>
								</div>
								<?php
								break;
						}
						$index++;
					endforeach;
				endif;
				?>
			</div>
		<?php endif; ?>

	</div>
	<?php
}

/**
 * Load Maps as per selected map in settings.
 *
 * @param Number $trip_id Current trip id.
 * @since 1.0.0
 * @since 5.0.2 Load all maps instead of google map only.
 */
function wptravel_trip_map( $trip_id ) {
	$wp_travel_itinerary = new WP_Travel_Itinerary();
	if ( ! $wp_travel_itinerary->get_location() ) {
		return;
	}
	$get_maps    = wptravel_get_maps(); // Get map data.
	$current_map = $get_maps['selected'];
	/**
	 * Load Map as per selected current map in the settings.
	 *
	 * @since 5.0.2
	 */
	do_action( 'wptravel_trip_map_' . $current_map, $trip_id, $get_maps );
}

/**
 * Google Map for frontend.
 *
 * @param number $trip_id Trip id.
 * @param array  $data    Map Related data.
 *
 * @since 5.0.2
 */
function wptravel_frontend_google_map( $trip_id, $data ) {
	$current_map = $data['selected'];

	$show_google_map = ( 'google-map' === $current_map ) ? true : false;
	$show_google_map = apply_filters( 'wp_travel_load_google_maps_api', $show_google_map ); // @phpcs:ignore

	if ( ! $show_google_map ) {
		return;
	}
	$settings = wptravel_get_settings();
	$api_key  = '';
	if ( isset( $settings['google_map_api_key'] ) && $settings['google_map_api_key'] ) {
		$api_key = $settings['google_map_api_key'];
	}

	$map_data = wptravel_get_map_data();
	$lat      = isset( $map_data['lat'] ) ? $map_data['lat'] : '';
	$lng      = isset( $map_data['lng'] ) ? $map_data['lng'] : '';

	$wrapper_class = wptravel_get_theme_wrapper_class();
	$id            = uniqid();
	$map_id        = sprintf( 'wp-travel-map-%s', $id );
	if ( $api_key && $show_google_map && ! empty( $lat ) && ! empty( $lng ) ) {
		?>
		<div class="wp-travel-map <?php echo esc_attr( $wrapper_class ); ?>">
			<div class="<?php echo esc_attr( $map_id ); ?>" id="<?php echo esc_attr( $map_id ); ?>" style="width:100%;height:300px"></div>
		</div>
		<script>
			jQuery(document).ready(function($) {
				// var options = {
				// 	lat : '27.693171845837',
				// 	lng : '85.281285846253',
				// }
				$( '#<?php echo esc_attr( $map_id ); ?>' ).wptravelGoogleMap();
			});
		</script>
		<?php
	} else {
		$use_lat_lng = get_post_meta( $trip_id, 'wp_travel_trip_map_use_lat_lng', true );
		if ( 'yes' === $use_lat_lng ) {
			$q = "{$lat},{$lng}";
		} else {
			$q = $map_data['loc'];
		}
		if ( ! empty( $q ) ) :
			?>
			<div class="wp-travel-map  <?php echo esc_attr( $wrapper_class ); ?>">
				<iframe
					style="width:100%;height:300px"
					src="https://maps.google.com/maps?q=<?php echo esc_attr( $q ); ?>&t=m&z=<?php echo esc_attr( $settings['google_map_zoom_level'] ); ?>&output=embed&iwloc=near"></iframe>
			</div>
			<?php
		endif;
	}
}
add_action( 'wptravel_trip_map_google-map', 'wptravel_frontend_google_map', 10, 2 );

/**
 * Display Related Product.
 *
 * @param Number $trip_id Post ID.
 * @return HTML
 */
function wptravel_related_itineraries( $trip_id ) {
	if ( ! $trip_id ) {
		return;
	}
	wptravel_get_related_post( $trip_id );
}

/**
 * Add Comment rating data.
 *
 * @param number  $comment_id Comment id of current trip.
 * @param Boolean $approve Approve or not.
 * @param array   $comment_data Required comment datas.
 *
 * @since 1.0.0
 */
function wptravel_add_comment_rating( $comment_id, $approve, $comment_data ) {
	if ( isset( $_POST['wp_travel_rate_val'] ) && WP_TRAVEL_POST_TYPE === get_post_type( $comment_data['comment_post_ID'] ) ) { // @phpcs:ignore
		if ( absint( $_POST['wp_travel_rate_val'] ) > 5 || absint( $_POST['wp_travel_rate_val'] ) < 0 ) { // @phpcs:ignore
			return;
		}
		add_comment_meta( $comment_id, '_wp_travel_rating', absint( $_POST['wp_travel_rate_val'] ), true ); // @phpcs:ignore
	}
}

function wptravel_clear_transients( $trip_id ) {
	delete_post_meta( $trip_id, '_wpt_average_rating' );
	delete_post_meta( $trip_id, '_wpt_rating_count' );
	delete_post_meta( $trip_id, '_wpt_review_count' );
}

function wptravel_verify_comment_meta_data( $commentdata ) {
	if (
	! is_admin()
	&& WP_TRAVEL_POST_TYPE === get_post_type( sanitize_text_field( $commentdata['comment_post_ID'] ) )
	&& 1 > sanitize_text_field( $_POST['wp_travel_rate_val'] ) // @phpcs:ignore
	&& '' === $commentdata['comment_type']
	) {
		wp_die( 'Please rate. <br><a href="javascript:history.go(-1);">Back </a>' );
		exit;
	}
	return $commentdata;
}

/**
 * Get the total amount (COUNT) of reviews.
 *
 * @param   Number $trip_id Post ID.
 * @since 1.0.0 / Modified 1.6.7
 * @return int The total number of trips reviews
 */
function wptravel_get_review_count( $trip_id = null ) {
	global $wpdb, $post;

	if ( ! $trip_id ) {
		$trip_id = $post->ID;
	}
	// No meta data? Do the calculation.
	if ( ! metadata_exists( 'post', $trip_id, '_wpt_review_count' ) ) {
		$count = $wpdb->get_var(
			$wpdb->prepare(
				"
			SELECT COUNT(*) FROM $wpdb->comments
			WHERE comment_parent = 0
			AND comment_post_ID = %d
			AND comment_approved = '1'
		",
				$trip_id
			)
		);

		update_post_meta( $trip_id, '_wpt_review_count', $count );
	} else {
		$count = get_post_meta( $trip_id, '_wpt_review_count', true );
	}

	$count = apply_filters( 'wp_travel_review_count', $count, $post ); // @phpcs:ignore

	return $count ? $count : 0;
}

/**
 * Get the average rating of product. This is calculated once and stored in postmeta.
 *
 * @param Number $trip_id   Post ID.
 *
 * @return string
 */
function wptravel_get_average_rating( $trip_id = null ) {
	global $wpdb, $post;

	if ( ! $trip_id ) {
		$trip_id = $post->ID;
	}
	$count = (int) get_comments_number( $trip_id );

	// @since 6.2.0
	$settings = wptravel_get_settings();

	if ( $settings['disable_admin_review'] == 'yes' ) {
		$get_reviews = get_comments( array( 'post_id' => $trip_id ) );

		$admin_count = 0;
		foreach ( $get_reviews as $review ) {

			if ( get_user_by( 'login', $review->comment_author ) ) {
				if ( in_array( get_user_by( 'login', $review->comment_author )->roles[0], array( 'administrator', 'editor', 'author' ) ) ) {
					$admin_count = $admin_count + 1;
				}
			}
		}
		$count = $count - $admin_count;
	}

	$average_rating_query = "SELECT SUM(meta_value) FROM $wpdb->commentmeta
	LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
	WHERE meta_key = '_wp_travel_rating'
	AND comment_post_ID = %d
	AND comment_approved = '1'
	AND meta_value > 0";

	// // No meta data? Do the calculation.
	// if ( ! metadata_exists( 'post', $trip_id, '_wpt_average_rating' ) ) {
	if ( $count ) {
		$ratings = $wpdb->get_var(
			$wpdb->prepare( $average_rating_query, $trip_id ) // @phpcs:ignore
		);
		$average = number_format( $ratings / $count, 2, '.', '' );
	} else {
		$average = 0;
	}
		update_post_meta( $trip_id, '_wpt_average_rating', $average );
	// } else {

	// $average = get_post_meta( $trip_id, '_wpt_average_rating', true );

	// if ( ! $average && $count > 0 ) { // re update average meta if there is number of reviews but no average ratings value.
	// if ( $count ) {
	// $ratings = $wpdb->get_var(
	// $wpdb->prepare( $average_rating_query, $trip_id ) // @phpcs:ignore
	// );
	// $average = number_format( $ratings / $count, 2, '.', '' );
	// } else {
	// $average = 0;
	// }
	// update_post_meta( $trip_id, '_wpt_average_rating', $average );
	// }

	// }
	return (string) floatval( $average );
}
/**
 * Get the total amount (COUNT) of ratings.
 *
 * @param  int $value Optional. Rating value to get the count for. By default returns the count of all rating values.
 * @todo Need to change custom query in this function to get rating count. use get_comments_number instead if possible.
 * @return int
 */
function wptravel_get_rating_count( $value = null ) {
	global $wpdb, $post;

	// No meta data? Do the calculation.
	if ( ! metadata_exists( 'post', $post->ID, '_wpt_rating_count' ) ) {
		$counts     = array();
		$raw_counts = $wpdb->get_results(
			$wpdb->prepare(
				"
			SELECT meta_value, COUNT( * ) as meta_value_count FROM $wpdb->commentmeta
			LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
			WHERE meta_key = '_wp_travel_rating'
			AND comment_post_ID = %d
			AND comment_approved = '1'
			AND meta_value > 0
			GROUP BY meta_value
		",
				$post->ID
			)
		);

		foreach ( $raw_counts as $count ) {
			$counts[ $count->meta_value ] = $count->meta_value_count;
		}
		update_post_meta( $post->ID, '_wpt_rating_count', $counts );
	} else {

		$counts = get_post_meta( $post->ID, '_wpt_rating_count', true );
	}
	if ( ! $value ) {
		return array_sum( $counts );
	} else {
		return isset( $counts[ $value ] ) ? $counts[ $value ] : 0;
	}
}


/**
 * Comment Template Loader callback function.
 *
 * @param String $template Template Full Path.
 */
function wptravel_comments_template_loader( $template ) {
	if ( WP_TRAVEL_POST_TYPE !== get_post_type() ) {
		return $template;
	}

	$single_review_template = wptravel_get_template( 'single-wp-travel-reviews.php' );
	if ( $single_review_template ) {
		return $single_review_template;
	}
	return $template;
}

/**
 * Load WP Travel Template file
 *
 * @param [type] $template Name of template.
 * @return String
 */
function wptravel_template_loader( $template ) {
	$layout_version = wptravel_layout_version();
	// Load template for post archive / taxonomy archive.
	$wptravel_tax_list = array( 'itinerary_types', 'travel_locations', 'travel_keywords', 'activity' );
	if( class_exists( 'WP_Travel_Pro' ) ){
		foreach( array_keys( get_option( 'wp_travel_custom_filters_option', array() ) ) as $data ){
			array_push( $wptravel_tax_list, $data );
		}
	}
	if ( is_post_type_archive( WP_TRAVEL_POST_TYPE ) || is_tax( $wptravel_tax_list ) ) {
		$archive_template = wptravel_get_template( $layout_version . '/archive-itineraries.php' ); // Load version specific template if version greater than v1.
		if ( 'v1' === $layout_version ) { // Legacy Template.
			$archive_template = wptravel_get_template( 'archive-itineraries.php' );
		}
		if ( $archive_template ) {
			return $archive_template;
		}
	}

	// Load Template for Single Trip.
	$post_types = array( WP_TRAVEL_POST_TYPE );
	if ( is_singular( $post_types ) ) {
		$single_template = wptravel_get_template( 'single-itineraries.php' );
		if ( $single_template ) {
			return $single_template;
		}
	}
	return $template;
}

/**
 * Return excerpt length for archive pages.
 *
 * @param  int $length word length of excerpt.
 * @return int return word length
 */
function wptravel_excerpt_length( $length ) {
	if ( get_post_type() !== WP_TRAVEL_POST_TYPE ) {
		return $length;
	}

	return 23;
}

/**
 * Pagination for archive pages
 *
 * @param  Int    $range range.
 * @param  String $pages Number of pages.
 *
 * @since 1.0.0
 * @since 5.3.1 added Query and hashlink param.
 * @return HTML
 */
function wptravel_pagination( $range = 2, $pages = '', $the_query = null, $hashlink = '' ) {
	$pagination_allowed = array( WP_TRAVEL_POST_TYPE, 'itinerary-booking', 'wp-travel-payment' );
	if ( in_array( get_post_type(), $pagination_allowed, true ) ) {
		$showitems = ( $range * 2 ) + 1;

		global $paged;
		if ( empty( $paged ) ) {
			$paged = 1;
		}

		if ( '' == $pages ) {
			if ( $the_query ) {
				$pages = $the_query->max_num_pages;
			} else {
				global $wp_query;
				$pages = $wp_query->max_num_pages;
				if ( ! $pages ) {
					$pages = 1;
				}
			}
		}
		$pagination = '';
		if ( 1 != $pages ) {
			$pagination .= '<nav class="wp-travel-navigation navigation wp-paging-navigation">';
			$pagination .= '<ul class="wp-page-numbers">';
			if ( $paged > 1 && $showitems < $pages ) {
				$pagination .= sprintf( '<li><a class="prev wp-page-numbers" href="%s">&laquo; </a></li>', get_pagenum_link( $paged - 1 ) . $hashlink );
			}

			for ( $i = 1; $i <= $pages; $i++ ) {
				if ( 1 != $pages && ( ! ( $i >= $paged + $range + 1 || $i <= $paged - $range - 1 ) || $pages <= $showitems ) ) {
					if ( $paged == $i ) {

						$pagination .= sprintf( '<li><a class="wp-page-numbers current" href="javascript:void(0)">%d</a></li>', $i );
					} else {
						$pagination .= sprintf( '<li><a class="wp-page-numbers" href="%s">%d</a></li>', get_pagenum_link( $i ) . $hashlink, $i );
					}
				}
			}

			if ( $paged < $pages && $showitems < $pages ) {
				$pagination .= sprintf( '<li><a class="next wp-page-numbers" href="%s">&raquo; </a></li>', get_pagenum_link( $paged + 1 ) . $hashlink );
			}

			$pagination .= "</nav>\n";
			echo $pagination; // @phpcs:ignore
		}
	}

}

/**
 * Offer HTML
 *
 * @param  int $trip_id ID of current Trip Post.
 * @return HTML
 */
function wptravel_save_offer( $trip_id ) {
	if ( get_post_type() !== WP_TRAVEL_POST_TYPE ) {
		return;
	}

	if ( ! $trip_id ) {
		return;
	}
	$strings     = WpTravel_Helpers_Strings::get();
	$save_label  = $strings['save'];
	$off_label   = $strings['off'];
	$enable_sale = WP_Travel_Helpers_Trips::is_sale_enabled( array( 'trip_id' => $trip_id ) );

	if ( ! $enable_sale ) {
		return;
	}

	$args                             = $args_regular = array( 'trip_id' => $trip_id );
	$args_regular['is_regular_price'] = true;
	$trip_price                       = WP_Travel_Helpers_Pricings::get_price( $args );
	$regular_price                    = WP_Travel_Helpers_Pricings::get_price( $args_regular );

	$layout_version = wptravel_layout_version();

	if ( $regular_price > $trip_price ) {
		$save = ( 1 - ( $trip_price / $regular_price ) ) * 100;
		$save = number_format( $save, 2, '.', ',' );
		if ( 'v2' === $layout_version ) {
			?>
			<span class="discount">
				<?php printf( '<span>%s&#37;</span> %s', $save, $off_label ); ?>
			</span>
			<?php
		} else {
			?>
			<div class="wp-travel-savings"><?php printf( '%s <span>%s&#37;</span>', $save_label, $save ); ?></div>
			<?php
		}
	}
}

/**
 * Filter Body Class.
 *
 * @param  array  $classes [description].
 * @param  String $class   [description].
 * @return array
 */
function wptravel_body_class( $classes, $class ) {

	if ( is_active_sidebar( 'sidebar-1' ) && is_singular( WP_TRAVEL_POST_TYPE ) ) {
		// If the has-sidebar class is in the $classes array, do some stuff.
		if ( in_array( 'has-sidebar', $classes ) ) {
			// Remove the class.
			unset( $classes[ array_search( 'has-sidebar', $classes ) ] );
		}
	}
	$layout_version = wptravel_layout_version();
	$classes[]      = 'wptravel-layout-' . $layout_version;
	// Give me my new, modified $classes.
	return $classes;
}

/**
 * Booking Booked Message.
 *
 * @return String
 */
function wptravel_booking_message() {
	if ( ! is_singular( WP_TRAVEL_POST_TYPE ) ) {
		return;
	}

	if ( ! WP_Travel::verify_nonce( true ) ) {
		return;
	}

	$submission_get = wptravel_sanitize_array( wp_unslash( $_GET ) );
	$booking_id     = ! empty( $submission_get['order_id'] ) ? $submission_get['order_id'] : '';
	$booking_option = get_post_meta( $booking_id, 'wp_travel_booking_option', true );

	if ( isset( $submission_get['booked'] ) && 1 == wptravel_sanitize_array( wp_unslash( $submission_get['booked'] ) ) ) :
		?>
		
		<script>
			history.replaceState({},null,window.location.pathname);
		</script>
		<?php if ( 'booking_only' == $booking_option ) { ?>
			<p class="col-xs-12 wp-travel-notice-success wp-travel-notice"><?php echo esc_html( apply_filters( 'wp_travel_booked_message', __( "Thank you for booking! We'll reach out to you soon.", 'wp-travel' ) ) ); ?><span><?php echo esc_html( apply_filters( 'wp_travel_booked_message_after_text', ' (' . __( 'Booking Option : Booking Only', 'wp-travel' ) . ').' ) ); ?></span></p>
			<?php
		} elseif ( 'booking_with_payment' == $booking_option ) {
			$payment_gatway = get_post_meta( $booking_id, 'wp_travel_payment_gateway', true );
			if ( 'bank_deposit' == $payment_gatway ) {
				$payment_status = get_post_meta( $booking_id, 'wp_travel_payment_status', true );
				// print_r($payment_status);
				if ( 'waiting_voucher' == $payment_status ) {
					?>
						<p class="col-xs-12 wp-travel-notice-success wp-travel-notice"><?php echo esc_html( apply_filters( 'wp_travel_booked_message', __( "Thank you for booking! We'll reach out to you soon.", 'wp-travel' ) ) ); ?><span><?php echo apply_filters( 'wp_travel_booked_message_after_text', '  (' . __( 'Payment Method: Bank Deposit, and Payment Status: Waiting for Voucher. Please', 'wp-travel' ) . '<a href="https://wptravel.io/docs/wp-travel-user-documentation/settings/payment/#h-bank-deposit" target="_blank" >' . __( 'submit', 'wp-travel' ) . '</a>' . __( 'your voucher', 'wp-travel' ) . '.)' ); ?></span> </p>
					<?php
				} else {
					?>
						<p class="col-xs-12 wp-travel-notice-success wp-travel-notice"><?php echo esc_html( apply_filters( 'wp_travel_booked_message', __( "Thank you for booking! We'll reach out to you soon.", 'wp-travel' ) ) ); ?><span><?php echo apply_filters( 'wp_travel_booked_message_after_text', '  (' . __( 'Payment Method: Bank Deposit, and Payment Status: Waiting for Voucher. Please', 'wp-travel' ) . '<a href="https://wptravel.io/docs/wp-travel-user-documentation/settings/payment/#h-bank-deposit" target="_blank" >' . __( 'submit', 'wp-travel' ) . '</a>' . __( 'your voucher', 'wp-travel' ) . '.)' ); ?></span> </p>
					<?php
				}
			} elseif ( 'paypal' == $payment_gatway ) {
				$payment_status = get_post_meta( $booking_id, 'wp_travel_payment_status', true );
				if ( 'paid' == $payment_status ) {
					?>
						<p class="col-xs-12 wp-travel-notice-success wp-travel-notice"><?php echo esc_html( apply_filters( 'wp_travel_booked_message', __( "Thank you for booking! We'll reach out to you soon.", 'wp-travel' ) ) ); ?><span><?php echo esc_html( apply_filters( 'wp_travel_booked_message_after_text', ' (' . __( 'Booking Option : Booking with Payment, Payment Methode : PayPal, and Payment Status : Paid', 'wp-travel' ) . '.)' ) ); ?></span></p>
					<?php
				} else {
					?>
						<p class="col-xs-12 wp-travel-notice-success wp-travel-notice"><?php echo esc_html( apply_filters( 'wp_travel_booked_message', __( "Thank you for booking! We'll reach out to you soon.", 'wp-travel' ) ) ); ?><span><?php echo esc_html( apply_filters( 'wp_travel_booked_message_after_text', ' (' . __( 'Booking Option : Booking with Payment, Payment Methode : PayPal', 'wp-travel' ) . '.)' ) ); ?></span></p>
					<?php
				}
			}
		} else {
			?>
				<p class="col-xs-12 wp-travel-notice-success wp-travel-notice"><?php echo esc_html( apply_filters( 'wp_travel_booked_message', __( "Thank you for booking! We'll reach out to you soon.", 'wp-travel' ) ) ); ?></p>
			<?php
		}
		?>
	<?php elseif ( isset( $submission_get['booked'] ) && 'false' == $submission_get['booked'] ) : ?>
		<script>
			history.replaceState({},null,window.location.pathname);
		</script>

		<?php

			$err_msg = __( 'Your Item has been added but the email could not be sent.', 'wp-travel' ) . "<br />\n" . __( 'Possible reason: your host may have disabled the mail() function.', 'wp-travel' )

		?>

		<p class="col-xs-12 wp-travel-notice-danger wp-travel-notice"><?php echo wp_kses_post( apply_filters( 'wp_travel_booked_message', $err_msg ) ); ?></p>
		<?php
	endif;

	wptravel_print_notices();
}

/**
 * Return No of Pax for current Trip.
 *
 * @param  int $trip_id ID of current trip post.
 * @return String.
 */
function wptravel_get_group_size( $trip_id = null ) {
	if ( class_exists( 'WpTravel_Helpers_Strings' ) ) {
		$strings = WpTravel_Helpers_Strings::get();
	}
	if ( ! is_null( $trip_id ) ) {
		$wp_travel_itinerary = new WP_Travel_Itinerary( get_post( $trip_id ) );
	} else {
		global $post;
		$wp_travel_itinerary = new WP_Travel_Itinerary( $post );
	}

	$group_size = $wp_travel_itinerary->get_group_size();


	if ( $group_size ) {
		return sprintf( apply_filters( 'wp_travel_template_group_size_text', __( '%d ' . ( $strings['bookings']['pax'] ? $strings['bookings']['pax'] : 'Pax' ), 'wp-travel' ) ), $group_size );
	}

	return apply_filters( 'wp_travel_default_group_size_text', esc_html__( 'No Size Limit', 'wp-travel' ) );
}


/**
 * When the_post is called, put product data into a global.
 *
 * @param mixed $post Post object or post id.
 * @return WP_Travel_Itinerary
 */
function wptravel_setup_itinerary_data( $post ) {
	unset( $GLOBALS['wp_travel_itinerary'] );

	if ( is_int( $post ) ) {
		$post = get_post( $post );
	}
	if ( empty( $post->post_type ) || WP_TRAVEL_POST_TYPE !== $post->post_type ) {
		return;
	}
	$GLOBALS['wp_travel_itinerary'] = new WP_Travel_Itinerary( $post );

	return $GLOBALS['wp_travel_itinerary'];
}

/**
 * WP Travel Filter By.
 *
 * @return void
 */
function wptravel_archive_filter_by( $submission_get = array() ) {
	// [NOTE: Removed archive condition to display when its used]
	// if ( ! WP_Travel::is_page( 'archive' ) ) {
	// return;
	// }

	$strings = WpTravel_Helpers_Strings::get();

	$filter_by_text = $strings['filter_by'];
	$price_text     = $strings['price'];
	$trip_type_text = $strings['trip_type'];
	$location_text  = $strings['location'];
	$show_text      = $strings['show'];
	$trip_date_text = $strings['trip_date'];
	$trip_name_text = $strings['trip_name'];

	?>
	<div class="wp-travel-post-filter clearfix">
		<div class="wp-travel-filter-by-heading">
			<h4><?php echo esc_html( $filter_by_text ); ?></h4>
			<button class="btn btn-wptravel-filter-by"><?php echo esc_html( $filter_by_text ); ?><i class="fas fa-chevron-down"></i></button>
		</div>

		<?php do_action( 'wp_travel_before_post_filter' ); ?>
		<input type="hidden" id="wp-travel-archive-url" value="<?php echo esc_url( get_post_type_archive_link( WP_TRAVEL_POST_TYPE ) ); ?>" />
		<?php
			$price     = ( isset( $submission_get['price'] ) ) ? $submission_get['price'] : '';
			$type      = ! empty( $submission_get['itinerary_types'] ) ? $submission_get['itinerary_types'] : '';
			$location  = ! empty( $submission_get['travel_locations'] ) ? $submission_get['travel_locations'] : '';
			$trip_date = ! empty( $submission_get['trip_date'] ) ? $submission_get['trip_date'] : '';
			$trip_name = ! empty( $submission_get['trip_name'] ) ? $submission_get['trip_name'] : '';

		if ( is_tax( array( 'itinerary_types', 'travel_locations', 'travel_keywords', 'activity' ) ) ) {

			$wt_taxonomy = get_query_var( 'taxonomy' );
			$wt_term     = get_query_var( 'term' );

			switch ( $wt_taxonomy ) {
				case 'travel_locations':
					$location = $wt_term;
					break;
				case 'itinerary_types':
					$type = $wt_term;
					break;
				default:
					break;
			}
		}
		?>

		<?php $enable_filter_price = apply_filters( 'wp_travel_post_filter_by_price', true ); ?>
		<?php if ( $enable_filter_price ) : ?>
			<div class="wp-toolbar-filter-field wt-filter-by-price">
				<select name="price" class="wp_travel_input_filters price">
					<option value=""><?php echo esc_html( $price_text ); ?></option>
					<option value="low_high" <?php selected( $price, 'low_high' ); ?> data-type="meta" ><?php esc_html_e( 'Price low to high', 'wp-travel' ); ?></option>
					<option value="high_low" <?php selected( $price, 'high_low' ); ?> data-type="meta" ><?php esc_html_e( 'Price high to low', 'wp-travel' ); ?></option>
				</select>
			</div>
		<?php endif; ?>
		<div class="wp-toolbar-filter-field wt-filter-by-itinerary-types">
			<?php
			wp_dropdown_categories(
				array(
					'taxonomy'          => 'itinerary_types',
					'name'              => 'itinerary_types',
					'class'             => 'wp_travel_input_filters type',
					'show_option_none'  => esc_html( $trip_type_text ),
					'option_none_value' => '',
					'selected'          => $type,
					'value_field'       => 'slug',
				)
			);
			?>
		</div>
		<div class="wp-toolbar-filter-field wt-filter-by-travel-locations">
			<?php
			wp_dropdown_categories(
				array(
					'taxonomy'          => 'travel_locations',
					'name'              => 'travel_locations',
					'class'             => 'wp_travel_input_filters location',
					'show_option_none'  => esc_html( $location_text ),
					'option_none_value' => '',
					'selected'          => $location,
					'value_field'       => 'slug',
				)
			);
			?>
		</div>
		<div class="wp-toolbar-filter-field wt-filter-by-trip-date">
				<select name="trip_date" class="wp_travel_input_filters trip-date">
					<option value=""><?php echo esc_html( $trip_date_text ); ?></option>
					<option value="asc" <?php selected( $trip_date, 'asc' ); ?> data-type="meta" ><?php esc_html_e( 'Ascending', 'wp-travel' ); ?></option>
					<option value="desc" <?php selected( $trip_date, 'desc' ); ?> data-type="meta" ><?php esc_html_e( 'Descending', 'wp-travel' ); ?></option>
				</select>
			</div>
		<div class="wp-toolbar-filter-field wt-filter-by-trip-name">
				<select name="trip_name" class="wp_travel_input_filters trip-name">
					<option value=""><?php echo esc_html( $trip_name_text ); ?></option>
					<option value="asc" <?php selected( $trip_name, 'asc' ); ?> data-type="meta" ><?php esc_html_e( 'Ascending', 'wp-travel' ); ?></option>
					<option value="desc" <?php selected( $trip_name, 'desc' ); ?> data-type="meta" ><?php esc_html_e( 'Descending', 'wp-travel' ); ?></option>
				</select>
			</div>
		<input type="hidden" name="_nonce" class="wp_travel_input_filters" value="<?php echo esc_attr( WP_Travel::create_nonce() ); ?>" />
		<div class="wp-travel-filter-button">
			<button class="btn-wp-travel-filter"><?php echo esc_html( $show_text ); ?></button>
		</div>
		<?php do_action( 'wp_travel_after_post_filter' ); ?>
	</div>
	<?php
}

/**
 * Archive page toolbar.
 *
 * @since 1.0.4
 * @return void
 */
function wptravel_archive_toolbar() {
	$sanitized_get  = WP_Travel::get_sanitize_request( 'get', true );
	$view_mode      = wptravel_get_archive_view_mode( $sanitized_get );
	$layout_version = wptravel_layout_version();

	if ( ( WP_Travel::is_page( 'archive' ) || is_search() ) && ! is_admin() ) :
		if ( WP_Travel::is_page( 'archive' ) || ( is_search() && ! empty( $_GET['post_type'] ) && 'itineraries' === $_GET['post_type'] ) ) :
			?>
			<div class="wp-travel-toolbar clearfix">
				<div class="wp-toolbar-content wp-toolbar-left">
					<?php wptravel_archive_filter_by( $sanitized_get ); ?>
				</div>
				<div class="wp-toolbar-content wp-toolbar-right">
					<?php
					$current_url = isset( $_SERVER['HTTP_HOST'] ) && isset( $_SERVER['REQUEST_URI'] ) ? '//' . sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) . sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
					// $current_url = add_query_arg( '_nonce', WP_Travel::create_nonce(), $current_url );
					// if ( 'v1' === $layout_version ) :
					?>
						<ul class="wp-travel-view-mode-lists">
							<li class="wp-travel-view-mode <?php echo ( 'grid' === $view_mode ) ? 'active-mode' : ''; ?>" data-mode="grid" ><a onClick="viewMode( 'grid' )" href="javascript:void(0)"><i class="dashicons dashicons-grid-view"></i></a></li>
							<li class="wp-travel-view-mode <?php echo ( 'list' === $view_mode ) ? 'active-mode' : ''; ?>" data-mode="list" ><a onClick="viewMode( 'list' )" href="javascript:void(0)"><i class="dashicons dashicons-list-view"></i></a></li>
						</ul>
					<?php // else : ?>
						<!-- <ul id="wp-travel-view-mode-lists"" class="wp-travel-view-mode-lists">
							<li class="wp-travel-view-mode <?php echo ( 'grid' === $view_mode ) ? 'active-mode' : ''; ?>"><a href="#" onclick="gridView()"><i class="dashicons dashicons-grid-view"></i></a></li>
							<li class="wp-travel-view-mode <?php echo ( 'list' === $view_mode ) ? 'active-mode' : ''; ?>"><a href="#" onclick="listView()"><i class="dashicons dashicons-list-view"></i></a></li>
						</ul> -->
					<?php // endif; ?>
				</div>
			</div>
			<?php
		endif;
		$archive_sidebar_class = '';

		if ( is_active_sidebar( 'wp-travel-archive-sidebar' ) ) {
			$archive_sidebar_class = 'wp-travel-trips-has-sidebar';
		}

		?>
		<div class="wp-travel-archive-content <?php echo esc_attr( $archive_sidebar_class ); ?>">
		<?php if ( 'grid' === $view_mode && 'v1' === $layout_version ) : ?>
			<?php $col_per_row = apply_filters( 'wp_travel_archive_itineraries_col_per_row', '3' ); ?>
			<?php
			if ( is_active_sidebar( 'wp-travel-archive-sidebar' ) ) {
				$col_per_row = apply_filters( 'wp_travel_archive_itineraries_col_per_row', '2' );
			}
			?>
			<div class="wp-travel-itinerary-items">
				<ul class="wp-travel-itinerary-list itinerary-<?php esc_attr_e( $col_per_row, 'wp-travel' ); ?>-per-row grid-view">
			<?php
		endif;
	endif;
}
/**
 * Archive page wrapper close.
 *
 * @since 1.0.4
 * @return void
 */
function wptravel_archive_wrapper_close() {
	if ( ( WP_Travel::is_page( 'archive' ) || is_search() ) && ! is_admin() ) :
		$sanitized_get  = WP_Travel::get_sanitize_request( 'get', true );
		$view_mode      = wptravel_get_archive_view_mode( $sanitized_get );
		$layout_version = wptravel_layout_version();
		?>
		<?php if ( 'grid' === $view_mode && 'v1' === $layout_version ) : ?>
				</ul>
			</div>
		<?php endif; ?>
		<?php
		$pagination_range = apply_filters( 'wp_travel_pagination_range', 2 );
		$max_num_pages    = apply_filters( 'wp_travel_max_num_pages', '' );
		wptravel_pagination( $pagination_range, $max_num_pages );
		?>
	</div>
		<?php
	endif;
}

/**
 * Archive page sidebar
 *
 * @since 1.2.1
 * @return void
 */

function wptravel_archive_listing_sidebar() {

	if ( ( WP_Travel::is_page( 'archive' ) || WP_Travel::is_page( 'search' ) ) && ! is_admin() && is_active_sidebar( 'wp-travel-archive-sidebar' ) ) :
		?>

		<div id="wp-travel-secondary" class="wp-travel-widget-area widget-area" role="complementary">
			<?php dynamic_sidebar( 'wp-travel-archive-sidebar' ); ?>
		</div>

		<?php

	endif;

}

/**
 * If submitted filter by post meta.
 *
 * @param  (wp_query object) $query object.
 *
 * @return void
 */
function wptravel_posts_filter( $query ) {

	if ( ! WP_Travel::verify_nonce( true ) ) {
		return $query;
	}

	global $pagenow;
	$type = '';

	$submission_get = wptravel_sanitize_array( wp_unslash( $_GET ) );

	if ( isset( $submission_get['post_type'] ) ) {
		$type = $submission_get['post_type'];
	}

	$enabled_react = wptravel_is_react_version_enabled();

	if ( $query->is_main_query() ) {

		if ( 'itinerary-booking' == $type && is_admin() && 'edit.php' == $pagenow && isset( $submission_get['wp_travel_post_id'] ) && '' !== $submission_get['wp_travel_post_id'] ) {

			$query->set( 'meta_key', 'wp_travel_post_id' );
			$query->set( 'meta_value', absint( $submission_get['wp_travel_post_id'] ) );
		}

		if ( 'itinerary-enquiries' == $type && is_admin() && 'edit.php' == $pagenow && isset( $submission_get['wp_travel_post_id'] ) && '' !== $submission_get['wp_travel_post_id'] ) {

			$query->set( 'meta_key', 'wp_travel_post_id' );
			$query->set( 'meta_value', absint( $submission_get['wp_travel_post_id'] ) );
		}

		/**
		 * Archive /Taxonomy page filters
		 *
		 * @since 1.0.4
		 */
		if ( WP_Travel::is_page( 'archive' ) && ! is_admin() ) {

			$current_meta = $query->get( 'meta_query' );
			$current_meta = ( $current_meta ) ? $current_meta : array();
			// Filter By Dates.
			if ( ( isset( $submission_get['trip_start'] ) || isset( $submission_get['trip_end'] ) ) && ! $enabled_react ) {

				$trip_start = ! empty( $submission_get['trip_start'] ) ? $submission_get['trip_start'] : 0;

				$trip_end = ! empty( $submission_get['trip_end'] ) ? $submission_get['trip_end'] : 0;

				if ( $trip_start || $trip_end ) {

					$date_format = get_option( 'date_format' );
					// Convert to timestamp.
					if ( ! $trip_start ) {
						$trip_start = date( 'Y-m-d' );
					}

					$custom_meta = array(
						'relation' => 'AND',
						array(
							'key'     => 'wp_travel_start_date',
							'value'   => $trip_start,
							'type'    => 'DATE',
							'compare' => '>=',
						),
					);

					if ( $trip_end ) {
						$custom_meta[] = array(
							'key'     => 'wp_travel_end_date',
							'value'   => $trip_end,
							'type'    => 'DATE',
							'compare' => '<=',
						);
					}
					$current_meta[] = $custom_meta;
				}
			}

			// Filter By Price.
			if ( isset( $submission_get['price'] ) && '' != $submission_get['price'] ) {
				$filter_by = $submission_get['price'];

				$query->set( 'meta_key', 'wp_travel_trip_price' );
				$query->set( 'orderby', 'meta_value_num' );

				switch ( $filter_by ) {
					case 'low_high':
						$query->set( 'order', 'asc' );
						break;
					case 'high_low':
						$query->set( 'order', 'desc' );
						break;
					default:
						break;
				}
			}
			// Trip Cost Range Filter.
			if ( ( isset( $submission_get['max_price'] ) || isset( $submission_get['min_price'] ) ) ) {

				$max_price = ! empty( $submission_get['max_price'] ) ? $submission_get['max_price'] : 0;

				$min_price = ! empty( $submission_get['min_price'] ) ? $submission_get['min_price'] : 0;

				if ( $min_price || $max_price ) {

					$query->set( 'meta_key', 'wp_travel_trip_price' );

					$custom_meta    = array(
						array(
							'key'     => 'wp_travel_trip_price',
							'value'   => array( $min_price, $max_price ),
							'type'    => 'numeric',
							'compare' => 'BETWEEN',
						),
					);
					$current_meta[] = $custom_meta;
				}
			}

			if ( isset( $submission_get['fact'] ) && '' != $submission_get['fact'] ) {
				$fact = $submission_get['fact'];

				$query->set( 'meta_key', 'wp_travel_trip_facts' );

				$custom_meta    = array(
					array(
						'key'     => 'wp_travel_trip_facts',
						'value'   => $fact,
						'compare' => 'LIKE',
					),
				);
				$current_meta[] = $custom_meta;
			}
			$query->set( 'meta_query', array( $current_meta ) );

			// Filter by Keywords.
			$current_tax = $query->get( 'tax_query' );
			$current_tax = ( $current_tax ) ? $current_tax : array();
			if ( isset( $submission_get['keyword'] ) && '' != $submission_get['keyword'] ) {

				$keyword = $submission_get['keyword'];

				$keywords = explode( ',', $keyword );

				$current_tax[] = array(
					array(
						'taxonomy' => 'travel_keywords',
						'field'    => 'name',
						'terms'    => $keywords,
					),
				);
			}
			$query->set( 'tax_query', $current_tax );

			if ( ! $enabled_react && ( isset( $submission_get['trip_date'] ) && '' != $submission_get['trip_date'] ) ) {
				$query->set( 'meta_key', 'trip_date' );
				$query->set( 'orderby', 'meta_value' );
				if ( 'asc' === $submission_get['trip_date'] ) {
					$query->set( 'order', 'asc' );
				} else {
					$query->set( 'order', 'desc' );
				}
			}

			// Filter by trip name.
			if ( isset( $submission_get['trip_name'] ) && '' != $submission_get['trip_name'] ) {
				$query->set( 'post_type', 'itineraries' );
				$query->set( 'orderby', 'post_title' );
				if ( 'asc' === $submission_get['trip_name'] ) {
					$query->set( 'order', 'asc' );
				} else {
					$query->set( 'order', 'desc' );
				}
			}
		}
	}
}

function wptravel_tab_show_in_menu( $tab_name ) {
	if ( ! $tab_name ) {
		return false;
	}
	$tabs = wptravel_get_frontend_tabs( $show_in_menu_query = true, $frontend_hide_content = true ); // $show_in_menu_query fixes the content filter in page builder.
	if ( ! isset( $tabs[ $tab_name ]['show_in_menu'] ) ) {
		return false;
	}

	if ( 'yes' === $tabs[ $tab_name ]['show_in_menu'] || $tabs[ $tab_name ]['show_in_menu'] == 1 ) {
		return true;
	}
	return false;
}

function wptravel_get_archive_view_mode( $sanitized_get = array() ) {
	$default_view_mode = 'list';
	$default_view_mode = apply_filters( 'wp_travel_default_view_mode', $default_view_mode );
	$view_mode         = $default_view_mode;
	if ( isset( $sanitized_get['view_mode'] ) && ( 'grid' === $sanitized_get['view_mode'] || 'list' === $sanitized_get['view_mode'] ) ) {
		$view_mode = sanitize_text_field( wp_unslash( $sanitized_get['view_mode'] ) );
	}
	$view_mode = isset( $_COOKIE['wptravel_view_mode'] ) && $_COOKIE['wptravel_view_mode'] ? $_COOKIE['wptravel_view_mode'] : $view_mode;
	return $view_mode;
}

/**
 * Clear Booking Stat Transient.
 *
 * @return void
 */
function wptravel_clear_booking_transient( $trip_id ) {
	if ( ! $trip_id ) {
		return;
	}
	$post_type = get_post_type( $trip_id );
	// If this isn't a 'itinerary-booking' post, don't update it.
	if ( 'itinerary-booking' != $post_type ) {
		return;
	}
	// Stat Transient
	delete_site_transient( '_transient_wt_booking_stat_data' );
	delete_site_transient( '_transient_wt_booking_top_country' );
	delete_site_transient( '_transient_wt_booking_top_itinerary' );

	// Booking Count Transient
	$trip_id = get_post_meta( $trip_id, 'wp_travel_post_id', true );
	// delete_site_transient( "_transient_wt_booking_count_{$trip_id}" );
	delete_post_meta( $trip_id, 'wp_travel_booking_count' );
	delete_site_transient( '_transient_wt_booking_payment_stat_data' );
	// @since 1.0.6
	do_action( 'wp_travel_after_deleting_booking_transient' );
}


/**
 * Excerpt.
 *
 * @param HTML $more Read more.
 * @return HTML
 */
function wptravel_excerpt_more( $more ) {
	global $post;
	if ( empty( $post->post_type ) || WP_TRAVEL_POST_TYPE !== $post->post_type ) {
		return $more;
	}

	return '...';
}

function wptravel_wpkses_post_iframe( $tags, $context ) {
	if ( 'post' === $context ) {
		$tags['iframe'] = array(
			'src'             => true,
			'height'          => true,
			'width'           => true,
			'frameborder'     => true,
			'allowfullscreen' => true,
		);
	}
	return $tags;
}

if ( ! function_exists( 'wptravel_is_endpoint_url' ) ) :
	/**
	 * Is_wp_travel_endpoint_url - Check if an endpoint is showing.
	 *
	 * @param string $endpoint Whether endpoint.
	 * @return bool
	 */
	function wptravel_is_endpoint_url( $endpoint = false ) {
		global $wp;
		$query_class         = new WP_Travel_Query();
		$wp_travel_endpoints = $query_class->get_query_vars();

		if ( false !== $endpoint ) {
			if ( ! isset( $wp_travel_endpoints[ $endpoint ] ) ) {
				return false;
			} else {
				$endpoint_var = $wp_travel_endpoints[ $endpoint ];
			}

			return isset( $wp->query_vars[ $endpoint_var ] );
		} else {
			foreach ( $wp_travel_endpoints as $key => $value ) {
				if ( isset( $wp->query_vars[ $key ] ) ) {
					return true;
				}
			}

			return false;
		}
	}
endif;


/**
 * No index our endpoints.
 * Prevent indexing pages like order-received.
 *
 * @since 2.5.3
 */
function wptravel_prevent_endpoint_indexing() {
	if ( wptravel_is_endpoint_url() ) { // WPCS: input var ok, CSRF ok.
		@header( 'X-Robots-Tag: noindex' ); // @codingStandardsIgnoreLine
	}
}

/**
 * Disable Jetpack Related Posts on Trips page
 *
 * @param array $options
 * @return void
 */
function wptravel_remove_jetpack_related_posts( $options ) {

	$disable_jetpack_related_for_trips = apply_filters( 'wp_travel_disable_jetpack_rp', true );

	if ( is_singular( WP_TRAVEL_POST_TYPE ) && $disable_jetpack_related_for_trips ) {
		$options['enabled'] = false;
	}
	return $options;
}

function wptravel_get_header_image_tag( $html ) {
	if ( ! is_tax( array( 'itinerary_types', 'travel_locations', 'travel_keywords', 'activity' ) ) ) {
		return $html;
	}
		$attr           = array();
		$queried_object = get_queried_object();
		$image_id       = get_term_meta( $queried_object->term_id, 'wp_travel_trip_type_image_id', true );
	if ( false == $image_id || '' == $image_id ) {
			return $html;
	}
		$header                = new stdClass();
		$image_meta            = get_post_meta( $image_id, '_wp_attachment_metadata', true );
		$header->url           = wp_get_attachment_url( $image_id );
		$header->attachment_id = $image_id;
		$width                 = absint( $image_meta['width'] );
		$height                = absint( $image_meta['height'] );

		$attr = wp_parse_args(
			$attr,
			array(
				'src'    => $header->url,
				'width'  => $width,
				'height' => $height,
				'alt'    => get_bloginfo( 'name' ),
			)
		);

		// Generate 'srcset' and 'sizes' if not already present.
	if ( empty( $attr['srcset'] ) && ! empty( $header->attachment_id ) ) {
			$size_array = array( $width, $height );

		if ( is_array( $image_meta ) ) {
				$srcset = wp_calculate_image_srcset( $size_array, $header->url, $image_meta, $header->attachment_id );
				$sizes  = ! empty( $attr['sizes'] ) ? $attr['sizes'] : wp_calculate_image_sizes( $size_array, $header->url, $image_meta, $header->attachment_id );

			if ( $srcset && $sizes ) {
				$attr['srcset'] = $srcset;
				$attr['sizes']  = $sizes;
			}
		}
	}

		$attr = array_map( 'esc_attr', $attr );
		$html = '<img';

	foreach ( $attr as $name => $value ) {
			$html .= ' ' . $name . '="' . $value . '"';
	}

		$html .= ' />';
		return $html;
}

/**
 * If return false then, the_content filter used for tab content to display.
 *
 * @param boolean $raw false to use the_content filter to fetch content.
 * @param string  $tab_key Frontend tab key.
 *
 * @since 2.0.6
 *
 * @return bool
 */
function wptravel_raw_output_on_tab_content( $raw, $tab_key ) {
	if ( 'gallery' === $tab_key ) { // Hide extra tab content on gallery tab.
		$raw = true;
	}
	return $raw;
}

/**
 * Hook call back for single itinerary.
 *
 * @return void
 */
function wptravel_single_itinerary_trip_content() {
	$itinerary_layout_v2_enabled = wptravel_use_itinerary_v2_layout();

	if ( $itinerary_layout_v2_enabled ) {
		wptravel_get_template_part( 'content', 'single-itineraries-v2' ); // @since v5.0.0.
	} else {
		wptravel_get_template_part( 'content', 'single-itineraries' );
	}
}
