<?php
/**
 * WP Travel Pro init
 *
 * @package WP_Travel
 */

/**
 * WP travel Pro Init class.
 *
 * @class Wp_Travel_Pro_Init
 */
class Wp_Travel_Pro_Init {

	/**
	 * The single instance of the class.
	 *
	 * @var WP Travel
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * WP Travel Pro version.
	 *
	 * @var string
	 */
	public $version = '1.3.1';

	/**
	 * Main WP_Travel Instance.
	 * Ensures only one instance of WP_Travel is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Wp_Travel_Pro_Init()
	 * @return Wp_Travel_Pro_Init - Pro Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	/**
	 * Constructor method.
	 */
	public function __construct() {

		$this->define_constants();
		$this->includes();
		$this->init_hooks();

	}

	/**
	 * Define WP Travel Pro Constants.
	 */
	private function define_constants() {
		$this->define( 'WP_TRAVEL_PRO_ABSPATH', dirname( __FILE__ ) . '/' );
		$this->define( 'WP_TRAVEL_PRO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'WP_TRAVEL_PRO_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		$this->define( 'WP_TRAVEL_PRO_TEMPLATE_PATH', 'wp-travel/' );
		$this->define( 'WP_TRAVEL_PRO_VERSION', $this->version );
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function init_hooks() {

		// add_filter( 'wp_travel_template_path', array( $this, 'wp_travel_template_path_override' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );

	}
	/**
	 * Define.
	 *
	 * @param string $name name of constant.
	 * @param string $value value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}
	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @return void
	 */
	public function includes() {

		include sprintf( '%s/inc/wp-travel-user-functions.php', WP_TRAVEL_PRO_ABSPATH );
		include sprintf( '%s/inc/class-wp-travel-user-account.php', WP_TRAVEL_PRO_ABSPATH );
		include sprintf( '%s/inc/class-wp-travel-pro-shortcodes.php', WP_TRAVEL_PRO_ABSPATH );
		include sprintf( '%s/inc/class-wp-travel-form-handler.php', WP_TRAVEL_PRO_ABSPATH );		

	}
	/**
	 * Override template path.
	 */
	public function wp_travel_template_path_override() {

		return WP_TRAVEL_PRO_ABSPATH . '/templates';

	}
	/**
	 * Frontend scripts.
	*/
	public function frontend_scripts() {

		wp_enqueue_style( 'wp-travel-pro-frontend-css', plugin_dir_url( __FILE__ ) . 'assets/css/wp-travel-frontend-pro.css' );
	}
}
/**
 * Main instance of WP Travel Pro Init.
 *
 * Returns the main instance of WP_Travel to prevent the need to use globals.
 *
 * @since  1.3.3
 * @return WP Travel Pro Init
 */
function Wp_Travel_Pro_Init() {
	return Wp_Travel_Pro_Init::instance();
}

// Start WP Travel Pro.
Wp_Travel_Pro_Init();
