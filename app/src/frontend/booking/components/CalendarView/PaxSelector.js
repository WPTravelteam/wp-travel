import { __ } from '@wordpress/i18n'
import DiscountTable from './_GroupDiscountTable';

import _ from 'lodash';
const __i18n = {
	..._wp_travel.strings
}

// WP Travel Functions.
import { wpTravelFormat, objectSum, GetConvertedPrice } from '../../_wptravelFunctions';

const PaxSelector = ( props ) => {
	// Component Props.
	const { tripData, bookingData, updateBookingData } = props;

	// Trip Data.
    const {
        is_fixed_departure:isFixedDeparture,
        dates,
        pricings,
        trip_duration
    } = tripData;
    const allPricings        = pricings && _.keyBy( pricings, p => p.id ) // Need object structure because pricing id may not be in sequencial order.
    const _dates             = 'undefined' !== typeof dates && dates.length > 0 ? dates : [];
    const datesById          = _.keyBy(_dates, d => d.id)
    const duration           = trip_duration.days && parseInt( trip_duration.days ) || 1;
	const isInventoryEnabled = tripData.inventory && tripData.inventory.enable_trip_inventory === 'yes';

    // Booking Data.
    const { selectedDate, selectedDateIds, selectedPricingId, excludedDateTimes, pricingUnavailable, inventory, paxCounts } = bookingData;


	const pricing = allPricings[selectedPricingId];

	let categories = pricing && pricing.categories || []
	const getCategoryPrice = (categoryId, single) => { // This function handles group discounts as well
		let category = pricing.categories.find(c => c.id == categoryId)
		if (!category) {
			return
		}
		let count = paxCounts[categoryId] || 0
		
		let price = category && category.is_sale ? category.sale_price : category.regular_price

		if( 'undefined' != typeof category.is_sale && category.is_sale && 'undefined' != typeof category.is_sale_percentage && category.is_sale_percentage ){
			price= (category.sale_percentage_val/100)*category.regular_price
		}

		if ( 'undefined' != typeof pricing.has_group_price && pricing.has_group_price && pricing.group_prices && pricing.group_prices.length > 0  ) {
			let totalPax = objectSum(paxCounts);
			let groupPrices = _.orderBy(pricing.group_prices, gp => parseInt(gp.max_pax))
			let group_price = groupPrices.find(gp => parseInt(gp.min_pax) <= totalPax && parseInt(gp.max_pax) >= totalPax)
			if (group_price && group_price.price) {
				if (single) {
					price = parseFloat(group_price.price);
					return  GetConvertedPrice( price ); // Add Multiple currency support to get converted price.
				}

				price =  parseFloat(group_price.price) * totalPax
			} else {
				if (single) {
					price = parseFloat(price);
					return GetConvertedPrice( price ); // Add Multiple currency support to get converted price.
				}
		
				price = parseFloat(price) * totalPax
			}
		} else if (category.has_group_price && category.group_prices.length > 0) { // If has group price/discount.
			let groupPrices = _.orderBy(category.group_prices, gp => parseInt(gp.max_pax))
			let group_price = groupPrices.find(gp => parseInt(gp.min_pax) <= count && parseInt(gp.max_pax) >= count)
			if (group_price && group_price.price) {
				if (single) {
					price = parseFloat(group_price.price)
					return GetConvertedPrice( price ); // Add Multiple currency support to get converted price.
				}
				price = 'group' === category.price_per ? (count > 0 ? parseFloat(group_price.price) : 0) : parseFloat(group_price.price) * count
			} else {
				if (single) {
					price = parseFloat(price)
					return GetConvertedPrice( price ); // Add Multiple currency support to get converted price.
				}
				price = 'group' === category.price_per ? (count > 0 ? parseFloat(price) : 0) : parseFloat(price) * count
			}
		} else {
			if (single) {
				price = parseFloat(price)
				return GetConvertedPrice( price ); // Add Multiple currency support to get converted price. 
			}
			price = 'group' === category.price_per ? (count > 0 ? parseFloat(price) : 0) : parseFloat(price) * count
		}
		price = price || 0;
		return GetConvertedPrice( price ); // Add Multiple currency support to get converted price.
	}

	const handlePaxChange = (id, value, tripPax) => e => {
		let count = paxCounts[id] + value < 0 ? 0 : paxCounts[id] + value
		if( tripData.enable_pax_all_pricing == "1" ){
			if( count > tripPax ){
				count = tripPax
				if (e.target.parentElement.querySelector('.error'))
					return
				let em = document.createElement('em')
				em.classList.add('error')
				em.textContent = __i18n.bookings.max_pax_exceeded
				e.target.parentElement.appendChild(em)
				setTimeout(() => {
					em.remove()
				}, 1000)
				return
			}
			
		}else{
			let _inventory = inventory.find(i => i.date === moment(selectedDate).format('YYYY-MM-DD[T]HH:mm')); // selectedDate : date along with time.
			let maxPax = _inventory && _inventory.pax_available;
			if ( ! maxPax ) {
				maxPax = pricing && pricing.max_pax ? pricing.max_pax : 1;
			}
	
			if (maxPax >= 1) {
	
				let _totalPax = _.size(paxCounts) > 0 && Object.values(paxCounts).reduce((acc, curr) => acc + curr) || 0
				if (_totalPax + value > parseInt(maxPax)) {
					if (e.target.parentElement.querySelector('.error'))
						return
					let em = document.createElement('em')
					em.classList.add('error')
					em.textContent = __i18n.bookings.max_pax_exceeded
					e.target.parentElement.appendChild(em)
					setTimeout(() => {
						em.remove()
					}, 1000)
					return
				} else {
					e.target.parentElement.querySelector('.error') && e.target.parentElement.querySelector('.error').remove()
				}
			}
		}

		updateBookingData({ paxCounts: { ...paxCounts, [id]: count } })
	}

	const handlePaxChangeInput = (id, datas ) => {
		const values = datas.target.value;
		let  count =  values != '' && parseInt( values ) < 0 ? 0 : values;
		let _inventory = inventory.find(i => i.date === moment(selectedDate).format('YYYY-MM-DD[T]HH:mm')); // selectedDate : date along with time.
		let maxPax = _inventory && _inventory.pax_available;
		if ( ! maxPax ) {
			maxPax = pricing && pricing.max_pax ? pricing.max_pax : 1;
		}

		if ( ! maxPax ) {
			maxPax = pricing && pricing.max_pax ? pricing.max_pax : 1;
		}

		if (maxPax >= 1) {

			if ( values > parseInt(maxPax)) {
				updateBookingData({ paxCounts: { ...paxCounts, [id]: 0 } })
				return;
			} else {
				datas.target.parentElement.querySelector('.error') && datas.target.parentElement.querySelector('.error').remove()
			}
		}
		updateBookingData({ paxCounts: { ...paxCounts, [id]: count != 0 && count >= 1 ? parseInt( count ) : count } })
	} 

	return <div className="wp-travel-booking__pax-selector-wrapper">
		<h4>{__i18n.bookings.booking_tab_pax_selector}</h4>
		<ul className="wp-travel-booking__trip-option-list">
			{
				categories.map((c, i) => {
					let price = c.is_sale ? c.sale_price : c.regular_price
					if ( 'undefined' == typeof c.term_info ) { // Fixes : index title of undefined.
						return <></>
					}
					let price_per_label = c.price_per;
					if ( 'undefined' != typeof( __i18n.price_per_labels[price_per_label] ) ) {
						price_per_label = __i18n.price_per_labels[price_per_label];
					}
					let _inventory = inventory.find(i => i.date === moment(selectedDate).format('YYYY-MM-DD[T]HH:mm')); // selectedDate : date along with time.
					let maxPax = isInventoryEnabled && _inventory && _inventory.pax_available ? _inventory.pax_available : pricing.max_pax; // Temp fixes for inventory disabled case.
					let minPax = paxCounts[c.id] ? paxCounts[c.id] : 0;
					console.log( c.term_info );

					var pricing_cat = c.term_info.title.replace(/ /g, "-");
					return <li className={pricing_cat} key={i}>
						<div className="text-left">
							<strong>
								{`${c.term_info.title}`} &nbsp;
								{<span className="wp_travel_pax_info">{ _wp_travel.pax_show_remove == '' ? `(${minPax}/${maxPax})` : _wp_travel.pax_show_remove }</span>}
							</strong>
							{( ( c.has_group_price && c.group_prices.length > 0 ) || pricing && 'undefined' != typeof pricing.has_group_price && pricing.has_group_price && pricing.group_prices.length > 0 ) && <span className="tooltip group-discount-button">
								<span>{__i18n.bookings.group_discount_tooltip}</span>
								<svg version="1.1" x="0px" y="0px" viewBox="0 0 512.003 512.003" style={{ enableBackground: 'new 0 0 512.003 512.003' }}><path d="M477.958,262.633c-2.06-4.215-2.06-9.049,0-13.263l19.096-39.065c10.632-21.751,2.208-47.676-19.178-59.023l-38.41-20.38
                                        c-4.144-2.198-6.985-6.11-7.796-10.729l-7.512-42.829c-4.183-23.846-26.241-39.87-50.208-36.479l-43.053,6.09
                                        c-4.647,0.656-9.242-0.838-12.613-4.099l-31.251-30.232c-17.401-16.834-44.661-16.835-62.061,0L193.72,42.859
                                        c-3.372,3.262-7.967,4.753-12.613,4.099l-43.053-6.09c-23.975-3.393-46.025,12.633-50.208,36.479l-7.512,42.827
                                        c-0.811,4.62-3.652,8.531-7.795,10.73l-38.41,20.38c-21.386,11.346-29.81,37.273-19.178,59.024l19.095,39.064
                                        c2.06,4.215,2.06,9.049,0,13.263l-19.096,39.064c-10.632,21.751-2.208,47.676,19.178,59.023l38.41,20.38
                                        c4.144,2.198,6.985,6.11,7.796,10.729l7.512,42.829c3.808,21.708,22.422,36.932,43.815,36.93c2.107,0,4.245-0.148,6.394-0.452
                                        l43.053-6.09c4.643-0.659,9.241,0.838,12.613,4.099l31.251,30.232c8.702,8.418,19.864,12.626,31.03,12.625
                                        c11.163-0.001,22.332-4.209,31.03-12.625l31.252-30.232c3.372-3.261,7.968-4.751,12.613-4.099l43.053,6.09
                                        c23.978,3.392,46.025-12.633,50.208-36.479l7.513-42.827c0.811-4.62,3.652-8.531,7.795-10.73l38.41-20.38
                                        c21.386-11.346,29.81-37.273,19.178-59.024L477.958,262.633z M196.941,123.116c29.852,0,54.139,24.287,54.139,54.139
                                        s-24.287,54.139-54.139,54.139s-54.139-24.287-54.139-54.139S167.089,123.116,196.941,123.116z M168.997,363.886
                                        c-2.883,2.883-6.662,4.325-10.44,4.325s-7.558-1.441-10.44-4.325c-5.766-5.766-5.766-15.115,0-20.881l194.889-194.889
                                        c5.765-5.766,15.115-5.766,20.881,0c5.766,5.766,5.766,15.115,0,20.881L168.997,363.886z M315.061,388.888
                                        c-29.852,0-54.139-24.287-54.139-54.139s24.287-54.139,54.139-54.139c29.852,0,54.139,24.287,54.139,54.139
                                        S344.913,388.888,315.061,388.888z"></path><path d="M315.061,310.141c-13.569,0-24.609,11.039-24.609,24.608s11.039,24.608,24.609,24.608
                                        c13.569,0,24.608-11.039,24.608-24.608S328.63,310.141,315.061,310.141z"></path><path d="M196.941,152.646c-13.569,0-24.608,11.039-24.608,24.608c0,13.569,11.039,24.609,24.608,24.609
                                        c13.569,0,24.609-11.039,24.609-24.609C221.549,163.686,210.51,152.646,196.941,152.646z"></path>
								</svg>
								{__i18n.bookings.view_group_discount}
								{pricing && 'undefined' != typeof pricing.has_group_price && pricing.has_group_price && pricing.group_prices.length > 0 ?
									<DiscountTable groupPricings={pricing.group_prices} />
								:
								<>
									{c.has_group_price && c.group_prices.length > 0 && <DiscountTable groupPricings={c.group_prices} />}
								</>
								}
							</span>}
						</div>
						<div className="text-right">
							<span className="item-price">{c.is_sale && <del dangerouslySetInnerHTML={{ __html: wpTravelFormat(GetConvertedPrice( c.regular_price )) }}></del>} <span dangerouslySetInnerHTML={{ __html: wpTravelFormat(getCategoryPrice(c.id, true)) }}></span>/{price_per_label}</span>
							<div className="pricing-area">
								<div className="qty-spinner">
									<button className="trip-page-pax-selector edit-pax-selector-qty" data-minpax={pricing.min_pax} data-allpricing={tripData.enable_pax_all_pricing} onClick={handlePaxChange(c.id, -1, maxPax )}>-</button>
			
									<input  className='wp-trave-pax-selected-frontend-second' 
										value={typeof paxCounts[c.id] == 'undefined' ? parseInt(c.default_pax) : paxCounts[c.id]} 
										onChange={ ( data ) => {
											handlePaxChangeInput( c.id, data )
									}} />
									<button className="trip-page-pax-selector edit-pax-selector-qty" data-minpax={pricing.min_pax} data-allpricing={tripData.enable_pax_all_pricing} onClick={handlePaxChange(c.id, 1, maxPax )}>+</button>
								</div>
							</div>
						</div>
						
					</li>
				})
			}
		</ul>
	</div>
}

export default PaxSelector;
// @todo inventory with trip time is not supported yet
// selectedTimeObject to selectedDateTimeObject