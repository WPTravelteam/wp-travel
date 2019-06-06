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
	public $views_path = '';
	/**
	 * Constructor WP_Travel_Admin_Coupon_Metaboxes.
	 */
	public function __construct() {
		$this->views_path = sprintf( '%s/inc/admin/views/tabs/', WP_TRAVEL_COUPON_PRO_ABSPATH );
		// Add coupons metabox.
		add_action( 'add_meta_boxes', array( $this, 'register_metaboxes' ), 10, 2 );
		// Save Metabox data.
		add_action( 'save_post', array( $this, 'save_coupons_metabox_data' ) );
		add_filter( 'wp_travel_admin_tabs', array( $this, 'add_tabs' ) );
		add_filter( 'postbox_classes_' . self::$post_type . '_' . self::$post_type . '-detail', array( $this, 'add_clean_metabox_class' ), 30 );

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
		wp_travel_print_notices();

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
			'priority'      => 110,
			'callback'      => 'wp_travel_coupons_general_tab_callback',
			'icon'          => 'fa-info-circle',
		);

		$coupons['coupons_restrictions'] = array(
			'tab_label'     => __( 'Restrictions', 'wp-travel' ),
			'content_title' => __( 'Coupon Restrictions', 'wp-travel' ),
			'priority'      => 110,
			'callback'      => 'wp_travel_coupons_restrictions_tab_callback',
			'icon'          => 'fa-lock',
		);

		$tabs[ self::$post_type ] = $coupons;
		return apply_filters( 'wp_travel_coupons_tabs', $tabs );
	}

	/**
	 * Clean Metabox Classes.
	 *
	 * @param array $classes Class list array.
	 */
	public function add_clean_metabox_class( $classes ) {
		array_push( $classes, 'wp-travel-clean-metabox wp-travel-styles' );
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
