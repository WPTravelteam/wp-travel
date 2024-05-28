import { registerStore } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

const DEFAULT_STATE = () => {
    /**
     * fixed _wp_travel undefine variable 
     * also fixed price and date not show after refreshing or realoa
     */
    var initState = {
        content: '',
        excerpt: '',
        addons: [],
        galleryIds: [],
        group_size:'',
        highest_price : '',
        
        pricing_type: 'multiple-price',
        pricings:[],
        _thumbnail_id: 0,

        // Dates states
        is_fixed_departure: true,
        is_multiple_dates: false,
        dates:[],
        enable_excluded_dates_times:false,
        excluded_dates_times:[],

        // Additional states
        has_state_changes:false,
        is_sending_request:true,
    };
    if ( typeof _wp_travel != 'undefined' && _wp_travel != null ) {
        initState = {...initState, settings : _wp_travel.wp_settings.settings }
    }

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

registerStore('WPTravel/TripEdit', {
    reducer(state = DEFAULT_STATE(), action) {
        switch (action.type) {
            case 'UPDATE_REQUEST_SENDING':
                return {...state,is_sending_request:action.requesting};
            case 'UPDATE_STATE_CHANGE':
                return {...state,has_state_changes:action.isChanged, show_updated_message:true};
            case 'DATA_UPDATED':
                return {...state, show_updated_message:action.isUpdated};
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
            case 'ADD_NEW_TRIP_DATE':
                let addDate = [...state.dates,action.dateData];
                
                return {
                    ...state,
                    dates: addDate,
                    has_state_changes:true
                };
            case 'SET_TRIP_TABS_DATA':                
                return {
                    ...state,
                    trip_tabs: action.data,
                    has_state_changes:false
                };
            case 'ADD_NEW_ITINERARY':
                let addItinerary = [...state.itineraries,action.itineraryData];
                
                return {
                    ...state,
                    itineraries: addItinerary,
                    has_state_changes:true
                };
            case 'ADD_NEW_FAQ':
                let addFaq = [...state.faqs,action.faqData];
                
                return {
                    ...state,
                    faqs: addFaq,
                    has_state_changes:true
                };
            case 'ADD_NEW_FACT':
                let addFact = [...state.trip_facts,action.factData];
                
                return {
                    ...state,
                    trip_facts: addFact,
                    has_state_changes:true
                };
                
            case 'SET_SETTINGS':
                return {
                    ...state,
                    settings: action.settings,
                    has_state_changes:false
                };
                
                
        }

        return state;
    },

    actions,

    selectors: { // store selector
        getAllStore(store) {
            return {...store};
        },
        getTripData( state ) {
            return {...state};
        },
        getTripPricingCategories(state){
            return {...state.pricing_categories}
        },
        getTripTabs(state) {
            return {...state.trip_tabs}
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
        // @todo : need to implement getTripTabs latter.
        * getTripTabs() {
            const url = `${ajaxurl}?action=wp_travel_get_trip_tabs&trip_id=${tripId}&_nonce=${_wp_travel._nonce}`;
            
            yield actions.updateRequestSending(true);
            const response = yield actions.getTripTabsFromAPI( url );
            if(false !== response.success && "WP_TRAVEL_TRIP_TABS" === response.data.code ) {
                return actions.setTripTabsData( response.data.trip_tabs );
            }
        },
        * getSettings() {
            const url = `${ajaxurl}?action=wptravel_get_settings&_nonce=${_wp_travel._nonce}`;
            
            yield actions.updateRequestSending(true);
            const response = yield actions.getSettingsFromAPI( url );
            if(false !== response.success && "WP_TRAVEL_SETTINGS" === response.data.code ) {
                return actions.setSettings( response.data.settings );
            }
        },
    },
});