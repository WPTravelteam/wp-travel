<?php
/**
 * Exit if accessed directly.
 *
 * @package wp-travel\incldues\widgets
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
class WP_Travel_Widget_Featured extends WP_Widget {

	private $no_of_trip_show;
	private $trip_per_row;
	private $view_mode;
	/**
	 * Constructor.
	 */
	function __construct() {
		// Instantiate the parent object.
		parent::__construct( false, __( 'WP Travel Featured Trip', 'wp-travel' ) );
		$this->no_of_trip_show = 2;
		$this->trip_per_row = 1;
		$this->view_mode = 'grid';
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
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$hide_title = isset( $instance['hide_title'] ) ? $instance['hide_title'] : '';
		$no_of_trip_show = isset( $instance['no_of_trip_show'] ) ? $instance['no_of_trip_show'] : $this->no_of_trip_show;
		// $trip_per_row = ( $instance['trip_per_row'] ) ? $instance['trip_per_row'] : $this->trip_per_row;
		$view_mode = isset( $instance['view_mode'] ) ? $instance['view_mode'] : $this->view_mode;

		echo $before_widget;
		if ( ! $hide_title ) {
			echo ( $title ) ? $before_title . $title . $after_title : '';
		}

		$featured_args = array(
			'posts_per_page'   => $no_of_trip_show,
			'offset'           => 0,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'meta_key'         => 'wp_travel_featured',
			'meta_value'       => 'yes',
			'post_type'        => WP_TRAVEL_POST_TYPE,
			'post_status'      => 'publish',
			'suppress_filters' => true,
		);

		$itineraries = new WP_Query( $featured_args );

		if ( $itineraries->have_posts() ) : ?>

			<div class="wp-travel-itinerary-items">
			
			<?php if ( 'grid' == $view_mode ) : ?> 
				
				<ul class="wp-travel-itinerary-list">

			<?php else : ?>

				<div class="wp-travel-itinerary-list">

			<?php endif; ?>

				<?php while( $itineraries->have_posts() ) : $itineraries->the_post();

					if ( 'grid' == $view_mode ) : 
						
						//Load Grid View Mode.
						wp_travel_get_template_part( 'shortcode/itinerary', 'item' ); 

					else :
						wp_travel_get_template_part( 'shortcode/itinerary-item', 'list' );
						//Load list View Mode.
					
					endif;
					
				endwhile; wp_reset_postdata(); ?>

			<?php if ( 'grid' == $view_mode ) : ?> 
							
				</ul>

			<?php else : ?>

				</div>

			<?php endif; ?>
			
			</div>
		
		<?php else : ?>
			<p class="itinerary-none"><?php esc_html_e( 'Featured Trips not found.', 'wp-travel' ) ?></p>
		<?php endif;
		echo $after_widget;
	}
	/**
	 * Update widget.
	 *
	 * @param  Mixed $new_instance New instance of widget.
	 * @param  Mixed $old_instance Old instance of widget.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['hide_title'] = isset( $new_instance['hide_title'] ) ? sanitize_text_field( $new_instance['hide_title'] ) : '';
		// $instance['trip_per_row'] = sanitize_text_field( $new_instance['trip_per_row'] );
		$instance['view_mode']   = sanitize_key( $new_instance['view_mode'] );
		$instance['no_of_trip_show'] = sanitize_text_field( $new_instance['no_of_trip_show'] );

		return $instance;
	}

	/**
	 * Search form of widget.
	 *
	 * @param  Mixed $instance Widget instance.
	 */
	function form( $instance ) {
		// Check values.
		$title = '';
		$hide_title = '';
		$no_of_trip_show = $this->no_of_trip_show;
		$trip_per_row = $this->trip_per_row;
		$view_mode = $this->view_mode;
		if ( isset( $instance['title'] ) ) {
			$title = esc_attr( $instance['title'] );
		}
		if ( isset( $instance['hide_title'] ) ) {
			$hide_title = esc_attr( $instance['hide_title'] );
		}
		// if ( $instance['trip_per_row'] ) {
		// 	$trip_per_row = esc_attr( $instance['trip_per_row'] );
		// }
		if ( isset( $instance['view_mode'] ) ) {
			$view_mode = esc_attr( $instance['view_mode'] );
		}
		if ( isset( $instance['no_of_trip_show'] ) ) {
			$no_of_trip_show = esc_attr( $instance['no_of_trip_show'] );
		} ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'wp-travel' ); ?>:</label>
			<input type="text" value="<?php echo esc_attr( $title ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'no_of_trip_show' ) ); ?>"><?php esc_html_e( 'No. of trip to show', 'wp-travel' ); ?>:</label>
			<input type="number" value="<?php echo esc_attr( $no_of_trip_show ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'no_of_trip_show' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'no_of_trip_show' ) ); ?>" min="1" max="100" class="widefat">
		</p>
		<!-- <p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'trip_per_row' ) ); ?>"><?php esc_html_e( 'Trip per row', 'wp-travel' ); ?>:</label>
			<input type="number" value="<?php echo esc_attr( $trip_per_row ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'trip_per_row' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'trip_per_row' ) ); ?>" min="1" max="9" class="widefat">
		</p> -->
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'view_mode' ) ); ?>"><?php esc_html_e( 'View Mode', 'wp-travel' ); ?>:</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'view_mode' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'view_mode' ) ); ?>" class="widefat">
				<?php
					$view_mode_options = array(
						'grid'   => __( 'Grid View', 'wp-travel' ),
						'list' => __( 'List View', 'wp-travel' ),
					);

				foreach ( $view_mode_options as $key => $value ) {
					echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $view_mode, false ) . '>' . esc_html( $value ) . '</option>';
				}
				?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'hide_title' ) ); ?>"><?php esc_html_e( 'Hide title', 'wp-travel' ); ?>:</label>
			<label style="display: block;"><input type="checkbox" value="1" name="<?php echo esc_attr( $this->get_field_name( 'hide_title' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'hide_title' ) ); ?>" class="widefat" <?php checked( 1, $hide_title ); ?>><?php esc_html_e( 'Check to Hide', 'wp-travel' ); ?></label>
		</p>
			
	<?php
	}
}

/**
 * Register Widgets.
 *
 * @return void
 */
function wp_travel_register_featured_widgets() {
	register_widget( 'WP_Travel_Widget_Featured' );
}
add_action( 'widgets_init', 'wp_travel_register_featured_widgets' );
