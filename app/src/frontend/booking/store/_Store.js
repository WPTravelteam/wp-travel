import { registerStore } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

export const DEFAULT_BOOKING_STATE = () => {
    let initState = {
        selectedDate: null,
        selectedDateIds: [],
        selectedPricingId: null,
        selectedTime: null,

        nomineePricings: [],
        nomineeTimes: [],
        // rruleAll: {},
        paxCounts: {},
        tripExtras: {},
        inventory: [],
        isLoading: false,
        excludedDateTimes: [],
        pricingUnavailable: false,
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
