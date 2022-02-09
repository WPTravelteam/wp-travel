<?php
/**
 * Deprecated Traits.
 *
 * @package WP_Travel
 */

require sprintf( '%s/inc/deprecated-class/class-assets.php', WP_TRAVEL_ABSPATH );
require sprintf( '%s/inc/deprecated-class/class-helpers-trips.php', WP_TRAVEL_ABSPATH );

// class WP_Travel_Session extends WpTravel_Session { }
class WP_Travel_Helpers_Trip_Excluded_Dates_Times extends WpTravel_Helpers_Trip_Excluded_Dates_Times { }
class WP_Travel_Helpers_Trip_Dates extends WpTravel_Helpers_Trip_Dates { }
class WP_Travel_Helpers_Pricings extends WpTravel_Helpers_Pricings { }
class WP_Travel_Helpers_Trip_Pricing_Categories extends WpTravel_Helpers_Trip_Pricing_Categories { }
