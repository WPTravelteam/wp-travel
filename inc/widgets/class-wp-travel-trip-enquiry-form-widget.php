<?php
/**
 * Exit if accessed directly.
 *
 * @package wp-travel
 * @subpackage wp-travel/includes/widgets
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enquiry Form Widget.
 *
 * @author   WenSolutions
 * @category Widgets
 * @package  wp-travel/Widgets
 * @extends  WP_Widget
 */
class WP_Travel_Trip_Enquiry_Form_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Instantiate the parent object.
		parent::__construct( false, __( 'WP Travel Enquiry Form Widget', 'wp-travel' ) );
	}

	/**
	 * Widget Output.
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		if ( ! wp_script_is( 'jquery-parsley', 'enqueued' ) ) {
			// Parsley For Frontend Single Trips.
			wp_enqueue_script( 'jquery-parsley' );
		}
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		extract( $args );
		echo $before_widget;
		echo $before_title . $title . $after_title;
		wp_travel_get_enquiries_form( true );
		echo $after_widget;
	}

	/**
	 * Update Widget.
	 *
	 * @return void
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		return $instance;
	}

	/**
	 * Widget Settings/Option Form.
	 *
	 * @return void
	 */
	public function form( $instance ) {
		$title = '';
		if ( isset( $instance['title'] ) ) {
			$title = esc_attr( $instance['title'] );
		}
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'wp-travel' ); ?>:</label>
			<input type="text" value="<?php echo esc_attr( $title ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat">
		</p>
		<?php
	}
}

function wp_travel_register_wp_travel_enquiry_form_widgets() {
	register_widget( 'WP_Travel_Trip_Enquiry_Form_Widget' );
}
add_action( 'widgets_init', 'wp_travel_register_wp_travel_enquiry_form_widgets' );
