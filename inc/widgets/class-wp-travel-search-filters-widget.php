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
		$title = apply_filters( 'wp_travel_search_widget_title', $instance['title'] );

		echo $before_widget;
        echo ( $title ) ? $before_title . $title . $after_title : '';
        ?>
            <div class="wp-travel-itinerary-items">
                <form>
                    <div class="wp-travel-form-field ">
                        <label><?php esc_html_e( 'Keyword:', 'wp-travel' ) ?></label>
                            <?php $placeholder = __( 'Ex: Trekking', 'wp-travel' ); ?>
                            <input type="text" name="s" id="s" value="<?php echo ( isset( $_GET['s'] ) ) ? esc_textarea( $_GET['s'] ) : ''; ?>" placeholder="<?php echo esc_attr( apply_filters( 'wp_travel_search_placeholder', $placeholder ) ); ?>">
                    </div>
                    <div class="wp-travel-form-field ">
                        <label><?php esc_html_e( 'Trip Type:', 'wp-travel' ) ?></label>
                        <?php
                            $taxonomy = 'itinerary_types';
                            $args = array(
                                'show_option_all'    => __( 'All', 'wp-travel' ),
                                'hide_empty'         => 0,
                                'selected'           => 1,
                                'hierarchical'       => 1,
                                'name'               => $taxonomy,
                                'class'              => 'wp-travel-taxonomy',
                                'taxonomy'           => $taxonomy,
                                'selected'           => ( isset( $_GET[$taxonomy] ) ) ? esc_textarea( $_GET[$taxonomy] ) : 0,
                                'value_field'		 => 'slug',
                            );

                        wp_dropdown_categories( $args, $taxonomy );
                        ?>			
                    </div>
                    <div class="wp-travel-form-field ">
                        <label><?php esc_html_e( 'Location:', 'wp-travel' ) ?></label>
                            <?php
                            $taxonomy = 'travel_locations';
                            $args = array(
                                'show_option_all'    => __( 'All', 'wp-travel' ),
                                'hide_empty'         => 0,
                                'selected'           => 1,
                                'hierarchical'       => 1,
                                'name'               => $taxonomy,
                                'class'              => 'wp-travel-taxonomy',
                                'taxonomy'           => $taxonomy,
                                'selected'           => ( isset( $_GET[$taxonomy] ) ) ? esc_textarea( $_GET[$taxonomy] ) : 0,
                                'value_field'		 => 'slug',
                            );

                            wp_dropdown_categories( $args, $taxonomy );
                            ?>
                    </div>
                    <div class="wp-travel-form-field ">
                        <input type="hidden" id="wp-travel-archive-url" value="<?php echo esc_url( get_post_type_archive_link( WP_TRAVEL_POST_TYPE ) ) ?>" />
                            <?php
                                $price = ( isset( $_GET['price'] ) ) ? $_GET['price'] : '';
                                $type = ( int ) ( isset( $_GET['type'] ) && '' !== $_GET['type'] ) ? $_GET['type'] : 0;
                                $location = ( int ) ( isset( $_GET['location'] ) && '' !== $_GET['location'] ) ? $_GET['location'] : 0;
                            ?>
                        <label for="trip_price">
                            <?php esc_html_e( 'Price', 'wp-travel' ); ?>
                        </label>
                        <select name="price" class="wp_travel_input_filters price">
                            <option value="">--</option>
                            <option value="low_high" <?php selected( $price, 'low_high' ) ?> data-type="meta" ><?php esc_html_e( 'Price low to high', 'wp-travel' ) ?></option>
                            <option value="high_low" <?php selected( $price, 'high_low' ) ?> data-type="meta" ><?php esc_html_e( 'Price high to low', 'wp-travel' ) ?></option>
                        </select>	
                    </div>
                    <div class="wp-travel-form-field wp-trave-price-range">
                        <label for="amount">Price range</label>
                        <input type="text" id="amount" readonly style="border:0; color:#f6931f; font-weight:bold;">
                        <div id="slider-range"></div>
                    </div>


                    <div class="wp-travel-form-field wp-travel-trip-duration">
                        <label>Trip Duration</label>
                        <span class="trip-duration-calender">
                            <small>From</small>
                            <input type="text" id="datepicker1" name="">
                            <span class="calender-icon"></span>
                        </span>
                        <span class="trip-duration-calender">
                            <small>To</small>
                            <input type="text" id="datepicker2" name="">
                            <span class="calender-icon"></span>
                        </span>
                        
                    </div>

                    <div class="wp-travel-search">
                        <input type="submit" name="wp-trip_search" id="wp-trip-search" class="button button-primary" value="Search">
                    </div>
                    
                </form>

            </div>
        <?php
	
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
		if ( $instance ) {
			$title = esc_attr( $instance['title'] );
		} ?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'wp-travel' ); ?>:</label>
			<input type="text" value="<?php echo esc_attr( $title ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat">
		</p>
			
	<?php
	}
}

function wp_travel_register_wp_travel_search_filter_widgets() {
	register_widget( 'WP_Travel_Widget_Filter_Search_Widget' );
}
add_action( 'widgets_init', 'wp_travel_register_wp_travel_search_filter_widgets' );
