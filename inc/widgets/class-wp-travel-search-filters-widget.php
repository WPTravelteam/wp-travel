<?php
/**
 * Exit if accessed directly.
 *
 * @package wp-travel\incldues
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Aditem Search Widget.
 *
 * @author   WenSolutions
 * @category Widgets
 * @package  wp-travel/Widgets
 * @extends  WP_Widget
 */
class WP_Travel_Widget_Filter_Search_Widget extends WP_Widget {
	/**
	 * Constructor.
	 */
	function __construct() {
		// Instantiate the parent object.
		parent::__construct( false, __( 'WP Travel Filters Widget', 'wp-travel' ) );
	}

	/**
	 * Display widget.
	 *
	 * @param  Mixed $args     Arguments of widget.
	 * @param  Mixed $instance Instance value of widget.
	 */
	function widget( $args, $instance ) {

		extract( $args );
		// These are the widget options.
		$title      = apply_filters( 'wp_travel_search_widget_title', isset( $instance['title'] ) ? $instance['title'] : '' );
		$hide_title = isset( $instance['hide_title'] ) ? $instance['hide_title'] : '';

		echo $before_widget;
		if ( ! $hide_title ) {
			echo ( $title ) ? $before_title . $title . $after_title : '';
		}

		echo wp_travel_get_search_filter_form( array( 'widget' => $instance ) );

		echo $after_widget;
	}
	/**
	 * Update widget.
	 *
	 * @param  Mixed $new_instance New instance of widget.
	 * @param  Mixed $old_instance Old instance of widget.
	 */
	function update( $new_instance, $old_instance ) {
		$instance               = $old_instance;
		$instance['title']      = isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['hide_title'] = isset( $new_instance['hide_title'] ) ? sanitize_text_field( $new_instance['hide_title'] ) : '';

		// Filters
		$search_fields = wp_travel_search_filter_widget_form_fields();
		foreach ( $search_fields as $key => $field ) {
			$instance[ $key ] = isset( $new_instance[ $key ] ) ? sanitize_text_field( $new_instance[ $key ] ) : '';
		}
		return $instance;
	}

	/**
	 * Search form of widget.
	 *
	 * @param  Mixed $instance Widget instance.
	 */
	function form( $instance ) {
		// Check values.
		$title      = '';
		$hide_title = '';

		if ( $instance ) {
			$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
			$hide_title = isset( $instance['hide_title'] ) ? esc_attr( $instance['hide_title'] ) : '';
		}
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'wp-travel' ); ?>:</label>
			<input type="text" value="<?php echo esc_attr( $title ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'hide_title' ) ); ?>"><?php esc_html_e( 'Hide title', 'wp-travel' ); ?>:</label>
			<label style="display: block;"><input type="checkbox" value="1" name="<?php echo esc_attr( $this->get_field_name( 'hide_title' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'hide_title' ) ); ?>" class="widefat" <?php checked( 1, $hide_title ); ?>><?php esc_html_e( 'Check to Hide', 'wp-travel' ); ?></label>
		</p>
		<div class="wp-travel-widget-filter">
			<p>
				<label><strong><?php esc_html_e( 'Enable Filters', 'wp-travel' ); ?>:</strong></label>
				<?php
				$search_fields = wp_travel_search_filter_widget_form_fields();
				foreach ( $search_fields as $key => $field ) {
					// Filters.
					$instance_value = isset( $instance[ $key ] ) ? esc_attr( $instance[ $key ] ) : 1;
					?>
					<label style="display: block;">
						<input type="checkbox" value="1" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" class="widefat" <?php checked( 1, $instance_value ); ?>>
						<?php echo esc_html( $field['label'] ); ?>
					</label>
					<?php
				}
				?>
			</p>
		</div>
		<?php
	}
}

function wp_travel_register_wp_travel_search_filter_widgets() {
	register_widget( 'WP_Travel_Widget_Filter_Search_Widget' );
}
add_action( 'widgets_init', 'wp_travel_register_wp_travel_search_filter_widgets', 100 );
