<?php
/**
 * Plugin Name: Trip
 * Plugin URI: http://www.wensolutions.com/plugins/trip
 * Description: This plugin is used to add trip for any travel and tour site
 * Version: 1.0.0
 * Author: WEN Solutions
 * Author URI: http://wensolutions.com
 * Requires at least: 4.4
 * Tested up to: 4.7
 *
 * Text Domain: trip
 * Domain Path: /i18n/languages/
 *
 * @package trip
 * @category Core
 * @author WenSolutions
 */

/**
 * Traval Door Init Fonction for init Hook.
 */
function travaldoor_trip_init() {
	$labels = array(
		'name'               => _x( 'Trips', 'post type general name', 'trip' ),
		'singular_name'      => _x( 'Trip', 'post type singular name', 'trip' ),
		'menu_name'          => _x( 'Traval Door Trips', 'admin menu', 'trip' ),
		'name_admin_bar'     => _x( 'Trip', 'add new on admin bar', 'trip' ),
		'add_new'            => _x( 'Add New', 'trip', 'trip' ),
		'add_new_item'       => __( 'Add New trip', 'trip' ),
		'new_item'           => __( 'New trip', 'trip' ),
		'edit_item'          => __( 'Edit trip', 'trip' ),
		'view_item'          => __( 'View trip', 'trip' ),
		'all_items'          => __( 'All trips', 'trip' ),
		'search_items'       => __( 'Search trips', 'trip' ),
		'parent_item_colon'  => __( 'Parent trips:', 'trip' ),
		'not_found'          => __( 'No trips found.', 'trip' ),
		'not_found_in_trash' => __( 'No trips found in Trash.', 'trip' ),
	);

	$args = array(
		'labels'             => $labels,
		'description'        => __( 'Description.', 'trip' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'trip' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'thumbnail' ),
		'menu_icon'          => 'dashicons-location',
	);
	/**
	 * Register a travaldoor_trip post type.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	register_post_type( 'travaldoor_trip', $args );

	// Add new taxonomy, make it hierarchical (like categories).
	$labels = array(
		'name'              => _x( 'Locations', 'taxonomy general name', 'textdomain' ),
		'singular_name'     => _x( 'Location', 'taxonomy singular name', 'textdomain' ),
		'search_items'      => __( 'Search Locations', 'textdomain' ),
		'all_items'         => __( 'All Locations', 'textdomain' ),
		'parent_item'       => __( 'Parent Location', 'textdomain' ),
		'parent_item_colon' => __( 'Parent Location:', 'textdomain' ),
		'edit_item'         => __( 'Edit Location', 'textdomain' ),
		'update_item'       => __( 'Update Location', 'textdomain' ),
		'add_new_item'      => __( 'Add New Location', 'textdomain' ),
		'new_item_name'     => __( 'New Location Name', 'textdomain' ),
		'menu_name'         => __( 'Location', 'textdomain' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'location' ),
	);

	register_taxonomy( 'travaldoor_location', array( 'travaldoor_trip' ), $args );

	// Add new taxonomy, make it hierarchical (like categories).
	$labels2 = array(
		'name'              => _x( 'Trip Types', 'taxonomy general name', 'textdomain' ),
		'singular_name'     => _x( 'Trip Type', 'taxonomy singular name', 'textdomain' ),
		'search_items'      => __( 'Search Trip Types', 'textdomain' ),
		'all_items'         => __( 'All Trip Types', 'textdomain' ),
		'parent_item'       => __( 'Parent Trip Type', 'textdomain' ),
		'parent_item_colon' => __( 'Parent Trip Type:', 'textdomain' ),
		'edit_item'         => __( 'Edit Trip Type', 'textdomain' ),
		'update_item'       => __( 'Update Trip Type', 'textdomain' ),
		'add_new_item'      => __( 'Add New Trip Type', 'textdomain' ),
		'new_item_name'     => __( 'New Tour Trip Name', 'textdomain' ),
		'menu_name'         => __( 'Trip Type', 'textdomain' ),
	);

	$args2 = array(
		'hierarchical'      => true,
		'labels'            => $labels2,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'trip_type' ),
	);

	register_taxonomy( 'travaldoor_trip_type', array( 'travaldoor_trip' ), $args2 );
}
add_action( 'init', 'travaldoor_trip_init' );

/**
 * Traval Door Scripts and styles.
 */
function travaldoor_admin_scripts() {
	$screen = get_current_screen();
	if ( 'travaldoor_trip' === $screen->id ) {
		wp_enqueue_style( 'traval-door-style', plugin_dir_url( __FILE__ ) . 'style.css' );
	}
}

add_action( 'admin_enqueue_scripts', 'travaldoor_admin_scripts' );

/**
 * Adding Metaboxes for traval door trip.
 *
 * @param string $post_type Post type name.
 * @param Object $post Post obj.
 */
function travaldoor_meta_boxes( $post_type, $post ) {

	add_meta_box(
		'travaldoor_trip_detail',
		__( 'Trip Detail' ),
		'travaldoor_trip_detail_html',
		'travaldoor_trip',
		'normal',
		'default'
	);
	add_meta_box(
		'travaldoor_trip_facts',
		__( 'Trip Facts' ),
		'travaldoor_trip_facts_html',
		'travaldoor_trip',
		'normal',
		'default'
	);
}

add_action( 'add_meta_boxes', 'travaldoor_meta_boxes', 10, 2 );

/**
 * Callback of Trip Fact meta box
 *
 * @param Object $post Post obj.
 */
function travaldoor_trip_facts_html( $post ) {
	if ( ! $post ) {
		return;
	}
	$travaldoor_attr = get_post_meta( $post->ID, 'travaldoor_attr', true );

	$trek_duration 	= isset( $travaldoor_attr['trek_duration'] ) ? $travaldoor_attr['trek_duration'] : '';
	$trip_code 		= isset( $travaldoor_attr['trip_code'] ) ? $travaldoor_attr['trip_code'] : '';
	$activity 		= isset( $travaldoor_attr['activity'] ) ? $travaldoor_attr['activity'] : '';
	$accomodation 	= isset( $travaldoor_attr['accomodation'] ) ? $travaldoor_attr['accomodation'] : '';
	$best_seasons 	= isset( $travaldoor_attr['best_seasons'] ) ? $travaldoor_attr['best_seasons'] : '';
	$trip_begins 	= isset( $travaldoor_attr['trip_begins'] ) ? $travaldoor_attr['trip_begins'] : '';
	$trip_ends 		= isset( $travaldoor_attr['trip_ends'] ) ? $travaldoor_attr['trip_ends'] : '';
	?>
	<div class="travaldoor-inputs">
		<?php  wp_nonce_field( 'save_traval_meta_action', 'save_traval_meta' );  ?>
		<div class="form-controls">
			<label> 
				<span class="form-controls-label">Trek Duration :</span>
				<input type="text" name="travaldoor[trek_duration]" value="<?php echo esc_attr( $trek_duration ); ?>" class="travaldoor_trek_duration" >
			</label>
		</div>
		<div class="form-controls">
			<label> 
				<span class="form-controls-label">Trip Code :</span>
				<input type="text" name="travaldoor[trip_code]" value="<?php echo esc_attr( $trip_code ); ?>" class="travaldoor_trip_code" >
			</label>
		</div>
		<div class="form-controls">
			<label> 
				<span class="form-controls-label">Activity :</span>
				<input type="text" name="travaldoor[activity]" value="<?php echo esc_attr( $activity ); ?>" class="travaldoor_activity" >
			</label>
		</div>
		<div class="form-controls">
			<label> 
				<span class="form-controls-label">Accomodation :</span>
				<input type="text" name="travaldoor[accomodation]" value="<?php echo esc_attr( $accomodation ); ?>" class="travaldoor_accomodation" >
			</label>
		</div>
		<div class="form-controls">
			<label> 
				<span class="form-controls-label">Best Seasons :</span>
				<input type="text" name="travaldoor[best_seasons]" value="<?php echo esc_attr( $best_seasons ); ?>" class="travaldoor_best_seasons" >
			</label>
		</div>
		<div class="form-controls">
			<label> 
				<span class="form-controls-label">Trip Begins :</span>
				<input type="text" name="travaldoor[trip_begins]" value="<?php echo esc_attr( $trip_begins ); ?>" class="travaldoor_trip_begins" >
			</label>
		</div>
		<div class="form-controls">
			<label> 
				<span class="form-controls-label">Trip Ends :</span>
				<input type="text" name="travaldoor[trip_ends]" value="<?php echo esc_attr( $trip_ends ); ?>" class="travaldoor_trip_ends" >
			</label>
		</div>
	</div>
<?php
}

/**
 * Callback of Trip Fact meta box
 *
 * @param Object $post Post obj.
 */
function travaldoor_trip_detail_html( $post ) {
	if ( ! $post ) {
		return;
	}
	$travaldoor_attr = get_post_meta( $post->ID, 'travaldoor_attr', true );

	$trip_highlights 	= isset( $travaldoor_attr['trip_highlights'] ) ? $travaldoor_attr['trip_highlights'] : '';
	$trip_include 		= isset( $travaldoor_attr['trip_include'] ) ? $travaldoor_attr['trip_include'] : '';
	$trip_exclude 		= isset( $travaldoor_attr['trip_exclude'] ) ? $travaldoor_attr['trip_exclude'] : '';
	$trip_itinerary 		= isset( $travaldoor_attr['trip_itinerary'] ) ? $travaldoor_attr['trip_itinerary'] : '';
	?>
	<div class="travaldoor-inputs">
		<?php  wp_nonce_field( 'save_traval_meta_action', 'save_traval_meta' );  ?>
		<div class="form-controls">
			<label> 
				<span class="form-controls-label">Trip Highlights :</span>
				<textarea name="travaldoor[trip_highlights]" rows="6" cols="30" class="travaldoor_trip_highlights"><?php echo esc_attr( $trip_highlights ); ?></textarea>				
			</label>
			<span class="travaldoor_note">list per line</span>
		</div>
		<div class="form-controls">
			<label> 
				<span class="form-controls-label">Trip Include :</span>
				<textarea name="travaldoor[trip_include]" rows="6" cols="30" class="travaldoor_trip_include"><?php echo esc_attr( $trip_include ); ?></textarea>				
			</label>
			<span class="travaldoor_note">list per line</span>
		</div>
		<div class="form-controls">
			<label> 
				<span class="form-controls-label">Trip Exclude :</span>
				<textarea name="travaldoor[trip_exclude]" rows="6" cols="30" class="travaldoor_trip_exclude"><?php echo esc_attr( $trip_exclude ); ?></textarea>				
			</label>
			<span class="travaldoor_note">list per line</span>
		</div>
		<div class="form-controls">
			<label> 
				<span class="form-controls-label">Trip Itinerary :</span>
				<?php
				$settings  = array( 'media_buttons' => false, 'textarea_name' => 'travaldoor[trip_itinerary]' );
				wp_editor( $trip_itinerary, 'trip_itinerary', $settings ); ?>				
			</label>
		</div>		
	</div>
<?php
}

/**
 * Save Traval Door Post meta data.
 *
 * @param  int $post_id Current post id.
 * @return post id or null
 */
function travaldoor_save_postmeta( $post_id ) {
	if ( 'travaldoor_trip' !== get_post_type( $post_id ) ) {
		return $post_id;
	}

	// Bail if we're doing an auto save.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// if our current user can't edit this post, bail.
	if ( ! current_user_can( 'edit_post' , $post_id ) ) {
		return $post_id;
	}

	if ( ! isset( $_POST['travaldoor'] ) ) {
		return $post_id;
	}

	if ( ! isset( $_POST['save_traval_meta'] ) ) {
		return $post_id;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['save_traval_meta'] ) ), 'save_traval_meta_action' ) ) {
		return $post_id;
	}

	$travaldoor_meta = array_map( 'sanitize_text_field', wp_unslash( $_POST['travaldoor'] ) );

	// Overriding some vars.
	$travaldoor_meta['trip_highlights'] = wp_kses( wp_unslash( $_POST['travaldoor']['trip_highlights'] ), array( 'br' => array( '\n' ) ) );
	$travaldoor_meta['trip_include'] = wp_kses( wp_unslash( $_POST['travaldoor']['trip_include'] ), array( 'br' => array( '\n' ) ) );
	$travaldoor_meta['trip_exclude'] = wp_kses( wp_unslash( $_POST['travaldoor']['trip_exclude'] ), array( 'br' => array( '\n' ) ) );

	$travaldoor_meta['trip_itinerary'] = ( $_POST['travaldoor']['trip_itinerary'] );

	update_post_meta( $post_id, 'travaldoor_attr', $travaldoor_meta );
}

add_action( 'save_post', 'travaldoor_save_postmeta' );
