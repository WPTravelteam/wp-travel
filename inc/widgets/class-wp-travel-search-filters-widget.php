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
		$title = apply_filters( 'wp_travel_search_widget_title', isset( $instance['title'] ) ? $instance['title'] : '' );
		$hide_title = isset( $instance['hide_title'] ) ? $instance['hide_title'] : '';

		// Filters
		$keyword_search = isset( $instance['keyword_search'] ) ? $instance['keyword_search'] : '';
		$fact = isset( $instance['fact'] ) ? $instance['fact'] : true;
		$trip_type_filter = isset( $instance['trip_type_filter'] ) ? $instance['trip_type_filter'] : '';
		$trip_location_filter = isset( $instance['trip_location_filter'] ) ? $instance['trip_location_filter'] : '';
		$price_orderby = isset( $instance['price_orderby'] ) ? $instance['price_orderby'] : '';
		$price_range = isset( $instance['price_range'] ) ? $instance['price_range'] : '';
		$trip_dates = isset( $instance['trip_dates'] ) ? $instance['trip_dates'] : '';

		$index = uniqid();

		echo $before_widget;
		if ( ! $hide_title ) {
			echo ( $title ) ? $before_title . $title . $after_title : '';
		}
		?>
		<?php
			$price = ( isset( $_GET['price'] ) ) ? $_GET['price'] : '';
			$type = ( int ) ( isset( $_GET['type'] ) && '' !== $_GET['type'] ) ? $_GET['type'] : 0;
			$location = ( int ) ( isset( $_GET['location'] ) && '' !== $_GET['location'] ) ? $_GET['location'] : 0;
			$min_price = ( int ) ( isset( $_GET['min_price'] ) && '' !== $_GET['min_price'] ) ? $_GET['min_price'] : '';
			$max_price = ( int ) ( isset( $_GET['max_price'] ) && '' !== $_GET['max_price'] ) ? $_GET['max_price'] : '';
			$trip_start = ( int ) ( isset( $_GET['trip_start'] ) && '' !== $_GET['trip_start'] ) ? $_GET['trip_start'] : '';
			$trip_end = ( int ) ( isset( $_GET['trip_end'] ) && '' !== $_GET['trip_end'] ) ? $_GET['trip_end'] : '';

		?>
			<div class="wp-travel-itinerary-items">
				<div>
				<?php if ( $keyword_search ) : ?>
					<div class="wp-travel-form-field ">
						<label><?php esc_html_e( 'Keyword:', 'wp-travel' ) ?></label>
							<?php $placeholder = __( 'Ex: Trekking', 'wp-travel' ); ?>
							<input class="wp_travel_search_widget_filters_input<?php echo $index; ?>" type="text" name="keyword" id="wp-travel-filter-keyword" value="<?php echo ( isset( $_GET['keyword'] ) ) ? esc_textarea( $_GET['keyword'] ) : ''; ?>" placeholder="<?php echo esc_attr( apply_filters( 'wp_travel_search_placeholder', $placeholder ) ); ?>">
					</div>
				<?php endif; ?>
				<?php if ( $fact ) : ?>
					<div class="wp-travel-form-field ">
						<label><?php esc_html_e( 'Fact:', 'wp-travel' ) ?></label>
							<?php $placeholder = __( 'Ex: guide', 'wp-travel' ); ?>
							<input class="wp_travel_search_widget_filters_input<?php echo $index; ?>" type="text" name="fact" id="wp-travel-filter-fact" value="<?php echo ( isset( $_GET['fact'] ) ) ? esc_textarea( $_GET['fact'] ) : ''; ?>" placeholder="<?php echo esc_attr( apply_filters( 'wp_travel_search_placeholder', $placeholder ) ); ?>">
					</div>
				<?php endif; ?>
				<?php if ( $trip_type_filter ) : ?>
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
								'class'              => 'wp_travel_search_widget_filters_input'.$index,
								'taxonomy'           => $taxonomy,
								'selected'           => ( isset( $_GET['type'] ) ) ? esc_textarea( $_GET['type'] ) : 0,
							);

						wp_dropdown_categories( $args, $taxonomy );
						?>			
					</div>
				<?php endif; ?>
				<?php if ( $trip_location_filter ) : ?>
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
								'class'              => 'wp_travel_search_widget_filters_input'.$index,
								'taxonomy'           => $taxonomy,
								'selected'           => ( isset( $_GET['location'] ) ) ? esc_textarea( $_GET['location'] ) : 0,
							);

							wp_dropdown_categories( $args, $taxonomy );
							?>
					</div>
				<?php endif; ?>
				<?php if ( $price_orderby ) :  ?>
					<div class="wp-travel-form-field ">
						<label for="price">
							<?php esc_html_e( 'Price', 'wp-travel' ); ?>
						</label>
						<select name="price" class="wp_travel_search_widget_filters_input<?php echo $index; ?> price">
							<option value="">--</option>
							<option value="low_high" <?php selected( $price, 'low_high' ) ?> data-type="meta" ><?php esc_html_e( 'Price low to high', 'wp-travel' ) ?></option>
							<option value="high_low" <?php selected( $price, 'high_low' ) ?> data-type="meta" ><?php esc_html_e( 'Price high to low', 'wp-travel' ) ?></option>
						</select>	
					</div>
				<?php endif; ?>
				<?php if ( $price_range ) : ?>
					<div class="wp-travel-form-field wp-trave-price-range">
						<label for="amount"><?php esc_html_e( 'Price Range', 'wp-travel' ); ?></label>
						<input type="text" id="amount" class="price-amount" readonly style="border:0; color:#f6931f; font-weight:bold;">
						<input type="hidden" class="wp_travel_search_widget_filters_input<?php echo $index; ?> wp-travel-filter-price-min" name="min_price" value="<?php echo $min_price; ?>">
						<input type="hidden" class="wp_travel_search_widget_filters_input<?php echo $index; ?> wp-travel-filter-price-max" name="max_price" value="<?php echo $max_price; ?>">
						<div class="wp-travel-range-slider"></div>
					</div>
				<?php endif; ?>
				<?php if ( $trip_dates ) : ?>
					<div class="wp-travel-form-field wp-travel-trip-duration">
						<label><?php esc_html_e('Trip Duration', 'wp-travel'); ?></label>
						<span class="trip-duration-calender">
							<small><?php esc_html_e( 'From', 'wp-travel' ); ?></small>
							<input value="<?php echo esc_attr( $trip_start ); ?>" class="wp_travel_search_widget_filters_input<?php echo $index; ?>" type="text" id="datepicker1" name="trip_start">
							<label for="datepicker1">
								<span class="calender-icon"></span>
							</label>
						</span>
						<span class="trip-duration-calender">
							<small><?php esc_html_e( 'To', 'wp-travel' ); ?></small>
							<input value="<?php echo esc_attr( $trip_end ); ?>" class="wp_travel_search_widget_filters_input<?php echo $index; ?>" type="text" id="datepicker2" name="trip_end" data-position='bottom right'>
							<label for="datepicker2">
								<span class="calender-icon"></span>
							</label>
						</span>
						
					</div>

				<?php endif; ?>

					<?php $view_mode = wp_travel_get_archive_view_mode(); ?>

					<div class="wp-travel-search">
						
						<input class="filter-data-index" type="hidden" data-index="<?php echo esc_attr( $index ); ?>">
							
						<input class="wp-travel-widget-filter-view-mode" type="hidden" name="view_mode" data-mode="<?php echo esc_attr( $view_mode ); ?>" value="<?php echo esc_attr( $view_mode ); ?>" >

						<input type="hidden" class="wp-travel-widget-filter-archive-url" value="<?php echo esc_url( get_post_type_archive_link( WP_TRAVEL_POST_TYPE ) ) ?>" />
						<input type="submit" id="wp-travel-filter-search-submit" class="button button-primary wp-travel-filter-search-submit" value="<?php esc_html_e( 'Search', 'wp-travel' ); ?>">
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
		$instance['hide_title'] = isset( $new_instance['hide_title'] ) ? sanitize_text_field( $new_instance['hide_title'] ) : '';
		$instance['keyword_search'] = isset( $new_instance['keyword_search'] ) ? sanitize_text_field( $new_instance['keyword_search'] ) : '';
		$instance['fact'] = isset( $new_instance['fact'] ) ? sanitize_text_field( $new_instance['fact'] ) : '';
		$instance['trip_type_filter'] = isset( $new_instance['trip_type_filter'] ) ? sanitize_text_field( $new_instance['trip_type_filter'] ) : '';
		$instance['trip_location_filter'] = isset( $new_instance['trip_location_filter'] ) ? sanitize_text_field( $new_instance['trip_location_filter'] ) : '';
		$instance['price_orderby'] = isset( $new_instance['price_orderby'] ) ? sanitize_text_field( $new_instance['price_orderby'] ) : '';
		$instance['price_range'] = isset( $new_instance['price_range'] ) ? sanitize_text_field( $new_instance['price_range'] ) : '';
		$instance['trip_dates'] = isset( $new_instance['trip_dates'] ) ? sanitize_text_field( $new_instance['trip_dates'] ) : '';

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

		// Filters.
		$keyword_search = 1;
		$fact = 1;
		$trip_type_filter = 1;
		$trip_location_filter = 1;
		$price_orderby = 1;
		$price_range = 1;
		$trip_dates = 1;

		if ( $instance ) {
			$title = esc_attr( $instance['title'] );
		}
		if ( isset( $instance['hide_title'] ) ) {
			$hide_title = esc_attr( $instance['hide_title'] );
		}
		//Filters.
		if ( isset( $instance['keyword_search'] ) ) {
			$keyword_search = esc_attr( $instance['keyword_search'] );
		}
		if ( isset( $instance['fact'] ) ) {
			$fact = esc_attr( $instance['fact'] );
		}
		if ( isset( $instance['trip_type_filter'] ) ) {
			$trip_type_filter = esc_attr( $instance['trip_type_filter'] );
		}
		if ( isset( $instance['trip_location_filter'] ) ) {
			$trip_location_filter = esc_attr( $instance['trip_location_filter'] );
		}
		if ( isset( $instance['price_orderby'] ) ) {
			$price_orderby = esc_attr( $instance['price_orderby'] );
		}
		if ( isset( $instance['price_range'] ) ) {
			$price_range = esc_attr( $instance['price_range'] );
		}
		if ( isset( $instance['trip_dates'] ) ) {
			$trip_dates = esc_attr( $instance['trip_dates'] );
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
					<label style="display: block;">
						<input type="checkbox" value="1" name="<?php echo esc_attr( $this->get_field_name( 'keyword_search' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'keyword_search' ) ); ?>" class="widefat" <?php checked( 1, $keyword_search ); ?>><?php esc_html_e( 'KeyWord Search', 'wp-travel' ); ?>
					</label>
					<label style="display: block;">
						<input type="checkbox" value="1" name="<?php echo esc_attr( $this->get_field_name( 'fact' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'fact' ) ); ?>" class="widefat" <?php checked( 1, $fact ); ?>><?php esc_html_e( 'Trip Fact', 'wp-travel' ); ?>
					</label>
					<label style="display: block;">
						<input type="checkbox" value="1" name="<?php echo esc_attr( $this->get_field_name( 'trip_type_filter' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'trip_type_filter' ) ); ?>" class="widefat" <?php checked( 1, $trip_type_filter ); ?>><?php esc_html_e( 'Trip Type Filter', 'wp-travel' ); ?>
					</label>
					<label style="display: block;">
						<input type="checkbox" value="1" name="<?php echo esc_attr( $this->get_field_name( 'trip_location_filter' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'trip_location_filter' ) ); ?>" class="widefat" <?php checked( 1, $trip_location_filter ); ?>><?php esc_html_e( 'Trip Location Filter', 'wp-travel' ); ?>
					</label>
					<label style="display: block;">
						<input type="checkbox" value="1" name="<?php echo esc_attr( $this->get_field_name( 'price_orderby' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'price_orderby' ) ); ?>" class="widefat" <?php checked( 1, $price_orderby ); ?>><?php esc_html_e( 'Price Orderby Filter', 'wp-travel' ); ?>
					</label>
					<label style="display: block;">
						<input type="checkbox" value="1" name="<?php echo esc_attr( $this->get_field_name( 'price_range' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'price_range' ) ); ?>" class="widefat" <?php checked( 1, $price_range ); ?>><?php esc_html_e( 'Price Range Filter', 'wp-travel' ); ?>
					</label>
					<label style="display: block;">
						<input type="checkbox" value="1" name="<?php echo esc_attr( $this->get_field_name( 'trip_dates' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'trip_dates' ) ); ?>" class="widefat" <?php checked( 1, $trip_dates ); ?>><?php esc_html_e( 'Trip Dates Filter', 'wp-travel' ); ?>
					</label>
			</p>
		</div>
			
	<?php
	}
}

function wp_travel_register_wp_travel_search_filter_widgets() {
	register_widget( 'WP_Travel_Widget_Filter_Search_Widget' );
}
add_action( 'widgets_init', 'wp_travel_register_wp_travel_search_filter_widgets' );
