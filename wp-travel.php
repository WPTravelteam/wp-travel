<?php
/**
 * Plugin Name: WP Travel
 * Plugin URI: http://www.wensolutions.com/plugins/trip
 * Description: This plugin is used to add trip for any travel and tour site
 * Version: 1.0.0
 * Author: WEN Solutions
 * Author URI: http://wensolutions.com
 * Requires at least: 4.4
 * Tested up to: 4.7
 *
 * Text Domain: trip
 * Domain Path: /i18n/languages/
 *
 * @package trip
 * @category Core
 * @author WenSolutions
 */

include sprintf( '%s/inc/template-functions.php', dirname( __FILE__ ) );
include sprintf( '%s/inc/class-admin-post-tabs.php', dirname( __FILE__ ) );
include sprintf( '%s/inc/class-post-types.php', dirname( __FILE__ ) );
include sprintf( '%s/inc/class-taxonomies.php', dirname( __FILE__ ) );
include sprintf( '%s/inc/class-admin-metaboxes.php', dirname( __FILE__ ) );

add_action( 'init', array( 'WP_Travel_Post_Types', 'init' ) );
add_action( 'init', array( 'Wp_Travel_Taxonomies', 'init' ) );

/**
 * Traval Door Scripts and styles.
 */
function travaldoor_admin_scripts() {
	$screen = get_current_screen();
	if ( 'itineraries' === $screen->id ) {
		wp_enqueue_style( 'traval-door-style', plugin_dir_url( __FILE__ ) . 'style.css' );
		wp_enqueue_style( 'traval-door-style1', plugin_dir_url( __FILE__ ) . 'assets/css/wp-travel-back-end.css' );
		wp_enqueue_script( 'traval-door-script', plugin_dir_url( __FILE__ ) . 'assets/js/wp-travel-back-end.js', array( 'jquery', 'jquery-ui-tabs' ), '', 1 );
	}
}

add_action( 'admin_enqueue_scripts', 'travaldoor_admin_scripts' );
