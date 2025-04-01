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
		add_shortcode( 'WP_TRAVEL_SEARCH', array( $this, 'wptravel_search_shortcode' ) );
		add_shortcode( 'WP_TRAVEL_FEATURED_TRIP', array( $this, 'wptravel_featured_trip_shortcode' ) );
		add_shortcode( 'WP_TRAVEL_SALE_TRIP', array( $this, 'wptravel_sale_trip_shortcode' ) );


		add_shortcode( 'WP_TRAVEL_ITINERARIES_BY_MONTHS', array( $this, 'get_itineraries_by_months_shortcode' ) );

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

		$multi_type = explode(",", $type);

		if ( ! empty( $iti_id ) ) :
			$args['p'] = $iti_id;
		else :
			$taxonomies = array( 'itinerary_types', 'travel_locations', 'activity' );
			// if type is taxonomy.
			if( is_array( $multi_type ) && count( $multi_type ) > 1 ){
				$relation = isset( $shortcode_atts['query_relation'] ) ? $shortcode_atts['query_relation'] : 'OR';
		
				$args['tax_query']['relation'] = $relation;

				foreach ( $multi_type as $value ) {

					if( $value == 'travel_locations' ){
						
						$terms = isset( $shortcode_atts['trip_location'] ) ? explode(",", $shortcode_atts['trip_location'] ) : '';

						$args['tax_query'][] = array(
							'taxonomy' => 'travel_locations',
							'field'    => 'term_id',
							'terms'    => $terms,
						);
					}

					if( $value == 'activity' ){
						
						$terms = isset( $shortcode_atts['trip_activity'] ) ? explode(",", $shortcode_atts['trip_activity'] ) : '';

						$args['tax_query'][] = array(
							'taxonomy' => 'activity',
							'field'    => 'term_id',
							'terms'    => $terms,
						);
					}

					if( $value == 'itinerary_types' ){
						
						$terms = isset( $shortcode_atts['trip_types'] ) ? explode(",", $shortcode_atts['trip_types'] ) : '';

						$args['tax_query'][] = array(
							'taxonomy' => 'itinerary_types',
							'field'    => 'term_id',
							'terms'    => $terms,
						);
					}
				}
				
			}else{
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
						),
					);
				}	
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
		if( isset( $shortcode_atts['type'] ) && $shortcode_atts['type'] == 'itinerary' ){
			
			$args = array(
				'post_type'      => WP_TRAVEL_POST_TYPE,
				'posts_per_page' => $limit,
				'status'         => 'published',
			);
			if( isset( $shortcode_atts['tripids'] ) ){
				$trip_ids = explode(",", $shortcode_atts['tripids']);
				$args['post__in'] = $trip_ids;
			}
			
		}
		if( isset( $shortcode_atts['pagination'] ) && $shortcode_atts['pagination'] == 'true' ){
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			$args['paged'] = $paged;
		}

	
		$query          = new WP_Query( $args );
		
		ob_start();
		?>
		<div class="wp-travel-itinerary-items">
			<?php if ( $query->have_posts() ) :
				if ( 'v1' === $layout_version ) : ?>
					<ul style="" class="wp-travel-itinerary-list itinerary-<?php echo esc_attr( $col_per_row ); ?>-per-row  <?php echo esc_attr( 'grid' === $view_mode ? 'grid-view' : '' ); ?>">
						<?php
						while ( $query->have_posts() ) :
							$query->the_post();
							
							if ( 'grid' === $view_mode ) :
								wptravel_get_template_part( 'shortcode/itinerary', 'item' );
							else :
								wptravel_get_template_part( 'shortcode/itinerary', 'item-list' );
							endif;
							
						endwhile; ?>
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
				<?php endif; 
					wp_reset_postdata(); 
					if( isset( $shortcode_atts['pagination'] ) && $shortcode_atts['pagination'] == 'true' ){
						echo '<div class="wp-travel-navigation navigation wp-paging-navigation">';
						$pagination_args = array(
							'total'   => $query->max_num_pages,
							'current' => $paged,
							'prev_text' => '&laquo;',
							'next_text' => '&raquo;',
						);
						$pagination = paginate_links($pagination_args);
						echo wp_kses_post($pagination);
						echo '</div>';
					}
				?>
			<?php else :
				wptravel_get_template_part( 'shortcode/itinerary', 'item-none' );
			endif; ?>
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
			echo esc_html__( 'Trips not found !!', 'wp-travel' );
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
								<strong><?php echo esc_html( $trip_fact['label'] ); ?></strong>:
								<?php
								if ( $trip_fact['type'] === 'multiple' ) {
									$count = count( $trip_fact['value'] );
									$i     = 1;
									foreach ( $trip_fact['value'] as $key => $val ) {
										echo esc_html( $val );
										if ( $count > 1 && $i !== $count ) {
											echo esc_html__( ',', 'wp-travel' );
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

	/**
	 * Search Form shortcode callback
	 *
	 * @return Html
	 */
	public function wptravel_search_shortcode( $atts, $content ){
		ob_start();
		wptravel_search_form($atts);
		$html = ob_get_clean();
		return $html;
	}

	/**
	 * Featured Trip shortcode callback
	 *
	 * @return Html
	 */
	public function wptravel_featured_trip_shortcode($atts, $content){
		ob_start();

		$view_mode = isset($atts['view_mode']) ? $atts['view_mode'] : 'grid';
		$show_post = isset($atts['limit']) ? $atts['limit'] : 3;
		$section_title = isset($atts['title']) ? $atts['title'] : '';
		$col_per_row    = isset($atts['col']) ? $atts['col'] : 3;
		$layout_version = wptravel_layout_version();
		$featured_args = array(
			'posts_per_page'   => absint( $show_post ),
			'offset'           => 0,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'meta_key'         => 'wp_travel_featured',
			'meta_value'       => 'yes',
			'post_type'        => WP_TRAVEL_POST_TYPE,
			'post_status'      => 'publish',
		);
		

		$itineraries = new WP_Query( $featured_args );
		if( !empty( $section_title ) ){
			echo "<h2>".esc_html($section_title)."</h2>";
		}
		?>
		<div class="wp-travel-itinerary-items">
			<?php if ( $itineraries->have_posts() ) : ?>
				<?php if ( 'v1' === $layout_version ) : ?>
					<ul style="" class="wp-travel-itinerary-list itinerary-<?php echo esc_attr( $col_per_row ); ?>-per-row  <?php echo esc_attr( 'grid' === $view_mode ? 'grid-view' : '' ); ?>">
						<?php
						while ( $itineraries->have_posts() ) :
							$itineraries->the_post();
					
							if ( 'grid' === $view_mode ) :
								wptravel_get_template_part( 'shortcode/itinerary', 'item' );
							else :
								wptravel_get_template_part( 'shortcode/itinerary', 'item-list' );
							endif;
						
						endwhile; ?>
					</ul>
				<?php else : ?>
					<div class="wp-travel-itinerary-items wptravel-archive-wrapper  <?php echo esc_attr( 'grid' === $view_mode ? 'grid-view' : 'list-view' ); ?> itinerary-<?php echo esc_attr( $col_per_row ); ?>-per-row" >
						<?php
						while ( $itineraries->have_posts() ) :
							$itineraries->the_post();
							wptravel_get_template_part( 'v2/content', 'archive-itineraries' );
						endwhile;
						?>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<?php wptravel_get_template_part( 'shortcode/itinerary', 'item-none' );
			endif; ?>
		</div>
		<?php
		$html = ob_get_clean();
		return $html;
	}

	/**
	 * Sale Trip shortcode callback
	 *
	 * @return Html
	 */
	public function wptravel_sale_trip_shortcode($atts, $content){
		ob_start();

		$view_mode = isset($atts['view_mode']) ? $atts['view_mode'] : 'grid';
		$show_post = isset($atts['limit']) ? $atts['limit'] : 3;
		$section_title = isset($atts['title']) ? $atts['title'] : '';
		$col_per_row    = isset($atts['col']) ? $atts['col'] : 3;
		$layout_version = wptravel_layout_version();
		$featured_args = array(
			'post_type'      => WP_TRAVEL_POST_TYPE,
			'posts_per_page' => absint( $show_post ),
			'meta_key'       => 'wptravel_enable_sale',
			'meta_query'     => array(
				'key'   => 'wptravel_enable_sale',
				'value' => 1,
			),
		);

		$itineraries = new WP_Query( $featured_args );
		if( !empty( $section_title ) ){
			echo "<h2>".esc_html($section_title)."</h2>";
		}
		?>
		<div class="wp-travel-itinerary-items">
			<?php if ( $itineraries->have_posts() ) :
				if ( 'v1' === $layout_version ) : ?>
					<ul style="" class="wp-travel-itinerary-list itinerary-<?php echo esc_attr( $col_per_row ); ?>-per-row  <?php echo esc_attr( 'grid' === $view_mode ? 'grid-view' : '' ); ?>">
						<?php
						while ( $itineraries->have_posts() ) :
							$itineraries->the_post();
							
							if ( 'grid' === $view_mode ) :
								wptravel_get_template_part( 'shortcode/itinerary', 'item' );
							else :
								wptravel_get_template_part( 'shortcode/itinerary', 'item-list' );
							endif;
							
						endwhile; ?>
					</ul>
				<?php else : ?>
					<div class="wp-travel-itinerary-items wptravel-archive-wrapper  <?php echo esc_attr( 'grid' === $view_mode ? 'grid-view' : 'list-view' ); ?> itinerary-<?php echo esc_attr( $col_per_row ); ?>-per-row" >
						<?php
						while ( $itineraries->have_posts() ) :
							$itineraries->the_post();
							wptravel_get_template_part( 'v2/content', 'archive-itineraries' );
						endwhile;
						?>
					</div>
				<?php endif;
			else :
				wptravel_get_template_part( 'shortcode/itinerary', 'item-none' );
			endif; ?>
		</div>
		<?php
		$html = ob_get_clean();
		return $html;
	}

	public function get_itineraries_by_months_shortcode ( $attrs, $content ){

		$days_offset = 3;

		if( isset($attrs['days_offset']) ){
			$days_offset = (int)$attrs['days_offset'];
		}
		
		global $wpdb;
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, trip_id, start_date, pricing_ids
				FROM {$wpdb->prefix}wt_dates
				WHERE recurring = %d
				  AND start_date >= DATE_ADD(CURDATE(), INTERVAL %d DAY)",
				0, 
				$days_offset 
			)
		);

		usort($results, function($a, $b) {
			return strtotime($a->start_date) - strtotime($b->start_date);
		});

		$months_label = array(
			'01' => esc_html( 'January', 'wp-travel' ),
			'02' => esc_html( 'February', 'wp-travel' ),
			'03' => esc_html( 'March', 'wp-travel' ),
			'04' => esc_html( 'April', 'wp-travel' ),
			'05' => esc_html( 'May', 'wp-travel' ),
			'06' => esc_html( 'June', 'wp-travel' ),
			'07' => esc_html( 'July', 'wp-travel' ),
			'08' => esc_html( 'August', 'wp-travel' ),
			'09' => esc_html( 'September', 'wp-travel' ),
			'10' => esc_html( 'October', 'wp-travel' ),
			'11' => esc_html( 'November', 'wp-travel' ),
			'12' => esc_html( 'December', 'wp-travel' )
		);

		ob_start();

		if($results):
		?>
		<div id="wp-travel-trip-by-months">
			<table class="table wp-travel-trip-by-months">
				<h3>Trips For <?php echo esc_html( date("Y") ); ?></h3>
				<tbody>
			
					<?php 
						$month = '00';
						foreach( $results as $data ){
							$date = $data->start_date;

							if( date('m', strtotime($date)) > $month ){
								$month = date('m', strtotime($date));
								?>
									<tr class="new-month">
										<th colspan="2"><?php echo $months_label[$month]; ?></th>
									</tr>
								<?php
							}

							$pricing_ids = explode(",", $data->pricing_ids);
	
							foreach( $pricing_ids as $id ){
								$inventory_args = '';
								$booking_full = false;
								if( class_exists( 'WP_Travel_Pro' ) ){
									$args = array(
										'trip_id'       => (int)$data->trip_id,
										'pricing_id'    => (int)$id,
										'selected_date' => $date,
										'times'         => '',
									);
									
									if( !isset( WP_Travel_Helpers_Inventory::get_inventory( $args )->errors ) ){
										$inventory_args = WP_Travel_Helpers_Inventory::get_inventory( $args )['inventory'][0];

										if( (int)$inventory_args['booked_pax'] == (int)$inventory_args['pax_limit'] ){
											$booking_full = true;
										}else{
											$booking_full = false;
										}
									}
									
								}
								
								$pricing_results = $wpdb->get_results(
									$wpdb->prepare(
										"SELECT price_per, regular_price, is_sale, sale_price, is_sale_percentage, sale_percentage_val
										FROM {$wpdb->prefix}wt_price_category_relation
										WHERE pricing_id = %d",
										(int)$id
									)
								)[0];
								$sale_price = 0;
					
								if( $pricing_results->is_sale_percentage == '1' ){
									$sale_price = ((float)$pricing_results->sale_percentage_val / 100) * (float)$pricing_results->regular_price;
								}else{
									$sale_price = $pricing_results->sale_price;
								}

								
								?>	
									<tr class="trip-item">
										<th></th>
										<td class="trip-item"> 
											<a class="<?php echo $booking_full == false ? 'book-active' : 'full-booked'; ?>" href="<?php echo esc_url( get_the_permalink( (int)$data->trip_id ) );?>">
												<span class="trip-title"><?php echo esc_html( get_the_title( (int)$data->trip_id ) );?></span>
												<span class="table-trip-metas">
													<span class="date"> <?php echo esc_html( $date );?> </span>
													<?php if( !empty( $inventory_args ) ): ?>
													<span class="pax"><?php echo sprintf("%d/%d %s", $inventory_args['booked_pax'], $inventory_args['pax_limit'], __( '( Pax )', 'wp-travel' ) ); ?></span> 
													<?php endif; ?>
													<span class="trip-price">
														<?php if( $pricing_results->is_sale == '1' ): ?>
															<del>
																<?php echo wp_kses_post( wptravel_get_formated_price_currency( WpTravel_Helpers_Trip_Pricing_Categories::get_converted_price( $pricing_results->regular_price ) ) ) ?>
															</del>
															<?php echo wp_kses_post( wptravel_get_formated_price_currency( WpTravel_Helpers_Trip_Pricing_Categories::get_converted_price( $sale_price ) ) ) ?>
															<?php else: ?>
																<?php echo wp_kses_post( wptravel_get_formated_price_currency( WpTravel_Helpers_Trip_Pricing_Categories::get_converted_price( $pricing_results->regular_price ) ) ) ?>
														<?php endif; ?>
														
													</span>
													<span class="pricing-per"><?php echo sprintf( "%s %s", __( 'per', 'wp-travel' ), $pricing_results->price_per ); ?></span>
													
													<?php if(!empty($inventory_args)): ?>
														<?php if($booking_full): ?>
															<span class="closed"><?php echo esc_html__('Booking Closed', 'wp-travel'); ?></span> 
															<?php else: ?>
																<span class="active"><?php echo esc_html__('Booking Active', 'wp-travel'); ?></span> 
														<?php endif; ?>
														
														<?php else: ?>
															<span class="active"><?php echo esc_html__('Booking Active', 'wp-travel'); ?></span> 
													<?php endif; ?>
													
												</span>
											</a> 
										</td>
									</tr>
								<?php
							}
						}
					?>
					<tr class="new-month end">
						<th colspan="2"><?php echo esc_html__('End Of Year', 'wp-travel'); ?></th>
					</tr>
				</tbody>
			</table>
		</div>
		
		<style>
			#wp-travel-trip-by-months{
				border: 1px solid #eeeeee;
				padding: 30px;
			}
			#wp-travel-trip-by-months table.wp-travel-trip-by-months {
				width: 100%;
				border-collapse: collapse;
			}

			#wp-travel-trip-by-months table.wp-travel-trip-by-months th, #wp-travel-trip-by-months table.wp-travel-trip-by-months td {
				border: none;
			}

			#wp-travel-trip-by-months tr.new-month {
				background-color: #f0f0f0;
				font-weight: bold;
				padding: 10px 0;
			}

			#wp-travel-trip-by-months tr.new-month th {
				text-align: left;
				padding: 10px 15px;
			}

			#wp-travel-trip-by-months .trip-item a {
				text-decoration: none;
				color: #333;
				display: block;
				padding: 10px;
				border-bottom: 1px solid #dddddd;
			}

			#wp-travel-trip-by-months tr:last-of-type,
			#wp-travel-trip-by-months tr:has(+ tr.new-month).trip-item a {
				border-bottom: none;
			}

			#wp-travel-trip-by-months .trip-item a:hover {
				background-color: #f4f4f4;
			}

			#wp-travel-trip-by-months .trip-title{
				font-weight: 600;
				display: flex;
   			 	align-items: center;
			}

			#wp-travel-trip-by-months .wp-travel-trip-by-months .trip-price,
			#wp-travel-trip-by-months .wp-travel-trip-by-months .pax,
			#wp-travel-trip-by-months .wp-travel-trip-by-months .date {
				font-style: italic;
				color: #777;
				font-size: 16px;
				margin-left: 20px;
				display: flex;
   			 	align-items: center;
			}

			#wp-travel-trip-by-months .wp-travel-trip-by-months .pricing-per{
				font-style: italic;
				margin-left: 3px;
				color: #777;
				display: flex;
   			 	align-items: center;
			}

			#wp-travel-trip-by-months .active {
				font-weight: bold;
				background: green;
				font-size: 12px;
				color: #fff;
				padding: 5px 10px;
				margin-left: 20px;
				display: flex;
   			 	align-items: center;
			}

			#wp-travel-trip-by-months .closed {
				font-weight: bold;
				background: #CC3300;
				font-size: 12px;
				color: #fff;
				padding: 5px 10px;
				margin-left: 20px;
				display: flex;
				align-items: center;
			}

			#wp-travel-trip-by-months .trip-item a {
				display: flex;
				justify-content: space-between;
				width: 100%;
			}

			#wp-travel-trip-by-months .trip-item a .date {
				margin-left: auto;
				margin-right: 10px;
			}

			#wp-travel-trip-by-months span.table-trip-metas {
				width: 70%;
				display: flex;
			}

			.wp-travel-trip-by-months .full-booked{
				pointer-events: none;
			}

			@media (max-width: 960px) {
				#wp-travel-trip-by-months .trip-item a{
					flex-direction: column;
				}

				#wp-travel-trip-by-months span.table-trip-metas {
					width: 100%;
				}

				#wp-travel-trip-by-months .trip-item a .date{
					margin-left: 0px;
				}
			}

			@media (max-width: 690px) {
				#wp-travel-trip-by-months .active,
				#wp-travel-trip-by-months .closed{
					display: none;
				}
			}

			@media (max-width: 560px) {
				#wp-travel-trip-by-months .trip-price del{
					display: none;
				}
			}

			@media (max-width: 500px) {
				#wp-travel-trip-by-months .pricing-per{
					display: none !important;
				}
			}

		</style>
		<?php
			else:
				echo esc_html__( 'No Departure Trips Found.', 'wp-travel' );

		endif;
		$html = ob_get_clean();
		return $html;
	}
}
