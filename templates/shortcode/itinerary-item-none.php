<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php
	 
 if ( post_password_required() ) {
 	echo get_the_password_form();
 	return;
 } ?>

<p class="itinerary-none"><?php esc_html_e( 'Itinerary Item not found!' ) ?></p>

