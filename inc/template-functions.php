<?php
function wp_travel_get_template( $path, $args ) {
	extract( $args );
	include sprintf( '%s/templates/%s', plugin_dir_path( dirname( __FILE__ ) ), $path );
}
