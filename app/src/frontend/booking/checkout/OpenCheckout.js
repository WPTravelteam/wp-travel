import { Button } from '@wordpress/components'
import Modal from 'react-modal';
import $ from 'jquery';
import { useState } from '@wordpress/element';
import OpenBookign from './OpenBookign';
import { useSelect, dispatch } from '@wordpress/data';
import TravelerInfo from './TravelerInfo';
// import { getSettings } from './api-working/getSettings';
import { _n, __} from '@wordpress/i18n'
import BillingFormField from './form-field/BillingFormField';
// import PaymentFormField from './form-field/PaymentFormField';
import BookingFormWithPayment from './form-field/BookingFormWithPayment';

const __i18n = {
    ..._wp_travel.strings
}
const bookingStoreName = 'WPTravelFrontend/BookingData';

export default () => {
    // console.log('detailsdfdf', _wp_travel);
    const [isOpen, setOpen] = useState(false);

    const openModal = () => setOpen(true);
    const closeModal = () => setOpen(false);


    // Booking Data/state.
    const bookingData = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch(bookingStoreName);

    const { bookingTabEnable, travelerInfo, tripBillingEnable, treipPaymentEnable } = bookingData;
    const tooltipText = __i18n.bookings.date_select_to_view_options;
    return <>
        <Button className="booknow-btn-booking wptravel-book-your-trip wp-travel-booknow-btn" onClick={openModal}>{__( 'Book Now', 'wp-travel' ) }</Button>
        <div className="wp-travel-checkout-one-page">
            <Modal
                className="booknow-btn-modal"
                isOpen={isOpen}
                onRequestClose={closeModal}
            >
                <button onClick={ closeModal} className="wptravel-single-page-close-btn">x</button>
               
                <h2>{typeof bookingTabEnable != 'undefined' && bookingTabEnable ? _wp_travel.select_you_pax : (typeof travelerInfo != 'undefined' && travelerInfo ? __('Traveler Details', 'wp-travel' ) : (typeof tripBillingEnable != 'undefined' && tripBillingEnable ? __('Billing Details', 'wp-travel' ) : __( 'Payment Details', 'wp-travel' ) ) ) } </h2>

                {typeof bookingTabEnable != 'undefined' && bookingTabEnable &&
                    <div className='wptravel-single-page-calender-booking wp-travel-calendar-view'>
                        <OpenBookign forceCalendarDisplay={false} calendarInline={false} showTooltip={true} tooltipText={tooltipText} />
                    </div>

                }
                {
                    typeof travelerInfo != 'undefined' && travelerInfo && <TravelerInfo />
                }
                {typeof tripBillingEnable != 'undefined' && tripBillingEnable && <BillingFormField />}
                {typeof treipPaymentEnable != 'undefined' && treipPaymentEnable && <BookingFormWithPayment />}

            </Modal>
        </div>
    </>
}