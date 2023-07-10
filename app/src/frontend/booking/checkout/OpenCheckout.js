import { Button } from '@wordpress/components'
import Modal from 'react-modal';

import { useState, useEffect } from '@wordpress/element';
import OpenBookign from './OpenBookign';
import { useSelect, dispatch } from '@wordpress/data';
import TravelerInfo from './TravelerInfo';
// import { getSettings } from './api-working/getSettings';

import BillingFormField from './form-field/BillingFormField';
// import PaymentFormField from './form-field/PaymentFormField';
import BookingFormWithPayment from './form-field/BookingFormWithPayment';

const __i18n = {
	..._wp_travel.strings
}
const bookingStoreName = 'WPTravelFrontend/BookingData';

export default () => {
    console.log('detailsdfdf', _wp_travel );
    console.log('def', wp_travel );
    const [ isOpen, setOpen ] = useState( false );
    
    const openModal = () => setOpen( true );
    const closeModal = () => setOpen( false );
    

    // Booking Data/state.
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );

    const { bookingTabEnable, travelerInfo, tripBillingEnable, treipPaymentEnable } = bookingData;
    console.log( 'selected data', bookingData );
    const tooltipText = __i18n.bookings.date_select_to_view_options;
    return <>
        <Button onClick={openModal}>Book Now</Button>
        <div className="wp-travel-checkout-one-page">
             <Modal 
                isOpen={isOpen}
                onRequestClose={closeModal}
                contentLabel={ typeof bookingTabEnable != 'undefined' && bookingTabEnable ? 'Select Your Pax' : ( typeof travelerInfo != 'undefined' && travelerInfo ? 'Enter traveler Detail' : ( typeof tripBillingEnable != 'undefined' && tripBillingEnable ? 'Billing Details' : 'Payment Field' ) ) } 
                >
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
            </Modal>
        </div>
    </>
}