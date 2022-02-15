// Additional lib
import ErrorBoundary from '../../ErrorBoundry/ErrorBoundry';

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
    console.log( 'cs', props );
	// End of Trip related datas.
    return <ErrorBoundary>
            <>
            {
                pricingType && 'custom-booking' === pricingType && <> {
					'custom-link' === customBookingType ? 
					<div className="wp-travel-custom-link">
						<a href={customBookingLink || '#'} target={customBookingLinkOpenInNewTab ? 'new' : ''}>{customBookingLinkText}</a>
					</div> 
					: 
					'custom-form' === customBookingType && <div className="wp-travel-custom-form" dangerouslySetInnerHTML={{__html: custom_booking_form || ''}}></div>
                }
                </>
		
            }
            </>
    </ErrorBoundary>
}
export default CustomBooking;