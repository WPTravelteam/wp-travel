import { Suspense, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { applyFilters } from '@wordpress/hooks';
import { useSelect, dispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
const __i18n = {
	..._wp_travel.strings
}
import { Spinner } from '@wordpress/components';

import _ from 'lodash';
import ErrorBoundary from '../../../ErrorBoundry/ErrorBoundry';

const bookingStoreName = 'WPTravelFrontend/BookingData';

// WP Travel Functions.
import { objectSum, wpTravelFormat, wpTravelTimeout, GetConvertedPrice } from '../_wptravelFunctions';


const WpTravelBookNow = ( props ) => {

    // Booking Data/state.
    const bookingAllData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);

    const { updateStore } = dispatch( bookingStoreName );
    // Component Props.
	const { tripData, bookingData, updateBookingData } = props;

    // Trip Data.
    const {
        dates,
        pricings,
		min_checkout_price
    } = tripData;
    const allPricings = pricings && _.keyBy( pricings, p => p.id ) // Need object structure because pricing id may not be in sequencial order.
    const _dates      = 'undefined' !== typeof dates && dates.length > 0 ? dates : [];
	var minCheckoputPrice = !min_checkout_price ? '0' : min_checkout_price;
    // Booking Data.
    const { selectedPricingId, nomineeTimes, selectedTime, selectedDate, paxCounts, inventory, nomineeTripExtras, tripExtras, selectedDateIds } = bookingData;

	let totalPax        = objectSum( paxCounts );
	let minPaxToBook    = selectedPricingId && allPricings[selectedPricingId].min_pax && parseInt( allPricings[selectedPricingId].min_pax ) || 1
	let activeInventory = inventory.find( i => i.date === moment( selectedDate ).format('YYYY-MM-DD[T]HH:mm') )
	let maxPaxToBook    = activeInventory && parseInt( activeInventory.pax_available )

	/**
	 * To get only trip price in add to cart ajax request we don't need withExtras param. Note extras pricing in add  to cart is calculated from php to add it in cart.
	 * @param { calculate price along with extras } withExtras 
	 * @returns number
	 */
    const getCartTotal = ( withExtras ) => {
		let total   = 0; // Total amount.
		let txTotal = 0; // Extras total.
		let tpax    = 0 // Total Pax.

		if ( selectedPricingId && 'undefined' != typeof allPricings[selectedPricingId].has_group_price && allPricings[selectedPricingId].has_group_price && allPricings[selectedPricingId].group_prices && allPricings[selectedPricingId].group_prices.length > 0  ) {
			total = getCategoryPrice();
			tpax = objectSum(paxCounts);
		} else {

			total = _.size(paxCounts) > 0 && Object.entries(paxCounts).map(([i, count]) => {
				tpax += parseInt(count)
				return count > 0 && getCategoryPrice(i, count) || 0 // i is category id here.
			}).reduce((acc, curr) => acc + curr) || 0
		}

		if ( ! withExtras || tpax <= 0 ) {
			return GetConvertedPrice( total );
		}
		let _tripExtras = selectedPricingId && _.keyBy( nomineeTripExtras, tx => tx.id )
		txTotal = _.size(tripExtras) > 0 && Object.entries(tripExtras).map(([i, count]) => {
			let tx = _tripExtras[i]
			if (!tx || typeof tx.tour_extras_metas == 'undefined') {
				return 0
			}
			let price = tx.is_sale && tx.tour_extras_metas.extras_item_sale_price || tx.tour_extras_metas.extras_item_price
			return parseFloat(price) * count
		}).reduce((acc, curr) => acc + curr) || 0
		return GetConvertedPrice( total + txTotal );  // Add Multiple currency support to get converted price.
	}
	const getCategoryPrice = ( categoryId, count ) => {
		let counts              = paxCounts;
		let pricing             = allPricings[ selectedPricingId ];
		let isPricingGroupPrice = 'undefined' != typeof pricing.has_group_price && pricing.has_group_price && pricing.group_prices && pricing.group_prices.length > 0 ; 
		let category            = pricing.categories.find( c => c.id == categoryId );
		let price               = 0;

		if ( category || isPricingGroupPrice ) {

			if ( category && 'undefined' != typeof category.regular_price ) {
				price = category && category.is_sale ? category.sale_price : category.regular_price
			}

			if( category ){
				if(  'undefined' != typeof category.is_sale && category.is_sale == true && 'undefined' != typeof category.is_sale_percentage && category.is_sale_percentage == true ){
					price = category.sale_percentage_val/100 * category.regular_price;
				}
			}
			
			if ( isPricingGroupPrice ) {
				// need one additional loop here to get default total price.
				Object.entries( counts ).map( ( [i, count]) => {
					let category = pricing.categories.find(c => c.id == i)
					let regular_price = category && 'undefined' != typeof category.regular_price ? category.regular_price : 0;
					let p = category && category.is_sale ? category.sale_price : regular_price
					price += parseFloat(p) * count
				})
				
				let totalPax = objectSum( counts );
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

	const addToCart = () => {
		jQuery( '.booking-loader' ).css( 'display', 'block');
		let data = {
			trip_id: tripData.id,
			arrival_date: moment(selectedDate).format('YYYY-MM-DD'),
			pricing_id: selectedPricingId,
			date_id: selectedDateIds,
			pax: paxCounts,
			category_pax: paxCounts,
			trip_price: getCartTotal(), // just trip price without extras.
		}

		let paxVal =  Object.values( paxCounts ); // get all pax value form price category
		let size = 0;
		if ( paxVal.length > 0 ) {
			paxVal.map( ( paxSize, ind ) => {
				size = size + paxSize; // pax count for multiple traveler
			} )
		}
		if (selectedTime)
			data.trip_time = moment(selectedDate).format('HH:mm')

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
		
		jQuery( document.body ).trigger( 'wptravel_adding_to_cart', [ data ] );

		wpTravelTimeout(
			apiFetch({
				url: `${wp_travel.ajaxUrl}?action=wp_travel_add_to_cart&_nonce=${_wp_travel._nonce}`,
				method: 'POST',
				data
			}).then(res => {

				if ( applyFilters( 'wptravel_redirect_to_checkout', true ) && true === res.success && 'WP_TRAVEL_ADDED_TO_CART' === res.data.code) {
					apiFetch( {
						url: `${wp_travel.ajaxUrl}?action=wptravel_get_payment_field&_nonce=${_wp_travel._nonce}`,
						method: 'GET',
					}).then( settingData => {
						if( _wp_travel.add_to_cart_system == "1" ){
							location.reload();
						}else{
							if ( typeof settingData != 'undefined' && typeof settingData.success != 'undefined' && typeof settingData.data != 'undefined' ) {
			
								if ( settingData.success === true && settingData.data != '' ) {
	
									updateStore( {...bookingData, paxSize : size, payment_form : settingData.data.payment, form_key : settingData.data.form_key, price_list : settingData.data.price_list, cart_amount : settingData.data.cart_price , bookingTabEnable: false, travelerInfo : true } )
								}
				
							} else {
								console.log( 'setting not get!' )
							}
						}
						
					}).catch(error => {
						console.log( 'You can not use one page checkout because setting not loaded!' );
					})

				}

				jQuery( document.body ).trigger( 'wptravel_added_to_cart', [ data ] );

			}), 1000 ).catch(error => {
				alert( __i18n.set_time_out );
		})
	}
	let enable_time = '';
	_dates.map( res => {
		if( res.id == selectedDateIds[0] ) {
			enable_time =  res.enable_time;
		}
	});

	var btnClass = "wp-travel-book";
	if( parseFloat( getCartTotal(true) ) < parseFloat( minCheckoputPrice ) ){
		var btnClass = "wp-travel-book btndisable";
	}

    return <>
        <ErrorBoundary>
            <Suspense>
                
                { selectedPricingId &&
                    <div className='new-bottom-booking-container'>
						<div className="wp-travel-booking__panel-bottom-new wp-travel-booking__panel-bottom">
                        
                        <div className="left-info" >
                            {selectedPricingId && <p><strong>{__i18n.bookings.combined_pricing}</strong>: {allPricings[selectedPricingId].title}</p>}
                            {selectedDate && <p><strong>{__i18n.trip_date}</strong>: <span>
								{/* Date  */}
								{moment(selectedDate).format( _wp_travel.date_format_moment )} 
								{/* Time */}
								{ selectedTime && <span className="trip-time"> { moment(selectedDate).format( 'h:mm A' )}</span> }
								</span></p>}
                        </div>
                        
                        <div className="right-info" >
                            <p>{__i18n.bookings.booking_tab_cart_total}<strong dangerouslySetInnerHTML={{ __html: wpTravelFormat(getCartTotal(true)) }}></strong></p>
							{	
								tripData.enable_pax_all_pricing == "1" &&
								<button onClick={addToCart} className={btnClass}><span className='booking-loader'><Spinner /></span>{typeof _wp_travel.add_to_cart_system != 'undefined' && _wp_travel.add_to_cart_system == true ? __i18n.set_add_to_cart : __i18n.bookings.booking_tab_booking_btn_label}</button>
								||
								
								<button disabled={ totalPax < minPaxToBook || totalPax > maxPaxToBook || ( enable_time && nomineeTimes.length > 0 && ! selectedTime ) } onClick={addToCart} className={btnClass}><span className='booking-loader'><Spinner /></span>{typeof _wp_travel.add_to_cart_system != 'undefined' && _wp_travel.add_to_cart_system == true ? __i18n.set_add_to_cart : __i18n.bookings.booking_tab_booking_btn_label}</button>
							}
						</div>
                    </div>
					</div>
                }
            </Suspense>
                
        </ErrorBoundary>
    </>
}
export default WpTravelBookNow;
