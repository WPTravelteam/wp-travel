<?php
/**
 * Admin Info Pointers
 *
 * @package WP Travel
 * @author WEN Solutions
 */

/**
 * Admin Info Pointers Class
 */
class WP_Travel_Admin_Info_Pointers {

function __construct() {

    add_filter( 'wp_travel_admin_pointers-plugins', array( $this, 'add_pointers' ) );

    add_action('admin_enqueue_scripts', array( $this, 'load_pointers' ), 999 );
    

}
/**
 * Main function for pointers.
 *
 * @since  1.1.0
 */
function load_pointers( $hook_suffix ) {
     
    // Don't run on WP < 3.3
    if ( get_bloginfo( 'version' ) < '3.3' )
    return;

    $screen = get_current_screen();

    $screen_id = $screen->id;

    // Get pointers for this screen
    $pointers = apply_filters( 'wp_travel_admin_pointers-'.$screen_id, array() );

    if ( ! $pointers || ! is_array( $pointers ) )
    return;

    // Get dismissed pointers
    $dismissed = get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true );
    $dismissed = explode( ',', $dismissed );

    $valid_pointers =array();

    // Check pointers and remove dismissed ones.
    foreach ( $pointers as $pointer_id => $pointer ) {

    // Sanity check
    if ( in_array( $pointer_id, $dismissed ) || empty( $pointer )  || empty( $pointer_id ) || empty( $pointer['target'] ) || empty( $pointer['options'] ) )
        continue;

    $pointer['pointer_id'] = $pointer_id;

    // Add the pointer to $valid_pointers array
    $valid_pointers['pointers'][] =  $pointer;

    }

    // No valid pointers? Stop here.
    if ( empty( $valid_pointers ) )
    return;

     // Add pointers style to queue.
     wp_enqueue_style( 'wp-pointer' );
    
     // Add pointers script to queue. Add custom script.
     wp_enqueue_script( 'wp-travel-admin-pointers-js', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . '/assets/js/wp-travel-backend-pointers.js', array( 'wp-pointer' ) );
 
     // Add pointer options to script.
     wp_localize_script( 'wp-travel-admin-pointers-js', 'wpctgPointer', $valid_pointers );
}

    /**
    * Pointer for Appearance on plugin activation.
    *
    * @since    1.1.0
    */
    function add_pointers( $q ) {
        
        $q['wp_travel_cng_info'] = array(
            'target' => '#menu-posts-trip',
            'options' => array(
                'content' => sprintf( '<h3> %s </h3> <p> %s </p>',
                __( 'WP Travel Options' ,'wp-travel'),
                __( 'Configure new settings from custom menu page for WP Travel here','wp-travel')
            ),
                'position' => array( 'edge' => 'left', 'align' => 'center' )
            )
        );

        return $q;
    }
}

new WP_Travel_Admin_Info_Pointers();
