<?php
global $post;

$map_data = get_wp_travel_map_data();


?>
<div class="location-wrap itineraries-tax-wrap">
  <?php
  post_categories_meta_box( $post, array( 'args' => array( 'taxonomy' => 'travel_locations' ) ) );
  printf( '<div class="tax-edit"><a href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=travel_locations&post_type=itineraries' ) ) . '">%s</a></div>', esc_html__( 'Edit All Locations' ) );
  ?>
</div>

<div class="map-wrap">
  <input id="search-input" class="controls" type="text" placeholder="Enter a location" value="<?php echo esc_html( $map_data['loc'] ); ?>" >
  <div id="gmap" style="width:100%;height:300px"></div>
  <input type="hidden" name="wp_traval_location" id="wp-traval-location" value="<?php echo esc_html( $map_data['loc'] ); ?>" >
  <input type="hidden" name="wp_traval_lat" id="wp-traval-lat" value="<?php echo esc_html( $map_data['lat'] ); ?>" >
  <input type="hidden" name="wp_traval_lng" id="wp-traval-lng" value="<?php echo esc_html( $map_data['lng'] ); ?>" >
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
