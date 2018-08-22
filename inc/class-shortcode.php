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
		add_shortcode( 'wp_travel_trip_facts', array( $this, 'wp_travel_trip_facts_shortcode' ) );

		/**
		 * Checkout Shortcodes.
		 * @since 2.2.3
		 * Shortcodes for new checkout process.
		 */
		$shortcodes = array(
			'wp_travel_cart'           => __CLASS__ . '::cart',
			'wp_travel_checkout' 	   => __CLASS__ . '::checkout',
			'wp_travel_user_account'   => __CLASS__ . '::user_account',
		);

		$shortcode = apply_filters( 'wp_travel_shortcodes', $shortcodes );

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}

	}

	/**
	 * Cart page shortcode.
	 *
	 * @return string
	 */
	public static function cart() {
		return self::shortcode_wrapper( array( 'WP_Travel_Cart', 'output' ) );
	}

	/**
	 * Checkout page shortcode.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function checkout( $atts ) {
		return self::shortcode_wrapper( array( 'WP_Travel_Checkout', 'output' ), $atts );
	}
	/**
	 * Add user Account shortcode.
	 *
	 * @return string
	 */
	public static function user_account() {
		return self::shortcode_wrapper( array( 'Wp_Travel_User_Account', 'output' ) );
	}

	/**
	 * Shortcode Wrapper.
	 *
	 * @param string[] $function Callback function.
	 * @param array    $atts     Attributes. Default to empty array.
	 * @param array    $wrapper  Customer wrapper data.
	 *
	 * @return string
	 */
	public static function shortcode_wrapper(
		$function,
		$atts = array(),
		$wrapper = array(
			'class'  => 'wp-travel',
			'before' => null,
			'after'  => null,
		)
	) {
		ob_start();

		// @codingStandardsIgnoreStart
		echo empty( $wrapper['before'] ) ? '<div class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
		call_user_func( $function, $atts );
		echo empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];
		// @codingStandardsIgnoreEnd

		return ob_get_clean();
	}

	/**
	 * Booking Form.
	 *
	 * @return HTMl Html content.
	 */
	public static function wp_travel_get_itineraries_shortcode(  $atts, $content = '' ) {

		$type = isset( $atts['type'] ) ? $atts['type'] : '';

		$iti_id = isset( $atts['itinerary_id'] ) ? absint($atts['itinerary_id']) : '';

		$view_mode = ( isset( $atts['view_mode'] ) && ! empty( $atts['view_mode'] ) ) ? $atts['view_mode'] : 'grid';

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
			<?php $col_per_row = ( isset( $atts['col'] ) && ! empty( $atts['col'] ) ) ? absint( $atts['col'] ) : apply_filters( 'wp_travel_itineraries_col_per_row' , '2' ); ?>
			<?php if ( $query->have_posts() ) : ?>				
				<ul style="" class="wp-travel-itinerary-list itinerary-<?php esc_attr_e( $col_per_row, 'wp-travel' ) ?>-per-row">
				<?php while ( $query->have_posts() ) : $query->the_post(); ?>
					<?php
					if( 'grid' === $view_mode ) :
						wp_travel_get_template_part( 'shortcode/itinerary', 'item' );
					else :
						wp_travel_get_template_part( 'shortcode/itinerary', 'item-list' );
					endif;
					?>
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
	$fact = true;
	$trip_type_filter = true;
	$trip_location_filter = true;
	$price_orderby = true;
	$price_range = true;
	$trip_dates = true;

	if ( isset( $atts['filters'] ) && 'all' !== $atts['filters'] ) {
		$filters = explode( ',',$atts['filters'] );
		
		$keyword_search = in_array( 'keyword', $filters ) ? true : false;
		$fact = in_array( 'fact', $filters ) ? true : false;
		$trip_type_filter = in_array( 'trip_type', $filters ) ? true : false;
		$trip_location_filter = in_array( 'trip_location', $filters ) ? true : false;
		$price_orderby = in_array( 'price_orderby', $filters ) ? true : false;
		$price_range = in_array( 'price_range', $filters ) ? true : false;
		$trip_dates = in_array( 'trip_dates', $filters ) ? true : false;
	}

	$index = uniqid();

	?>
	<?php
		$price = ( isset( $_GET['price'] ) ) ? $_GET['price'] : '';
		$type = ( int ) ( isset( $_GET['type'] ) && '' !== $_GET['type'] ) ? $_GET['type'] : 0;
		$location = ( int ) ( isset( $_GET['location'] ) && '' !== $_GET['location'] ) ? $_GET['location'] : 0;
		$min_price = ( int ) ( isset( $_GET['min_price'] ) && '' !== $_GET['min_price'] ) ? $_GET['min_price'] : '';
		$max_price = ( int ) ( isset( $_GET['max_price'] ) && '' !== $_GET['max_price'] ) ? $_GET['max_price'] : '';
		$trip_start = ( int ) ( isset( $_GET['trip_start'] ) && '' !== $_GET['trip_start'] ) ? $_GET['trip_start'] : '';
		$trip_end = ( int ) ( isset( $_GET['trip_end'] ) && '' !== $_GET['trip_end'] ) ? $_GET['trip_end'] : '';
	
	ob_start();

	?>
	<div class="widget_wp_travel_filter_search_widget">
	<div class="wp-travel-itinerary-items">
				<div>
				<?php if ( $keyword_search ) : ?>
					<div class="wp-travel-form-field ">
						<label><?php esc_html_e( 'Keyword:', 'wp-travel' ) ?></label>
							<?php $placeholder = __( 'Ex: Trekking', 'wp-travel' ); ?>
							<input class="wp_travel_search_widget_filters_input<?php echo esc_attr($index); ?>" type="text" name="keyword" id="wp-travel-filter-keyword" value="<?php echo ( isset( $_GET['keyword'] ) ) ? esc_textarea( $_GET['keyword'] ) : ''; ?>" placeholder="<?php echo esc_attr( apply_filters( 'wp_travel_search_placeholder', $placeholder ) ); ?>">
					</div>
				<?php endif; ?>
				<?php if ( $fact ) : ?>
					<div class="wp-travel-form-field ">
						<label><?php esc_html_e( 'Trip Fact:', 'wp-travel' ) ?></label>
							<?php $placeholder = __( 'Ex: guide', 'wp-travel' ); ?>
							<input class="wp_travel_search_widget_filters_input<?php echo esc_attr($index); ?>" type="text" name="fact" id="wp-travel-filter-fact" value="<?php echo ( isset( $_GET['fact'] ) ) ? esc_textarea( $_GET['fact'] ) : ''; ?>" placeholder="<?php echo esc_attr( apply_filters( 'wp_travel_search_placeholder', $placeholder ) ); ?>">
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
						<select name="price" class="wp_travel_search_widget_filters_input<?php echo esc_attr($index); ?> price">
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
							<input type="hidden" class="wp_travel_search_widget_filters_input<?php echo esc_attr($index); ?> wp-travel-filter-price-min" name="min_price" value="<?php echo $min_price; ?>">
							<input type="hidden" class="wp_travel_search_widget_filters_input<?php echo esc_attr($index); ?> wp-travel-filter-price-max" name="max_price" value="<?php echo $max_price; ?>">
							<div class="wp-travel-range-slider"></div>
						</div>
				<?php endif; ?>
				<?php if ( $trip_dates ) : ?>
					<div class="wp-travel-form-field wp-travel-trip-duration">
						<label><?php esc_html_e('Trip Duration', 'wp-travel'); ?></label>
						<span class="trip-duration-calender">
							<small><?php esc_html_e( 'From', 'wp-travel' ); ?></small>
							<input value="<?php echo esc_attr( $trip_start ); ?>" class="wp_travel_search_widget_filters_input<?php echo esc_attr($index); ?>" type="text" id="datepicker1" name="trip_start">
							<label for="datepicker1">
								<span class="calender-icon"></span>
							</label>
						</span>
						<span class="trip-duration-calender">
							<small><?php esc_html_e( 'To', 'wp-travel' ); ?></small>
							<input value="<?php echo esc_attr( $trip_end ); ?>" class="wp_travel_search_widget_filters_input<?php echo esc_attr($index); ?>" type="text" id="datepicker2" name="trip_end" data-position='bottom right'>
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
					<input type="submit" id="wp-travel-filter-search-submit" class="button button-primary wp-travel-filter-search-submit" value="Search">
				</div>
					
				</div>

			</div>

			</div>

<?php

	$data = ob_get_clean();

	return $data;

}
	/**
	 * Trip facts Shortcode callback.
	 */
	public function wp_travel_trip_facts_shortcode( $atts, $content = '' ) {

		$trip_id = ( isset( $atts['id'] ) && '' != $atts['id'] ) ? $atts['id'] : false;

		if ( ! $trip_id ) {

			return;
		}

		$settings = wp_travel_get_settings();

		if ( ! isset( $settings['wp_travel_trip_facts_settings'] ) && ! count( $settings['wp_travel_trip_facts_settings'] ) > 0 ) {
			return '';
		}

		$wp_travel_trip_facts = get_post_meta( $trip_id, 'wp_travel_trip_facts', true );

		if ( is_string( $wp_travel_trip_facts ) && '' != $wp_travel_trip_facts ){

			$wp_travel_trip_facts = json_decode( $wp_travel_trip_facts,true );

		}

		if ( is_array( $wp_travel_trip_facts ) && count( $wp_travel_trip_facts ) > 0 ) {

				ob_start();
			?>

			<!-- TRIP FACTS -->
			<div class="tour-info">
				<div class="tour-info-box clearfix">
					<div class="tour-info-column clearfix">
						<?php foreach ( $wp_travel_trip_facts as $key => $trip_fact ) : ?>
							<?php

								$icon = array_filter( $settings['wp_travel_trip_facts_settings'], function( $setting ) use ( $trip_fact ) {

									return $setting['name'] === $trip_fact['label'];
								} );

							foreach ( $icon as $key => $ico ) {

								$icon = $ico['icon'];
							}
							?>
							<span class="tour-info-item tour-info-type">
								<i class="fa <?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
								<strong><?php echo esc_html( $trip_fact['label'] );?></strong>: 
								<?php 
								if ( $trip_fact['type'] === 'multiple' ) {
									$count = count( $trip_fact['value'] );
									$i = 1;
									foreach ( $trip_fact['value'] as $key => $val ) {
										echo esc_html( $val );
										if ( $count > 1 && $i !== $count ) {
											echo esc_html( ',', 'wp-travel' );
										}
									$i++;
									}

								}
								else {
									echo esc_html( $trip_fact['value'] );
								}

								?>  
							</span>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<!-- TRIP FACTS END -->
			<?php
				
				$content = ob_get_clean();
			

			return $content;

		}
	}

}
