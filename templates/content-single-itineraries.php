<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php
	 do_action( 'wp_travel_before_single_itinerary' );
	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>

<div id="itinerary-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
	<?php the_title('<h1 class="entry-title">', '</h1>'); ?>
	</header>
	<div class="content entry-content">

		<?php
			echo apply_filters( 'the_content', get_the_content() );
		?>

	</div><!-- .summary -->

</div><!-- #itinerary-<?php the_ID(); ?> -->

<?php do_action( 'wp_travel_after_single_itinerary' ); ?>
