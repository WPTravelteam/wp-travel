<?php
/**
 * Metabox for Iteneraries fields.
 *
 * @package wp-travel\inc\admin\extras
 */

/**
 * WP_Travel_Admin_Tour_Extras_Metaboxes Class.
 */
class WP_Travel_Admin_Tour_Extras_Metaboxes {
	/**
	 * Private var $post_type.
	 *
	 * @var string
	 */
	private static $post_type = 'tour-extras';
	/**
	 * Public var $views_path.
	 *
	 * @var string
	 */
	public $views_path = '';
	/**
	 * Constructor WP_Travel_Admin_Tour_Extras_Metaboxes.
	 */
	public function __construct() {
		$this->views_path = WP_TRAVEL_ABSPATH . 'inc/admin/extras/views/tabs/';
		// Add Extras metabox.
		add_action( 'add_meta_boxes', array( $this, 'register_metaboxes' ), 10, 2 );
		// Save Metabox data.
		add_action( 'save_post', array( $this, 'save_extras_metabox_data' ) );
		add_filter( 'wp_travel_admin_tabs', array( $this, 'add_tabs' ), 9 );
		add_filter( 'postbox_classes_' . self::$post_type . '_' . self::$post_type . '-detail', array( $this, 'add_clean_metabox_class' ), 30 );

	}
	/**
	 * Register metabox.
	 */
	public function register_metaboxes() {
		add_meta_box( self::$post_type . '-detail', __( 'Trip Extras', 'wp-travel' ), array( $this, 'load_tour_extras_tab_template' ), self::$post_type, 'normal', 'high' );

	}
	/**
	 * Load Extras Tab Template.
	 */
	public function load_tour_extras_tab_template( $post ) {

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
		$tour_extras['tour_extras_general'] = array(
			'tab_label'     => __( 'General', 'wp-travel' ),
			'content_title' => __( 'General Settings', 'wp-travel' ),
			'priority'      => 10,
			'callback'      => 'wp_travel_tour_extras_general_tab_callback',
			'icon'          => 'fa-sticky-note',
		);
		$tour_extras['tour_extras_gallery'] = array(
			'tab_label'     => __( 'Gallery', 'wp-travel' ),
			'content_title' => __( 'Gallery', 'wp-travel' ),
			'priority'      => 20,
			'callback'      => 'wp_travel_tour_extras_gallery_tab_callback',
			'icon'          => 'fa-images',
		);


		$tabs[ self::$post_type ] = $tour_extras;
		return apply_filters( 'wp_travel_tour_extras_tabs', $tabs );
	}

	/**
	 * Callback Function for Tour Extras General Tabs.
	 *
	 * @param  string $tab tab name 'General'.
	 * @return Mixed
	 */
	public function tour_extras_general_tab_callback( $tab ) {
		global $post;
		if ( 'tour_extras_general' !== $tab ) {
			return;
		}
		include sprintf( '%s/tour-extras-general.php', $this->views_path );
	}
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
	 * Save Tour Extras Metabox Data
	 */
	public function save_extras_metabox_data( $post_id ) {

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

		// If this isn't a wp_travel_extras post, don't update it.
		if ( 'tour-extras' !== $post_type ) {
			return;
		}

		if ( isset( $_POST['wp_travel_extras'] ) ) {

			$tour_extras_metas   = $_POST['wp_travel_extras'];
			$sanitized_data = $this->sanitize_array_values( $tour_extras_metas );

			update_post_meta( $post_id, 'wp_travel_tour_extras_metas', $sanitized_data );

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
			return $data;
		}

		$sanitized_data = stripslashes_deep( $data );

		return $sanitized_data;

	}

}

new WP_Travel_Admin_Tour_Extras_Metaboxes();
