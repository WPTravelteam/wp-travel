<?php

global $post;
$obj = get_post_type_object( 'itineraries' );

echo '<div class="trip-type-wrap">';
post_categories_meta_box( $post, array( 'args' => array( 'taxonomy' => 'itinerary_types' ) ) );
printf( '<div class="tax-edit"><a href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=itinerary_types&post_type=itineraries' ) ) . '">%s</a></div>', esc_html__( 'Edit All Trip Type' ) );
echo '</div>';