<?php
/**
 * User dashboard template.
 *
 * @package WP_Travel
 */

// Print Errors / Notices.
wptravel_print_notices();

// Set User.
$current_user         = $args['current_user'];
$user_dashboard_menus = wptravel_sort_array_by_priority( $args['dashboard_menus'] );
$bookings             = get_user_meta( $current_user->ID, 'wp_travel_user_bookings', true );
$bookings_glance      = false;

$getBooking = new WP_Query([
	'post_type' => 'itinerary-booking',
	'posts_per_page' => -1
	
]);
if( empty( $bookings ) ) {
	$bookings = [];
}
while($getBooking->have_posts()) {
	$getBooking->the_post();
	$traveler_data = get_post_meta( get_the_ID(), 'wp_travel_email_traveller', true);
	foreach( $traveler_data as $key => $value ) {
		if( isset($value[0] ) && $value[0] === $current_user->user_email ) {
			if( ! empty( $bookings) ) {
				if ( ! in_array( get_the_ID(), $bookings ) ) {
					$bookings[] = get_the_ID();
				}
			} else {
				$bookings[] = get_the_ID();
			}
		}
	}
}

// Resverse Chronological Order For Bookings.
if ( ! empty( $bookings ) && is_array( $bookings ) ) {
	$bookings        = array_reverse( $bookings );
	$bookings_glance = array_slice( $bookings, 0, 5 );
}


$biling_glance_data = get_user_meta( $current_user->ID, 'wp_travel_customer_billing_details', true );
?>
<div class="dashboard-tab">
	<?php if ( ! empty( $user_dashboard_menus ) ) : ?>
	<ul class="resp-tabs-list ver_1">
		<?php foreach ( $user_dashboard_menus as $key => $menu ) : ?>
		<li id="<?php echo $key; ?>" class="wp-travel-ert "><i class="<?php echo esc_attr( $menu['menu_icon'] ); ?>" aria-hidden="true"></i><?php echo $menu['menu_title']; ?></li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
	<?php if ( ! empty( $user_dashboard_menus ) ) : ?>
	<div class="resp-tabs-container ver_1">
		<?php foreach ( $user_dashboard_menus as $key => $menu ) : ?>
		<div id="wp-travel-tab-content-<?php echo $key; ?>" class="tab-list-content" >
			<?php
			if ( ! empty( $menu['menu_content_cb'] ) ) {
				$args['bookings_glance']    = $bookings_glance;
				$args['biling_glance_data'] = $biling_glance_data;
				$args['bookings']           = $bookings;
				call_user_func( $menu['menu_content_cb'], $args );
			}
			?>
		</div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
</div>
