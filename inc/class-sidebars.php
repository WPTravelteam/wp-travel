<?php
/**
 * Sidebars
 *
 * @package wp-travel\inc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WP travel Sidebars class.
 *
 */
class Wp_Travel_Sidebars {

	public function init() {

	    add_action( 'widgets_init', array( $this, 'wp_travel_register_additional_custom_sidebars' ) );
	}

	/**
	 * WP Travel Additional Sidebars.
	 *
	 * @return void
	 */
	public static function wp_travel_register_additional_custom_sidebars(  $atts, $content = '' ) {

        register_sidebar( array(
            'name' => __( 'WP Travel : Archive Sidebar', 'wp-travel' ),
            'id' => 'wp-travel-archive-sidebar',
            'description' => __( 'Widgets in this area will be shown on WP Travel Trip Archives', 'wp-travel' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ) );
		
	}
}
