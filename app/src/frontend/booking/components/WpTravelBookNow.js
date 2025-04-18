import { Suspense } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { applyFilters } from '@wordpress/hooks';
import { DEFAULT_BOOKING_STATE } from '../store/_Store';

const __i18n = {
	..._wp_travel.strings
}

import _ from 'lodash';
import ErrorBoundary from '../../../ErrorBoundry/ErrorBoundry';

import { __ } from '@wordpress/i18n';

// WP Travel Functions.
import { objectSum, wpTravelFormat, wpTravelTimeout, GetConvertedPrice } from '../_wptravelFunctions';

const WpTravelBookNow = ( props ) => {
	const initialState = DEFAULT_BOOKING_STATE();
    // Component Props.
	const { tripData, bookingData, updateBookingData } = props;

    // Trip Data.
    const {
		title,
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
				if( category.is_sale && 'undefined' != typeof category.is_sale_percentage && category.is_sale_percentage ){
					price= (category.sale_percentage_val/100)*category.regular_price
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

	const addToCart = event => {
		/**
		 * Added button disable after clicking once on Book Now button
		 * 
		 * @since 7.6.0
		 */
		event.currentTarget.disabled = true;
		let data = {
			trip_id: tripData.id,
			arrival_date: moment(selectedDate).format('YYYY-MM-DD'),
			pricing_id: selectedPricingId,
			date_id: selectedDateIds,
			pax: paxCounts,
			category_pax: paxCounts,
			trip_price: getCartTotal(), // just trip price without extras.
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

					if ( _wp_travel.add_to_cart_system == false ) {
						location.href = typeof _wp_travel.add_to_cart_system != 'undefined' && _wp_travel.add_to_cart_system == true ? window.location.href :  wp_travel.checkoutUrl; // [only checkout page url]
					} else {
						var cartCount = Object.keys(res.data.cart.cart_items).length;
						/**
						 * Added toast to display successful booking 
						 * 
						 * @since 7.6.0
						 */
						jQuery( '#wp-travel__add-to-cart_notice' ).addClass( 'success' ).append( `
								<span><i class="fa fa-check-circle"></i><strong>` + title + `</strong> ` + __i18n.set_added_cart + `</span>
								<span id="toast-close"><i class="fa fa-times"></i></span>
							` );
						
						/**
						 * Added close [X] button on toast to remove it
						 * 
						 * @since 7.6.0
						 */
						jQuery( '#toast-close' ).on('click', function() {
							jQuery( '#wp-travel__add-to-cart_notice' ).removeClass( 'success' )
							/**
							 * Remove style [ display: none ], as the notice was just hidden in DOM and not removed
							 * Fixed: Remove toast from DOM after the toast duration expires [ 8sec ]
							 * 
							 * @since 7.6.0
							 */
							jQuery( '#wp-travel__add-to-cart_notice span' ).remove();
				
						});

						setTimeout( () => {
							jQuery( '#wp-travel__add-to-cart_notice' ).removeClass( 'success' )
							/**
							 * Remove style [ display: none ], as the notice was just hidden in DOM and not removed
							 * Fixed: Remove toast from DOM after the toast duration expires [ 8sec ]
							 * 
							 * @since 7.6.0
							 */
							jQuery( '#wp-travel__add-to-cart_notice span' ).remove();
							jQuery( '#wp-travel__notice_time-bar' ).remove();
						}, __i18n.add_to_cart_notice );
	
						updateBookingData( initialState );
						
						if( cartCount > 0 ) {
							jQuery( '.wp-travel-cart-items-number' ).css('display', 'inline-flex')
							jQuery( '.wp-travel-cart-items-number' ).html( cartCount );
						}
						window.scrollTo({
							top: 0,
							behavior: 'smooth',
						})
					}
				}

				jQuery( document.body ).trigger( 'wptravel_added_to_cart', [ data ] );

			}), 1000 ).catch(error => {
				console.log( error )
				alert( __( '[X] Request Timeout!', 'wp-travel' ) );
		})
	}
	let enable_time = '';
	_dates.map( res => {
		if( res.id == selectedDateIds[0] ) {
			enable_time =  res.enable_time;
		}
	});

    return <>
        <ErrorBoundary>
            <Suspense>
                
                { selectedPricingId &&
                    <div className="wp-travel-booking__panel-bottom">
                        
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
                            <p><strong dangerouslySetInnerHTML={{ __html: wpTravelFormat(getCartTotal(true)) }}></strong></p>

							<button disabled={ parseFloat(minCheckoputPrice ) > parseFloat(getCartTotal(true) ) || totalPax < minPaxToBook || ( enable_time && nomineeTimes.length > 0 && ! selectedTime ) } onClick={addToCart} className="wp-travel-book">{typeof _wp_travel.add_to_cart_system != 'undefined' && _wp_travel.add_to_cart_system == true ? __i18n.set_add_to_cart : __i18n.bookings.booking_tab_booking_btn_label}</button>
			
							
                        </div>
                    </div>
                }
            </Suspense>
                
        </ErrorBoundary>
    </>
}
export default WpTravelBookNow;
