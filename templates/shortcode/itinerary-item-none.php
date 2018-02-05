<?php
/**
 * Itinerary Shortcode Contnet None Template
 *
 * This template can be overridden by copying it to yourtheme/wp-travel/shortcode/itinerary-item-none.php.
 *
 * HOWEVER, on occasion wp-travel will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.wensolutions.com/document/template-structure/
 * @author      WenSolutions
 * @package     wp-travel/Templates
 * @since       1.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php
	 
 if ( post_password_required() ) {
 	echo get_the_password_form();
 	return;
 } ?>

<p class="itinerary-none wp-travel-no-detail-found-msg"><?php esc_html_e( 'Trips not found!', 'wp-travel' ) ?></p>

