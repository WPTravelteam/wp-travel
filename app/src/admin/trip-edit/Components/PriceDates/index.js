import {  TabPanel} from '@wordpress/components';
import { applyFilters, addFilter } from '@wordpress/hooks';
import { _n, __} from '@wordpress/i18n';

import ErrorBoundary from '../../../../ErrorBoundry/ErrorBoundry';

import './Price';
import './Dates';

const __i18n = {
	..._wp_travel_admin.strings
}

// Single Components for hook callbacks.
const PriceDates = ( {allData} ) => {
    return <TabPanel className="wp-travel-trip-edit-menu wp-travel-trip-edit-menu-horizontal wp-travel-trip-edit-menu-add-gap"
        activeClass="active-tab"
        onSelect={() => false}
        tabs={[
            {
                name: 'prices',
                title: __i18n.prices,
                className: 'tab-one',
            },
            {
                name: 'dates',
                title: __i18n.dates,
                className: 'tab-two',
            },
        ]}>
        {
            (tab) => 'prices' == tab.name ?
            <ErrorBoundary>{applyFilters( `wptravel_trip_edit_sub_tab_content_prices`, [], allData)}</ErrorBoundary>
                :
            <ErrorBoundary>{applyFilters( `wptravel_trip_edit_sub_tab_content_dates`, [], allData)}</ErrorBoundary>
        }
    </TabPanel>;
}

// Callbacks.
const PriceDatesCB = ( content, allData ) => {
    return [ ...content, <PriceDates allData={allData} /> ];
}

// Hooks.
addFilter( 'wptravel_trip_edit_tab_content_price_dates', 'WPTravel/TripEdit/PriceDates', PriceDatesCB, 10 );

addFilter( 'wptravel_trip_edit_block_tab_booking', 'WPTravel/TripEdit/Block/Tab/Booking/PriceDates', PriceDatesCB, 10 );
