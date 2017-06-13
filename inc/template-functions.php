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

	$settings = wp_traval_get_settings();

	ob_start();
	do_action( 'wp_travel_before_trip_details', $post, $settings );
	?>
	<div class="wp-travel-trip-details">
		<?php do_action( 'wp_travel_trip_details', $post, $settings ); ?>
	</div>
	<?php
	do_action( 'wp_travel_after_trip_details', $post, $settings );
	$content .= ob_get_contents();
	ob_end_clean();
	return $content;
}

add_filter( 'the_content', 'wp_travel_content_filter' );


add_action ( 'wp_travel_trip_details', 'wp_travel_trip_code', 10, 2 );
add_action ( 'wp_travel_trip_details', 'wp_travel_trip_price', 10, 2 );
add_action ( 'wp_travel_trip_details', 'wp_travel_trip_gallery', 10, 2 );
add_action ( 'wp_travel_trip_details', 'wp_travel_trip_outline', 10, 2 );
add_action ( 'wp_travel_trip_details', 'wp_travel_trip_include', 10, 2 );
add_action ( 'wp_travel_trip_details', 'wp_travel_trip_exclude', 10, 2 );
add_action ( 'wp_travel_trip_details', 'wp_travel_trip_map', 10, 2 );


function wp_travel_trip_code( $post, $settings ) {
?>
	<div class="wp-travel-trip-code"><?php esc_html_e( 'Trip Code : ', 'wp-travel' ); ?> <span><?php esc_html_e( wp_traval_get_trip_code( $post->ID ), 'wp-travel' ); ?></span></div>
<?php
}

function wp_travel_trip_price( $post, $settings ) {
	$trip_price 	= get_post_meta( $post->ID, 'wp_travel_price', true );
	$currency_code = ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
	$currency_symbol = wp_traval_get_currency_symbol( $currency_code ); ?>
	<div class="wp-travel-trip-detail"><?php esc_html_e( 'Trip Price : ', 'wp-travel' ); ?> <span><?php echo apply_filters( 'wp_travel_itinerary_price', $currency_symbol . $trip_price, $currency_symbol, $trip_price ) ; ?></span></div>
	
<?php
} 


function wp_travel_trip_gallery( $post, $settings ) {
	$gallery_ids 	= get_post_meta( $post->ID, 'wp_travel_itinerary_gallery_ids', true ); ?>

	<?php if ( count( $gallery_ids ) > 0 ) : ?>
		<div class="wp-travel-gallery">
		<h4><?php esc_html_e( 'Gallery', 'wp-travel' ); ?></h4>
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
	
<?php
}

function wp_travel_trip_outline( $post, $settings ) {
	$trip_outline	= get_post_meta( $post->ID, 'wp_travel_outline', true ); ?>
	<?php if ( '' != $trip_outline ) : ?>
	<div class="wp-travel-trip-outline">
		<h4><?php esc_html_e( 'Trip outline', 'wp-travel' ) ?></h4>
		<p>
			<?php _e( wpautop( $trip_outline ), 'wp-travel' ); ?>
		</p>
	</div>
	<?php endif; ?>
	
<?php
}

function wp_travel_trip_include( $post, $settings ) {
	$trip_include	= get_post_meta( $post->ID, 'wp_travel_trip_include', true ); ?>
	<?php if ( '' != $trip_include ) : ?>
	<div class="wp-travel-trip-include">
		<h4><?php esc_html_e( 'Trip include', 'wp-travel' ) ?></h4>
		<p>
			<?php _e( wpautop( $trip_include ), 'wp-travel' ); ?>
		</p>
	</div>
	<?php endif; ?>
<?php
}

function wp_travel_trip_exclude( $post, $settings ) {
	$trip_exclude	= get_post_meta( $post->ID, 'wp_travel_trip_exclude', true ); ?>
	<?php if ( '' != $trip_exclude ) : ?>
	<div class="wp-travel-trip-exclude">
		<h4><?php esc_html_e( 'Trip exclude', 'wp-travel' ) ?></h4>
		<p>
			<?php _e( wpautop( $trip_exclude ), 'wp-travel' ); ?>
		</p>
	</div>
	<?php endif; ?>
	
<?php
}

function wp_travel_trip_map( $post, $settings ) {
?>
	<div class="wp-travel-map">
		<h4><?php esc_html_e( 'Map', 'wp-travel' ) ?></h4>
		<div id="gmap" style="width:100%;height:300px"></div>
	</div>
<?php
}

/**
 * Wrapper Start
 */
add_action ( 'wp_travel_before_single_itinerary', 'wp_travel_wrapper_start' );

function wp_travel_wrapper_start() {
	if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$template = get_option( 'template' );

	switch( $template ) {
		case 'twentyeleven' :
			echo '<div id="primary"><div id="content" role="main" class="twentyeleven">';
			break;
		case 'twentytwelve' :
			echo '<div id="primary" class="site-content"><div id="content" role="main" class="twentytwelve">';
			break;
		case 'twentythirteen' :
			echo '<div id="primary" class="site-content"><div id="content" role="main" class="entry-content twentythirteen">';
			break;
		case 'twentyfourteen' :
			echo '<div id="primary" class="content-area"><div id="content" role="main" class="site-content twentyfourteen"><div class="tfWSC">';
			break;
		case 'twentyfifteen' :
			echo '<div id="primary" role="main" class="content-area twentyfifteen"><div id="main" class="site-main t15WSC">';
			break;
		case 'twentysixteen' :
			echo '<div id="primary" class="content-area twentysixteen"><main id="main" class="site-main" role="main">';
			break;
		case 'twentyseventeen' :
			echo '<div class="wrap"><div id="primary" class="content-area twentyseventeen"><div id="main" class="site-main">';
			break;
		default :
			echo '<div id="container"><div id="content" class="classified-content" role="main">';
			break;
	}
}

add_action ( 'wp_travel_after_single_itinerary', 'wp_travel_wrapper_end' );

function wp_travel_wrapper_end() {
	$template = get_option( 'template' );

	switch( $template ) {
		case 'twentyeleven' :
			echo '</div></div>';
			break;
		case 'twentytwelve' :
			echo '</div></div>';
			break;
		case 'twentythirteen' :
			echo '</div></div>';
			break;
		case 'twentyfourteen' :
			echo '</div></div></div>';
			get_sidebar( 'content' );
			break;
		case 'twentyfifteen' :
			echo '</div></div>';
			break;
		case 'twentysixteen' :
			echo '</div></main>';
			break;
		case 'twentyseventeen' :
			echo '</div></div></div>';
			break;
		default :
			echo '</div></div>';
			break;
	}
}