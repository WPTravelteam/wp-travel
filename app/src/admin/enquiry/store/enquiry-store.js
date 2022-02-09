import { registerStore } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

const DEFAULT_STATE = () => {
    let initState = {
        // Additional states
        has_state_changes:false,
        is_sending_request:true,
        disable_save:false,
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
    disableSave( disable ) {
        return {
            type: 'DISABLE_SAVE',
            disable
        };
    },
    displaySavedMessage(isUpdated) {
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

    updateEnquiry(data){
        return {
            type: 'UPDATE_ENQUIRY',
            data,
        };
    },
    getEnquiryFromAPI(url) {
        return {
            type: 'FETCH_FROM_API',
            url,
        };
    },
    setEnquiry(enquiry) {
        return {
            type: 'SET_ENQUIRY',
            enquiry,
        };
    }
    
    
};

registerStore('WPTravel/Enquiry', {
    selectors: {
        setEnquiry(state) {
            return {...state.enquiry}
        },
        getAllStore(store) {
            return {...store};
        },
    },
    resolvers: {
        * setEnquiry(enquiry_id) {
            const url = `${ajaxurl}?action=wptravel_get_enquiry&_nonce=${_wp_travel._nonce}&enquiry_id=${enquiry_id}`;
            yield actions.updateRequestSending(true);
            const response = yield actions.getEnquiryFromAPI( url );
            //console.log('set',response);
            if(false !== response.success && "WP_TRAVEL_ENQUIRY" === response.data.code ) {
                yield actions.updateRequestSending(false);
                return actions.setEnquiry( response.data.enquiry );
            }
        },
    },
    actions,
    reducer(state = DEFAULT_STATE(), action) {
        switch (action.type) {
            case 'UPDATE_REQUEST_SENDING':
                return {...state,is_sending_request:action.requesting};
            case 'UPDATE_STATE_CHANGE':
                return {...state,has_state_changes:action.isChanged, show_updated_message:true};
            case 'DISABLE_SAVE':
                return {...state,disable_save:action.disable};
            case 'DATA_UPDATED':
                return {...state, show_updated_message:action.isUpdated};
                
            case 'SET_ENQUIRY':
                return {
                    ...state,
                    ...action.enquiry,
                    is_sending_request:false
                };
            case 'UPDATE_ENQUIRY':
                return {...state,
                    ...action.data,
                    has_state_changes:true
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