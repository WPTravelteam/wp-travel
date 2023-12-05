<?php
/**
 * Admin Helper
 *
 * @package WP_Travel
 */

/**
 * All Admin Init hooks listed here.
 *
 * @since 1.0.7
 */
function wptravel_admin_init() {
	add_action( 'wp_trash_post', 'wptravel_clear_booking_count_transient', 10 ); // @since 1.0.7
	add_action( 'untrash_post', 'wptravel_clear_booking_count_transient_untrash', 10 ); // @since 2.0.3

	if ( version_compare( WP_TRAVEL_VERSION, '1.2.0', '>' ) ) {
		include_once sprintf( '%s/upgrade/update-121.php', WP_TRAVEL_ABSPATH );
	}
	if ( version_compare( WP_TRAVEL_VERSION, '1.3.6', '>' ) ) {
		include_once sprintf( '%s/upgrade/update-137.php', WP_TRAVEL_ABSPATH );
	}
}

/**
 * WP Travel market place page.
 */
function wptravel_marketplace_page() {

	// Hardcoded themes data.
	$themes_data = array(
		'travel-knock'     => array(
			'name'       => __( 'Travel Knock', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'http://wpdemo.wensolutions.com/wp-content/uploads/2023/12/Travel-Knock-2-min.png',
			'demo_url'   => 'https://wpdemo.wensolutions.com/travel-knock/',
			'detail_url' => 'https://wensolutions.com/themes/travel-knock/',
		),
		'wp-yatra'     => array(
			'name'       => __( 'WP Yatra', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://wensolutions.com/wp-content/uploads/elementor/thumbs/hero-qbkhhc4a7nv7rej5i8ckxpsgrxhtscltq568froge8.png',
			'demo_url'   => 'https://wpdemo.wensolutions.com/wp-yatra/',
			'detail_url' => 'https://wensolutions.com/themes/wp-yatra/',
		),
		'travelin'     => array(
			'name'       => __( 'Travelin', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'http://wpdemo.wensolutions.com/travelin/wp-content/uploads/sites/17/2023/11/screenshot-1-min-1.png',
			'demo_url'   => 'https://wpdemo.wensolutions.com/travelin/',
			'detail_url' => 'https://wensolutions.com/themes/travelin/',
		),
		'travelvania'     => array(
			'name'       => __( 'Travelvania', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'http://wpdemo.wensolutions.com/travelvania/wp-content/uploads/sites/16/2023/11/Travelvania-1-1024x736-min.png',
			'demo_url'   => 'https://wpdemo.wensolutions.com/travelvania/',
			'detail_url' => 'https://wensolutions.com/themes/travelvania/',
		),
		'wp-travel-fse'     => array(
			'name'       => __( 'WP Travel FSE', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/wp-travel-fse/1.0.6/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://wpdemo.wensolutions.com/wp-travel-fse/',
			'detail_url' => 'https://wensolutions.com/themes/wp-travel-fse/',
		),
		'travel-init'     => array(
			'name'       => __( 'Travel Init', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travel-init/1.1/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://wpdemo.wensolutions.com/travel-init/',
			'detail_url' => 'https://wensolutions.com/themes/travel-init/',
		),
		'travel-log-pro'     => array(
			'name'       => __( 'Travel Log Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travel-log/1.4.3/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://wpdemo.wensolutions.com/travel-log-pro/',
			'detail_url' => 'https://wensolutions.com/themes/travel-log-pro/',
		),
		'travel-log'         => array(
			'name'       => __( 'Travel Log', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travel-log/1.4.3/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://wpdemo.wensolutions.com/travel-log-pro/',
			'detail_url' => 'https://wensolutions.com/themes/travel-log-pro/',
		),
		'travel-buzz-pro'     => array(
			'name'       => __( 'Travel Buzz Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travel-buzz/2.0/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://wpdemo.wensolutions.com/travel-buzz-pro/',
			'detail_url' => 'https://wensolutions.com/themes/travel-buzz-pro/',
		),
		'travel-buzz'         => array(
			'name'       => __( 'Travel Buzz', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travel-buzz/2.0/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://wpdemo.wensolutions.com/travel-buzz-pro/',
			'detail_url' => 'https://wensolutions.com/themes/travel-buzz-pro/',
		),
		'travel-joy-pro'     => array(
			'name'       => __( 'Travel Joy Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travel-joy/1.1.2/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://wpdemo.wensolutions.com/travel-joy-pro/',
			'detail_url' => 'https://wensolutions.com/themes/travel-joy-pro/',
		),
		'travel-joy'         => array(
			'name'       => __( 'Travel Joy', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travel-joy/1.1.2/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://wpdemo.wensolutions.com/travel-joy-pro/',
			'detail_url' => 'https://wensolutions.com/themes/travel-joy-pro/',
		),
		'travel-one'         => array(
			'name'       => __( 'Travel One', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travel-one/1.0.5/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://wpdemo.wensolutions.com/travel-one/',
			'detail_url' => 'https://wensolutions.com/themes/travel-one/',
		),
		'travelstore'         => array(
			'name'       => __( 'Travelstore', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travelstore/1.0.5/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://wpdemo.wensolutions.com/travelstore/',
			'detail_url' => 'https://wensolutions.com/themes/travelstore/',
		),
		'travel-ocean'         => array(
			'name'       => __( 'Travel Ocean', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travel-ocean/1.0.5/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://wpdemo.wensolutions.com/travel-ocean/',
			'detail_url' => 'https://wensolutions.com/themes/travel-ocean/',
		),
		'travel-escape-pro'         => array(
			'name'       => __( 'Travel Escape Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travel-escape/1.0.6/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://wpdemo.wensolutions.com/travel-escape-pro/',
			'detail_url' => ' https://wensolutions.com/themes/travel-escape-pro/',
		),
		'travel-escape'         => array(
			'name'       => __( 'Travel Escape', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travel-escape/1.0.6/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://wpdemo.wensolutions.com/travel-escape-pro/',
			'detail_url' => 'https://wensolutions.com/themes/travel-escape-pro/',
		),
		'gotrip'         => array(
			'name'       => __( 'Gotrip', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://www.eaglevisionit.com/wp-content/uploads/2023/09/screenshot-41-1024x768.png',
			'demo_url'   => 'https://demos.eaglevisionit.com/gotrip/',
			'detail_url' => 'https://www.eaglevisionit.com/downloads/gotrip/',
		),
		'travel-store'         => array(
			'name'       => __( 'Travel Store', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://www.eaglevisionit.com/wp-content/uploads/2023/08/screenshot-26-1024x768.png',
			'demo_url'   => 'https://demos.eaglevisionit.com/travel-store/',
			'detail_url' => 'https://www.eaglevisionit.com/downloads/travel-store/',
		),
		'travel-fse'         => array(
			'name'       => __( 'Travel FSE', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://www.eaglevisionit.com/wp-content/uploads/2023/06/screenshot-1-1-1024x768.png',
			'demo_url'   => 'https://demos.eaglevisionit.com/travel-fse/',
			'detail_url' => 'https://www.eaglevisionit.com/downloads/travel-fse/',
		),
		'travel-ride'         => array(
			'name'       => __( 'Travel Ride', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://www.eaglevisionit.com/wp-content/uploads/2023/09/travel-ride-1024x768.png',
			'demo_url'   => 'https://demos.eaglevisionit.com/travel-ride/',
			'detail_url' => 'https://www.eaglevisionit.com/downloads/travel-ride/',
		),
		'bloguide-pro'         => array(
			'name'       => __( 'Bloguide Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/bloguide/1.0.1/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/bloguide-pro/',
			'detail_url' => 'https://themepalace.com/downloads/bloguide-pro/',
		),
		'bloguide'         => array(
			'name'       => __( 'Bloguide', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/bloguide/1.0.1/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/bloguide/',
			'detail_url' => 'https://themepalace.com/downloads/bloguide/',
		),
		'ultravel-pro'         => array(
			'name'       => __( 'Ultravel Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/ultravel/1.0.2/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/ultravel-pro/',
			'detail_url' => 'https://themepalace.com/downloads/ultravel-pro/',
		),
		'ultravel'         => array(
			'name'       => __( 'Ultravel', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/ultravel/1.0.2/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/ultravel/',
			'detail_url' => 'https://themepalace.com/downloads/ultravel/',
		),
		'travelism-pro'         => array(
			'name'       => __( 'Travelism Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travelism/1.0.3/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/travelism-pro/',
			'detail_url' => 'https://themepalace.com/downloads/travelism-pro/',
		),
		'travelism'         => array(
			'name'       => __( 'Travelism', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travelism/1.0.3/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/travelism/',
			'detail_url' => 'https://themepalace.com/downloads/travelism/',
		),
		'swingpress-pro'         => array(
			'name'       => __( 'Swingpress Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/swingpress/1.0.3/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/swingpress-pro/',
			'detail_url' => 'https://themepalace.com/downloads/swingpress-pro/',
		),
		'swingpress'         => array(
			'name'       => __( 'Swingpress', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/swingpress/1.0.3/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/swingpress/',
			'detail_url' => 'https://themepalace.com/downloads/swingpress/',
		),
		'wen-travel-pro'         => array(
			'name'       => __( 'Wen Travel Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/wen-travel/1.2.3/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://demo.wenthemes.com/wen-travel-pro/',
			'detail_url' => 'https://themepalace.com/downloads/wen-travel-pro/',
		),
		'wen-travel'         => array(
			'name'       => __( 'Wen Travel', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/wen-travel/1.2.3/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://demo.wenthemes.com/wen-travel-free/',
			'detail_url' => 'https://themepalace.com/downloads/wen-travel/',
		),
		'travel-life-pro'         => array(
			'name'       => __( 'Travel Life Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travel-life/1.0.5/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/travel-life-pro/',
			'detail_url' => 'https://themepalace.com/downloads/travel-life-pro/',
		),
		'travel-life'         => array(
			'name'       => __( 'Travel Life', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travel-life/1.0.5/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/travel-life/',
			'detail_url' => 'https://themepalace.com/downloads/travel-life/',
		),
		'top-travel-pro'         => array(
			'name'       => __( 'Top Travel Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/top-travel/1.0.6/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/top-travel-pro/',
			'detail_url' => 'https://themepalace.com/downloads/top-travel-pro/',
		),
		'top-travel'         => array(
			'name'       => __( 'Top Travel', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/top-travel/1.0.6/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/top-travel/',
			'detail_url' => 'https://themepalace.com/downloads/top-travel/',
		),
		'next-travel-pro'         => array(
			'name'       => __( 'Next Travel Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/next-travel/1.0.9/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/next-travel-pro/',
			'detail_url' => 'https://themepalace.com/downloads/next-travel-pro/',
		),
		'next-travel'         => array(
			'name'       => __( 'Next Travel', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/next-travel/1.0.9/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/next-travel/',
			'detail_url' => 'https://themepalace.com/downloads/next-travel/',
		),
		'travel-master-pro'         => array(
			'name'       => __( 'Travel Master Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travel-master/1.2.2/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/travel-master-pro/',
			'detail_url' => 'https://themepalace.com/downloads/travel-master-pro/',
		),
		'travel-master'         => array(
			'name'       => __( 'Travel Master', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travel-master/1.2.2/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/travel-master/',
			'detail_url' => 'https://themepalace.com/downloads/travel-master/',
		),
		'tale-travel-pro'         => array(
			'name'       => __( 'Tale Travel Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/tale-travel/1.1.9/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/tale-travel-pro/',
			'detail_url' => 'https://themepalace.com/downloads/tale-travel-pro/',
		),
		'tale-travel'         => array(
			'name'       => __( 'Tale Travel', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/tale-travel/1.1.9/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/tale-travel/',
			'detail_url' => 'https://themepalace.com/downloads/tale-travel/',
		),
		'travel-ultimate-pro'         => array(
			'name'       => __( 'Travel Ultimate Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travel-ultimate/1.3.2/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/travel-ultimate-pro/',
			'detail_url' => 'https://themepalace.com/downloads/travel-ultimate-pro/',
		),
		'travel-ultimate'         => array(
			'name'       => __( 'Travel Ultimate', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travel-ultimate/1.3.2/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/travel-ultimate/',
			'detail_url' => 'https://themepalace.com/downloads/travel-ultimate/',
		),
		'travel-gem-pro'         => array(
			'name'       => __( 'Travel Gem Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travel-gem/1.2.3/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://demo.wenthemes.com/travel-gem-pro/',
			'detail_url' => 'https://themepalace.com/downloads/travel-gem-pro/',
		),
		'travel-gem'         => array(
			'name'       => __( 'Travel Gem', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travel-gem/1.2.3/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://demo.wenthemes.com/travel-gem/',
			'detail_url' => 'https://themepalace.com/downloads/travel-gem/',
		),
		'tourable-pro'         => array(
			'name'       => __( 'Tourable Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/tourable/1.2.4/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/tourable-pro/',
			'detail_url' => 'https://themepalace.com/downloads/tourable-pro/',
		),
		'tourable'         => array(
			'name'       => __( 'Tourable', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/tourable/1.2.4/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/tourable/',
			'detail_url' => 'https://themepalace.com/downloads/tourable/',
		),
		'travel-base-pro'         => array(
			'name'       => __( 'Travel Base Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travel-base/1.2.6/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/travel-base-pro/',
			'detail_url' => 'https://themepalace.com/downloads/travel-base-pro/',
		),
		'travel-base'         => array(
			'name'       => __( 'Travel Base', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travel-base/1.2.6/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/travel-base/',
			'detail_url' => 'https://themepalace.com/downloads/travel-base/',
		),
		'pleased-pro'         => array(
			'name'       => __( 'Pleased Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/pleased/1.2.3/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/pleased-pro/',
			'detail_url' => 'https://themepalace.com/downloads/pleased-pro/',
		),
		'pleased'         => array(
			'name'       => __( 'Pleased', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/pleased/1.2.3/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/pleased/',
			'detail_url' => 'https://themepalace.com/downloads/pleased/',
		),
		'travel-insight-pro'         => array(
			'name'       => __( 'Travel Insight Pro', 'wp-travel' ),
			'type'       => 'premium',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travel-insight/1.2.2/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/travel-insight-pro/',
			'detail_url' => 'https://themepalace.com/downloads/travel-insight-pro/',
		),
		'travel-insight'         => array(
			'name'       => __( 'Travel Insight', 'wp-travel' ),
			'type'       => 'free',
			'img_url'    => 'https://i0.wp.com/themes.svn.wordpress.org/travel-insight/1.2.2/screenshot.png?w=572&strip=all',
			'demo_url'   => 'https://themepalacedemo.com/travel-insight/',
			'detail_url' => 'https://themepalace.com/downloads/travel-insight/',
		),
	);

	

	$info_btn_text     = __( 'View Demo', 'wp-travel' );
	$install_btn_text     = __( 'Install', 'wp-travel' );
	$download_btn_text = __( 'View Detail', 'wp-travel' );

	?>
	<div class="wrap">
		
		<div id="poststuff">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Marketplace', 'wp-travel' ); ?></h1>
			<div id="post-body">
				<div id="wptravel-theme-install-loader">
					<svg style="margin: auto; background: rgb(255, 255, 255); display: block; shape-rendering: auto;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
						<circle cx="50" cy="50" fill="none" stroke="#079812" stroke-width="16" r="41" stroke-dasharray="193.20794819577225 66.40264939859075">
						  <animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="2s" values="0 50 50;360 50 50" keyTimes="0;1"></animateTransform>
						</circle>
					</svg>
					<div style="text-align: center; margin-top: 10px;">
						<?php echo __( 'Installing Theme...', 'wp-travel' ) ?>
					</div>
				</div>
				<div id="wptravel-market-content">
					<div class="wp-travel-marketplace-tab-wrap">
						<div id="tabs-2" class="tab-pannel">
							<div class="marketplace-module clearfix">
								<?php foreach ( $themes_data as $key => $theme ) : ?>
									<div class="single-module">
										<div class="single-module-image">
											<a href="<?php echo esc_url( $theme['demo_url'] ); ?>" target="_blank">
											<img width="423" height="237" src="<?php echo esc_url( $theme['img_url'] ); ?>" class="" alt="" >
											</a>
										</div>
										<div class="single-module-content clearfix">
											<h4 class="text-title"><a href="<?php echo esc_url( $theme['detail_url'] ); ?>" target="_blank">
											<span class="dashicons-wp-travel">
											</span><?php echo esc_html( $theme['name'] ); ?></a></h4>
											<a class="btn-default pull-left" href="<?php echo esc_url( $theme['demo_url'] ); ?>" target="_blank"><?php echo esc_html( $info_btn_text ); ?></a>									
											<a class="btn-default pull-right" href="<?php echo esc_url( $theme['detail_url'] ); ?>" target="_blank"><?php echo esc_html( $download_btn_text ); ?></a>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>

					</div>

					

					<div id="aside-wrap-container">
						<div id="aside-wrap" class="single-module-side">
							<div class="aside-wrap-buttons-container">
								<h2 class="wp-travel-aside-wrap-block-title">
									<span><?php esc_html_e( 'Need Help?', 'wp-travel' ); ?></span>
								</h2>
								<div class="wp-travel-aside-help-block">
									<?php
									wptravel_meta_box_support();
									wptravel_meta_box_documentation();
									?>
								</div>
							</div>
							<?php
								wptravel_meta_box_review();
							?>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
	<?php
}

/**
 *
 * Will Display HTML content of support box
 *
 * @since 4.4.2
 */
function wptravel_meta_box_support() {
	?>
	<div id="wp_travel_support_block_id">
		<p class="text-center">
			<a class="wp-travel-aside-block-button" href="https://wptravel.io/contact/" target="_blank">
				<i class="fa fa-question-circle"></i>
				<?php esc_html_e( 'Support', 'wp-travel' ); ?>
			</a>
		</p>
	</div>
	<?php
}

/**
 *
 * Will Display HTML content of documentation box
 *
 * @since 4.4.2
 */
function wptravel_meta_box_documentation() {

	?>
	<div id="wp_travel_doc_block_id">
		<p class="text-center">
			<a class="wp-travel-aside-block-button" href="https://wptravel.io/wp-travel-documentations/" target="_blank">
				<i class="fa fa-book"></i>
				<?php esc_html_e( 'Documentation', 'wp-travel' ); ?>
			</a>
		</p>
	</div>
	<?php

}

/**
 *
 * Will Display HTML content of review box
 *
 * @since 4.4.2
 */
function wptravel_meta_box_review() {

	$wp_travel_reviews = array(
		array(
			'title'       => __( 'WP TRAVEL PLUGIN IS PERFECT', 'wp-travel' ),
			'description' => __( '"Works perfectly for Travel tours booking. Definitely recommended for using Wp Travel Plugin. Easy for client to book through your website which Runs with Wp Travel.	Tirupati is so helpful and patient to solve the problem one by one.	10 out of 10 service."', 'wp-travel' ),
			'profile'     => 'eliandyao',
		),
		array(
			'title'       => __( 'Amazing Customer Service', 'wp-travel' ),
			'description' => __( '"For a free plugin, there is more than enough to build out a travel website. I reached out to ask about a sort by data filter and at the time there wasn’t anything available. Not only 2 days later they updated the plugin and even contacted me via Facebook to tell me they had done so. Amazing Service."', 'wp-travel' ),
			'profile'     => 'dannrcm',
		),
	);
	?>
	<div id="wp_travel_review_block_id">
		<button type="button" class="handlediv" aria-expanded="true">
			<span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Reviews', 'wp-travel' ); ?></span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
		<h2 class="wp-travel-aside-wrap-block-title">
			<span><?php esc_html_e( 'Reviews', 'wp-travel' ); ?></span>
		</h2>
		<div class="inside">
			<?php
			foreach ( $wp_travel_reviews as $wp_travel_review ) {
				?>
					<div class="thumbnail">
						<p class="text-center">
							<i class="dashicons dashicons-star-filled" aria-hidden="true"></i>
							<i class="dashicons dashicons-star-filled" aria-hidden="true"></i>
							<i class="dashicons dashicons-star-filled" aria-hidden="true"></i>
							<i class="dashicons dashicons-star-filled" aria-hidden="true"></i>
							<i class="dashicons dashicons-star-filled" aria-hidden="true"></i>
						</p>
						<h4><?php echo esc_html( $wp_travel_review['title'] ); ?></h4>
						<h5><?php echo esc_html( $wp_travel_review['description'] ); ?></h5>
						<span class="by"><strong> <a class="wp-travel-anchor" href="https://profiles.wordpress.org/<?php echo esc_attr( $wp_travel_review['profile'] ); ?>" target="_blank"><?php echo esc_html( $wp_travel_review['profile'] ); ?></a></strong></span>
					</div>
					<?php
			}
			?>
			<div class="thumbnail last">
				<h5><?php esc_html_e( 'Enjoying WP-Travel? Feel free to leave us a review.', 'wp-travel' ); ?></h5>
					<a class="wp-travel-aside-block-button fit" href="https://wordpress.org/plugins/wp-travel/#reviews" target="_blank">
						<?php esc_html_e( 'Leave a Review', 'wp-travel' ); ?>
					</a>
			</div>
		</div>
	</div>
	<?php

}

/**
 * Upsell Message Callback for Travel Guide submenu. WP Travel > Travel Guide.
 */
function wptravel_get_travel_guide_upsell(){
	?>
	<h2><?php echo esc_html( 'Travel Guide' ); ?></h2>
	<?php
	if ( ! class_exists( 'WP_Travel_Travel_Guide_Core' ) ) :
		$args = array(
			'title'      => __( 'Need to add your Travel Guide ?', 'wp-travel' ),
			'content'    => __( 'By upgrading to Pro, you can add your Travel Guide in all of your trips !', 'wp-travel' ),
			'link'       => 'https://wptravel.io/wp-travel-pro/',
			'link_label' => __( 'Get WP Travel Pro', 'wp-travel' ),
		);
		wptravel_upsell_message( $args );
		if ( class_exists( 'WP_Travel_Pro' ) ) {
		?>	
		<div class="components-notice is-warning">
			<div class="components-notice__content">
				<p>Travel Guides is currently disabled please go to Modules Settings to enable it and reload the page. </p>
				<div class="components-notice__actions">

				</div>
			</div>
		</div>
		<?php
		}
	endif;
}

/**
 * Upsell Message Callback for Download submenu. WP Travel > Downloads.
 */
function wptravel_get_download_upsell() {
	?>
	<h2><?php echo esc_html( 'Downloads' ); ?></h2>
	<?php
	if ( ! class_exists( 'WP_Travel_Downloads_Core' ) ) :
		$args = array(
			'title'      => __( 'Need to add your downloads ?', 'wp-travel' ),
			'content'    => __( 'By upgrading to Pro, you can add your downloads in all of your trips !', 'wp-travel' ),
			'link'       => 'https://wptravel.io/wp-travel-pro/',
			'link_label' => __( 'Get WP Travel Pro', 'wp-travel' ),
		);
		wptravel_upsell_message( $args );
		if ( class_exists( 'WP_Travel_Pro' ) ) {
			$settings = wptravel_get_settings();
			$modules  = $settings['modules'];
			if ( isset( $modules['show_wp_travel_downloads'] ) ) {
				$active = 'yes' === $modules['show_wp_travel_downloads'];
				if ( ! $active ) {
					?>
					<p>Downloads is currently disabled please go to Modules Settings to enable it and reload the page.</p>
					<?php
				}
			}
		}
	endif;
}

/**
 * Upsell Message Callback for Custom Filters submenu. WP Travel > Custom Filters.
 */
function wptravel_custom_filters_upsell() {
	?>
	<h2><?php echo esc_html( 'Custom Filters' ); ?></h2>
	<?php
	if ( ! class_exists( 'WP_Travel_Custom_Filters_Core' ) ) :
		$args = array(
			'title'      => __( 'Need custom search filters?', 'wp-travel' ),
			'content'    => __( 'By upgrading to Pro, you can add your custom search filter fields to search trips !', 'wp-travel' ),
			'link'       => 'https://wptravel.io/wp-travel-pro/',
			'link_label' => __( 'Get WP Travel Pro', 'wp-travel' ),
		);
		wptravel_upsell_message( $args );
	
		if ( class_exists( 'WP_Travel_Pro' ) ) {
			$settings = wptravel_get_settings();
			$modules  = $settings['modules'];
			if ( isset( $modules['show_wp_travel_custom_filters'] ) ) {
				$active = 'yes' === $modules['show_wp_travel_custom_filters'];
				if ( ! $active ) {
					?>
					<p>Custom Filters is currently disabled please go to Modules Settings to enable it and reload the page.</p>
					<?php
				}
			}
		}
	endif;
}

/**
 * Modify Admin Footer Message.
 */
function wptravel_modify_admin_footer_admin_settings_page() {

	printf( wp_kses_post( __( 'Love %1$1s, Consider leaving us a %2$2s rating, also checkout %3$3s . A huge thanks in advance!', 'wp-travel' ) ), '<strong>WP Travel ?</strong>', '<a target="_blank" href="https://wordpress.org/support/plugin/wp-travel/reviews/">★★★★★</a>', '<a target="_blank" href="https://wptravel.io/downloads/">WP Travel modules</a>' ); // @phpcs:ignore
}
/**
 * Modify Admin Footer Message.
 */
function wptravel_modify_admin_footer_version() {
	/* translators: %s is WP Travel version. */
	printf( wp_kses_post( __( 'WP Travel version: %s', 'wp-travel' ) ), '<strong>' . esc_html( WP_TRAVEL_VERSION ) . '</strong>' );
}

/**
 * Add Footer Custom Text Hook.
 */
function wptravel_doc_support_footer_custom_text() {

	$screen = get_current_screen();

	if ( WP_TRAVEL_POST_TYPE === $screen->post_type ) {

		add_filter( 'admin_footer_text', 'wptravel_modify_admin_footer_admin_settings_page' );
		add_filter( 'update_footer', 'wptravel_modify_admin_footer_version', 11 );
	}
}

add_action( 'current_screen', 'wptravel_doc_support_footer_custom_text' );

/**
 * Clear the booking count transient.
 *
 * @param int $booking_id Booking post ID.
 */
function wptravel_clear_booking_count_transient( $booking_id ) {
	if ( ! $booking_id ) {
		return;
	}
	global $post_type;
	if ( 'itinerary-booking' !== $post_type ) {
		return;
	}
	$trip_id = get_post_meta( $booking_id, 'wp_travel_post_id', true );
	delete_site_transient( "_transient_wt_booking_count_{$trip_id}" );
	delete_post_meta( $trip_id, 'wp_travel_booking_count' );
	do_action( 'wp_travel_action_after_trash_booking', $booking_id ); // @phpcs:ignore
	do_action( 'wptravel_action_after_trash_booking', $booking_id ); // @since 2.0.3 to update current booking inventory data.
}

/**
 * Restore Booking on untrash booking.
 *
 * @param Number $booking_id Booking post ID.
 */
function wptravel_clear_booking_count_transient_untrash( $booking_id ) {
	if ( ! $booking_id ) {
		return;
	}
	global $post_type;
	if ( 'itinerary-booking' !== $post_type ) {
		return;
	}
	$trip_id = get_post_meta( $booking_id, 'wp_travel_post_id', true );
	delete_post_meta( $trip_id, 'wp_travel_booking_count' );
	do_action( 'wp_travel_action_after_untrash_booking', $booking_id ); // @phpcs:ignore
	do_action( 'wptravel_action_after_untrash_booking', $booking_id ); // @since 2.0.3 to update current booking inventory data.
}

/**
 * Return the booking count.
 *
 * @param int $trip_id Trip id.
 */
function wptravel_get_booking_count( $trip_id ) {
	if ( ! $trip_id ) {
		return 0;
	}
	global $wpdb;
	$booking_count = get_post_meta( $trip_id, 'wp_travel_booking_count', true );
	if ( ! $booking_count ) {
		$booking_count = 0;

		$results = $wpdb->get_row( $wpdb->prepare( "SELECT count( itinerary_id ) as booking_count FROM {$wpdb->posts} P JOIN ( Select distinct( post_id ), meta_value as itinerary_id from {$wpdb->postmeta} WHERE meta_key = 'wp_travel_post_id' and meta_value > 0 ) I on P.ID = I.post_id  where post_type='itinerary-booking' and post_status='publish' and itinerary_id=%d group by itinerary_id", $trip_id ) );

		if ( $results ) {
			$booking_count = $results->booking_count;
		}

		// Post meta only for sorting. // @since 3.0.4 it is also used for count.
		update_post_meta( $trip_id, 'wp_travel_booking_count', $booking_count );
	}
	return $booking_count;
}

/*
 * ADMIN COLUMN - HEADERS
 */
add_filter( 'manage_edit-' . WP_TRAVEL_POST_TYPE . '_columns', 'wptravel_itineraries_columns' );

/**
 * Customize Admin column.
 *
 * @param  array $itinerary_columns List of columns.
 * @return array                  [description]
 */
function wptravel_itineraries_columns( $itinerary_columns ) {
	$comment = isset( $itinerary_columns['comments'] ) ? $itinerary_columns['comments'] : '';
	$date    = $itinerary_columns['date'];
	unset( $itinerary_columns['date'] );
	unset( $itinerary_columns['comments'] );

	$itinerary_columns['booking_count'] = __( 'Booking', 'wp-travel' );
	$itinerary_columns['featured']      = __( 'Featured', 'wp-travel' );
	if ( $comment ) {
		$itinerary_columns['comments'] = $comment;
	}
	$itinerary_columns['date'] = __( 'Date', 'wp-travel' );
	return $itinerary_columns;
}

/*
 * ADMIN COLUMN - CONTENT
 */
add_action( 'manage_' . WP_TRAVEL_POST_TYPE . '_posts_custom_column', 'wptravel_itineraries_manage_columns', 10, 2 );

/**
 * Add data to custom column.
 *
 * @param  String $column_name Custom column name.
 * @param  int    $id          Post ID.
 */
function wptravel_itineraries_manage_columns( $column_name, $id ) {
	switch ( $column_name ) {
		case 'booking_count':
			$booking_count = wptravel_get_booking_count( $id );
			echo esc_html( $booking_count );
			break;
		case 'featured':
			$featured = get_post_meta( $id, 'wp_travel_featured', true );
			$featured = ( isset( $featured ) && '' !== $featured ) ? $featured : 'no';

			$icon_class = ' dashicons-star-empty ';
			if ( ! empty( $featured ) && 'yes' === $featured ) {
				$icon_class = ' dashicons-star-filled ';
			}
			$nonce = wp_create_nonce( 'wp_travel_featured_nounce' );
			printf( wp_kses_post( '<a href="#" class="wp-travel-featured-post dashicons %s" data-post-id="%d"  data-nonce="%s"></a>' ), esc_attr( $icon_class ), esc_attr( $id ), esc_attr( $nonce ) );
			break;
		default:
			break;
	} // end switch
}

/**
 * Sort the itineraries in admin column.
 *
 * @param array $columns Columns array.
 */
function wptravel_itineraries_sort( $columns ) {

	$custom = array(
		'booking_count' => 'booking_count',
	);
	return wp_parse_args( $custom, $columns );
}

/*
 * ADMIN COLUMN - SORTING - MAKE HEADERS SORTABLE
 * https://gist.github.com/906872
 */
add_filter( 'manage_edit-' . WP_TRAVEL_POST_TYPE . '_sortable_columns', 'wptravel_itineraries_sort' );

/*
 * ADMIN COLUMN - SORTING - ORDERBY
 * http://scribu.net/wordpress/custom-sortable-columns.html#comment-4732
 */
add_filter( 'request', 'wptravel_itineraries_column_orderby' );

/**
 * Manage Order By custom column.
 *
 * @param  Array $vars Order By array.
 * @return Array       Order By array.
 */
function wptravel_itineraries_column_orderby( $vars ) {
	if ( isset( $vars['orderby'] ) && 'booking_count' === $vars['orderby'] ) {
		$vars = array_merge(
			$vars,
			array(
				'meta_key' => 'wp_travel_booking_count',
				'orderby'  => 'meta_value',
			)
		);
	}
	return $vars;
}

/**
 * Ajax for adding feature aditem.
 */
function wptravel_featured_admin_ajax() {

	if ( ! isset( $_POST['nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'wp_travel_featured_nounce' ) ) {
		exit( 'invalid' );
	}

	header( 'Content-Type: application/json' );
	if ( ! isset( $_POST['post_id'] ) ) {
		return;
	}
	$post_id         = absint( $_POST['post_id'] );
	$featured_status = esc_attr( get_post_meta( $post_id, 'wp_travel_featured', true ) );
	$new_status      = 'yes' === $featured_status ? 'no' : 'yes';
	update_post_meta( $post_id, 'wp_travel_featured', $new_status );
	echo wp_json_encode(
		array(
			'ID'         => $post_id,
			'new_status' => $new_status,
		)
	);
	die();
}
add_action( 'wp_ajax_wp_travel_featured_post', 'wptravel_featured_admin_ajax' );

/**
 * Metabox publish WP Travel.
 */
function wptravel_publish_metabox() {
	global $post;
	if ( get_post_type( $post ) === 'itinerary-booking' ) {
		?>
		<div class="misc-pub-section misc-pub-booking-status">
			<?php
			$status    = wptravel_get_booking_status();
			$label_key = get_post_meta( $post->ID, 'wp_travel_booking_status', true );
			?>

			<label for="wp-travel-post-id"><?php esc_html_e( 'Booking Status', 'wp-travel' ); ?></label>
			<select id="wp_travel_booking_status" name="wp_travel_booking_status" >
			<?php foreach ( $status as $value => $st ) : ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $label_key ); ?>>
					<?php echo esc_html( $status[ $value ]['text'] ); ?>
				</option>
			<?php endforeach; ?>
			</select>
		</div>
		<?php
	}
}
add_action( 'post_submitbox_misc_actions', 'wptravel_publish_metabox' );

/*
 * ADMIN COLUMN - HEADERS
 */
add_filter( 'manage_edit-itinerary-booking_columns', 'wptravel_booking_payment_columns', 20 );

/**
 * Customize Admin column.
 *
 * @param  Array $booking_columns List of columns.
 * @return Array                  [description]
 */
function wptravel_booking_payment_columns( $booking_columns ) {

	$date = $booking_columns['date'];
	unset( $booking_columns['date'] );

	$booking_columns['payment_mode']   = __( 'Payment Mode', 'wp-travel' );
	$booking_columns['payment_status'] = __( 'Payment Status', 'wp-travel' );
	$booking_columns['date']           = $date;
	return $booking_columns;
}



/**
 * Add data to custom column.
 *
 * @param  String $column_name Custom column name.
 * @param  int    $id          Post ID.
 */
function wptravel_booking_payment_manage_columns( $column_name, $id ) {
	$payment_info = wptravel_booking_data( $id );
	switch ( $column_name ) {
		case 'payment_status':
			$payment_id = $payment_info['payment_id'];

			if ( is_array( $payment_id ) ) {
				if ( count( $payment_id ) > 0 ) {
					$payment_id = $payment_id[0];
				}
			}

			$label_key = get_post_meta( $payment_id, 'wp_travel_payment_status', true );
			if ( ! $label_key ) {
				$label_key = 'N/A';
				update_post_meta( $payment_id, 'wp_travel_payment_status', $label_key );
			}
			$status = wptravel_get_payment_status();
			echo '<span class="wp-travel-status wp-travel-payment-status" style="background: ' . esc_attr( $status[ $label_key ]['color'] ) . ' ">' . esc_html( $status[ $label_key ]['text'] ) . '</span>';
			break;
		case 'payment_mode':
			echo '<span >' . esc_html( $payment_info['payment_mode'] ) . '</span>';
			break;
		default:
			break;
	} // end switch
}
/**
 * ADMIN COLUMN - CONTENT
 */
add_action( 'manage_itinerary-booking_posts_custom_column', 'wptravel_booking_payment_manage_columns', 10, 2 );

/**
 * Manage Order By custom column.
 *
 * @param  Array $vars Order By array.
 * @since 1.0.0
 * @return Array       Order By array.
 */
function wptravel_booking_payment_column_orderby( $vars ) {
	if ( isset( $vars['orderby'] ) && 'payment_status' === $vars['orderby'] ) {
		$vars = array_merge(
			$vars,
			array(
				'meta_key' => 'wp_travel_payment_status',
				'orderby'  => 'meta_value',
			)
		);
	}
	if ( isset( $vars['orderby'] ) && 'payment_mode' === $vars['orderby'] ) {
		$vars = array_merge(
			$vars,
			array(
				'meta_key' => 'wp_travel_payment_mode',
				'orderby'  => 'meta_value',
			)
		);
	}
	return $vars;
}
add_filter( 'request', 'wptravel_booking_payment_column_orderby' );

/**
 * Create a page and store the ID in an option.
 *
 * @param mixed  $slug Slug for the new page.
 * @param string $option Option name to store the page's ID.
 * @param string $page_title (default: '') Title for the new page.
 * @param string $page_content (default: '') Content for the new page.
 * @param int    $post_parent (default: 0) Parent for the new page.
 * @return int page ID
 */
function wptravel_create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0 ) {
	global $wpdb;

	$option_value = get_option( $option );
	$page_object  = get_post( $option_value );
	if ( $option_value > 0 && ( $page_object ) ) {
		if ( 'page' === $page_object->post_type && ! in_array( $page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ), true ) ) {
			// Valid page is already in place.
			if ( strlen( $page_content ) > 0 ) {
				// Search for an existing page with the specified page content (typically a shortcode).
				$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
			} else {
				// Search for an existing page with the specified page slug.
				$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", $slug ) );
			}

			$valid_page_found = apply_filters( 'wp_travel_create_page_id', $valid_page_found, $slug, $page_content ); // @phpcs:ignore
			$valid_page_found = apply_filters( 'wptravel_create_page_id', $valid_page_found, $slug, $page_content );

			if ( $valid_page_found ) {
				if ( $option ) {
					update_option( $option, $valid_page_found );
				}
				return $valid_page_found;
			}
		}
	}

	// Search for a matching valid trashed page.
	if ( strlen( $page_content ) > 0 ) {
		// Search for an existing page with the specified page content (typically a shortcode).
		$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
	} else {
		// Search for an existing page with the specified page slug.
		$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug ) );
	}

	if ( $trashed_page_found ) {
		$page_id   = $trashed_page_found;
		$page_data = array(
			'ID'          => $page_id,
			'post_status' => 'publish',
		);
		wp_update_post( $page_data );
	} else {
		$page_data = array(
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => 1,
			'post_name'      => $slug,
			'post_title'     => $page_title,
			'post_content'   => $page_content,
			'post_parent'    => $post_parent,
			'comment_status' => 'closed',
		);
		$page_id   = wp_insert_post( $page_data );
	}

	if ( $option ) {
		update_option( $option, $page_id );
	}

	return $page_id;
}

/**
 * Tour Extras Multiselect Options.
 *
 * @param int    $post_id Post ID.
 * @param bool   $context Context.
 * @param string $fetch_key Which key to fetch.
 * @param bool   $table_row Display table row.
 */
function wptravel_admin_tour_extra_multiselect( $post_id, $context = false, $fetch_key = '', $table_row = false ) {
	$tour_extras = wp_count_posts( 'tour-extras' );
	// Check Tour Extras Count.
	if ( 0 === $tour_extras->publish ) {
		ob_start();
		if ( $table_row ) :
			?>
			<td>
			<?php
		else :
			?>
			<div class="one-third">
			<?php
		endif;
		?>
		<label for=""><?php esc_html_e( 'Trip Extras', 'wp-travel' ); ?></label>
		<?php
		if ( $table_row ) :
			?>
			</td>
			<td>
			<?php
		else :
			?>
			</div>
			<div class="two-third">
			<?php
		endif;
		?>
		<p class="wp-travel-trip-extra-notice good" id="pass-strength-result"><span class="dashicons dashicons-info"></span> Please <a href="post-new.php?post_type=tour-extras">Click here </a> to add Trip Extra first.</p>
		<?php
		if ( $table_row ) :
			?>
			</td>
			<?php
		else :
			?>
			</div>
			<?php
		endif;
		$data = ob_get_clean();
		return $data;
	}
	if ( empty( $post_id ) || empty( $fetch_key ) ) {
		return;
	}
	$name = 'wp_travel_tour_extras[]';
	if ( $context && 'pricing_options' === $context ) {
		$pricing_options = get_post_meta( $post_id, 'wp_travel_pricing_options', true );
		$trip_extras     = isset( $pricing_options[ $fetch_key ]['tour_extras'] ) && ! empty( $pricing_options[ $fetch_key ]['tour_extras'] ) ? $pricing_options[ $fetch_key ]['tour_extras'] : false;
		$name            = 'wp_travel_pricing_options[' . $fetch_key . '][tour_extras][]';
	} elseif ( ! $context && 'wp_travel_tour_extras' === $fetch_key ) {
		$trip_extras = get_post_meta( $post_id, 'wp_travel_tour_extras', true );
	}
	$restricted_trips = ( $trip_extras ) ? $trip_extras : array();
	$restricted_trips = array_map( 'intval', $restricted_trips ); // Typecase all value inside array into integer.
	$itineraries      = wptravel_get_tour_extras_array();
	ob_start();
	if ( $table_row ) :
		?>
		<td>
		<?php
	else :
		?>
		<div><div class="one-third">
		<?php
	endif;
	?>
		<label for=""><?php echo esc_html__( 'Trip Extras', 'wp-travel' ); ?></label>
	<?php
	if ( $table_row ) :
		?>
		</td><td>
		<?php
	else :
		?>
		</div><div class="two-third">
		<?php
	endif;
	?>
	<div class="custom-multi-select">
		<?php
		$count_options_data   = count( $restricted_trips );
		$count_itineraries    = count( $itineraries );
		$multiple_checked_all = '';
		if ( $count_options_data === $count_itineraries ) {
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
					if ( in_array( $key, $restricted_trips, true ) ) {
						$checked            = 'checked=checked';
						$selecte_list_class = 'selected';
					}
					?>
					<li class="wp-travel-multi-inner <?php echo esc_attr( $selecte_list_class ); ?>">
						<label class="checkbox wp-travel-multi-inner ">
							<input <?php echo esc_attr( $checked ); ?>  name="<?php echo esc_attr( $name ); ?>" type="checkbox" id="wp-travel-multi-input-<?php echo esc_attr( $key ); ?>" class="wp-travel-multi-inner multiselect-value" value="<?php echo esc_attr( $key ); ?>">  <?php echo esc_html( $iti ); ?>
						</label>
					</li>
				<?php } ?>
			</ul>
		</span>
		<?php if ( ! class_exists( 'WP_Travel_Tour_Extras_Core' ) ) : ?>
			<p class="description">
				<?php printf( esc_html__( 'Need advance Trip Extras options? %1$s GET PRO%2$s', 'wp-travel' ), '<a href="https://wptravel.io/wp-travel-pro/" target="_blank" class="wp-travel-upsell-badge">', '</a>' ); // @phpcs:ignore ?>
			</p>
		<?php endif; ?>
	</div>
	<?php
	if ( $table_row ) :
		?>
		</td>
		<?php
	else :
		?>
		</div></div>
		<?php
	endif;
	// @since 2.0.3
	do_action( 'wp_travel_trip_extras_fields', $post_id, $context, $fetch_key, $table_row ); // @phpcs:ignore
	do_action( 'wptravel_trip_extras_fields', $post_id, $context, $fetch_key, $table_row );
	$data = ob_get_clean();
	return $data;
}

add_action( 'wp_travel_extras_pro_options', 'wptravel_extras_pro_option_fields' );

/**
 * WP Travel Tour Extras Pro fields.
 *
 * @return void
 */
function wptravel_extras_pro_option_fields() {

	$is_pro_enabled = apply_filters( 'wp_travel_extras_is_pro_enabled', false ); // @phpcs:ignore
	$is_pro_enabled = apply_filters( 'wptravel_extras_is_pro_enabled', $is_pro_enabled );

	if ( $is_pro_enabled ) {
		do_action( 'wp_travel_extras_pro_single_options' ); // @phpcs:ignore
		do_action( 'wptravel_extras_pro_single_options' );
		return;
	}
	if ( class_exists( 'WP_Travel_Pro' ) ) {
		$settings = wptravel_get_settings();
		$modules  = $settings['modules'];
		if ( isset( $modules['show_wp_travel_tour_extras'] ) ) {
			$active = 'yes' === $modules['show_wp_travel_tour_extras'];
			if ( ! $active ) {
				?>
				<tr class="pro-options-note"><td colspan="2"><p>Trip Extras is currently disabled please go to Modules Settings to enable it and reload the page.</p></td></tr>
				<?php
			}
		}
		return;
	}
	?>
<tr class="pro-options-note"><td colspan="10"><?php esc_html_e( 'Pro options', 'wp-travel' ); ?></td></tr>
<tr class="wp-travel-pro-mockup-option">
<td><label for="extra-item-price"><?php esc_html_e( 'Price', 'wp-travel' ); ?></label>
	<span class="tooltip-area" title="<?php esc_html_e( 'Item Price', 'wp-travel' ); ?>">
		<i class="wt-icon wt-icon-question-circle" aria-hidden="true"></i>
	</span>
</td>
<td>
	<span id="coupon-currency-symbol" class="wp-travel-currency-symbol">
			<?php echo wptravel_get_currency_symbol(); //phpcs:ignore ?>
	</span>
	<input disabled="disabled" type="number" min="1" step="0.01" id="extra-item-price" placeholder="<?php echo esc_attr__( 'Price', 'wp-travel' ); ?>" >
</td>
</tr>
<tr class="wp-travel-pro-mockup-option">
<td><label for="extra-item-sale-price"><?php esc_html_e( 'Sale Price', 'wp-travel' ); ?></label>
	<span class="tooltip-area" titl.e="<?php esc_html_e( 'Sale Price(Leave Blank to disable sale)', 'wp-travel' ); ?>">
		<i class="wt-icon wt-icon-question-circle" aria-hidden="true"></i>
	</span>
</td>
<td>
	<span id="coupon-currency-symbol" class="wp-travel-currency-symbol">
		<?php echo wptravel_get_currency_symbol(); //phpcs:ignore ?>
	</span>
	<input type="number" min="1" step="0.01" id="extra-item-sale-price" placeholder="<?php echo esc_attr__( 'Sale Price', 'wp-travel' ); ?>" disabled="disabled" >
</td>
</tr>
<tr class="wp-travel-pro-mockup-option">
<td><label for="extra-item-price-per"><?php esc_html_e( 'Price Per', 'wp-travel' ); ?></label>
</td>
<td>
	<select disabled="disabled" id="extra-item-price-per">
		<option value="unit"><?php esc_html_e( 'Unit', 'wp-travel' ); ?></option>
		<option value="person"><?php esc_html_e( 'Person', 'wp-travel' ); ?></option>
	</select>
</td>
</tr>
<tr class="wp-travel-upsell-message">
<td colspan="2">
	<?php
	if ( ! class_exists( 'WP_Travel_Tour_Extras_Core' ) ) :
		$args = array(
			'title'      => __( 'Want to use above pro features?', 'wp-travel' ),
			'content'    => __( 'By upgrading to Pro, you can get features with gallery, detail extras page in Front-End and more !', 'wp-travel' ),
			'link'       => 'https://wptravel.io/wp-travel-pro/',
			'link_label' => __( 'Get WP Travel Pro', 'wp-travel' ),
		);
		wptravel_upsell_message( $args );
		endif;
	?>
</td>
</tr>

	<?php
}

/**
 * Check if current page is WP Travel admin page.
 *
 * @param  array $pages Pages to check.
 * @return boolean
 */
function wptravel_is_admin_page( $pages = array() ) {
	if ( ! is_admin() ) {
		return false;
	}
	$screen            = get_current_screen();
	$wp_travel_pages[] = array( 'itinerary-booking_page_settings' );
	if ( ! empty( $pages ) ) {
		foreach ( $pages as $page ) {
			if ( 'settings' === $page ) {
				$settings_allowed_screens = array( 'itinerary-booking_page_settings', 'itinerary-booking_page_settings2' );
				if ( in_array( $screen->id, $settings_allowed_screens, true ) ) {
					return true;
				}
			}
		}
	} elseif ( in_array( $screen->id, $wp_travel_pages, true ) ) {
		return true;
	}

	return false;
}

/**
 * WP Travel Pricing Option List.
 */
function wptravel_get_pricing_option_list() {
	$type = array(
		'multiple-price' => __( 'Multiple Price', 'wp-travel' ),
	);

	$hide_single_for_new_user = get_option( 'wp_travel_user_after_multiple_pricing_category' );  // @since 3.0.0

	if ( 'yes' !== $hide_single_for_new_user ) { // Single pricing is only available for old user who is using it.
		$type['single-price'] = __( 'Single Price', 'wp-travel' );
	}

	$type = apply_filters( 'wp_travel_pricing_option_list', $type ); // @phpcs:ignore
	return apply_filters( 'wptravel_pricing_option_list', $type );
}

/**
 * Upsell message WP Travel.
 *
 * @param array $args Arguments.
 */
function wptravel_upsell_message( $args ) {
	$defaults   = array(
		'type'               => array( 'wp-travel-pro' ),
		'title'              => __( 'Get WP Travel PRO', 'wp-travel' ),
		'content'            => __( 'Get addon for Payment, Trip Extras, Inventory Management, Field Editor and other premium features.', 'wp-travel' ),
		'link'               => 'https://wptravel.io/wp-travel-pro/',
		'link_label'         => __( 'Get WP Travel Pro', 'wp-travel' ),
		'main_wrapper_class' => array( 'wp-travel-upsell-message-wide' ),
	);
	$args       = wp_parse_args( $args, $defaults );
	$add_groups = array(
		'maps'     => array( 'wp-travel-here-map' ),
		'payments' => array( 'wp-travel-paypal-express-checkout' ),
	);
	$types      = $args['type'];
	if ( is_string( $types ) ) {
		$types = isset( $add_groups[ $args['type'] ] ) ? $add_groups[ $args['type'] ] : $types;
	}

	$types[]     = 'wp-travel-pro';
	$show_upsell = apply_filters( 'wp_travel_show_upsell_message', true, $types ); // @phpcs:ignore
	$show_upsell = apply_filters( 'wptravel_show_upsell_message', $show_upsell, $types );

	if ( ! $show_upsell ) {
		return;
	}
	?>
<div class="wp-travel-upsell-message wp-travel-pro-feature-notice clearfix <?php echo esc_attr( implode( ' ', $args['main_wrapper_class'] ) ); ?>">
<!-- <div class=""> -->
	<div class="section-one">
		<h4><?php echo esc_html( $args['title'] ); ?></h4>
		<p><?php echo wp_kses_post( $args['content'] ); ?></p>
	</div>
	<div class="section-two">
	<div class="buy-pro-action buy-pro">
		<a target="_blank" href="<?php echo esc_url( $args['link'] ); ?>" class="action-btn" ><?php echo esc_html( $args['link_label'] ); ?></a>
		<?php if ( ! empty( $args['link2'] ) ) : ?>
		<p>
			<?php esc_html_e( 'or', 'wp-travel' ); ?> <a target="_blank" class="link-default" href="<?php echo esc_url( $args['link2'] ); ?>"><?php echo esc_html( $args['link2_label'] ); ?></a>
		</p>
		<?php endif; ?>
		</div>
		<?php if ( ! empty( $args['link3'] ) ) : ?>
		<div class="buy-pro-action action2">
			<a target="_blank" href="<?php echo esc_url( $args['link3'] ); ?>" class="action-btn" ><?php echo esc_html( $args['link3_label'] ); ?></a>
		</div>
		<?php endif; ?>
	</div>
<!-- </div> -->
</div>
	<?php
}

/**
 * Checks if specific wp travel addon is active or not.
 * Enter addon name as 'WP Travel Downloads'
 *
 * @param  string $plugin_name Plugin name as that you want to check.
 * @return boolean
 */
function wptravel_is_plugin_active( $plugin_name ) {
	$plugin_upper = ucfirst( $plugin_name );
	$plugin_class = str_replace( ' ', '_', $plugin_upper );

	$plugin_lower = strtolower( $plugin_name );
	$plugin_name  = str_replace( ' ', '_', $plugin_lower );

	$settings          = wptravel_get_settings();
	$is_plugin_enabled = isset( $settings[ 'show_' . $plugin_name ] ) && ! empty( $settings[ 'show_' . $plugin_name ] ) ? $settings[ 'show_' . $plugin_name ] : 'yes';
	$does_class_exists = class_exists( $plugin_class ) || class_exists( $plugin_class . '_Core' ) ? true : false;
	if ( ! $does_class_exists || 'yes' !== $is_plugin_enabled ) {
		return false;
	}
	return true;
}
