import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

const _ = lodash
const storeName = 'WPTravelFrontend/BookingWidget';
const __i18n = {
	..._wp_travel.strings
}
const PricingListing =  ({ selected, options, onPricingSelect }) => {

	const allData = useSelect((select) => {
		return select(storeName).getAllStore()
	}, []);

	let pricings = allData.tripData && allData.tripData.pricings && _.keyBy(allData.tripData.pricings, p => p.id)
	return <div className="wp-travel-booking__pricing-name">
		{
			options.length > 1 && <>
				<h4>{__i18n.bookings.pricings_list_label}</h4>
				{
					options.map(
						(id, i) => <button key={i}
							disabled={selected == id}
							className={selected == id ? 'active' : ''}
							onClick={ () => onPricingSelect(id) }>
							{pricings[id] && pricings[id].title}
						</button>
					)
				}
			</>
		}
	</div>
}

export default PricingListing