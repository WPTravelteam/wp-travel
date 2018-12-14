<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Function to display OG Tags in front end.
 * 
 * @since 1.7.6
 */
function wp_travel_insert_og_tags() {
    $settings = wp_travel_get_settings();
	$enable_og_tags = isset( $settings['enable_og_tags'] ) ? $settings['enable_og_tags'] : 'no';

    if ( 'yes' !== $enable_og_tags ) {
        return;
    }
    wp_travel_insert_common_tags();

    if ( is_singular( WP_TRAVEL_POST_TYPE ) ) {
        wp_travel_insert_post_tags();			
    }
}

add_action( 'wp_head', 'wp_travel_insert_og_tags' );

/**
 * Common Tags
 */
function wp_travel_insert_common_tags() {
	$sitename = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
    echo '<meta property="og:site_name" content="' . $sitename . '">' . "\n";
}

function wp_travel_insert_post_tags() {
    
    $post_id = get_the_ID();
    $post = get_post( $post_id );
    
    $sitename = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
    $ogtitle = wp_title( '|', false, 'right' ) . ' ' . $sitename;

    // Description.
    if ( has_excerpt( $post ) ) {
        $ogdescription = get_the_excerpt( $post_id );
    } else {
        $ogdescription = $post->post_content;
        if ( strlen( $ogdescription ) > 250 ) {
            $ogdescription = substr( $ogdescription, 0, 250 ) . '...';
        }
    }
    
    $ogurl = get_permalink( $post_id );
    $image_size = apply_filters( 'wp_travel_og_image_size', 'large' );
    // Image   
    $ogimage = wp_travel_get_post_thumbnail_url( $post_id, $image_size );
    
    // Author
    $articleauthor = get_the_author();

    $trip_terms = get_the_terms( $post_id, 'itinerary_types');

    $articlesection = '';    
    if ( is_array( $trip_terms ) && count( $trip_terms ) > 0 && isset( $trip_terms[0] ) && $trip_terms[0]->name ) {
        $articlesection = $trip_terms[0]->name;
    }

    // Tags
    $tags = wp_get_post_tags( $post_id );
    
    foreach ( $tags as $tag ) {
        $articletag = $tag->name;
        echo '<meta property="article:tag" content="' . $articletag . '">' . "\n"; 
    }

    echo '<meta property="og:title" content="' . esc_attr( $ogtitle ) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $ogdescription ) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_attr( $ogurl ) . '">' . "\n";
    echo '<meta property="og:type" content="article">' . "\n";
    echo '<meta property="og:image" content="' . esc_attr( $ogimage ) . '">' . "\n";
    echo '<meta property="article:section" content="' . esc_attr( $articlesection ) . '">' . "\n";
    echo '<meta property="article:publisher" content="https://www.facebook.com/facebook">' . "\n";

}
