import { registerStore } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

const DEFAULT_STATE = () => {
    let initState = {
        // Additional states
        has_state_changes:false,
        is_sending_request:true
    };

    return initState;
}

const actions = {
    updateStateChange(isChanged) {
        return {
            type: 'UPDATE_STATE_CHANGE',
            isChanged
        };
    },
    displayUpdatedMessage(isUpdated) {
        return {
            type: 'DATA_UPDATED',
            isUpdated
        };
    },
    
    updateRequestSending(requesting) {
        return {
            type: 'UPDATE_REQUEST_SENDING',
            requesting
        };
    },
    setTripPricingType(pricingType) {
        return {
            type: 'SET_TRIP_PRICING_TYPE',
            pricingType
        };
    },
    updateTripPrices(pricings){
        return {
            type: 'UPDATE_TRIP_PRICES',
            pricings
        };
    },
    updateTripPricing(pricingData, index) {
        return {
            type: 'UPDATE_TRIP_PRICING',
            pricingData,
            index
        };
    },
    addTripPricing(pricingData) {
        return {
            type: 'ADD_TRIP_PRICING',
            pricingData
        };
    },
    getTripDataFromAPI( url ) {
        return {
            type: 'FETCH_FROM_API',
            url,
        };
    },
    getTripPricingCategoriesFromAPI( url ) {
        return {
            type: 'FETCH_FROM_API',
            url,
        };
    },
    setTripData(data){ // sent it to reducer.
        return {
            type: 'SET_TRIP_DATA',
            data,
        };
    },
    updateTripData(data){
        return {
            type: 'UPDATE_TRIP_DATA',
            data,
        };
    },
    setTripPricingCategoriesData(data){
        return {
            type: 'SET_TRIP_PRICING_CATEGORY_DATA',
            data,
        };
    },
    getTripTabsFromAPI(url){
        return {
            type: 'FETCH_FROM_API',
            url,
        };
    },
    setTripTabsData(data){
        return {
            type: 'SET_TRIP_TABS_DATA',
            data,
        };
    },
    addNewItinerary(itineraryData) {
        return {
            type: 'ADD_NEW_ITINERARY',
            itineraryData
        };
    },
    addNewFaq(faqData) {
        return {
            type: 'ADD_NEW_FAQ',
            faqData
        };
    },
    addNewFact(factData) {
        return {
            type: 'ADD_NEW_FACT',
            factData
        };
    },
    addNewTripDate(dateData) {
        return {
            type: 'ADD_NEW_TRIP_DATE',
            dateData
        };
    },
    getSettingsFromAPI(url) {
        return {
            type: 'FETCH_FROM_API',
            url,
        };
    },
    setSettings(settings) {
        return {
            type: 'SET_SETTINGS',
            settings,
        };
    }
    
    
};

registerStore('WPTravel/Settings', {
    reducer(state = DEFAULT_STATE(), action) {
        switch (action.type) {
            // case 'UPDATE_REQUEST_SENDING':
            //     return {...state,is_sending_request:action.requesting};
            // case 'UPDATE_STATE_CHANGE':
            //     return {...state,has_state_changes:action.isChanged, show_updated_message:true};
            // case 'DATA_UPDATED':
            //     return {...state, show_updated_message:action.isUpdated};
                
            case 'SET_SETTINGS':
                return {
                    ...state,
                    ...action.settings,
                    is_sending_request:false
                };
                
        }

        return state;
    },

    actions,

    selectors: { // store selector
        getAllStore(store) {
            return {...store};
        },
        getSettings(state) {
            return {...state.settings}
        },
    },

    controls: {
        FETCH_FROM_API( action ) {
            return apiFetch( { url: action.url } );
        },
    },
 
    resolvers: {
        * getSettings() {
            const url = `${ajaxurl}?action=wp_travel_get_settings&_nonce=${_wp_travel._nonce}`;
            
            yield actions.updateRequestSending(true);
            const response = yield actions.getSettingsFromAPI( url );
            if(false !== response.success && "WP_TRAVEL_SETTINGS" === response.data.code ) {
                yield actions.updateRequestSending(false);
                return actions.setSettings( response.data.settings );
            }
        },
    },
});