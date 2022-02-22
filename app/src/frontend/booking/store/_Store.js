import { registerStore } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

export const DEFAULT_BOOKING_STATE = () => {
    let initState = {
        selectedDate: null, // Object Date along with time if time is available/selected. This will also for display purpose. Time will display if time is selected [selectedTime]
        selectedDateIds: [],
        pricingUnavailable: false,
        nomineePricingIds: [],
        selectedPricingId: null,
        selectedPricing: null, // Just TO Display only.
        nomineeTimes: [],
        selectedTime: null,
        excludedDateTimes: [],
        paxCounts: {}, // Total pax object  { categoryid1:noOfPax, categoryid2:noOfPax }
        
        // rruleAll: {},
        tripExtras: {},
        inventory: [],
        isLoading: false,
    };
    return initState;
}
registerStore('WPTravelFrontend/BookingData', {
    selectors: {
        getAllStore(store) {
            return {...store};
        },
    },
    resolvers: {
    },
    actions: {
        updateStore(data){
            return {
                type: 'UPDATE_BOOKING_STORE',
                data,
            };
        },
    },
    reducer( state = DEFAULT_BOOKING_STATE(), action ) {
        switch ( action.type ) {
            case 'UPDATE_BOOKING_STORE':
                return {...state,
                    ...action.data,
                };
        }
        return state;
    },
    controls: {
        FETCH_FROM_API( action ) {
            return apiFetch( { url: action.url } );
        },
    },
});
