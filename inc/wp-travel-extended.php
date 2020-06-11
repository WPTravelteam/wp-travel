<?php
define( 'WP_TRAVEL_EXTENDED_FILE', WP_TRAVEL_PLUGIN_FILE );
define( 'WP_TRAVEL_EXTENDED_FILE_PATH', dirname( WP_TRAVEL_PLUGIN_FILE ) );

require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/helpers/response_codes.php';
require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/helpers/error_codes.php';

// Actions.
require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/actions/register_taxonomies.php';
require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/actions/activation.php';

// Libraries.
require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/lib/cart.php';

// Helpers.
require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/helpers/media.php';
require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/helpers/trip-pricing-categories-taxonomy.php';
require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/helpers/trip-extras.php';
require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/helpers/trip-dates.php';
require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/helpers/trip-excluded-dates-times.php';
require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/helpers/pricings.php';
require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/helpers/trip-pricing-categories.php';
require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/helpers/trips.php';
require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/helpers/cart.php';
require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/helpers/rest-api.php';

// Ajax.
require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/ajax/settings.php';
require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/ajax/trip-pricing-categories-taxonomy.php';
require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/ajax/trip-extras.php';
require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/ajax/trip-pricing-categories.php';
require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/ajax/trip-dates.php';
require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/ajax/trip-excluded-dates-times.php';
require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/ajax/pricings.php';
require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/ajax/cart.php';
require WP_TRAVEL_EXTENDED_FILE_PATH . '/core/ajax/trips.php';

// Assets.
// include WP_TRAVEL_EXTENDED_FILE_PATH.'/core/assets/frontend.php';
// include WP_TRAVEL_EXTENDED_FILE_PATH.'/core/assets/admin.php';
// include WP_TRAVEL_EXTENDED_FILE_PATH.'/core/localize/admin.php';

// Views
// include WP_TRAVEL_EXTENDED_FILE_PATH.'/core/views/admin/trip-metabox.php';

/**
 * App Part
 */

// Front End.
require WP_TRAVEL_EXTENDED_FILE_PATH . '/app/inc/admin/metabox-trip-edit.php';
require WP_TRAVEL_EXTENDED_FILE_PATH . '/app/inc/admin/assets.php';
require WP_TRAVEL_EXTENDED_FILE_PATH . '/app/inc/admin/localize.php';

// Front End
require WP_TRAVEL_EXTENDED_FILE_PATH . '/app/inc/frontend/single-itinerary-hooks.php';
require WP_TRAVEL_EXTENDED_FILE_PATH . '/app/inc/frontend/assets.php';
