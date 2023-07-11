import BookingType from "./booking/BookingType"
import PartialPyamet from "./booking/PartialPyamet"
import PaymentFormField from "./booking/PaymentFormField"
import PaymentPrice from "./booking/PaymentPrice"
import TotalPrice from "./booking/TotalPrice"
import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { applyFilters, doAction } from '@wordpress/hooks';
import HiddenText from "./form/HiddenText"
import BillingHiddenField from "./form/BillingHiddenField"
import { Button } from "@wordpress/components"
// import { useEffect } from '@wordpress/element'
// import apiFetch from '@wordpress/api-fetch';
// import { hari } from './booking/data'

export default () => {
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { checkoutDetails, payment_form, form_key  } = bookingData;
    const { booking_selected, payment_select  } = checkoutDetails;
    const { wp_travel_payment_gateway } = typeof payment_select != 'undefined' && payment_select || 'no';
    const selected_payment = typeof wp_travel_payment_gateway != 'undefined' && wp_travel_payment_gateway || 'no'
    const travelerData = typeof checkoutDetails != undefined && checkoutDetails[form_key] || '';
    const billingData = typeof checkoutDetails != undefined && typeof checkoutDetails.billing != 'undefined' && checkoutDetails.billing || '';
    const travelerKey = travelerData != '' && Object.keys(travelerData) || [];
    const billingKey = billingData != '' && Object.keys(billingData) || [];
    const { wp_travel_booking_option } = typeof booking_selected != 'undefined' && booking_selected || "booking_with_payment"
    const { payment_gateway } = payment_form;
    const partial_enable = _wp_travel.partial_enable;
    const { wp_travel_payment_mode } = typeof booking_selected != 'undefined' && booking_selected || "partial"
    // console.log( 'partial', partial_enable, _wp_travel.partial_enable );

    const handlingForm = ( e ) => {
        // e.preventDefault();
    }
    return <>  { typeof payment_gateway != 'undefined' && <>
    <div className="wptravel-booking-payment-page">
        <form method="POST" action={_wp_travel.checkout_url} className="wp-travel-booking" id="wp-travel-booking" >
            <BookingType />
            { wp_travel_booking_option == "booking_with_payment" && <>
            <PaymentFormField />
            { partial_enable == 'yes' && <PartialPyamet /> }
            </> }
            <TotalPrice />
            { wp_travel_booking_option == "booking_with_payment" && wp_travel_payment_mode == 'partial' &&  partial_enable == 'yes' && <>
            <PaymentPrice />
            </> }
            { travelerKey.length > 0 && travelerKey.map( ( keyList, indexs ) => {
                const trvValue = travelerData[keyList]
                const newTravelerKey = typeof trvValue != 'undefined' && Object.keys( trvValue ) || [];
            return newTravelerKey.length > 0 && newTravelerKey.map( ( finalKeys, index) => { return <div key={indexs} ><HiddenText names={keyList} values={trvValue[finalKeys]} index={index} keys={form_key} /></div> })
            })}
            {billingKey.length > 0 && billingKey.map((keyList, indexs) => {
                const billValue = billingData[keyList];
                return <div key={indexs} >{billValue != '' && <BillingHiddenField names={keyList} values={billingData[keyList]} />}</div>

            })}
            { wp_travel_booking_option == 'booking_with_payment' && typeof payment_select != 'undefined' && typeof payment_select.wp_travel_payment_gateway != 'undefined' && payment_select.wp_travel_payment_gateway == 'stripe' && <><label> {wp_travel.strip_card }</label><div id="card-element"></div> </>}
            
            { <input type="hidden" id="wp-travel-partial-payment" value={partial_enable} name="wp_travel_is_partial_payment" /> }
            <div className="wp-travel-form-field button-field" >
                {doAction( 'wptravel_booking_button_payment', bookingData )}
                <input type="hidden" value={_wp_travel._nonce} name="_nonce" />
                { selected_payment == 'stripe' && applyFilters( 'wptravel_booking_button_payment_strp', [<input type="submit" name="wp_travel_book_now" id="wp-travel-book-now" value="Booke Now" /> ], bookingData )
                || selected_payment == 'authorizenet' && applyFilters( 'wptravel_booking_button_payment_auth', [<input type="submit" name="wp_travel_book_now" id="wp-travel-book-now" value="Booke Now" /> ], bookingData )
                ||    <input type="submit" name="wp_travel_book_now" id="wp-travel-book-now" value="Booke Now" onClick={ e => handlingForm(e) }/> }
            </div>
        </form>
    </div></> || <input type="submit" name="wp_travel_book_now" id="wp-travel-book-now" value="Booke Now" onClick={ e => handlingForm(e) }/> }
    <Button onClick={ () => { 
            updateStore({...bookingData, tripBillingEnable : true, treipPaymentEnable : false })
    }} >Go Back</Button>

    </>
}