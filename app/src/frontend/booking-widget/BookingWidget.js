import apiFetch from '@wordpress/api-fetch';
import { useSelect } from '@wordpress/data';
import { applyFilters } from '@wordpress/hooks';
import {  useEffect, lazy } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import moment from 'moment';
import ErrorBoundry from './ErrorBoundry';
import { wpTravelFormat, wpTravelTimeout } from "./functions";

import PricingListing from './sub-components/PricingListing';
import PaxSelector from './sub-components/PaxSelector';
import TripTimesListing from './sub-components/TripTimesListing';
import TripExtrasListing from './sub-components/TripExtrasListing';

const _ = lodash;

const storeName = 'WPTravelFrontend/BookingWidget';

const Notice = ({ children, className }) => {
	return <div className="wp-travel-booking__notice is-info">
		<svg id="Capa_1" enableBackground="new 0 0 524.235 524.235" height="512" viewBox="0 0 524.235 524.235" width="512"><path d="m262.118 0c-144.53 0-262.118 117.588-262.118 262.118s117.588 262.118 262.118 262.118 262.118-117.588 262.118-262.118-117.589-262.118-262.118-262.118zm17.05 417.639c-12.453 2.076-37.232 7.261-49.815 8.303-10.651.882-20.702-5.215-26.829-13.967-6.143-8.751-7.615-19.95-3.968-29.997l49.547-136.242h-51.515c-.044-28.389 21.25-49.263 48.485-57.274 12.997-3.824 37.212-9.057 49.809-8.255 7.547.48 20.702 5.215 26.829 13.967 6.143 8.751 7.615 19.95 3.968 29.997l-49.547 136.242h51.499c.01 28.356-20.49 52.564-48.463 57.226zm15.714-253.815c-18.096 0-32.765-14.671-32.765-32.765 0-18.096 14.669-32.765 32.765-32.765s32.765 14.669 32.765 32.765c0 18.095-14.668 32.765-32.765 32.765z"></path></svg>
		{children}
	</div>
}


const __i18n = {
	..._wp_travel.strings
}

const InventoryNotice = ({ inventory }) => {
	const {
		wp_travel_inventory_pax_limit_type,
		wp_travel_inventory_sold_out_action,
		wp_travel_inventory_custom_max_pax,
		wp_travel_inventory_sold_out_message,
		wp_travel_inventory_status_message_format
	} = inventory

	if (wp_travel_inventory_sold_out_action == 'show_sold_out_msg_only') {
		return <p className="wp-travel-sold-out">{wp_travel_inventory_sold_out_message}</p>
	}
	if (wp_travel_inventory_sold_out_action == 'allow_trip_enquiry') {
		return <p className="wp-travel-sold-out">
			<a
				className="wp-travel-send-enquiries"
				data-effect="mfp-move-from-top"
				href="#wp-travel-enquiries"
				onClick={() => {
					let enquiryBtn = document.getElementById('wp-travel-send-enquiries')
					enquiryBtn && enquiryBtn.click()
				}}>
				{__i18n.trip_enquiry}
			</a>
		</p>
	}
	return <p>{__i18n.bookings.pricing_not_available}</p>
}

const BookingWidget = (props) => {
	const { 
		selectedDate,
		selectedTripDate,
		selectedTime,
		nomineePricings,
		nomineeTimes,
		selectedPricing,
		selectedDateTime,
		excludedDateTimes,
		rruleAll,
		paxCounts,
		tripExtras,
		inventory,
		isLoading,
		pricingUnavailable,
		tempExcludeDate,
		// Functions
		updateState,
		handlePricingSelect,
		handleTimeClick,
		handlePaxChange
		} = props;

	

	const allData = useSelect((select) => {
		return select(storeName).getAllStore()
	}, []);

	// Pricing and dates data.
	const pricings = allData.tripData && allData.tripData.pricings && _.keyBy(allData.tripData.pricings, p => p.id)
	const _dates   = 'undefined' !== typeof allData.tripData.dates && allData.tripData.dates.length > 0 ? allData.tripData.dates : [];

	// const handlePaxChange = (id, value) => e => {
	// 	let pricing = pricings[selectedPricing]
	// 	let category = pricing.categories.find(c => c.id === id)
	// 	let count = paxCounts[id] + value < 0 ? 0 : paxCounts[id] + value

	// 	let _inventory = inventory.find(i => i.date === moment(selectedDateTime).format('YYYY-MM-DD[T]HH:mm'))
	// 	let maxPax = _inventory && _inventory.pax_available
	// 	// }
	// 	if (maxPax >= 1) {
	// 		let _totalPax = _.size(paxCounts) > 0 && Object.values(paxCounts).reduce((acc, curr) => acc + curr) || 0
	// 		if (_totalPax + value > parseInt(maxPax)) {
	// 			if (e.target.parentElement.querySelector('.error'))
	// 				return
	// 			let em = document.createElement('em')
	// 			em.classList.add('error')
	// 			em.textContent = __i18n.bookings.max_pax_exceeded
	// 			e.target.parentElement.appendChild(em)
	// 			setTimeout(() => {
	// 				em.remove()
	// 			}, 1000)
	// 			return
	// 		} else {
	// 			e.target.parentElement.querySelector('.error') && e.target.parentElement.querySelector('.error').remove()
	// 		}
	// 	}

	// 	updateState({ paxCounts: { ...paxCounts, [id]: count } })
	// }

	const getCartTotal = (withExtras) => {
		let total = 0;
		let txTotal = 0;

		let tpax = 0

		if ( selectedPricing && 'undefined' != typeof pricings[selectedPricing].has_group_price && pricings[selectedPricing].has_group_price && pricings[selectedPricing].group_prices && pricings[selectedPricing].group_prices.length > 0  ) {
			total = getCategoryPrice();
			tpax = objectSum(paxCounts);
		} else {

			total = _.size(paxCounts) > 0 && Object.entries(paxCounts).map(([i, count]) => {
				tpax += parseInt(count)
				return count > 0 && getCategoryPrice(i, count) || 0 // i is category id here.
				// return parseFloat(price) * count
			}).reduce((acc, curr) => acc + curr) || 0
		}
		if (!withExtras || tpax <= 0) {
			return total
		}
		let _tripExtras = selectedPricing && _.keyBy(pricings[selectedPricing].trip_extras, tx => tx.id)
		txTotal = _.size(tripExtras) > 0 && Object.entries(tripExtras).map(([i, count]) => {
			let tx = _tripExtras[i]
			if (!tx || typeof tx.tour_extras_metas == 'undefined') {
				return 0
			}
			let price = tx.is_sale && tx.tour_extras_metas.extras_item_sale_price || tx.tour_extras_metas.extras_item_price
			return parseFloat(price) * count
		}).reduce((acc, curr) => acc + curr) || 0
		return total + txTotal
	}

	const addToCart = () => {
		let data = {
			trip_id: allData.tripData.id,
			arrival_date: moment(selectedDateTime).format('YYYY-MM-DD'),
			pricing_id: selectedPricing,
			pax: paxCounts,
			category_pax: paxCounts,
			trip_price: getCartTotal(),
		}

		if (selectedTime)
			data.trip_time = moment(selectedDateTime).format('HH:mm')

		let _txs = {}
		Object.entries(tripExtras).forEach(([key, value]) => {
			if (value > 0) {
				_txs = { ..._txs, [key]: value }
			}
		})

		if (_.size(_txs) > 0) {
			data.wp_travel_trip_extras = {
				id: Object.keys(_txs),
				qty: Object.values(_txs)
			}
		}
		wpTravelTimeout(
			apiFetch({
				url: `${wp_travel.ajaxUrl}?action=wp_travel_add_to_cart&_nonce=${_wp_travel._nonce}`,
				method: 'POST',
				data
			})
				.then(res => {
					if (true === res.success && 'WP_TRAVEL_ADDED_TO_CART' === res.data.code) {
						location.href = wp_travel.checkoutUrl; // [only checkout page url]
					}
				}), 1000)
			.catch(error => {
				alert('[X] Request Timeout!')
			})
	}

	const objectSum = (obj) => {
		var sum = 0;
		for( var el in obj ) {
		  if( obj.hasOwnProperty( el ) ) {
			sum += parseFloat( obj[el] );
		  }
		}
		return sum;
	}
	const getCategoryPrice = (categoryId, count) => {
		let counts = paxCounts;
		let pricing = pricings[selectedPricing]
		let isPricingGroupPrice = 'undefined' != typeof pricing.has_group_price && pricing.has_group_price && pricing.group_prices && pricing.group_prices.length > 0 ; 
		let category = pricing.categories.find(c => c.id == categoryId)
		let price = 0;
		if ( category || isPricingGroupPrice ) {

			if ( category && 'undefined' != typeof category.regular_price ) {
				price = category && category.is_sale ? category.sale_price : category.regular_price
			}
	
			if ( isPricingGroupPrice ) {
				// need one additional loop here to get default total price.
				Object.entries(counts).map( ( [i, count]) => {
					let category = pricing.categories.find(c => c.id == i)
					let p = category && category.is_sale ? category.sale_price : category.regular_price
					price += parseFloat(p) * count
				})
				
				let totalPax = objectSum(counts);
				let groupPrices = _.orderBy(pricing.group_prices, gp => parseInt(gp.max_pax))
				let group_price = groupPrices.find(gp => parseInt(gp.min_pax) <= totalPax && parseInt(gp.max_pax) >= totalPax)
				if (group_price && group_price.price) {
					price =  parseFloat(group_price.price) * totalPax
				} 
				
			} else if (category.has_group_price && category.group_prices.length > 0) { // If has group price/discount.
				// hasGroupPrice = true
				let groupPrices = _.orderBy(category.group_prices, gp => parseInt(gp.max_pax))
				let group_price = groupPrices.find(gp => parseInt(gp.min_pax) <= count && parseInt(gp.max_pax) >= count)
				if (group_price && group_price.price) {
					price = 'group' === category.price_per ? count > 0 && parseFloat(group_price.price) || 0 : parseFloat(group_price.price) * count
				} else {
					price = 'group' === category.price_per ? count > 0 && parseFloat(price) || 0 : parseFloat(price) * count
				}
			} else {
				price = 'group' === category.price_per ? count > 0 && parseFloat(price) || 0 : parseFloat(price) * count
			}
		}
		return price || 0
	}

	useEffect(() => {
		jQuery('.wti__selector-item.active').find('.wti__selector-content-wrapper').slideDown();
	}),[];

	let _mindate = _.chain(_dates).sortBy(d => moment(d.start_date).unix()).value() || []
	_mindate = _mindate.find(md => moment(md.start_date).isAfter(moment(new Date())) || moment(md.start_date).isSame(moment(new Date())))

	// calendar Data ends.
	let totalPax = _.size(paxCounts) > 0 && Object.values(paxCounts).reduce((acc, curr) => acc + curr) || 0
	let minPaxToBook = selectedPricing && pricings[selectedPricing].min_pax && parseInt(pricings[selectedPricing].min_pax) || 1
	let activeInventory = inventory.find(i => i.date === moment(selectedDateTime).format('YYYY-MM-DD[T]HH:mm'))
	let maxPaxToBook = activeInventory && parseInt(activeInventory.pax_available)

	return <>
		

			{selectedDateTime && 
				<>
					{/* <Suspense fallback={<Loader />}> */}
						{ ( nomineePricings.length > 1 || ( !pricingUnavailable && nomineeTimes.length > 0 ) ) &&
							<div className={isLoading ? 'wp-travel-booking__pricing-wrapper wptravel-loading' : 'wp-travel-booking__pricing-wrapper'}>
								{
									nomineePricings.length > 1 && <ErrorBoundry>
										{/* <Suspense fallback={<Loader />}> */}
											<PricingListing
												selected={selectedPricing}
												options={nomineePricings}
												onPricingSelect={handlePricingSelect}
											/>
										{/* </Suspense> */}
									</ErrorBoundry>
								}
								{
									! pricingUnavailable && nomineeTimes.length > 0 && <ErrorBoundry>
										{/* <Suspense fallback={<Loader />}> */}
											<TripTimesListing
												selected={selectedDateTime}
												onTimeSelect={handleTimeClick}
												options={nomineeTimes}
											/>
										{/* </Suspense> */}
									</ErrorBoundry>
								}
								
								
							</div>
						}

						<div className="wp-travel-booking__pricing-wrapper wptravel-pax-selector">
							{
								!pricingUnavailable && selectedPricing && inventory.find(i => i.pax_available > 0) && 
								<ErrorBoundry>
									{/* <Suspense fallback={<Loader />}> */}
										<PaxSelector
											pricing={pricings[selectedPricing] || null}
											onPaxChange={handlePaxChange}
											counts={paxCounts}
											inventory={inventory}
											selected={selectedDateTime}
										/>
									{/* </Suspense> */}
								</ErrorBoundry>

							}
							{
								!pricingUnavailable && totalPax > 0 && _.size(pricings[selectedPricing].trip_extras) > 0 && <ErrorBoundry>
									{/* <Suspense fallback={<Loader />}> */}
										<TripExtrasListing
											options={pricings[selectedPricing].trip_extras}
											onChange={(id, value) => () => updateState({ tripExtras: { ...tripExtras, [id]: parseInt(value) } })}
											counts={tripExtras}
										/>
									{/* </Suspense> */}
								</ErrorBoundry>
							}
							{pricingUnavailable && 
							<>
								{/* <Suspense fallback={<Loader />}> */}
									<Notice>
										{
											allData
											&& allData.tripData.inventory
											&& allData.tripData.inventory.enable_trip_inventory == 'yes'
											&& <InventoryNotice inventory={allData.tripData.inventory} />
										}
									</Notice>
								{/* </Suspense> */}
							</>
							}
						</div>

						{selectedPricing &&
							<>
								{/* <Suspense fallback={<Loader />}> */}
									<div className="wp-travel-booking__panel-bottom">
										
										<div className="left-info" >
											{selectedPricing && <p><strong>{__i18n.bookings.combined_pricing}</strong>: {pricings[selectedPricing].title}</p>}
											{selectedDateTime && <p><strong>{__i18n.trip_date}</strong>: <span>{moment(selectedDateTime).format( _wp_travel.date_format_moment )}</span></p>}
										</div>
										
										<div className="right-info" >
											<p>{__i18n.bookings.booking_tab_cart_total}<strong dangerouslySetInnerHTML={{ __html: wpTravelFormat(getCartTotal(true)) }}></strong></p>
											<button disabled={totalPax < minPaxToBook || totalPax > maxPaxToBook} onClick={addToCart} className="wp-travel-book">{__i18n.bookings.booking_tab_booking_btn_label}</button>
										</div>
									</div>
								{/* </Suspense> */}
							</>
						}
					{/* </Suspense> */}
				</>
			}
			
	</>
}

export default BookingWidget
