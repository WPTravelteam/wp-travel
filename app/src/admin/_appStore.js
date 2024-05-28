import { registerStore } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

const DEFAULT_STATE = () => {
    let initState = {
        currencySymbol: '$'
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
    updateRequestSending(requesting) {
        return {
            type: 'UPDATE_REQUEST_SENDING',
            requesting
        };
    },
    getSettingsDataFromAPI( url ) {
        return {
            type: 'FETCH_FROM_API',
            url,
        };
    },
    setSettingsData(data){
        return {
            type: 'SET_SETTINGS_DATA',
            data,
        };
    },
    updateSettingsData(data){
        return {
            type: 'UPDATE_SETTINGS_DATA',
            data,
        };
    },
};

registerStore('WPTravel/appStore', {
    reducer(state = DEFAULT_STATE(), action) {
        switch (action.type) {
            case 'UPDATE_REQUEST_SENDING':
                return {...state,is_sending_request:action.requesting};
            case 'UPDATE_STATE_CHANGE':
                return {...state,has_state_changes:action.isChanged};
            case 'SET_SETTINGS_DATA':
                return {...state,
                    ...action.data,
                    is_sending_request:false
                };
            case 'UPDATE_SETTINGS_DATA':
                return {...state,
                    ...action.data,
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
        getSettingsData( state ) {
            return {...state};
        }
    },

    controls: {
        FETCH_FROM_API( action ) {
            return apiFetch( { url: action.url } );
        },
    },
 
    resolvers: {
        * getSettings() {
            const url = `${ajaxurl}?action=wptravel_get_settings&_nonce=${_wp_travel._nonce}`;
            
            const response = yield actions.getSettingsDataFromAPI( url );
            
            if(false !== response.success && "WP_TRAVEL_TRIP_INFO" === response.data.code ) {
                return actions.setSettingsData( response.data.trip );
            }
        }
    },
});