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
import { Button, PanelBody, PanelRow } from "@wordpress/components"
import BankDetail from "./form/BankDetail"
import {_n, __} from '@wordpress/i18n'
import { useEffect, useState } from '@wordpress/element'
import apiFetch from '@wordpress/api-fetch';
import { paypalPayment } from './booking/data'
import CouponApplyAmount from "./booking/CouponApplyAmount"
// import { ProgressBary } from "../ProgressBary"
const i18n = _wp_travel.strings;

export default () => {
    const [loaders, setLoaders] = useState(false)
    const [couponError, setCouponError] = useState('')
    const [ couponDisable, setDisable ]  = useState(false)
    const [ couponLoaders, setCouponLoaders ] = useState(false)
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { checkoutDetails, payment_form, price_list, form_key, traveler_form, couponCode, apply_coupon, cart_amount, currency_symbol } = bookingData;
    const { booking_selected, payment_select  } = checkoutDetails;

    const { trip_price, partial_amount } = price_list;
    const { wp_travel_payment_gateway } = typeof payment_select != 'undefined' && payment_select || 'no';
    const selected_payment = typeof wp_travel_payment_gateway != 'undefined' && wp_travel_payment_gateway || 'no'
    const travelerData = typeof checkoutDetails != undefined && checkoutDetails[form_key] || '';
    const billingData = typeof checkoutDetails != undefined && typeof checkoutDetails.billing != 'undefined' && checkoutDetails.billing || '';
    const travelerKey = [];
    Object.keys( traveler_form ).length > 0 && Object.keys(traveler_form).map( ( fstKey, index ) => {
        const finalFstKey = typeof traveler_form[fstKey] != 'undefined' && traveler_form[fstKey] || {};
        const trvName = typeof finalFstKey.name != 'undefined' && finalFstKey.name || ''
        if ( trvName != '' ){
            travelerKey.push( trvName );
        }
    } )
    const billingKey = billingData != '' && Object.keys(billingData) || [];
    const { wp_travel_booking_option } = typeof booking_selected != 'undefined' && booking_selected || "booking_only"
    const { payment_gateway } = payment_form;
    const partial_enable = _wp_travel.partial_enable;
    const { wp_travel_payment_mode } = typeof booking_selected != 'undefined' && booking_selected || "partial"
    const handlingForm = ( e ) => {
        e.preventDefault();
        alert( i18n.set_gateway_select )
    }
    useEffect( () => {
        paypalPayment();
    },[ wp_travel_booking_option, wp_travel_payment_mode, price_list ])
    // hari();
    const applyCouponCode = () => {
        if ( typeof couponCode != 'undefined' && couponCode != '' ) {
            setCouponLoaders(true)
            apiFetch( {
                url: `${wp_travel.ajaxUrl}?action=wp_travel_apply_coupon&_nonce=${_wp_travel._nonce}`,
				method: 'POST',
				data : {
                    couponCode: couponCode
                }
            }).then( resp => {
                if ( resp.success == true && typeof resp.data.code != 'undefined' && resp.data.code == "WP_TRAVEL_COUPON_APPLIED" ) {
                    const cart_detail = typeof resp.data.cart != 'undefined' && resp.data.cart || {};
                    const { cart_total, cart_total_regular, total } = cart_detail;
                    const { discount }     = typeof total != 'undefined' && total || {};
                    const cart_total_price = typeof total != 'undefined' && typeof total.total != 'undefined' && total.total || '0'
                    const total_partial = typeof total != 'undefined' && typeof total.total_partial != 'undefined' && total.total_partial || '0';
                    const price_data = { partial_amount : total_partial, trip_price : cart_total_price  }
                    updateStore( { ...bookingData, cart_amount : total, price_list : price_data, apply_coupon : true } )
                    setCouponError( i18n.set_coupon_apply );
                    setCouponLoaders(false)
                    setDisable(true)
                } else {
                    setCouponError( i18n.set_invalid_coupon_error );
                    setCouponLoaders(false)
                }
            })
            
        } else {
            setCouponError( i18n.set_coupon_empty );
        }
    }
    const createCouponError = () => {
        setCouponLoaders(true)
        setCouponError(i18n.set_cart_error )
        setCouponLoaders(false)
    }

    return <> 
        {/* This section containce add your coupon code and get discount */}
        { typeof _wp_travel.coupon_available != 'undefined' && _wp_travel.coupon_available == true &&  <div className="wptravel-on-page-coupon-apply">
            <div className="wptravel-on-page-coupon-data">
                <input type='text' value={typeof couponCode != 'undefined' && couponCode || '' } placeholder={i18n.set_enter_coupon_message} className="wp-travel-coupon-code" onChange={ ( e ) => {
                const values =  e.target.value;
                updateStore({...bookingData, couponCode : values } );
                }} />
            </div>
            <button {...couponDisable} className="wptravel-on-page-coupon-code-btn components-button" onClick={ couponDisable == true ? createCouponError : applyCouponCode}>{i18n.set_coupon_btn}{couponLoaders && <img className="wptravel-single-page-loader-btn" src={_wp_travel.loader_url } /> }</button>
        </div> }
        { couponError != '' && <p className="wptravel-on-page-coupon-error">{couponError}</p> }
        {/* This section contain your hole booking process booking and booking with payment */}
        <form method="POST" action={_wp_travel.checkout_url} className="wp-travel-booking" id="wp-travel-booking" > { typeof payment_gateway != 'undefined' && <>
            
            <div className="wptravel-booking-payment-page">
                <BookingType />
                { wp_travel_booking_option == "booking_with_payment" && <>
                <div className="wptravel-onepage-payment-gateway"><PaymentFormField /> </div>
                { partial_enable == 'yes' && <PartialPyamet /> }
                </> }
                {/* Show all price data  */}
                <div className="wptravel-on-page-price-with-payment-field-show">
                    <CouponApplyAmount coupon_data={cart_amount} currency_symbol={ currency_symbol } booking_option={wp_travel_booking_option} payment_mode={wp_travel_payment_mode} partial_enable={ partial_enable }  /> 
                    { selected_payment == 'stripe_ideal' && <div className="wptravel-on-page-stripe-ideal-checkout-field">
                        <label>{i18n.set_ideal_bank}</label><div id="stripeIdealElement"></div>
                    </div> }
                </div>
                { travelerKey.length > 0 && travelerKey.map( ( keyList, indexs ) => {
                    const trvValue = typeof travelerData[keyList] != 'undefined' && travelerData[keyList] || { 1 : ''}
                    const newTravelerKey = typeof trvValue != 'undefined' && Object.keys( trvValue ) || [];
                    return newTravelerKey.length > 0 && newTravelerKey.map( ( finalKeys, index) => { return <div key={indexs + index } ><HiddenText names={keyList} values={trvValue[finalKeys]} index={index} keys={form_key} /></div> })
                })}
                {billingKey.length > 0 && billingKey.map((keyList, indexs) => {
                    const billValue = billingData[keyList];
                    return <div key={indexs} >{billValue != '' && <BillingHiddenField names={keyList} values={billingData[keyList]} />}</div>

                })}
                { wp_travel_booking_option == "booking_with_payment"&& selected_payment == 'bank_deposit' && <BankDetail />}
                { wp_travel_booking_option == 'booking_with_payment' && typeof payment_select != 'undefined' && typeof payment_select.wp_travel_payment_gateway != 'undefined' && payment_select.wp_travel_payment_gateway == 'stripe' && <div className="wptravel-single-page-stripcheckout"><label> {wp_travel.strip_card }</label><div id="card-element"></div> </div>}
                
                { <input type="hidden" id="wp-travel-partial-payment" value={partial_enable} name="wp_travel_is_partial_payment" /> }
                <input type="hidden" value={_wp_travel._nonce} name="_nonce" />
                <input type="hidden" value={ partial_enable == 'yes' && wp_travel_payment_mode == 'partial' ? partial_amount : trip_price } name="onpage-trip_price" id="onpage-trip_price" />
                {doAction( 'wptravel_booking_button_payment', bookingData )}
                <div className="wptravel-onepage-navigation-btn">
                    <button onClick={ () => { 
                        updateStore({...bookingData, tripBillingEnable : true, treipPaymentEnable : false })
                    }} >{i18n.set_go_back}</button>
                    <div className="wp-travel-form-field button-field" >
                    {  wp_travel_booking_option == "booking_with_payment" && selected_payment == 'stripe' && applyFilters( 'wptravel_booking_button_payment_strp', [<div><input type="submit" name="wp_travel_book_now" id="wp-travel-book-now" value={i18n.set_book_now} disabled /></div> ], bookingData )
                        ||  wp_travel_booking_option == "booking_with_payment" && selected_payment == 'authorizenet' && applyFilters( 'wptravel_booking_button_payment_auth', [<div><input type="submit" name="wp_travel_book_now" id="wp-travel-book-now" value={i18n.set_book_now} disabled /></div>], bookingData )
                        ||  wp_travel_booking_option == "booking_with_payment" && selected_payment == 'express_checkout' && applyFilters( 'wptravel_booking_button_payment_express_checkout', [<div><input type="submit" name="wp_travel_book_now" id="wp-travel-book-now" value={i18n.set_book_now} disabled /></div>], bookingData )  
                        ||  wp_travel_booking_option == "booking_with_payment" && selected_payment == 'stripe_ideal' && applyFilters( 'wptravel_booking_button_payment_ideal_checkout', [<div><input type="submit" name="wp_travel_book_now" id="wp-travel-book-now" value={i18n.set_book_now} disabled /></div>], bookingData )  
                        ||  wp_travel_booking_option == "booking_with_payment" && ( selected_payment == 'bank_deposit' || selected_payment == 'paypal' ) && <input type="submit" name="wp_travel_book_now" id="wp-travel-book-now" value={i18n.set_book_now} />
                        ||  wp_travel_booking_option == "booking_with_payment" && ( typeof selected_payment == 'undefined' || selected_payment == '' ) && <div><input type="submit" name="wp_travel_book_now" id="wp-travel-book-now" value={i18n.set_book_now} onClick={ e => handlingForm(e) } /></div> 
                        || wp_travel_booking_option == "booking_only" && <input type="submit" name="wp_travel_book_now" id="wp-travel-book-now" value={i18n.set_book_now} />
                        || <input type="submit" name="wp_travel_book_now" id="wp-travel-book-now" value={i18n.set_book_now} onClick={ e => handlingForm(e) } /> }
                    </div> 
                </div></div></> || <>
                <div className="wptravel-on-page-price-with-payment-field-show">
                    <CouponApplyAmount coupon_data={cart_amount} currency_symbol={ currency_symbol } booking_option={wp_travel_booking_option} payment_mode={wp_travel_payment_mode} partial_enable={ partial_enable }  /> 
                </div>
                <input type="hidden" name="wp_travel_booking_option" value="booking_only" />
                { travelerKey.length > 0 && travelerKey.map( ( keyList, indexs ) => {
                    const trvValue = typeof travelerData[keyList] != 'undefined' && travelerData[keyList] || { 1 : ''}
                    const newTravelerKey = typeof trvValue != 'undefined' && Object.keys( trvValue ) || [];
                    return newTravelerKey.length > 0 && newTravelerKey.map( ( finalKeys, index) => { return <div key={indexs + index } ><HiddenText names={keyList} values={trvValue[finalKeys]} index={index} keys={form_key} /></div> })
                })}
                {billingKey.length > 0 && billingKey.map((keyList, indexs) => {
                    const billValue = billingData[keyList];
                    return <div key={indexs} >{billValue != '' && <BillingHiddenField names={keyList} values={billingData[keyList]} />}</div>

                })}
                <input type="hidden" value={_wp_travel._nonce} name="_nonce" />
                <div className="wptravel-onepage-navigation-btn">
                    <button className="wptravel-onpage-booking-back-buttons" onClick={ () => { 
                        updateStore({...bookingData, tripBillingEnable : true, treipPaymentEnable : false })
                    }} >{i18n.set_go_back}</button>
                    <input type="submit" name="wp_travel_book_now" id="wp-travel-book-now" value={i18n.set_book_now} />
                </div>
                </>
                 }
        </form>
    </>
}