<?php
/**
 * Trip edit metabox admin file.
 *
 * @package wp-travel/upgrade.
 */

/**
 * WpTravel_Admin_Metabox_Trip_Edit class.
 */
class WpTravel_Admin_Metabox_Trip_Edit {
	/**
	 * Init.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'register_meta_box' ) );
	}

	/**
	 * Metabox register function.
	 *
	 * @return void
	 */
	public static function register_meta_box() {
		$settings        = wptravel_get_settings();
		$switch_to_react = $settings['wp_travel_switch_to_react'];
		if ( 'yes' === $switch_to_react ) {
			add_meta_box( 'wp-travel-trip-options', esc_html__( 'Trip Options', 'wp-travel' ), array( __CLASS__, 'meta_box_callback' ), WP_TRAVEL_POST_TYPE, 'advanced', 'high' );
		}
	}

	/**
	 * Callback for metabox.
	 *
	 * @return void
	 */
	public static function meta_box_callback() {
		?>
		<div id="wp-travel-trip-options-wrap"></div>
		<?php
	}


}

WpTravel_Admin_Metabox_Trip_Edit::init();
