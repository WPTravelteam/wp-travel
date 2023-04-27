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

    updateCoupon(data){
        return {
            type: 'UPDATE_COUPON',
            data,
        };
    },
    getCouponsFromAPI(url) {
        return {
            type: 'FETCH_FROM_API',
            url,
        };
    },
    setCoupon(coupon) {
        return {
            type: 'SET_COUPON',
            coupon,
        };
    }
    
    
};

registerStore('WPTravel/Coupon', {
    selectors: {
        setCoupon(state) {
            return {...state.coupon}
        },
        getAllStore(store) {
            return {...store};
        },
    },
    resolvers: {
        * setCoupon(couponId) {
            const url = `${ajaxurl}?action=wptravel_get_coupon&_nonce=${_wp_travel._nonce}&coupon_id=${couponId}`;
            yield actions.updateRequestSending(true);
            const response = yield actions.getCouponsFromAPI( url );
            console.log(response);
            if(false !== response.success && "WP_TRAVEL_COUPON" === response.data.code ) {
                yield actions.updateRequestSending(false);
                return actions.setCoupon( response.data.coupon );
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
                
            case 'SET_COUPON':
                return {
                    ...state,
                    ...action.coupon,
                    is_sending_request:false
                };
            case 'UPDATE_COUPON':
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