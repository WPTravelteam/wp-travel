<?php
/**
 * Shortcode callbacks.
 *
 * @package wp-travel\inc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WP travel Shortcode class.
 *
 * @class WP_Pattern
 * @version	1.0.0
 */
class Wp_Travel_Shortcodes {

	public function init() {
	    add_shortcode( 'WP_TRAVEL_ITINERARIES', array( $this, 'wp_travel_get_itineraries_shortcode' ) );
		add_shortcode( 'wp_travel_itineraries', array( $this, 'wp_travel_get_itineraries_shortcode' ) );
		add_shortcode( 'wp_travel_trip_filters', array( $this, 'wp_travel_trip_filters_shortcode' ) );
	}

	/**
	 * Booking Form.
	 *
	 * @return HTMl Html content.
	 */
	public static function wp_travel_get_itineraries_shortcode(  $atts, $content = '' ) {

		$type = isset( $atts['type'] ) ? $atts['type'] : '';

		$iti_id = isset( $atts['itinerary_id'] ) ? absint($atts['itinerary_id']) : '';

		$id   = isset( $atts['id'] ) ? $atts['id'] : 0;
		$id   = absint( $id );
		$slug = isset( $atts['slug'] ) ? $atts['slug'] : '';
		$limit = isset( $atts['limit'] ) ? $atts['limit'] : 20;
		$limit = absint( $limit );

		$args = array(
			'post_type' 		=> 'itineraries',
			'posts_per_page' 	=> $limit,
			'status'       => 'published',
		);

		if ( ! empty( $iti_id ) ) :
			$args['p'] 	= $iti_id;
		else :
			$taxonomies = array( 'itinerary_types', 'travel_locations' );
			// if type is taxonomy.
			if ( in_array( $type, $taxonomies ) ) {

				if (  $id > 0 ) {
					$args['tax_query']	 = array(
											array(
												'taxonomy' => $type,
												'field'    => 'term_id',
												'terms'    => $id,
												),
											);
				} elseif ( '' !== $slug ) {
					$args['tax_query']	 = array(
											array(
												'taxonomy' => $type,
												'field'    => 'slug',
												'terms'    => $slug,
												),
											);
				}
			} elseif ( 'featured' === $type ) {
				$args['meta_key']   = 'wp_travel_featured';
				$args['meta_query'] = array(
									array(
										'key'     => 'wp_travel_featured',
										'value'   => 'yes',
										// 'compare' => 'IN',
									),
								);
			}

		endif;

		$query = new WP_Query( $args );
		ob_start();
		?>
		<div class="wp-travel-itinerary-items">
			<?php $col_per_row = apply_filters( 'wp_travel_itineraries_col_per_row' , '2' ); ?>
			<?php if ( $query->have_posts() ) : ?>				
				<ul style="" class="wp-travel-itinerary-list itinerary-<?php esc_attr_e( $col_per_row, 'wp-travel' ) ?>-per-row">
				<?php while ( $query->have_posts() ) : $query->the_post(); ?>
					<?php wp_travel_get_template_part( 'shortcode/itinerary', 'item' ); ?>
				<?php endwhile; ?>
				</ul>
			<?php else : ?>
				<?php wp_travel_get_template_part( 'shortcode/itinerary', 'item-none' ); ?>
			<?php endif; ?>
		</div>
		<?php wp_reset_query();
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

public static function wp_travel_trip_filters_shortcode( $atts, $content ) {

	$keyword_search = true;
	$trip_type_filter = true;
	$trip_location_filter = true;
	$price_orderby = true;
	$price_range = true;
	$trip_dates = true;

	if ( isset( $atts['filters'] ) && 'all' !== $atts['filters'] ) {
		$filters = explode( ',',$atts['filters'] );
		
		$keyword_search = in_array( 'keyword', $filters ) ? true : false;
		$trip_type_filter = in_array( 'trip_type', $filters ) ? true : false;
		$trip_location_filter = in_array( 'trip_location', $filters ) ? true : false;
		$price_orderby = in_array( 'price_orderby', $filters ) ? true : false;
		$price_range = in_array( 'price_range', $filters ) ? true : false;
		$trip_dates = in_array( 'trip_dates', $filters ) ? true : false;
	}

	?>
	<div class="wp-travel-itinerary-items">
                <div>
                <?php if ( $keyword_search ) : ?>
                    <div class="wp-travel-form-field ">
                        <label><?php esc_html_e( 'Keyword:', 'wp-travel' ) ?></label>
                            <?php $placeholder = __( 'Ex: Trekking', 'wp-travel' ); ?>
                            <input class="wp_travel_search_widget_filters_input" type="text" name="keyword" id="wp-travel-filter-keyword" value="<?php echo ( isset( $_GET['keyword'] ) ) ? esc_textarea( $_GET['keyword'] ) : ''; ?>" placeholder="<?php echo esc_attr( apply_filters( 'wp_travel_search_placeholder', $placeholder ) ); ?>">
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
                                'class'              => 'wp_travel_search_widget_filters_input',
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
                                'class'              => 'wp_travel_search_widget_filters_input',
                                'taxonomy'           => $taxonomy,
                                'selected'           => ( isset( $_GET['location'] ) ) ? esc_textarea( $_GET['location'] ) : 0,
                            );

                            wp_dropdown_categories( $args, $taxonomy );
                            ?>
                    </div>
                <?php endif; ?>
                <?php if ( $price_orderby ) :  ?>
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
                <?php endif; ?>
                <?php if ( $price_range ) : ?>
                    <div class="wp-travel-form-field wp-trave-price-range">
                        <label for="amount"><?php esc_html_e( 'Price Range', 'wp-travel' ); ?></label>
                        <input type="text" id="amount" readonly style="border:0; color:#f6931f; font-weight:bold;">
                        <input id="wp-travel-filter-price-min" type="hidden" class="wp_travel_search_widget_filters_input" name="min_price" value="<?php echo $min_price; ?>">
                        <input id="wp-travel-filter-price-max" type="hidden" class="wp_travel_search_widget_filters_input" name="max_price" value="<?php echo $max_price; ?>">
                        <div id="slider-range"></div>
                    </div>
                <?php endif; ?>
                <?php if ( $trip_dates ) : ?>
                    <div class="wp-travel-form-field wp-travel-trip-duration">
                        <label><?php esc_html_e('Trip Duration', 'wp-travel'); ?></label>
                        <span class="trip-duration-calender">
                            <small><?php esc_html_e( 'From', 'wp-travel' ); ?></small>
                            <input value="<?php echo esc_attr( $trip_start ); ?>" class="wp_travel_search_widget_filters_input" type="text" id="datepicker1" name="trip_start">
                            <label for="datepicker1">
                                <span class="calender-icon"></span>
                            </label>
                        </span>
                        <span class="trip-duration-calender">
                            <small><?php esc_html_e( 'To', 'wp-travel' ); ?></small>
                            <input value="<?php echo esc_attr( $trip_end ); ?>" class="wp_travel_search_widget_filters_input" type="text" id="datepicker2" name="trip_end" data-position='bottom right'>
                            <label for="datepicker2">
                                <span class="calender-icon"></span>
                            </label>
                        </span>
                        
                    </div>

                <?php endif; ?>

                    <?php $view_mode = wp_travel_get_archive_view_mode(); ?>

                    <input id="wp-travel-widget-filter-view-mode" type="hidden" name="view_mode" data-mode="<?php echo esc_attr( $view_mode ); ?>" value="<?php echo esc_attr( $view_mode ); ?>" >

                    <input type="hidden" id="wp-travel-widget-filter-archive-url" value="<?php echo esc_url( get_post_type_archive_link( WP_TRAVEL_POST_TYPE ) ) ?>" />

                    <div class="wp-travel-search">
                        <input type="submit" id="wp-travel-filter-search-submit" class="button button-primary" value="Search">
                    </div>
                    
                </div>

            </div>

<?php
}

}
