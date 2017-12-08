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
}
