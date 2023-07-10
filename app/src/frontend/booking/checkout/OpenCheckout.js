import { Button } from '@wordpress/components'
import Modal from 'react-modal';
import $ from 'jquery';
import { useState, useEffect } from '@wordpress/element';
import OpenBookign from './OpenBookign';
import { useSelect, dispatch } from '@wordpress/data';
import TravelerInfo from './TravelerInfo';
// import { getSettings } from './api-working/getSettings';

import BillingFormField from './form-field/BillingFormField';
// import PaymentFormField from './form-field/PaymentFormField';
import BookingFormWithPayment from './form-field/BookingFormWithPayment';

// $(document).ready(function() {
//     $(document).on('click', '#wp-travel-one-page-checkout-enables', function() {
//         setTimeout(function() {
//             // Your code to execute after a delay
//             $('.booknow-btn-modal').addClass('show');
//           }, 1000);

     
//     });
//   });
  
  
  
const __i18n = {
    ..._wp_travel.strings
}
const bookingStoreName = 'WPTravelFrontend/BookingData';

export default () => {
    console.log('detailsdfdf', _wp_travel);
    console.log('def', wp_travel);
    const [isOpen, setOpen] = useState(false);

    const openModal = () => setOpen(true);
    const closeModal = () => setOpen(false);


    // Booking Data/state.
    const bookingData = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch(bookingStoreName);

    const { bookingTabEnable, travelerInfo, tripBillingEnable, treipPaymentEnable } = bookingData;
    console.log('selected data', bookingData);
    const tooltipText = __i18n.bookings.date_select_to_view_options;
    // Modal.setAppElement('#OpenCeckout');
    return <>
        <Button className="booknow-btn-booking" onClick={openModal}>Book Now</Button>
        <div className="wp-travel-checkout-one-page">
            <Modal
                className="booknow-btn-modal"
                isOpen={isOpen}
                onRequestClose={closeModal}
            >
                <h2>{typeof bookingTabEnable != 'undefined' && bookingTabEnable ? 'Select Your Pax' : (typeof travelerInfo != 'undefined' && travelerInfo ? 'Enter traveler Detail' : (typeof tripBillingEnable != 'undefined' && tripBillingEnable ? 'Billing Details' : 'Payment Field'))} </h2>
                { typeof bookingTabEnable != 'undefined' && bookingTabEnable && 
                    <OpenBookign forceCalendarDisplay={false} calendarInline={false} showTooltip={true} tooltipText={tooltipText} />
                }
                {
                    typeof travelerInfo != 'undefined' && travelerInfo && <TravelerInfo />
                }
                { typeof tripBillingEnable != 'undefined' && tripBillingEnable && <BillingFormField /> }
                { typeof treipPaymentEnable != 'undefined' && treipPaymentEnable && <BookingFormWithPayment /> }
                    
                   
                {/* <BookingFormWithPayment /> */}
                {/* <TravelerInfo /> */}
                {/* <div className='wptravel-traveller-info-container'><TravelerInfo /></div> */}
            </Modal>
        </div>
    </>
}