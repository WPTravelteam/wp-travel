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
