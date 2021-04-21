import { useSelect } from '@wordpress/data';
import { render, lazy, Suspense } from '@wordpress/element';
import ErrorBoundary from './ErrorBoundry';
import './_Store';

/**
 * It is important to set global var before any imports.
 * https://stackoverflow.com/questions/39879680/example-of-setting-webpack-public-path-at-runtime
 */
__webpack_public_path__ = _wp_travel.build_path;

const BookingWidget = lazy(() => import("./BookingCalender"));

const storeName = 'WPTravelFrontend/BookingWidget';

const WPTravelBookingWidget = () => {
	const renderLoader = () => <div className="loader"></div>;

    const allData = useSelect((select) => {
        return select(storeName).getAllStore()
    }, []);
    let tripData = allData.tripData;
    const {
        pricing_type,
        custom_booking_type,
        custom_booking_link,
        custom_booking_link_text,
        custom_booking_form,
        custom_booking_link_open_in_new_tab
    } = tripData;
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
					<Suspense fallback={renderLoader()}>
                    	<BookingWidget />
					</Suspense>
                </ErrorBoundary>
            }
        </ErrorBoundary>
    </>
}
let bookingWidgetElementId = _wp_travel.itinerary_v2 ? 'wti__booking' : 'booking';
if (document.getElementById(bookingWidgetElementId)) {
    render(<WPTravelBookingWidget />, document.getElementById(bookingWidgetElementId));
}
