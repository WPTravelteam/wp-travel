// Additional lib
import ErrorBoundary from '../../../ErrorBoundry/ErrorBoundry';
const __i18n = {
	..._wp_travel.strings
}
const CustomBooking = ( props ) => {
	// Component Props.
	const {
        pricing_type:pricingType,
        custom_booking_type:customBookingType,
        custom_booking_link:customBookingLink,
        custom_booking_link_open_in_new_tab:customBookingLinkOpenInNewTab,
        custom_booking_link_text:customBookingLinkText,
        custom_booking_form,
    } = props;
    let linkText = customBookingLinkText ? customBookingLinkText : __i18n.book_now;
    return <ErrorBoundary>
            <>
            {
                pricingType && 'custom-booking' === pricingType && <> {
					'custom-link' === customBookingType ? 
					<div className="wp-travel-custom-link">
						<a href={customBookingLink || '#'} target={customBookingLinkOpenInNewTab ? 'new' : ''}>{linkText}</a>
					</div> 
					: 
                    // need to support shortcode
					'custom-form' === customBookingType && <div className="wp-travel-custom-form" dangerouslySetInnerHTML={{__html: custom_booking_form || ''}}></div>
                }
                </>
            }
            </>
    </ErrorBoundary>
}
export default CustomBooking;