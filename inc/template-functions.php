<?php
function wp_travel_get_template( $path, $args = array() ) {
	$file =  sprintf( '%s/templates/%s', untrailingslashit( plugin_dir_path( dirname( __FILE__ ) ) ), $path );
	if ( file_exists( $file ) ) {
		return $file;
	}
	return false;
}

function wp_travel_get_template_part( $slug, $name = '' ) {
	$template = '';
	$file_name = ( $name ) ? "{$slug}-{$name}.php" : "{$slug}.php";
	if( $name ) {
		$template = wp_travel_get_template( $file_name );
	}
	if ( $template ) {
    load_template( $template, false );
  }
}

function wp_travel_load_template( $path, $args = array() ) {
	$template = wp_travel_get_template( $path, $args );
	if( $template ){
		include $template;
	}
}

function wp_travel_content_filter( $content ) {

	if ( ! is_singular( 'itineraries' ) ) {
		return $content;
	}
	global $post;

	$trip_price 	= get_post_meta( $post->ID, 'wp_travel_price', true );
	$trip_outline	= get_post_meta( $post->ID, 'wp_travel_outline', true );
	$trip_include	= get_post_meta( $post->ID, 'wp_travel_trip_include', true );
	$trip_exclude	= get_post_meta( $post->ID, 'wp_travel_trip_exclude', true );
	$gallery_ids 	= get_post_meta( $post->ID, 'wp_travel_itinerary_gallery_ids', true );

	$settings = wp_traval_get_settings();

	$currency_code = ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
	$currency_symbol = wp_traval_get_currency_symbol( $currency_code );

	ob_start(); ?>
	<div class="wp-travel-trip-details">
		<div class="wp-travel-trip-code"><?php esc_html_e( 'Trip Code : ', 'wp-travel' ); ?> <span><?php esc_html_e( wp_traval_get_trip_code( $post->ID ), 'wp-travel' ); ?></span></div>

		<div class="wp-travel-trip-detail"><?php esc_html_e( 'Trip Price : ', 'wp-travel' ); ?> <span><?php esc_html_e( $currency_symbol . $trip_price, 'wp-travel' ); ?></span></div>
		<?php if ( count( $gallery_ids ) > 0 ) : ?>
		<div class="wp-travel-gallery">
			<ul>
				<?php foreach ( $gallery_ids as $gallery_id ) : ?>
				<li>
				<?php $gallery_image = wp_get_attachment_image_src( $gallery_id, 'medium' );  ?>
				<a href="<?php echo ( wp_get_attachment_url( $gallery_id ) ); ?>">
				<img src="<?php echo ( $gallery_image[0] ); ?>" />
				</a>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>
		<div class="wp-travel-trip-outline">
			<h4><?php esc_html_e( 'Trip outline', 'wp-travel' ) ?></h4>
			<p>
				<?php esc_html_e( $trip_outline, 'wp-travel' ); ?>
			</p>
		</div>
		<div class="wp-travel-trip-include">
			<h4><?php esc_html_e( 'Trip include', 'wp-travel' ) ?></h4>
			<p>
				<?php esc_html_e( $trip_include, 'wp-travel' ); ?>
			</p>
		</div>
		<div class="wp-travel-trip-exclude">
			<h4><?php esc_html_e( 'Trip exclude', 'wp-travel' ) ?></h4>
			<p>
				<?php esc_html_e( $trip_exclude, 'wp-travel' ); ?>
			</p>
		</div>

		<div class="wp-travel-map">
			<h4><?php esc_html_e( 'Map', 'wp-travel' ) ?></h4>			
			<div id="gmap" style="width:100%;height:300px"></div>
		</div>

	</div>
	<?php
	$content .= ob_get_contents();
	ob_end_clean();
	return $content;
}

add_filter( 'the_content', 'wp_travel_content_filter' );
