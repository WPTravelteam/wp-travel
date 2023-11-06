import { Button } from '@wordpress/components'
import Modal from 'react-modal';
import $ from 'jquery';
import { useState } from '@wordpress/element';
import OpenBookign from './OpenBookign';
import { useSelect, dispatch } from '@wordpress/data';
import TravelerInfo from './TravelerInfo';
// import { getSettings } from './api-working/getSettings';
import { _n, __ } from '@wordpress/i18n'
import BillingFormField from './form-field/BillingFormField';
// import PaymentFormField from './form-field/PaymentFormField';
import BookingFormWithPayment from './form-field/BookingFormWithPayment';


const __i18n = {
    ..._wp_travel.strings
}
const bookingStoreName = 'WPTravelFrontend/BookingData';


import { DEFAULT_BOOKING_STATE } from '../store/_Store'; 

const initialState = DEFAULT_BOOKING_STATE();

export default () => {
    const [isOpen, setOpen] = useState(true);


    // Booking Data/state.
    const bookingData = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch(bookingStoreName);
    const updateBookingData = ( data ) => {
        updateStore({ ...bookingData, ...data });
    }
    const { bookingTabEnable, travelerInfo, tripBillingEnable, treipPaymentEnable, payment_form } = bookingData;
    const paymentEnable = typeof payment_form != 'undefined' && typeof payment_form.payment_gateway != 'undefined' && true || false
    const tooltipText = __i18n.bookings.date_select_to_view_options;
    
    const openModal = () => {
        $( '.ReactModalPortal' ).css( 'display', 'block' );
        $('.single-itineraries').addClass('wp-travel-one-page-open-for-booking')
    };
    const closeModal = () => {
        updateBookingData( initialState );
        $( '.ReactModalPortal' ).css( 'display', 'none' );
        $('.single-itineraries').removeClass('wp-travel-one-page-open-for-booking')
    } 

    return <>
        <Button className=" wptravel-book-your-trips  wp-travel-booknow-btns" onClick={openModal}>{__i18n.set_book_now}</Button>
        <div className="wp-travel-checkout-one-page">
            <Modal
                className="booknow-btn-modal"
                isOpen={isOpen}
                onRequestClose={closeModal}
                shouldCloseOnOverlayClick={false}
            >
                <div className="wptravel-onpage-header">
                    <h2>{typeof bookingTabEnable != 'undefined' && bookingTabEnable ? _wp_travel.select_you_pax : (typeof travelerInfo != 'undefined' && travelerInfo ? __i18n.set_traveler_details : (typeof tripBillingEnable != 'undefined' && tripBillingEnable ? __i18n.set_booking_details : (paymentEnable && __i18n.set_booking_with || __i18n.set_booking_only)))} </h2>

                    <button onClick={closeModal} className="wptravel-single-page-close-btn"><i className='fa fa-times'></i></button>
                </div>

                {typeof bookingTabEnable != 'undefined' && bookingTabEnable &&
                    <div className='wptravel-single-page-calender-booking wp-travel-calendar-view'>
                        <OpenBookign forceCalendarDisplay={false} calendarInline={false} showTooltip={true} tooltipText={tooltipText} />
                    </div>
                }
                {typeof travelerInfo != 'undefined' && travelerInfo && <TravelerInfo />}
                {typeof tripBillingEnable != 'undefined' && tripBillingEnable && <BillingFormField />}
                {typeof treipPaymentEnable != 'undefined' && treipPaymentEnable && <BookingFormWithPayment />}

            </Modal>
        </div>
    </>
}