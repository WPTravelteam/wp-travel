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
                <div>
                    <div class="wp-travel-form-field ">
                        <label><?php esc_html_e( 'Keyword:', 'wp-travel' ) ?></label>
                            <?php $placeholder = __( 'Ex: Trekking', 'wp-travel' ); ?>
                            <input class="wp_travel_search_widget_filters_input" type="text" name="keyword" id="wp-travel-filter-keyword" value="<?php echo ( isset( $_GET['keyword'] ) ) ? esc_textarea( $_GET['keyword'] ) : ''; ?>" placeholder="<?php echo esc_attr( apply_filters( 'wp_travel_search_placeholder', $placeholder ) ); ?>">
                    </div>
                    <div class="wp-travel-form-field ">
                        <label><?php esc_html_e( 'Trip Type:', 'wp-travel' ) ?></label>
                        <?php
                            $taxonomy = 'itinerary_types';
                            $args = array(
                                'show_option_all'    => __( 'All', 'wp-travel' ),
                                'hide_empty'         => 1,
                                'selected'           => 1,
                                'hierarchical'       => 1,
                                'name'               => 'type',
                                'class'              => 'wp_travel_search_widget_filters_input',
                                'taxonomy'           => $taxonomy,
                                'selected'           => ( isset( $_GET['type'] ) ) ? esc_textarea( $_GET['type'] ) : 0,
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
                                'hide_empty'         => 1,
                                'selected'           => 1,
                                'hierarchical'       => 1,
                                'name'               => 'location',
                                'class'              => 'wp_travel_search_widget_filters_input',
                                'taxonomy'           => $taxonomy,
                                'selected'           => ( isset( $_GET['location'] ) ) ? esc_textarea( $_GET['location'] ) : 0,
                                'value_field'		 => 'slug',
                            );

                            wp_dropdown_categories( $args, $taxonomy );
                            ?>
                    </div>
                    <div class="wp-travel-form-field ">
                            <?php
                                $price = ( isset( $_GET['price'] ) ) ? $_GET['price'] : '';
                                $type = ( int ) ( isset( $_GET['type'] ) && '' !== $_GET['type'] ) ? $_GET['type'] : 0;
                                $location = ( int ) ( isset( $_GET['location'] ) && '' !== $_GET['location'] ) ? $_GET['location'] : 0;
                                $min_price = ( int ) ( isset( $_GET['min_price'] ) && '' !== $_GET['min_price'] ) ? $_GET['min_price'] : '';
                                $max_price = ( int ) ( isset( $_GET['max_price'] ) && '' !== $_GET['max_price'] ) ? $_GET['max_price'] : '';
                                $trip_start = ( int ) ( isset( $_GET['trip_start'] ) && '' !== $_GET['trip_start'] ) ? $_GET['trip_start'] : '';
                                $trip_end = ( int ) ( isset( $_GET['trip_end'] ) && '' !== $_GET['trip_end'] ) ? $_GET['trip_end'] : '';

                            ?>
                        <label for="price">
                            <?php esc_html_e( 'Price', 'wp-travel' ); ?>
                        </label>
                        <select name="price" class="wp_travel_search_widget_filters_input price">
                            <option value="">--</option>
                            <option value="low_high" <?php selected( $price, 'low_high' ) ?> data-type="meta" ><?php esc_html_e( 'Price low to high', 'wp-travel' ) ?></option>
                            <option value="high_low" <?php selected( $price, 'high_low' ) ?> data-type="meta" ><?php esc_html_e( 'Price high to low', 'wp-travel' ) ?></option>
                        </select>	
                    </div>
                    <div class="wp-travel-form-field wp-trave-price-range">
                        <label for="amount"><?php esc_html_e( 'Price Range', 'wp-travel' ); ?></label>
                        <input type="text" id="amount" readonly style="border:0; color:#f6931f; font-weight:bold;">
                        <input id="wp-travel-filter-price-min" type="hidden" class="wp_travel_search_widget_filters_input" name="min_price" value="<?php echo $min_price; ?>">
                        <input id="wp-travel-filter-price-max" type="hidden" class="wp_travel_search_widget_filters_input" name="max_price" value="<?php echo $max_price; ?>">
                        <div id="slider-range"></div>
                    </div>

                    <div class="wp-travel-form-field wp-travel-trip-duration">
                        <label><?php esc_html_e('Trip Duration', 'wp-travel'); ?></label>
                        <span class="trip-duration-calender">
                            <small><?php esc_html_e( 'From', 'wp-travel' ); ?></small>
                            <input value="<?php echo esc_attr( $trip_start ); ?>" class="wp_travel_search_widget_filters_input" type="text" id="datepicker1" name="trip_start">
                            <span class="calender-icon"></span>
                        </span>
                        <span class="trip-duration-calender">
                            <small><?php esc_html_e( 'To', 'wp-travel' ); ?></small>
                            <input value="<?php echo esc_attr( $trip_end ); ?>" class="wp_travel_search_widget_filters_input" type="text" id="datepicker2" name="trip_end">
                            <span class="calender-icon"></span>
                        </span>
                        
                    </div>

                    <?php $view_mode = wp_travel_get_archive_view_mode(); ?>

                    <input id="wp-travel-widget-filter-view-mode" type="hidden" name="view_mode" data-mode="<?php echo esc_attr( $view_mode ); ?>" value="<?php echo esc_attr( $view_mode ); ?>" >

                    <input type="hidden" id="wp-travel-widget-filter-archive-url" value="<?php echo esc_url( get_post_type_archive_link( WP_TRAVEL_POST_TYPE ) ) ?>" />

                    <div class="wp-travel-search">
                        <input type="submit" id="wp-travel-filter-search-submit" class="button button-primary" value="Search">
                    </div>
                    
                </div>

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
