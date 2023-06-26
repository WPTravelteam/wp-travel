<?php
/**
 * 
 * Render Callback For Trip Filters
 * 
 */

function wptravel_block_trip_filters_render( $attributes ) {

	$keyword_search       = $attributes['keyWordSearch'];
	$fact                 = $attributes['tripFact'];
	$trip_type_filter     = $attributes['tripTypeFilter'];
	$trip_location_filter = $attributes['tripLocationFilter'];
	$price_orderby        = $attributes['priceOrderFilter'];
	$price_range          = $attributes['priceRangeFilter'];
	$trip_dates           = $attributes['tripDateFilter'];

	$defaults = array(
		'keyword_search'       => $keyword_search,
		'fact'                 => $fact,
		'trip_type_filter'     => $trip_type_filter,
		'trip_location_filter' => $trip_location_filter,
		'price_orderby'        => $price_orderby,
		'price_range'          => $price_range,
		'trip_dates'           => $trip_dates,
	);

	ob_start();
	?>
	<style>
		.wptravel-block-trip-filters,
		.wptravel-block-trip-filters .wp-travel-itinerary-items .wp-travel-form-field label{
			color: <?php echo esc_attr($attributes['textColor']); ?>;
		}
		.wptravel-block-trip-filters .wp-travel-itinerary-items .wp-travel-form-field select,
		.wptravel-block-trip-filters .wp-travel-itinerary-items .wp-travel-form-field input{
			background-color: <?php echo esc_attr($attributes['inputBackgroundColor']); ?>;
			border-color: <?php echo esc_attr($attributes['inputBorderColor']); ?>;
		}
		.wptravel-block-trip-filters #wp-travel-filter-search-submit{
			background-color: <?php echo esc_attr($attributes['btnBackgroundColor']); ?>;
			border: 1px solid;
			border-color: <?php echo esc_attr($attributes['btnBorderColor']); ?>;
			border-radius: <?php echo esc_attr($attributes['btnBorderRadius']); ?>px;
			color: <?php echo esc_attr($attributes['btnTextColor']); ?>;
		}
	</style>
	<div id="wptravel-block-trip-filters" class="wptravel-block-wrapper wptravel-block-trip-filters">
		<?php
		wptravel_get_search_filter_form( array( 'widget' => $defaults ) );
		?>
	</div>
	<?php

	return ob_get_clean();
}