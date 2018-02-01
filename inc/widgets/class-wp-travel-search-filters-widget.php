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
								<label>Keyword</label>
  								<input type="text" id="" name="">
							</div>
							<div class="wp-travel-form-field ">
								<label for="trip_types">
									Trip Type
								</label>
								<select name="itinerary_types" id="trip_types" class="wp-travel-taxonomy">
									<option value="0" selected="selected">All</option>
									<option class="level-0" value="casual-tours">Casual Tours</option>
								</select>			
							</div>
							<div class="wp-travel-form-field ">
								<label for="trip_locations">
									Location
								</label>
								<select name="travel_locations" id="trip_locations" class="wp-travel-taxonomy">
									<option value="0" selected="selected">All</option>
									<option class="level-0" value="bhutan">Bhutan</option>
									<option class="level-0" value="nepal">Nepal</option>
									<option class="level-0" value="thailand">Thailand</option>
								</select>		
							</div>
							<div class="wp-travel-form-field ">
								<label for="trip_price">
									Price
								</label>
								<select id="trip_price" name="price" class="wp_travel_input_filters price">
									<option value="low_high" data-type="meta">Price low to high</option>
									<option value="high_low" data-type="meta">Price high to low</option>
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



							<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.js"></script>

							<script type="text/javascript">
								jQuery(document).ready(function($) {
								    $( "#slider-range" ).slider({
								      range: true,
								      min: 0,
								      max: 500,
								      values: [ 75, 300 ],
								      slide: function( event, ui ) {
								        $( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
								      }
								    });
								    $( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) +
								      " - $" + $( "#slider-range" ).slider( "values", 1 ) );

								    $( ".trip-duration-calender input" ).datepicker();

								    });
							</script>
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
