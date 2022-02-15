import { registerStore } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

export const DEFAULT_BOOKING_STATE = () => {
    let initState = {
        selectedDate: null,
        selectedTripDate: [],
        nomineePricings: [],
        nomineeTimes: [],
        selectedPricing: null,
        selectedTime: null,
        rruleAll: {},
        paxCounts: {},
        tripExtras: {},
        inventory: [],
        isLoading: false,
        excludedDateTimes: [],
        pricingUnavailable: false,
        tempExcludeDate: [] // Temp fixes. Just to check exclude date
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
        updateBooking(data){
            return {
                type: 'UPDATE_BOOKING',
                data,
            };
        },
    },
    reducer( state = DEFAULT_BOOKING_STATE(), action ) {
        switch ( action.type ) {
            case 'UPDATE_BOOKING':
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
