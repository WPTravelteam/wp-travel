<?php
function wp_travel_get_gallery_ids( $post_id ) {
	$gallery_ids = get_post_meta( $post_id, 'wp_travel_itinerary_gallery_ids', true );
	if ( false === $gallery_ids || empty( $gallery_ids ) ) {
		return false;
	}
	return $gallery_ids;
}

/** Return All Settings of WP Traval. */
function wp_traval_get_settings() {
	$settings = get_option( 'wp_travel_settings' );
	return $settings;
}

/** Return Trip Code */
function wp_traval_get_trip_code( $post_id ) {
	if ( ! $post_id ) {
		return;
	}

	if ( $post_id < 10 ) {
		$post_id .= '0' . $post_id;
	}

	return apply_filters( 'wp_traval_trip_code', 'NTD-NTO '. $post_id, $post_id );
}

/**
 * Return dropdown.
 *
 * @param  array $args Arguments for dropdown list.
 * @return HTML  return dropdown list.
 */
function wp_traval_get_dropdown_list( $args = array() ) {

	$currency_list = wp_traval_get_currency_list();

	$default = array(
		'id'		=> '',
		'class'		=> '',
		'name'		=> '',
		'option'	=> '',
		'options'	=> '',
		'selected'	=> '',
		);

	$args = array_merge( $default, $args );

	$dropdown = '';
	if ( is_array( $currency_list )  && count( $currency_list ) > 0 ) {
		$dropdown .= '<select name="' . $args['name'] . '" id="' . $args['id'] . '" class="' . $args['class'] . '" >';
		if ( '' != $args['option'] ) {
			$dropdown .= '<option value="" >' . $args['option'] . '</option>';
		}

		foreach ( $currency_list as $key => $currency ) {

			$dropdown .= '<option value="' . $key . '" ' . selected( $args['selected'], $key, false ) . '  >' . $currency . '</option>';
		}
		$dropdown .= '</select>';

	}

	return $dropdown;
}

/**
 * Return Tree Form of post Object.
 *
 * @param Object $elements Post Object.
 * @param Int    $parent_id Parent ID of post.
 * @return Object Return Tree Form of post Object.
 */
function wp_traval_build_post_tree(array &$elements, $parent_id = 0) {
	$branch = array();

	foreach ( $elements as $element ) {
		if ( $element->post_parent == $parent_id ) {
			$children = wp_traval_build_post_tree( $elements, $element->ID );
			if ( $children ) {
				$element->children = $children;
			}
			$branch[ $element->ID ] = $element;
			unset( $elements[ $element->ID ] );
		}
	}
	return $branch;
}

function wp_traval_get_post_hierarchy_dropdown( $list_serialized, $selected, $nesting_level= 0, $echo = true ) {
	$contents = '';
	if ( $list_serialized ) :
		
		$space = '';
		for ( $i=1; $i<= $nesting_level; $i ++ ) {
			$space .= '&nbsp;&nbsp;&nbsp;';
		}

		foreach ( $list_serialized as $content ) {

			$contents .= '<option value="' . $content->ID . '" ' . selected( $selected, $content->ID, false ) . ' >' . $space . $content->post_title . '</option>';
			if ( isset( $content->children ) ) {
				$contents .= wp_traval_get_post_hierarchy_dropdown( $content->children, $selected, ( $nesting_level + 1 ) , false );
			}
		}
	endif;
	if ( ! $echo ) {
		return $contents;
	}

	echo $contents;
	return false;
}