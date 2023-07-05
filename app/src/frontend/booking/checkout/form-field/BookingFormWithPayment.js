import BookingType from "./booking/BookingType"
import PartialPyamet from "./booking/PartialPyamet"
import PaymentFormField from "./booking/PaymentFormField"
import PaymentPrice from "./booking/PaymentPrice"
import TotalPrice from "./booking/TotalPrice"
import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { applyFilters } from '@wordpress/hooks';
import HiddenText from "./form/HiddenText"
import BillingHiddenField from "./form/BillingHiddenField"
// import { useEffect } from '@wordpress/element'
// import apiFetch from '@wordpress/api-fetch';
// import { hari } from './booking/data'

export default () => {
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { checkoutDetails, payment_form, form_key  } = bookingData;
    const { booking_selected  } = checkoutDetails;
    const travelerData = typeof checkoutDetails != undefined && checkoutDetails[form_key] || '';
    const billingData = typeof checkoutDetails != undefined && typeof checkoutDetails.billing != 'undefined' && checkoutDetails.billing || '';
    const travelerKey = travelerData != '' && Object.keys( travelerData ) || [];
    const billingKey = billingData != '' && Object.keys( billingData ) || [];
    const { wp_travel_booking_option } = typeof booking_selected != 'undefined' && booking_selected || "booking_with_payment"
    const { payment_gateway } = payment_form;
    const { wp_travel_payment_mode } = typeof booking_selected != 'undefined' && booking_selected || "partial"
    // hari();
    const ab = false;
    const handelers = ( e ) => {
        if ( ab == false ) {
            return false;
        }
    }
    return  typeof payment_gateway != 'undefined' && <>
    <form method="POST" action={_wp_travel.checkout_url} className="wp-travel-booking" id="wp-travel-booking" onSubmit={ ( e ) => handelers( e ) } >
        <BookingType />
        { wp_travel_booking_option == "booking_with_payment" && <>
        <PaymentFormField />
        <PartialPyamet />
        </> }
        <TotalPrice />
        { wp_travel_booking_option == "booking_with_payment" && wp_travel_payment_mode == 'partial' && <>
        <PaymentPrice />
        </> }
        { travelerKey.length > 0 && travelerKey.map( ( keyList, indexs ) => {
            const trvValue = travelerData[keyList]
           return <div key={indexs} ><HiddenText names={keyList} values={travelerData[keyList]} index='0' keys={form_key} /></div>

        } ) }
        { billingKey.length > 0 && billingKey.map( ( keyList, indexs ) => {
            const billValue = billingData[keyList];
           return <div key={indexs} >{ billValue != '' && <BillingHiddenField names={keyList} values={billingData[keyList]} /> }</div>

        } ) }
        <div className="wp-travel-form-field button-field" >
            <input type="hidden" value={_wp_travel._nonce} name="_nonce" />
            { applyFilters( 'wptravel_booking_button_payment', [<input type="submit" name="wp_travel_book_now" id="wp-travel-book-now" value="Booke Now" /> ], bookingData ) }
        </div>
    </form>
    </>
}