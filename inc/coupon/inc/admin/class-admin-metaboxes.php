<?php
/**
 * Metabox for Iteneraries fields.
 *
 * @package wp-travel-coupons-pro\inc\admin
 */

/**
 * WP_Travel_Admin_Coupon_Metaboxes Class.
 */
class WP_Travel_Admin_Coupon_Metaboxes {
	/**
	 * Private var $post_type.
	 *
	 * @var string
	 */
	private static $post_type = 'wp-travel-coupons';
	/**
	 * Public var $views_path.
	 *
	 * @var string
	 */
	public $views_path = WP_TRAVEL_COUPON_PRO_ABSPATH . '/inc/admin/views/tabs/';
	/**
	 * Constructor WP_Travel_Admin_Coupon_Metaboxes.
	 */
	public function __construct() {
		// Add coupons metabox.
		add_action( 'add_meta_boxes', array( $this, 'register_metaboxes' ), 10, 2 );
		// Save Metabox data.
		add_action( 'save_post', array( $this, 'save_coupons_metabox_data' ) );
		add_filter( 'wp_travel_admin_tabs', array( $this, 'add_tabs' ) );
		add_filter( 'postbox_classes_' . self::$post_type . '_' . self::$post_type . '-detail', array( $this, 'add_clean_metabox_class' ), 30 );
		add_action( 'wp_travel_tabs_content_' . self::$post_type, array( $this, 'coupons_general_tab_callback' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_' . self::$post_type, array( $this, 'coupons_restrictions_tab_callback' ), 10, 2 );

	}
	/**
	 * Register metabox.
	 */
	public function register_metaboxes() {
		add_meta_box( self::$post_type . '-detail', __( 'Coupon Options', 'wp-travel' ), array( $this, 'load_coupons_tab_template' ), self::$post_type, 'normal', 'high' );

	}
	/**
	 * Load Coupons Tab Template.
	 */
	public function load_coupons_tab_template( $post ) {

		// Print Errors / Notices.
		WP_Travel()->notices->print_notices( 'error', true );
		WP_Travel()->notices->print_notices( 'success', true );

		$args['post'] = $post;
		WP_Travel()->tabs->load( self::$post_type, $args );

	}
	/**
	 * Function to add tab.
	 *
	 * @param array $tabs Array list of all tabs.
	 * @return array
	 */
	public function add_tabs( $tabs ) {
		$coupons['coupons_general'] = array(
			'tab_label'     => __( 'General', 'wp-travel' ),
			'content_title' => __( 'General Settings', 'wp-travel' ),
		);

		$coupons['coupons_restrictions'] = array(
			'tab_label'     => __( 'Restrictions', 'wp-travel' ),
			'content_title' => __( 'Coupon Restrictions', 'wp-travel' ),
		);

		// $coupons['coupons_usage_limits'] = array(
		// 	'tab_label'     => __( 'Usage', 'wp-travel-coupon-pro' ),
		// 	'content_title' => __( 'Usage Options', 'wp-travel-coupon-pro' ),
		// );

		$tabs[ self::$post_type ] = $coupons;
		return apply_filters( 'wp_travel_coupons_tabs', $tabs );
	}

	/**
	 * Callback Function for Coupons General Tabs.
	 *
	 * @param  string $tab tab name 'General'.
	 * @return Mixed
	 */
	public function coupons_general_tab_callback( $tab ) {
		global $post;
		if ( 'coupons_general' !== $tab ) {
			return;
		}
		include sprintf( '%s/coupons-general.php', $this->views_path );
	}
	/**
	 * Callback Function for Coupons Restrictions Tabs.
	 *
	 * @param  string $tab tab name 'Restrictions'.
	 * @return Mixed
	 */
	public function coupons_restrictions_tab_callback( $tab ) {
		global $post;
		if ( 'coupons_restrictions' !== $tab ) {
			return;
		}
		include sprintf( '%s/coupons-restrictions.php', $this->views_path );
	}
	// /**
	//  * Callback Function for Coupons Usage Limits Tabs.
	//  *
	//  * @param  string $tab tab name 'Usage Limits'.
	//  * @return Mixed
	//  */
	// public function coupons_usage_limits_tab_callback( $tab ) {
	// 	global $post;
	// 	if ( 'coupons_usage_limits' !== $tab ) {
	// 		return;
	// 	}
	// 	include sprintf( '%s/coupons-usage-limits.php', $this->views_path );
	// }
	/**
	 * Clean Metabox Classes.
	 *
	 * @param array $classes Class list array.
	 */
	public function add_clean_metabox_class( $classes ) {
		array_push( $classes, 'wp-travel-clean-metabox' );
		return $classes;
	}

	/**
	 * Save Coupons Metabox Data
	 */
	public function save_coupons_metabox_data( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$post_type = get_post_type( $post_id );

		// If this isn't a WP_TRAVEL_COUPON_POST_TYPE post, don't update it.
		if ( WP_TRAVEL_COUPON_POST_TYPE !== $post_type ) {
			return;
		}

		if ( isset( $_POST['wp_travel_coupon_code'] ) && ! empty( $_POST['wp_travel_coupon_code'] ) ) {

			$coupon_code = $_POST['wp_travel_coupon_code'];

			update_post_meta( $post_id, 'wp_travel_coupon_code', $coupon_code );

		}

		if ( isset( $_POST['wp_travel_coupon'] ) ) {

			$coupon_metas   = $_POST['wp_travel_coupon'];
			$sanitized_data = $this->sanitize_array_values( $coupon_metas );

			update_post_meta( $post_id, 'wp_travel_coupon_metas', $sanitized_data );

		}

	}
	/**
	 * Sanitize values in the array befor save.
	 *
	 * @param array $data Data Data Array.
	 * @return array $sanitized_data Sanitized Array. 
	 */
	public function sanitize_array_values( $data ) {

		if ( empty( $data ) ) {
			return;
		}

		$sanitized_data = stripslashes_deep( $data );

		return $sanitized_data;

	}

}

new WP_Travel_Admin_Coupon_Metaboxes();
