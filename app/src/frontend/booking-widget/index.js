import { useSelect } from '@wordpress/data';
import { render } from '@wordpress/element';
import ErrorBoundary from './ErrorBoundry';
import './_Store';

import BookingWidget from './BookingCalender';
const storeName = 'WPTravelFrontend/BookingWidget';

const WPTravelBookingWidget = () => {
    const allData = useSelect((select) => {
        return select(storeName).getAllStore()
    }, []);
    let tripData = allData.tripData
    const {
        pricing_type,
        custom_booking_type,
        custom_booking_link,
        custom_booking_link_text,
        custom_booking_form,
        custom_booking_link_open_in_new_tab
    } = tripData
    return <>
        <ErrorBoundary>
            {
                tripData && pricing_type === 'custom-booking' && <>
                    {
                        custom_booking_type == 'custom-link' ? <div className="wp-travel-custom-link">
                            <a href={custom_booking_link || '#'} target={custom_booking_link_open_in_new_tab ? 'new' : ''}>{custom_booking_link_text}</a>
                        </div> : custom_booking_type == 'custom-form' && <div className="wp-travel-custom-form" dangerouslySetInnerHTML={{__html: custom_booking_form || ''}}>
                        </div>
                    }
                </> || <ErrorBoundary>
                    <BookingWidget />
                </ErrorBoundary>
            }
        </ErrorBoundary>
    </>
}
let bookingWidgetElementId = _wp_travel.itinerary_v2 ? 'wti__booking' : 'booking';
if (document.getElementById(bookingWidgetElementId)) {
    render(<WPTravelBookingWidget />, document.getElementById(bookingWidgetElementId));
}