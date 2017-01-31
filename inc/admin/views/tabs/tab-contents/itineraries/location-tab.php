<?php
global $post;

$post_meta_lat = get_post_meta( $post->ID, 'wp_traval_lat', true );
$post_meta_lng = get_post_meta( $post->ID, 'wp_traval_lng', true );
$post_meta_loc = get_post_meta( $post->ID, 'wp_traval_location', true );

$lat = -12.043333;
if ( isset( $post_meta_lat ) && '' != $post_meta_lat ) {
	$lat = $post_meta_lat;
}

$lng = -77.028333;
if ( isset( $post_meta_lng ) && '' != $post_meta_lng ) {
	$lng = $post_meta_lng;
}

$loc = __( 'Lima' );
if ( isset( $post_meta_loc ) && '' != $post_meta_loc ) {
	$loc = $post_meta_loc;
} ?>
<div class="map-wrap">
  <input id="search-input" class="controls" type="text" placeholder="Enter a location" value="<?php echo esc_html( $loc ); ?>" >
  <div id="gmap" style="width:100%;height:300px"></div>
  <input type="hidden" name="wp_traval_location" id="wp-traval-location" value="<?php echo esc_html( $lat ); ?>" >
  <input type="hidden" name="wp_traval_lat" id="wp-traval-lat" value="<?php echo esc_html( $lng ); ?>" >
  <input type="hidden" name="wp_traval_lng" id="wp-traval-lng" value="<?php echo esc_html( $loc ); ?>" >
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
