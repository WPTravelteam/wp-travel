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
	let allPricings = tripData && tripData.pricings && _.keyBy( tripData.pricings, p => p.id );

	const { nomineePricingIds, selectedPricingId } = bookingData;
	return <>
		{
			nomineePricingIds.length > 1 &&
			<div className="wp-travel-booking__pricing-wrapper">
				<div className="wp-travel-booking__pricing-name">
					<h4>{__i18n.bookings.pricings_list_label}</h4>
					{
						nomineePricingIds.map(
							(id, i) => <button key={i}
								disabled={selectedPricingId === id  }
								className={selectedPricingId === id ? 'active' : ''}
								onClick={ () => {
									updateBookingData({
										selectedPricingId:id
									});
								} }>
								{allPricings[id] && allPricings[id].title}
							</button>
						)
					}
				</div>
			</div>
		}
	</>
}

export default Pricings