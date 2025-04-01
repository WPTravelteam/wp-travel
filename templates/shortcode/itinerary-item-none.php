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
 * @see     http://docs.wensolutions.com/document/template-structure/
 * @author  WenSolutions
 * @package WP_Travel
 * @since   1.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php

if ( post_password_required() ) {
$allow_html =  wp_kses_allowed_html();
	$allow_html[ 'form' ] = array(
		'class' => true,
		'action' => true,
		'method' => true
	);
	$allow_html[ 'input' ] = array(
		'type' => true,
		'class' => true,
		'name' => true,
		'id' => true,
		'value' => true,
		'spellcheck' => true,
		'size' => true,
	);
	$allow_html[ 'label' ] = array(
		'class' => true,
		'for' => true
	);
	$allow_html[ 'p' ] = array(
		'class' => true
	);

	echo wp_kses( get_the_password_form(), $allow_html );

	return;
}
?>

<p class="itinerary-none wp-travel-no-detail-found-msg"><?php esc_html_e( 'Trips not found!', 'wp-travel' ); ?></p>

