<?php

/**
 * Callback for Trip tab.
 *
 * @param  Array $tab  List of tabs.
 * @param  Array $args Settings arg list.
 */
function settings_callback_itinerary( $tab, $args ) {
	$settings = $args['settings'];

	$hide_related_itinerary      = $settings['hide_related_itinerary'];
	$enable_multiple_travellers  = $settings['enable_multiple_travellers'];
	$trip_pricing_options_layout = wp_travel_get_pricing_option_listing_type( $settings );
	do_action( 'wp_travel_tab_content_before_trips', $args );
	?>
	<div class="form_field">
		<label class="label_title" for="hide_related_itinerary"><?php esc_html_e( 'Hide related ', 'wp-travel' ); echo esc_attr( WP_TRAVEL_POST_TITLE ); ?></label>
		<div class="subject_input">
			<div class="onoffswitch">
				<input value="no" name="hide_related_itinerary" type="hidden" />
				<input <?php checked( $hide_related_itinerary, 'yes' ); ?> value="yes" name="hide_related_itinerary" id="hide_related_itinerary" type="checkbox" class="onoffswitch-checkbox" />
				<label class="onoffswitch-label" for="hide_related_itinerary">
					<span class="onoffswitch-inner"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
			<figcaption><label for="hide_related_itinerary"><?php esc_html_e( sprintf( 'This will hide your related %s.', WP_TRAVEL_POST_TITLE ), 'wp-travel' ); ?></label></figcaption>
		</div>
	</div>
	<div class="form_field">
		<label class="label_title" for="enable_multiple_travellers"><?php esc_html_e( 'Enable multiple travelers', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<div class="onoffswitch">
				<input value="no" name="enable_multiple_travellers" type="hidden" />
				<input <?php checked( $enable_multiple_travellers, 'yes' ); ?> value="yes" name="enable_multiple_travellers" id="enable_multiple_travellers" type="checkbox" class="onoffswitch-checkbox" />
				<label class="onoffswitch-label" for="enable_multiple_travellers">
					<span class="onoffswitch-inner"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
			<figcaption><label for="enable_multiple_travellers"><?php esc_html_e( sprintf( 'Check to enable.' ), 'wp-travel' ); ?></label></figcaption>
		</div>
	</div>

	<div class="form_field wp-travel-tax-price-options">
		<label class="label_title" for=""><?php esc_html_e( 'Trip Pricing Options Listing', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<label><input <?php checked( 'by-pricing-option', $trip_pricing_options_layout ); ?> name="trip_pricing_options_layout" value="by-pricing-option" type="radio">
			<?php esc_html_e( 'List by pricing options ( Default )', 'wp-travel' ); ?></label>

			<label> <input <?php checked( 'by-date', $trip_pricing_options_layout ); ?> name="trip_pricing_options_layout" value="by-date" type="radio">
			<?php esc_html_e( 'List by fixed departure dates', 'wp-travel' ); ?></label>
			<figcaption><?php esc_html_e( 'This options will control how you display trip dates and prices.', 'wp-travel' ); ?></figcaption>
		</div>
	</div>

	<?php
	do_action( 'wp_travel_tab_content_after_trips', $args );
}

