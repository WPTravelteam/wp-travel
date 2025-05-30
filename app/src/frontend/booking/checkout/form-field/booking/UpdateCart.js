import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { useState, useEffect } from '@wordpress/element'
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';
import _ from 'lodash';
const i18n = _wp_travel.strings;

import { wpTravelFormat, objectSum, GetConvertedPrice } from '../../../_wptravelFunctions';
export default () => {
    const [ cartOpen, setCartOpen ]     = useState( false );
    const [ cartUpdateMessage, setUpdateMessage ] = useState('')
    const [loaders, setLoaders ]  = useState( false )
    const [ cartError, setCartError ]  = useState('')
    const [ updatePriceData, setUpdatePriceData ]  = useState({})
    const [ updateExtraPrice, setUpdateExtraPrice ]  = useState({})
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { nomineePricingIds, priceCart, paxCounts, form_key, currency_symbol, tripExtras, nomineeTripExtras, inventory, selectedDate, selectedPricingId } = bookingData;
    const priceCategoryList = typeof priceCart != 'undefined' && typeof priceCart.priceCategoryList != 'undefined' && priceCart.priceCategoryList || [];
    var prcMax = typeof priceCart != 'undefined' && typeof priceCart.max_pax != 'undefined' && priceCart.max_pax || 0;
    const prcMin = typeof priceCart != 'undefined' && typeof priceCart.min_pax != 'undefined' && priceCart.min_pax || 0;
    const trpsExtras = typeof priceCart != 'undefined' && typeof priceCart.extras != 'undefined' && priceCart.extras || [];
    let tripData = 'undefined' !== typeof _wp_travel.trip_data ? _wp_travel.trip_data : {};
    const { pricings }  = tripData;

    const isInventoryEnabled = tripData.inventory && tripData.inventory.enable_trip_inventory === 'yes';

    if( isInventoryEnabled ){
        let _inventory = inventory.find(i => i.date === moment(selectedDate).format('YYYY-MM-DD[T]HH:mm'));
        prcMax = _inventory && _inventory.pax_available;
    }
    
    
    // Open update cart field which is use to update cart pax
    const cartUpdateOpen = () => {
        typeof priceCart == 'undefined' && typeof pricings != 'undefined' && pricings.length > 0 && pricings.forEach( ( priceList, index ) => {
            const { id, categories, max_pax, min_pax, trip_extras }    = priceList;
            if ( typeof selectedPricingId != 'undefined' && selectedPricingId == id ) {
                var prcCategory = {}
                var priceFirst = {};
                if ( typeof categories != 'undefined' && categories.length > 0 ) {
                    categories.forEach( ( priceCatList, ind ) => {
                        const { term_info, regular_price, is_sale, sale_price, is_sale_percentage, sale_percentage_val } = priceCatList;
                        const catName = typeof term_info != 'undefined' && typeof term_info.title != 'undefined' && term_info.title || '';
                        const catId = typeof priceCatList.id != 'undefined' && priceCatList.id || 0;
                        const optionCat = { title : catName, catId : catId, is_sale : is_sale, regular_price : regular_price, sale_price : sale_price }
                        prcCategory[catId]  = optionCat;
                        if ( is_sale == true ) {
                            var firstPrice = paxCounts[catId] * sale_price;
                            if(  is_sale_percentage == true ){
                                firstPrice = paxCounts[catId] * ( sale_percentage_val/100 * regular_price );
                            }
                        } else {
                            var firstPrice = paxCounts[catId] * regular_price;
                        }
                        priceFirst[catId] = firstPrice;
                    })
                    setUpdatePriceData( priceFirst )
                    if ( Object.values( prcCategory ).length > 0 ) {
                        var extPrinces = {}
                        if( nomineeTripExtras.length > 0 ) {
                            nomineeTripExtras.map( ( ext, ind) => {
                                const { id, is_sale, sale_price, tour_extras_metas } = ext;
                                const { extras_item_price } = typeof tour_extras_metas != 'undefined' && tour_extras_metas || 0;
                                if ( is_sale ) {
                                    extPrinces[id] = typeof tripExtras[id] != 'undefined' && typeof sale_price != 'undefined' && tripExtras[id] * sale_price || 0;
                                } else {
                                    extPrinces[id] = typeof tripExtras[id] != 'undefined' && typeof extras_item_price != 'undefined' && tripExtras[id] * extras_item_price || 0;
                                }
                            })
                        }
                        setUpdateExtraPrice(extPrinces)
                        const finalPrice = { max_pax : max_pax, min_pax : min_pax, priceCategoryList : Object.values( prcCategory ), extras : trip_extras }
                        updateStore( {...bookingData, priceCart :  finalPrice } )
                    }
                }
            }
        } )
        setCartOpen( true )

    }
    const cartUpdateClose = () => {

        setCartOpen(false)
    }


    const[ totalPax, setTotalPax ] = useState( 0 );
    let _totalPax = _.size(paxCounts) > 0 && Object.values(paxCounts).reduce((acc, curr) => acc + curr) || 0;
    // Increament pax throught + icon
    const paxIncreament = ( categoryId, sale_enable, price_regular, price_sale ) => {

        const cat  = typeof paxCounts[categoryId] != 'undefined' && paxCounts[categoryId] || 0;
        const paxCalculate = cat >= prcMax ? prcMax : cat + 1;
        const priceCalulate = sale_enable == true ? price_sale * paxCalculate : price_regular * paxCalculate;
        const newPax = {...paxCounts, [categoryId] : cat >= prcMax ? prcMax : cat + 1 }

        if( tripData.enable_pax_all_pricing == "1" ){ 
            const newPriceList = { ...updatePriceData, [categoryId] : priceCalulate }
            setUpdatePriceData( newPriceList );
            updateStore( {...bookingData, paxCounts : newPax } );
        }else{
            if( _totalPax < prcMax ){
                const newPriceList = { ...updatePriceData, [categoryId] : priceCalulate }
                setUpdatePriceData( newPriceList );
                updateStore( {...bookingData, paxCounts : newPax } );
            }
        }

        let sum = 0;
        for (let key in paxCounts) {
            if( categoryId != key ){
                sum += paxCounts[key];
            }
            
        }

        sum += paxCalculate
       
        if( sum >= prcMin ){
            document.getElementById("wptravel-update-onpage-cart").disabled = false;
        }
        
    }
    // Decreament pax throught - icon
    const paxDecreament = ( categoryId, sale_enable, price_regular, price_sale ) => {
        const cat  = typeof paxCounts[categoryId] != 'undefined' && paxCounts[categoryId] || 0;
        const paxCalculate = cat > 0 ? cat - 1 : 0;
        const priceCalulate = sale_enable == true ? price_sale * paxCalculate : price_regular * paxCalculate;
        const newPax = {...paxCounts, [categoryId] : cat > 0 ? cat - 1 : 0 }
        const newPriceList = { ...updatePriceData, [categoryId] : priceCalulate }
        
        let sum = 0;
        for (let key in paxCounts) {
            if( categoryId != key ){
                sum += paxCounts[key];
            }
            
        }

        sum += paxCalculate
       
        if( sum < prcMin ){
            document.getElementById("wptravel-update-onpage-cart").disabled = true;
        }

        setUpdatePriceData( newPriceList );
        updateStore( {...bookingData, paxCounts : newPax } );
    }
    const extraDecreament = ( extId, extPrince, extSale, extSalePrince ) => {
        const cat  = typeof tripExtras[extId] != 'undefined' && tripExtras[extId] || 0;
        const extraCalculate = cat - 1;
        const priceCalulate = extSale == true ? extSalePrince * extraCalculate : extPrince * extraCalculate;
        const newExtra = {...tripExtras, [extId] : extraCalculate < 0 ? 0 : extraCalculate }
        const newPriceList = { ...updateExtraPrice, [extId] : priceCalulate }
        setUpdateExtraPrice( newPriceList );
        updateStore( {...bookingData, tripExtras : newExtra } );
    }
    const extraIncreament = ( extId, extPrince, extSale, extSalePrince, quantity ) => {
        const cat  = typeof tripExtras[extId] != 'undefined' && tripExtras[extId] || 0;
        const extraCalculate = cat + 1;

        if( quantity > 0 ){
            if( extraCalculate <= quantity ){
                const priceCalulate = extSale == true ? extSalePrince * extraCalculate : extPrince * extraCalculate;
                const newExtra = {...tripExtras, [extId] : extraCalculate }
                const newPriceList = { ...updateExtraPrice, [extId] : priceCalulate }
                setUpdateExtraPrice( newPriceList );
                updateStore( {...bookingData, tripExtras : newExtra } );
            }
        }else{
            const priceCalulate = extSale == true ? extSalePrince * extraCalculate : extPrince * extraCalculate;
            const newExtra = {...tripExtras, [extId] : extraCalculate }
            const newPriceList = { ...updateExtraPrice, [extId] : priceCalulate }
            setUpdateExtraPrice( newPriceList );
            updateStore( {...bookingData, tripExtras : newExtra } );  
        }
    }
    const disableCart = () => { 
        setUpdateMessage('')
        setCartOpen( false )
    }

    const updateYouCart = () => {
        setLoaders( true );
        const _nonce = typeof _wp_travel._nonce != 'undefined' && _wp_travel._nonce || '';
        const extraDatas  = Object.keys( tripExtras );
		var newExtreaKey = [];
		var newExtraVal = [];
		extraDatas.map( (val, indexs) => {
			if ( typeof tripExtras[val] != 'undefined' && tripExtras[val] > 0 ) {
				newExtreaKey = [...newExtreaKey, val ]
				newExtraVal = [...newExtraVal, tripExtras[val] ];
			}
		})
        const cartDatas = {
            pax : paxCounts,
            wp_travel_trip_extras : {
                id : newExtreaKey,
                qty : newExtraVal
            }
        };
        apiFetch({
            url: `${wp_travel.ajaxUrl}?action=wp_travel_update_cart_item&_nonce=${_nonce}&cart_id=${form_key}`,
            method: 'POST',
            data : cartDatas
        }).then(res => {
            if ( typeof res.success != 'undefined' && res.success == true && typeof res.data != 'undefined' ) {
                const responceData = res.data;
                if ( typeof responceData.code != 'undefined' && responceData.code == 'WP_TRAVEL_CART_ITEM_UPDATED' && typeof responceData.cart != 'undefined' ) {
                    const responceCart = responceData.cart;
                    const { total } = responceCart;
                    const totalTrpPrice = typeof total != 'undefined' && typeof total.total != 'undefined' && total.total || 0;
                    const partalTrpPrice = typeof total != 'undefined' && typeof total.total_partial != 'undefined' && total.total_partial || 0;
                    const priceList = {
                        partial_amount : partalTrpPrice,
                        trip_price : totalTrpPrice
                    }
                    let paxVal =  Object.values( paxCounts ); // get all pax value form price category
                    let size = 0;
                    if ( paxVal.length > 0 ) {
                        paxVal.map( ( paxSize, ind ) => {
                            size = size + paxSize; // pax count for multiple traveler
                        } )
                    }
                    updateStore( {...bookingData, price_list : priceList, paxSize : size, cart_amount : total })
                    setUpdateMessage( i18n.set_cart_updated )
                    setLoaders( false )
                   setTimeout( disableCart, 3000 )
                } else {
                    setCartError( i18n.set_cart_updated_error )
                    setLoaders( false )
                }
            } else {
                setCartError( i18n.set_cart_updated_server_responce )
                setLoaders( false )
            }
         }).catch( err => {alert( i18n.set_cart_server_error )

            setLoaders( false )
        } )

    }

    const allPricings        = pricings && _.keyBy( pricings, p => p.id ) 
    const pricing = allPricings[selectedPricingId];


    const getCategoryPrice = (categoryId, single) => { // This function handles group discounts as well
		let category = pricing.categories.find(c => c.id == categoryId)
		if (!category) {
			return
		}
		let count = paxCounts[categoryId] || 0
		let price = category && category.is_sale ? category.sale_price : category.regular_price

        if(category.sale_price && category.is_sale_percentage){
            price = category.sale_percentage_val/100 * category.regular_price;
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
    

    return <>
            <div className='wptravel-udate-cart-wrapper'>
            <button className='components-button' onClick={cartOpen == true ? cartUpdateClose : cartUpdateOpen} >{ cartOpen == true ? i18n.set_close_cart : i18n.set_view_cart }</button>
        { cartOpen == true && <>
            <div className="wptravel-on-page-booking-update-cart-section animated-wp-travel fadeIn-wp-travel">
                <div className="wptravel-on-page-booking-update-cart-section-wrapper">
                <span className='pax-selector-label'> {i18n.bookings.booking_tab_pax_selector} </span>
                { typeof priceCategoryList != 'undefined' && priceCategoryList.length > 0  && priceCategoryList.map( ( listed, index ) => {
                    var { title, catId, is_sale, regular_price  }  = listed;

                    regular_price = getCategoryPrice(  catId, true )
                    const sale_price = getCategoryPrice(  catId, true )
                    return <>
                        <div className="wptrave-on-page-booking-cart-update-field">
                        <label>{title} ( {paxCounts[catId]} / {prcMax} )</label>
                            <span className="item-price">
                                { is_sale && 
                                    <del dangerouslySetInnerHTML={{ __html: wpTravelFormat( GetConvertedPrice( regular_price ) ) } }></del>
                                } 
                                <span dangerouslySetInnerHTML={{ __html: wpTravelFormat( getCategoryPrice(  catId, true ) ) }}>
                                </span>/{pricings.find(item => item.id == selectedPricingId ).categories.find(item => item.id === catId ).price_per}
                            </span>
                            
                            <div className="wp-travel-on-page-cart-update-button">
                                <button className='wptravel-page-cart-update-btn-increase' onClick={ () => paxDecreament( catId, is_sale, regular_price, sale_price )}>-</button>
                                <p>{paxCounts[catId]}</p>
                                <button className='wptravel-page-cart-update-btn-increase' onClick={ () => paxIncreament( catId, is_sale, regular_price, sale_price )}>+</button>
                            </div>
    
                        </div>
                    </>

                } )}
                </div>
                
                { typeof nomineeTripExtras != 'undefined' && nomineeTripExtras.length > 0 && <> 
                <div className="wptravel-on-page-booking-update-trip-extras-wrapper">
                <span className='trip-extra-label'> {i18n.bookings.trip_extras_list_label}</span>
                { typeof nomineeTripExtras != 'undefined' && nomineeTripExtras.length > 0 && nomineeTripExtras.map( ( trpExtra, extraIndex ) => {
                            let extraIds = typeof trpExtra.id != 'undefined' &&  trpExtra.id || 0;
                            let extraTitles = typeof trpExtra.title != 'undefined' &&  trpExtra.title || 0;
                            const { is_sale, sale_price, unit, tour_extras_metas } = trpExtra;
                            const extras_item_price = typeof tour_extras_metas != 'undefined' && typeof tour_extras_metas.extras_item_price != 'undefined' && tour_extras_metas.extras_item_price || 0;
                        
                            return typeof trpsExtras != 'undefined' && trpsExtras.length > 0 && trpsExtras.includes( extraIds.toString() ) && <>
                             <div className="wptrave-on-page-booking-cart-update-field" key={extraIndex *20 }>
                             {	
                                
                                ( tour_extras_metas.extras_item_quantity != -1 ) &&
                                <>
                                    <label>{extraTitles} { tour_extras_metas.extras_item_quantity > 0 && <>( {tripExtras[extraIds]} / {tour_extras_metas.extras_item_quantity} )</>} </label>
                                </>
                                ||
                                <label>{extraTitles}</label>
                            }	
                            <span className="item-price">{ is_sale && <del dangerouslySetInnerHTML={{ __html: wpTravelFormat( extras_item_price ) } }></del>} { is_sale && <span dangerouslySetInnerHTML={{ __html: wpTravelFormat( sale_price ) }}></span> || <span dangerouslySetInnerHTML={{ __html: wpTravelFormat( extras_item_price ) }}></span> }/{unit}</span>
                            
                            <div className="wp-travel-on-page-cart-update-button">

                                <button className='wptravel-page-cart-update-btn-increase' onClick={ () => extraDecreament( extraIds, extras_item_price, is_sale, sale_price )}>-</button>
                                <p>{tripExtras[extraIds]}</p>
                                <button className='wptravel-page-cart-update-btn-increase' onClick={ () => extraIncreament( extraIds, extras_item_price, is_sale, sale_price, tour_extras_metas.extras_item_quantity )}>+</button>

                            </div>
     

                        </div> </>} )} </div>
                </> }
                
                
               

                <div className="wptravel-on-page-booking-cart-update-btn">
                    <button id="wptravel-update-onpage-cart" className='components-button' onClick={updateYouCart}>{i18n.set_updated_cart_btn}{loaders && <img className='wptravel-single-page-loader-btn' src={_wp_travel.loader_url } /> }</button>
                    { cartUpdateMessage !== '' && <span className="wptravel-onpage-cart-updated-message"><i class="fa fa-check-circle"></i>{ cartUpdateMessage }</span> }
                </div>
            </div>

        </>}
            </div>

    </>;
}