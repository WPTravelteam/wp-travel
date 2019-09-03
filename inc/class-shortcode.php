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
 * @version 1.0.0
 */
class Wp_Travel_Shortcodes {

	public function init() {
		add_shortcode( 'WP_TRAVEL_ITINERARIES', array( $this, 'wp_travel_get_itineraries_shortcode' ) );
		add_shortcode( 'wp_travel_itineraries', array( $this, 'wp_travel_get_itineraries_shortcode' ) );
		add_shortcode( 'wp_travel_trip_filters', array( $this, 'wp_travel_trip_filters_shortcode' ) );
		add_shortcode( 'wp_travel_trip_facts', array( $this, 'wp_travel_trip_facts_shortcode' ) );
		add_shortcode( 'wp_travel_trip_enquiry_form', array( $this, 'wp_travel_trip_enquiry_form_shortcode' ) );

		/**
		 * Checkout Shortcodes.
		 *
		 * @since 2.2.3
		 * Shortcodes for new checkout process.
		 */
		$shortcodes = array(
			'wp_travel_cart'         => __CLASS__ . '::cart',
			'wp_travel_checkout'     => __CLASS__ . '::checkout',
			'wp_travel_user_account' => __CLASS__ . '::user_account',
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
	public static function wp_travel_get_itineraries_shortcode( $atts, $content = '' ) {
		$default = array(
			'id'           => 0,
			'type'         => '',
			'itinerary_id' => '',
			'view_mode'    => 'grid',
			'slug'         => '',
			'limit'        => 20,
			'col'          => apply_filters( 'wp_travel_itineraries_col_per_row', "2" ),
		);

		$atts = shortcode_atts( $default, $atts, 'WP_TRAVEL_ITINERARIES' );

		$type      = $atts['type'];
		$iti_id    = $atts['itinerary_id'];
		$view_mode = $atts['view_mode'];
		$id        = absint( $atts['id'] );
		$slug      = $atts['slug'];
		$limit     = absint( $atts['limit'] );

		$args = array(
			'post_type'      => WP_TRAVEL_POST_TYPE,
			'posts_per_page' => $limit,
			'status'         => 'published',
		);

		if ( ! empty( $iti_id ) ) :
			$args['p'] = $iti_id;
		else :
			$taxonomies = array( 'itinerary_types', 'travel_locations' );
			// if type is taxonomy.
			if ( in_array( $type, $taxonomies ) ) {

				if ( $id > 0 ) {
					$args['tax_query'] = array(
						array(
							'taxonomy' => $type,
							'field'    => 'term_id',
							'terms'    => $id,
						),
					);
				} elseif ( '' !== $slug ) {
					$args['tax_query'] = array(
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
						'key'   => 'wp_travel_featured',
						'value' => 'yes',
						// 'compare' => 'IN',
					),
				);
			}

		endif;

		$query = new WP_Query( $args );
		ob_start();
		?>
		<div class="wp-travel-itinerary-items">
			<?php $col_per_row = $atts['col']; ?>
			<?php if ( $query->have_posts() ) : ?>
				<ul style="" class="wp-travel-itinerary-list itinerary-<?php echo esc_attr( $col_per_row ); ?>-per-row">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					?>
					<?php
					if ( 'grid' === $view_mode ) :
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
		<?php
		wp_reset_query();
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	/**
	 * WP Travel Trip Filters Shortcode.
	 *
	 * @param String $atts Shortcode Attributes.
	 * @param [type] $content
	 * @return String
	 */
	public static function wp_travel_trip_filters_shortcode( $atts, $content ) {
		$search_widget_fields = wp_travel_search_filter_widget_form_fields();
		$defaults             = array(
			'keyword_search'       => 1,
			'fact'                 => 1,
			'trip_type_filter'     => 1,
			'trip_location_filter' => 1,
			'price_orderby'        => 1,
			'price_range'          => 1,
			'trip_dates'           => 1,
		);

		$defaults = apply_filters( 'wp_travel_shortcode_atts', $defaults );

		if ( isset( $atts['filters'] ) && 'all' !== $atts['filters'] ) {
			$atts = explode( ',', $atts['filters'] );

			if ( count( $atts ) > 0 ) {
				$defaults = array();
				foreach ( $search_widget_fields as $key => $filter ) {
					if ( isset( $filter['name'] ) ) {
						if ( in_array( $filter['name'], $atts ) ) {
							$defaults[ $key ] = 1;
						}
					} else {
						if ( in_array( $key, $atts ) ) {
							$defaults[ $key ] = 1;
						}
					}
				}
			}
		}
		if ( isset( $atts['exclude'] ) ) {
			$atts = explode( ',', $atts['exclude'] );
			if ( count( $atts ) > 0 ) {
				foreach ( $search_widget_fields as $key => $filter ) {
					if ( isset( $filter['name'] ) && in_array( $filter['name'], $atts ) ) {
						unset( $defaults[ $key ] );
					}
				}
				// foreach ( $atts as $key ) {
				// unset( $defaults[ $key ] );
				// }
			}
		}

		ob_start();
		echo '<div class="widget_wp_travel_filter_search_widget">';
		wp_travel_get_search_filter_form( array( 'shortcode' => $defaults ) );
		echo '</div>';
		return ob_get_clean();
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

		if ( is_string( $wp_travel_trip_facts ) && '' != $wp_travel_trip_facts ) {

			$wp_travel_trip_facts = json_decode( $wp_travel_trip_facts, true );

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

								$icon = array_filter(
									$settings['wp_travel_trip_facts_settings'],
									function( $setting ) use ( $trip_fact ) {

										return $setting['name'] === $trip_fact['label'];
									}
								);

							foreach ( $icon as $key => $ico ) {

								$icon = $ico['icon'];
							}
							?>
							<span class="tour-info-item tour-info-type">
								<i class="fa <?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
								<strong><?php echo esc_html( $trip_fact['label'] ); ?></strong>:
								<?php
								if ( $trip_fact['type'] === 'multiple' ) {
									$count = count( $trip_fact['value'] );
									$i     = 1;
									foreach ( $trip_fact['value'] as $key => $val ) {
										echo esc_html( $val );
										if ( $count > 1 && $i !== $count ) {
											echo esc_html( ',', 'wp-travel' );
										}
										$i++;
									}
								} else {
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

	/**
	 * Enquiry Form shortcode callback
	 *
	 * @return String
	 */
	public function wp_travel_trip_enquiry_form_shortcode() {
		ob_start();
		wp_travel_get_enquiries_form( true );
		$html = ob_get_clean();
		return $html;
	}

}
