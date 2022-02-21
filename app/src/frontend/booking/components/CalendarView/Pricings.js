import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
const __i18n = {
	..._wp_travel.strings
}

// Additional lib
const _ = lodash
const Pricings =  ( props ) => {
	// Component Props.
	const { tripData, bookingData, updateBookingData } = props;
	let allPricings = tripData && tripData.pricings && _.keyBy( tripData.pricings, p => p.id ); // Need object structure because pricing id may not be in sequencial order.

	const { selectedDate, nomineePricingIds, selectedPricingId } = bookingData;
	return <>
		{
			nomineePricingIds.length > 1 ?
				<>
				<h4>{__i18n.bookings.pricings_list_label}</h4>
				{
					nomineePricingIds.map(
						(id, i) => <button key={i}
							disabled={selectedPricingId === id  }
							className={selectedPricingId === id ? 'active' : ''}
							onClick={ () => {
								let selectedPricingId = id;
								let selectedPricing   = allPricings[ selectedPricingId ].title;
								updateBookingData({
									selectedPricingId:id,
									selectedPricing:selectedPricing
								});
							} }>
							{allPricings[id] && allPricings[id].title}
						</button>
					)
				}
				</>
				
			: 1 != nomineePricingIds.length && <h4>{ __( 'Sorry!! Pricing not found for selected date. Please select another date.', 'wp-travel' ) }</h4>
		}
	</>
}

export default Pricings