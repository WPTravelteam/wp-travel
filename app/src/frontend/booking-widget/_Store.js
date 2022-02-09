import { registerStore } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

const DEFAULT_STATE = () => {
    let initState = {
        tripData:{},
        currencySymbol: '',
        basePrice: '0',
        pricing:{},
        dates:{},
        activeStep: 'step1',
        totalPaxCount:0,
        paxCounts:{},
        cartTotal:0,
        isValid:true,
        selectedGroupPrice:{},
        group_discount_applicable_pax:{},
        group_discount_non_applicable_pax:{},
        selectedDate : false,
        selectedTime:false
    };

    if ( 'undefined' !== typeof _wp_travel.trip_data ) {
        initState.tripData = {..._wp_travel.trip_data}
    }

    return initState;
}

const actions = {
    updateStoreData(data) {
        return {
            type: 'UPDATE_STORE_DATA',
            data
        };
    }
};

registerStore( 'WPTravelFrontend/BookingWidget', {
    reducer(state = DEFAULT_STATE(), action) {
        switch (action.type) {
            case 'UPDATE_STORE_DATA':
                return {...state,...action.data};
            case 'UPDATE_REQUEST_SENDING':
                return {...state,is_sending_request:action.requesting};
            case 'UPDATE_STATE_CHANGE':
                return {...state,has_state_changes:action.isChanged};
            case 'SET_TRIP_DATA':
                return {...state,
                    ...action.data,
                    is_sending_request:false
                };
            case 'UPDATE_TRIP_DATA':
                return {...state,
                    ...action.data,
                    has_state_changes:true
                };
            case 'SET_TRIP_PRICING_CATEGORY_DATA':
                
                return {...state,
                    pricing_categories:action.data,
                    is_sending_request:false
                };
            case 'SET_TRIP_PRICING_TYPE':
                return {
                    ...state,
                    pricing_type: action.pricingType,
                    has_state_changes:true
                };
            case 'UPDATE_TRIP_PRICES':
                let newPricings = action.pricings;
                return {
                    ...state,
                    pricings: newPricings,
                    has_state_changes:true
                };
            case 'UPDATE_TRIP_PRICING':
                let newPricing = [...state.pricings];
                newPricing[action.index] = action.pricingData;
                return {
                    ...state,
                    pricings: newPricing,
                    has_state_changes:true
                };
            
            case 'ADD_TRIP_PRICING':
                let addPricing = [...state.pricings,action.pricingData];
                
                return {
                    ...state,
                    pricings: addPricing,
                    has_state_changes:true
                };
        }

        return state;
    },

    actions,

    selectors: {
        getAllStore(store) {
            return {...store};
        },
        getTripData( state ) {
            return {...state};
        },
        getTripPricingCategories(state){
            return {...state.pricing_categories}
        }
    },

    controls: {
        FETCH_FROM_API( action ) {
            return apiFetch( { url: action.url } );
        },
    },
 
    resolvers: {
        * getTripData(tripId) {
            const url = `${ajaxurl}?action=wp_travel_get_trip&trip_id=${tripId}&_nonce=${_wp_travel._nonce}`;
            
            yield actions.updateRequestSending(true);
            
            const response = yield actions.getTripDataFromAPI( url );
            
            if(false !== response.success && "WP_TRAVEL_TRIP_INFO" === response.data.code ) {
                return actions.setTripData( response.data.trip );
            }
        },
        * getTripPricingCategories() {
            const url = `${ajaxurl}?action=wp_travel_get_trip_pricing_categories_terms&_nonce=${_wp_travel._nonce}`;
            
            yield actions.updateRequestSending(true);
            const response = yield actions.getTripPricingCategoriesFromAPI( url );
            if(false !== response.success && "WP_TRAVEL_TRIP_PRICING_CATEGORIES" === response.data.code ) {
                return actions.setTripPricingCategoriesData( response.data.pricing_categories );
            }
        },
    },
});

// Store 2 for booking data.
export const DEFAULT_BOOKING_STATE = () => {
    let initState = {
        selectedDate: null,
        selectedTripDate: [],
        nomineePricings: [],
        nomineeTimes: [],
        selectedPricing: null,
        selectedDateTime: null,
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
registerStore('WPTravel/Booking', {
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