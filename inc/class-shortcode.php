<?php
/**
 * Shortcode callbacks.
 *
 * @package WP_Travel
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
		add_shortcode( 'WP_TRAVEL_ITINERARIES', array( $this, 'get_itineraries_shortcode' ) );
		add_shortcode( 'wp_travel_itineraries', array( $this, 'get_itineraries_shortcode' ) );
		add_shortcode( 'wp_travel_trip_filters', array( $this, 'trip_filters_shortcode' ) );
		add_shortcode( 'wp_travel_trip_facts', array( $this, 'trip_facts_shortcode' ) );
		add_shortcode( 'wp_travel_trip_enquiry_form', array( $this, 'trip_enquiry_form_shortcode' ) );

		add_shortcode( 'WP_TRAVEL_TRIP_CATEGORY_ITEMS', array( $this, 'get_category_items_shortcode' ) );
		add_shortcode( 'wp_travel_trip_category_items', array( $this, 'get_category_items_shortcode' ) );
		add_shortcode( 'wp_travel_itinerary_filter', array( $this, 'wptravel_filter_itinerary' ) );
		add_shortcode( 'WP_TRAVEL_ITINERARY_FILTER', array( $this, 'wptravel_filter_itinerary' ) );

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
		$wrapper_class     = wptravel_get_theme_wrapper_class();
		$wrapper['class'] .= ' ' . $wrapper_class;
		ob_start();

		// @codingStandardsIgnoreStart
		echo empty( $wrapper['before'] ) ? '<div class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
		call_user_func( $function, $atts );
		echo empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];
		// @codingStandardsIgnoreEnd

		return ob_get_clean();
	}

	/**
	 * List of trips as per shortcode attrs.
	 *
	 * @return HTMl Html content.
	 */
	public static function get_itineraries_shortcode( $shortcode_atts, $content = '' ) {
		$default = array(
			'id'           => 0,
			'type'         => '',
			'itinerary_id' => '',
			'view_mode'    => 'grid',
			'slug'         => '',
			'limit'        => 20,
			'col'          => apply_filters( 'wp_travel_itineraries_col_per_row', '3' ),
			// 'orderby'      => 'trip_date',
			'order'        => 'asc',
		);

		$atts = shortcode_atts( $default, $shortcode_atts, 'WP_TRAVEL_ITINERARIES' );

		$type      = $atts['type'];
		$iti_id    = $atts['itinerary_id'];
		$view_mode = $atts['view_mode'];
		$id        = absint( $atts['id'] );
		$slug      = $atts['slug'];
		$limit     = absint( $atts['limit'] );
		$order     = $atts['order'];

		$args = array(
			'post_type'      => WP_TRAVEL_POST_TYPE,
			'posts_per_page' => $limit,
			'status'         => 'published',
		);

		if ( ! empty( $iti_id ) ) :
			$args['p'] = $iti_id;
		else :
			$taxonomies = array( 'itinerary_types', 'travel_locations', 'activity' );
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
		if ( isset( $shortcode_atts['order'] ) ) {
			$args = array(
				'post_type'      => WP_TRAVEL_POST_TYPE,
				'posts_per_page' => $limit,
				'orderby'        => 'post_title',
				'order'          => $order,
				'status'         => 'published',
				'tax_query'		=> isset( $args['tax_query' ] ) ? $args['tax_query'] : '',
				
			);
		}
		// Sorting Start.
		if ( isset( $shortcode_atts['orderby'] ) ) { // if attribute passed from shortcode.

			switch ( $shortcode_atts['orderby'] ) {
				case 'trip_date':
					$args['meta_query'] = array(
						array( 'key' => 'trip_date' ),
					);
					$args['orderby']    = array( 'trip_date' => $atts['order'] );
					break;
				case 'trip_price':
						// @todo: on v4
					break;
			}
		}

		$col_per_row    = $atts['col'];
		$layout_version = wptravel_layout_version();
		$query          = new WP_Query( $args );
		ob_start();
		?>
		<div class="wp-travel-itinerary-items">
			<?php if ( $query->have_posts() ) : ?>
				<?php if ( 'v1' === $layout_version ) : ?>
					<ul style="" class="wp-travel-itinerary-list itinerary-<?php echo esc_attr( $col_per_row ); ?>-per-row  <?php echo esc_attr( 'grid' === $view_mode ? 'grid-view' : '' ); ?>">
						<?php
						while ( $query->have_posts() ) :
							$query->the_post();
							?>
							<?php
							if ( 'grid' === $view_mode ) :
								wptravel_get_template_part( 'shortcode/itinerary', 'item' );
							else :
								wptravel_get_template_part( 'shortcode/itinerary', 'item-list' );
							endif;
							?>
						<?php endwhile; ?>
					</ul>
				<?php else : ?>
					<div class="wp-travel-itinerary-items wptravel-archive-wrapper  <?php echo esc_attr( 'grid' === $view_mode ? 'grid-view' : 'list-view' ); ?> itinerary-<?php echo esc_attr( $col_per_row ); ?>-per-row" >
						<?php
						while ( $query->have_posts() ) :
							$query->the_post();
							wptravel_get_template_part( 'v2/content', 'archive-itineraries' );
						endwhile;
						?>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<?php wptravel_get_template_part( 'shortcode/itinerary', 'item-none' ); ?>
			<?php endif; ?>
		</div>
		<?php
		wp_reset_query();
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	/**
	 * List of taxonomies along with number of trips.
	 *
	 * @since 5.3.0
	 * @return HTMl Html content.
	 */
	public static function get_category_items_shortcode( $shortcode_atts, $content = '' ) {

		$default = array(
			'taxonomy'   => 'travel_locations',
			'orderby'    => 'name',
			'order'      => 'ASC',
			'hide_empty' => false,
			'number'     => '',
			'include'    => array(),
			'exclude'    => array(),
			'child'      => 'no',
			'parent'     => 'no',
		);

		$atts = shortcode_atts( $default, $shortcode_atts, 'WP_TRAVEL_ITINERARIES' );

		// Convert string attr into bool.
		if ( 'string' === gettype( $atts['hide_empty'] ) ) {
			$atts['hide_empty'] = 'true' === $atts['hide_empty'] ? true : false;
		}

		$args = array(
			'taxonomy'   => $atts['taxonomy'],
			'orderby'    => $atts['orderby'],
			'order'      => $atts['order'],
			'hide_empty' => $atts['hide_empty'],
			'number'     => $atts['number'],
			'include'    => $atts['include'],
			'exclude'    => $atts['exclude'],
		);

		$the_query = new WP_Term_Query( $args );
		$terms     = $the_query->get_terms();

		ob_start();

		if ( count( $terms ) > 0 ) {
			?>
				<div class="wp-travel-itinerary-items">
					<div class="wp-travel-itinerary-items wptravel-archive-wrapper grid-view itinerary-3-per-row" >
						<?php
						foreach ( $terms as $term ) {
							if ( $atts['child'] === 'yes' ) {
								if ( $term->parent > 0 ) {
									?>
									<div class="taxonomy-item-wrapper">
										<div class="taxonomy-thumb">
											<a href="<?php echo esc_url( get_term_link( $term->term_id ) ); ?>"><?php echo wptravel_get_term_thumbnail( $term->term_id ); // @phpcs:ignore ?></a>
										</div>
										<div class="taxonomy-content">
											<h4 class="taxonomy-title"><a href="<?php echo esc_url( get_term_link( $term->term_id ) ); ?>"><?php echo esc_html( $term->name ); ?></a></h4>
											<div class="taxonomy-meta">
												<span><i class="fas fa-suitcase-rolling"></i> <?php printf( _n( '%s Trip available', '%s Trips available', $term->count, 'wp-travel' ), esc_html( $term->count ) ); // @phpcs:ignore ?></span>
												<div class="taxonomy-read-more-link"><a href="<?php echo esc_url( get_term_link( $term->term_id ) ); ?>"><?php esc_html_e( 'View', 'wp-travel' ); ?></a></div></div></div>
											</div>
									<?php
								}
							} elseif ( $atts['parent'] === 'yes' ) {
								if ( $term->parent === 0 ) {
									?>
									<div class="taxonomy-item-wrapper">
										<div class="taxonomy-thumb">
											<a href="<?php echo esc_url( get_term_link( $term->term_id ) ); ?>"><?php echo wptravel_get_term_thumbnail( $term->term_id ); // @phpcs:ignore ?></a>
										</div>
										<div class="taxonomy-content">
											<h4 class="taxonomy-title"><a href="<?php echo esc_url( get_term_link( $term->term_id ) ); ?>"><?php echo esc_html( $term->name ); ?></a></h4>
											<div class="taxonomy-meta">
												<span><i class="fas fa-suitcase-rolling"></i> <?php printf( _n( '%s Trip available', '%s Trips available', $term->count, 'wp-travel' ), esc_html( $term->count ) ); // @phpcs:ignore ?></span>
												<div class="taxonomy-read-more-link"><a href="<?php echo esc_url( get_term_link( $term->term_id ) ); ?>"><?php esc_html_e( 'View', 'wp-travel' ); ?></a></div></div></div>
											</div>
									<?php
								}
							} else {
								?>
								<div class="taxonomy-item-wrapper">
									<div class="taxonomy-thumb">
										<a href="<?php echo esc_url( get_term_link( $term->term_id ) ); ?>"><?php echo wptravel_get_term_thumbnail( $term->term_id ); // @phpcs:ignore ?></a>
									</div>
									<div class="taxonomy-content">
										<h4 class="taxonomy-title"><a href="<?php echo esc_url( get_term_link( $term->term_id ) ); ?>"><?php echo esc_html( $term->name ); ?></a></h4>
										<div class="taxonomy-meta">
											<span><i class="fas fa-suitcase-rolling"></i> <?php printf( _n( '%s Trip available', '%s Trips available', $term->count, 'wp-travel' ), esc_html( $term->count ) ); // @phpcs:ignore ?></span>
											<div class="taxonomy-read-more-link"><a href="<?php echo esc_url( get_term_link( $term->term_id ) ); ?>"><?php esc_html_e( 'View', 'wp-travel' ); ?></a></div></div></div>
										</div>
								<?php
							}
						}
						?>
					</div>
				</div>
			<?php
		} else {
			echo esc_html( 'Trips not found !!', 'wp-travel' );
		}
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	/**
	 * Adding itinerary shortcode
	 *
	 * @since 5.3.8
	 */
	public static function wptravel_filter_itinerary( $atts, $content ) {
		$sanitized_get = WP_Travel::get_sanitize_request();
		// $search_widget_fields = wptravel_search_filter_widget_form_fields( $sanitized_get );
		ob_start();
		?>
		<div class="wp-travel-toolbar clearfix">
			<div class="wp-toolbar-content wp-toolbar-left">
			   <?php wptravel_itinerary_filter_by( $sanitized_get ); ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
	/**
	 * WP Travel Trip Filters Shortcode.
	 *
	 * @param String $atts Shortcode Attributes.
	 * @param [type] $content
	 * @return String
	 */
	public static function trip_filters_shortcode( $atts, $content ) {
		$sanitized_get        = WP_Travel::get_sanitize_request();
		$search_widget_fields = wptravel_search_filter_widget_form_fields( $sanitized_get );
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
		wptravel_get_search_filter_form( array( 'shortcode' => $defaults ) );
		echo '</div>';
		return ob_get_clean();
	}

	/**
	 * Trip facts Shortcode callback.
	 */
	public function trip_facts_shortcode( $atts, $content = '' ) {

		$trip_id = ( isset( $atts['id'] ) && '' != $atts['id'] ) ? $atts['id'] : false;

		if ( ! $trip_id ) {

			return;
		}

		$settings = wptravel_get_settings();

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
					<div class="tour-info-column">
						<?php foreach ( $wp_travel_trip_facts as $key => $trip_fact ) : ?>
							<?php

								$icon  = array_filter(
									$settings['wp_travel_trip_facts_settings'],
									function( $setting ) use ( $trip_fact ) {

										return $setting['name'] === $trip_fact['label'];
									}
								);
							$icon_args = array();
							foreach ( $icon as $key => $ico ) {

								$icon      = $ico['icon'];
								$icon_args = $ico;
							}
							?>
							<span class="tour-info-item tour-info-type">
								<?php WpTravel_Helpers_Icon::get( $icon_args ); ?>
								<!-- <i class="fa <?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i> -->
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
	public function trip_enquiry_form_shortcode() {
		ob_start();
		wptravel_get_enquiries_form( true );
		$html = ob_get_clean();
		return $html;
	}

}
