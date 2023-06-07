import { registerStore } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';

const DEFAULT_STATE = () => {
    let initState = {
        // Additional states
        has_state_changes: false,
        is_sending_request: true,
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
    updateSettings(data) {
        return {
            type: 'UPDATE_SETTINGS',
            data,
        };
    },
    addNewBankDetail(bankData) {
        return {
            type: 'ADD_NEW_BANK_DETAIL',
            bankData
        };
    },
    addNewFact(newData) {
        return {
            type: 'ADD_NEW_FACT',
            newData
        };
    },
    addNewCPFact(newData) {
        return {
            type: 'ADD_NEW_CP',
            newData
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

registerStore('WPTravel/Admin', {
    reducer(state = DEFAULT_STATE(), action) {
        switch (action.type) {
            case 'UPDATE_REQUEST_SENDING':
                return { ...state, is_sending_request: action.requesting };
            case 'UPDATE_STATE_CHANGE':
                return { ...state, has_state_changes: action.isChanged, show_updated_message: true };
            case 'DATA_UPDATED':
                return { ...state, show_updated_message: action.isUpdated };
            case 'SET_SETTINGS':
                return {
                    ...state,
                    ...action.settings,
                    is_sending_request: false
                };
            case 'UPDATE_SETTINGS':
                return {
                    ...state,
                    ...action.data,
                    has_state_changes: true
                };
            case 'ADD_NEW_BANK_DETAIL':
                let addNewBank = [...state.wp_travel_bank_deposits, action.bankData];
                return {
                    ...state,
                    wp_travel_bank_deposits: addNewBank,
                    has_state_changes: true
                };
            case 'ADD_NEW_FACT':
                let addNewData = [...state.wp_travel_trip_facts_settings, action.newData];
                return {
                    ...state,
                    wp_travel_trip_facts_settings: addNewData,
                    has_state_changes: true
                };
            case 'ADD_NEW_CP':
                let addNewCPData = [...state.conditional_payment_list, action.newData];
                return {
                    ...state,
                    conditional_payment_list: addNewCPData,
                    has_state_changes: true
                };
        }
        return state;
    },

    actions,

    selectors: { // store selector
        getAllStore(store) {
            return { ...store };
        },
        getSettings(state) {
            return { ...state.settings }
        },
    },

    controls: {
        FETCH_FROM_API(action) {
            return apiFetch({ url: action.url });
        },
    },

    resolvers: {
        * getSettings() {
            const url = `${ajaxurl}?action=wptravel_get_settings&_nonce=${_wp_travel._nonce}`;
            yield actions.updateRequestSending(true);
            const response = yield actions.getSettingsFromAPI(url);
            if (response.success && "WP_TRAVEL_SETTINGS" === response.data.code) {
                yield actions.updateRequestSending(false);
                return actions.setSettings(response.data.settings);
            }
        },
    },
});