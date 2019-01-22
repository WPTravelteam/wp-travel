<?php
global $post;

$map_data = get_wp_travel_map_data();
$settings = wp_travel_get_settings();
add_action( 'wp_travel_admin_map_area', 'wp_travel_google_map', 10, 2 );

?>
<h4><?php _e( 'Destination', 'wp-travel'); ?></h4>
<div class="location-wrap itineraries-tax-wrap">
  <?php
  post_categories_meta_box( $post, array( 'args' => array( 'taxonomy' => 'travel_locations' ) ) );
  printf( '<div class="tax-edit"><a href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=travel_locations&post_type=itineraries' ) ) . '">%s</a></div>', esc_html__( 'Edit All Locations', 'wp-travel' ) );
  ?>
</div>

<h4><?php _e( 'Map', 'wp-travel'); ?></h4>

  <?php do_action( 'wp_travel_admin_map_area', $settings, $map_data ); ?>
  <br>
  <div class="wp-travel-upsell-message">
    <div class="wp-travel-pro-feature-notice">
      <h4><?php esc_html_e( 'Need alternative maps ?', 'wp-travel' ); ?></h4>
      <p><?php printf( __( 'If you need alternative to current map then you can get free or pro maps for WP Travel. %1$sView WP Travel Map addons%2$s', 'wp-travel' ), '<br><a target="_blank" href="https://wptravel.io/downloads/category/map/">', '</a>' ); ?></p>
    </div>
  </div>
<style>
.map-wrap{
  position: relative;
}
.controls {
    margin-top: 10px;
    border: 1px solid transparent;
    border-radius: 2px 0 0 2px;
    box-sizing: border-box;
    -moz-box-sizing: border-box;
    height: 32px;
    outline: none;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
}
#search-input {
  background-color: #fff;
  font-family: Roboto;
  font-size: 15px;
  font-weight: 300;
  margin-left: 12px;
  padding: 0 11px 0 13px;
  text-overflow: ellipsis;
  width: 67%;
  position: absolute;
  top: 0px;
  z-index: 1;
  left: 165px;
}
#search-input:focus {
    border-color: #4d90fe;
}
</style>

<?php

function wp_travel_google_map( $settings, $map_data ) {

  $api_key = '';

  $get_maps = wp_travel_get_maps();
  $current_map = $get_maps['selected'];

  $show_google_map = ( 'google-map' === $current_map ) ? true : false;
  $show_google_map = apply_filters( 'wp_travel_load_google_maps_api', $show_google_map );

  if ( isset( $settings['google_map_api_key'] ) && '' != $settings['google_map_api_key'] ) {
    $api_key = $settings['google_map_api_key'];
  }

  if ( $show_google_map ) {
    if ( '' != $api_key ) : ?>
      <div class="map-wrap">
        <input id="search-input" class="controls" type="text" placeholder="Enter a location" value="<?php echo esc_html( $map_data['loc'] ); ?>" >
        <div id="gmap" style="width:100%;height:300px"></div>
        <input type="hidden" name="wp_travel_location" id="wp-travel-location" value="<?php echo esc_html( $map_data['loc'] ); ?>" >
        <input type="hidden" name="wp_travel_lat" id="wp-travel-lat" value="<?php echo esc_html( $map_data['lat'] ); ?>" >
        <input type="hidden" name="wp_travel_lng" id="wp-travel-lng" value="<?php echo esc_html( $map_data['lng'] ); ?>" >
      </div>
    <?php else : ?>
      <div class="map-wrap">
      <p class="good" id="pass-strength-result"><?php echo sprintf( "Please add 'google map api key' in the <a href=\"edit.php?post_type=itinerary-booking&page=settings\">settings</a>" ) ?></p>
    </div>
    <?php endif;
  }
}


