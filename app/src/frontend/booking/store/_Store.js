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
        inventory: [],
        nomineeTripExtras: [],
        tripExtras: {}, // object structure for selected extras like paxCounts
        isLoading: false,
        dateListingChangeType:null, // To prevent multiple ajax request on pricing select. [only used in date listing]

        // for modal field
        travelerInfo : false,
        bookingTabEnable : true,
        tripBillingEnable: false,
        treipPaymentEnable : false,
        traveler_form : typeof _wp_travel != 'undefined' && typeof _wp_travel.checkout_field != 'undefined' && typeof _wp_travel.checkout_field.form != 'undefined' && typeof _wp_travel.checkout_field.form.traveller_fields && _wp_travel.checkout_field.form.traveller_fields || undefined,
        billing_form : typeof _wp_travel != 'undefined' && typeof _wp_travel.checkout_field != 'undefined' && typeof _wp_travel.checkout_field.form != 'undefined' && typeof _wp_travel.checkout_field.form.billing_fields && _wp_travel.checkout_field.form.billing_fields || undefined,
        payment_form : typeof _wp_travel != 'undefined' && typeof _wp_travel.checkout_field != 'undefined' && typeof _wp_travel.checkout_field.form != 'undefined' && typeof _wp_travel.checkout_field.form.payment_fields && _wp_travel.checkout_field.form.payment_fields || undefined,
        form_key : typeof _wp_travel != 'undefined' && typeof _wp_travel.checkout_field != 'undefined' && typeof _wp_travel.checkout_field.form_key && _wp_travel.checkout_field.form_key || 'travelerOne',
        checkoutDetails : {}
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
