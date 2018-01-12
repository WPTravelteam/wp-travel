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

    add_filter( 'wp_travel_admin_pointers-plugins', array( $this, 'add_plugin_pointers' ) );

    add_filter( 'wp_travel_admin_pointers-'.WP_TRAVEL_POST_TYPE, array( $this, 'add_single_post_edit_screen_pointers' ) );

    add_filter( 'wp_travel_admin_pointers-dashboard', array( $this, 'add_dashboard_screen_pointers' ) );


    add_action('admin_enqueue_scripts', array( $this, 'load_pointers' ), 999 );

    add_action( 'admin_notices', array( $this, 'paypal_addon_admin_notice' ) );
    

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
    function add_plugin_pointers( $q ) {

        $pointer_1_content = '<ul class="changes-list">
            <li>Itineraries menu changed to Trips.</li>
            <li>Locations menu changed to Destinations.</li>
            <li>Trips can be group by activities.</li>
            <li>Marketplace: Check WP travel addons &amp; Themes.</li>
            <li>View other changes <a target="_blank" href="http://wptravel.io/wp-travel-1-1-0-release-note/">here</a>.</li>
        </ul>';
        
        $q['wp_travel_post_type_chges'] = array(
            'target' => '#menu-posts-'.WP_TRAVEL_POST_TYPE,
            'options' => array(
                'content' => sprintf( '<h3 class="update-notice"> %s </h3> <p> %s </p>',
                __( 'New in WP Travel v.1.1.0' ,'wp-travel'),
                $pointer_1_content
            ),
                'position' => array( 'edge' => 'left', 'align' => 'center' )
            )
        );

        return $q;
    }

    /**
    * Pointer for Appearance on plugin activation.
    *
    * @since    1.1.0
    */
    function add_single_post_edit_screen_pointers( $q ) {
        
        $q['wp_travel_post_edit_page_cngs'] = array(
            'target' => '#wp-travel-trip-info',
            'options' => array(
                'content' => sprintf( '<h3 class="update-notice"> %s </h3> <p> %s </p>',
                __( 'New in WP Travel v.1.1.0' ,'wp-travel'),
                __('"Trip Code" has been moved to sidebar "Trip Info" metabox. ', 'wp-travel' )
            ),
                'position' => array( 'edge' => 'right', 'align' => 'center' )
            )
        );

        $content = '<ul class="changes-list">
        <li><strong>"Group Size"</strong> has been moved <strong>"Additional info"</strong> tab.</li>
        <li><strong>"Outline"</strong> has been moved <strong>"Itinerary"</strong> tab.</li>
        <li><strong>"Trip Includes" & "Trip Excludes" </strong> has been moved <strong>"Includes / Excludes"</strong> tab.</li>
        <li>Number of Nights added in <strong>"Trip Duration"</strong></li>
        <li>View other changes <a target="_blank" href="http://wptravel.io/wp-travel-1-1-0-release-note/">here</a>.</li>
    </ul>';

        $q['wp_travel_post_edit_page_cngs_2'] = array(
            'target' => '#wp-travel-tab-additional_info',
            'options' => array(
                'content' => sprintf( '<h3 class="update-notice"> %s </h3> <p> %s </p>',
                __( 'New in WP Travel v.1.1.0' ,'wp-travel'),
                $content
            ),
                'position' => array( 'edge' => 'left', 'align' => 'center' )
            )
        );


        return $q;
    }

    /**
    * Pointer for Appearance on plugin activation.
    *
    * @since    1.1.0
    */
    function add_dashboard_screen_pointers( $q ) {

        $pointer_content = 'WP travel archive slugs for Trips, Destinations, Trip Types & Activities can be changed from Permalinks page.
        <li>View other changes <a target="_blank" href="http://wptravel.io/wp-travel-1-1-0-release-note/">here</a>.</li>';
        
        $q['wp_travel_post_type_chges'] = array(
            'target' => '#menu-settings',
            'options' => array(
                'content' => sprintf( '<h3 class="update-notice"> %s </h3> <p> %s </p>',
                __( 'WP Travel permalink options' ,'wp-travel'),
                $pointer_content
            ),
                'position' => array( 'edge' => 'left', 'align' => 'center' )
            )
        );

        return $q;
    }

    function paypal_addon_admin_notice(){

        if ( ! is_plugin_active( 'wp-travel-standard-paypal/wp-travel-paypal.php' ) ) {
           
            $class = 'notice notice-info is-dismissible';

        ?>

            <div class="<?php echo esc_attr( $class ) ?>">
                <p>
                    <strong><?php printf( __( 'Want to add payment gateway in WP Travel booking? %1sDownload "Standard PayPal"%2s addon for free!!', 'wp-travel' ), '<a target="_blank" href="http://wptravel.io/downloads/standard-paypal/">', '</a>' ); ?></strong>
                </p>
            </div>
        
        <?php 
        
        } 

    }

}

new WP_Travel_Admin_Info_Pointers();
