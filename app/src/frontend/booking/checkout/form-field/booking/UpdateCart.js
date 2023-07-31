import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { useState, useEffect } from '@wordpress/element'
import apiFetch from '@wordpress/api-fetch';

export default () => {
    const [ cartOpen, setCartOpen ]     = useState( false );
    const [ cartError, setCartError ]  = useState('')
    const [ updatePriceData, setUpdatePriceData ]  = useState({})
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { nomineePricingIds, priceCart, paxCounts, form_key, currency_symbol, tripExtras, nomineeTripExtras } = bookingData;
    const priceCategoryList = typeof priceCart != 'undefined' && typeof priceCart.priceCategoryList != 'undefined' && priceCart.priceCategoryList || [];
    const prcMax = typeof priceCart != 'undefined' && typeof priceCart.max_pax != 'undefined' && priceCart.max_pax || 0;
    const prcMin = typeof priceCart != 'undefined' && typeof priceCart.min_pax != 'undefined' && priceCart.min_pax || 0;
    let tripData = 'undefined' !== typeof _wp_travel.trip_data ? _wp_travel.trip_data : {};
    const { pricings }  = tripData;
    // Open update cart field which is use to update cart pax 
    const cartUpdateOpen = () => {
        typeof priceCart == 'undefined' && typeof pricings != 'undefined' && pricings.length > 0 && pricings.forEach( ( priceList, index ) => {
            const { id, categories, max_pax, min_pax }    = priceList;
            console.log( 'bestign', nomineePricingIds.includes( id.toString() ), id, categories )
            if ( nomineePricingIds[0] == id ) {
                var prcCategory = {}
                var priceFirst = {};
                if ( typeof categories != 'undefined' && categories.length > 0 ) {
                    console.log( 'in the file ' )
                    categories.forEach( ( priceCatList, ind ) => {
                        const { term_info, regular_price, is_sale, sale_price } = priceCatList;
                        const catName = typeof term_info != 'undefined' && typeof term_info.title != 'undefined' && term_info.title || '';
                        const catId = typeof priceCatList.id != 'undefined' && priceCatList.id || 0;
                        const optionCat = { title : catName, catId : catId, is_sale : is_sale, regular_price : regular_price, sale_price : sale_price }
                        console.log( 'catde', optionCat );
                        prcCategory[catId]  = optionCat;
                        if ( is_sale == true ) {
                            var firstPrice = paxCounts[catId] * sale_price;
                        } else {
                            var firstPrice = paxCounts[catId] * regular_price;
                        }
                        priceFirst[catId] = firstPrice;
                    })
                    setUpdatePriceData( priceFirst )
                    console.log( 'prcCategory', prcCategory );
                    if ( Object.values( prcCategory ).length > 0 ) {
                        const finalPrice = { max_pax : max_pax, min_pax : min_pax, priceCategoryList : Object.values( prcCategory ) }
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
    // Increament pax throught + icon
    const paxIncreament = ( categoryId, sale_enable, price_regular, price_sale ) => {
        const cat  = typeof paxCounts[categoryId] != 'undefined' && paxCounts[categoryId] || 0;
        const paxCalculate = cat >= prcMax ? prcMax : cat + 1;
        const priceCalulate = sale_enable == true ? price_sale * paxCalculate : price_regular * paxCalculate;
        const newPax = {...paxCounts, [categoryId] : cat >= prcMax ? prcMax : cat + 1 }
        const newPriceList = { ...updatePriceData, [categoryId] : priceCalulate }
        setUpdatePriceData( newPriceList );
        updateStore( {...bookingData, paxCounts : newPax } );
    }
    // Decreament pax throught - icon
    const paxDecreament = ( categoryId, sale_enable, price_regular, price_sale ) => {
        const cat  = typeof paxCounts[categoryId] != 'undefined' && paxCounts[categoryId] || 0;
        const paxCalculate = cat > prcMin ? cat - 1 : prcMin;
        const priceCalulate = sale_enable == true ? price_sale * paxCalculate : price_regular * paxCalculate;
        const newPax = {...paxCounts, [categoryId] : cat > prcMin ? cat - 1 : prcMin }
        const newPriceList = { ...updatePriceData, [categoryId] : priceCalulate }
        setUpdatePriceData( newPriceList );
        updateStore( {...bookingData, paxCounts : newPax } );
    }

    const updateYouCart = () => {
        const _nonce = typeof _wp_travel._nonce != 'undefined' && _wp_travel._nonce || '';
        const extraDatas  = Object.keys( tripExtras );
        const extraValues = Object.values( tripExtras );
        const cartDatas = {
            pax : paxCounts,
            wp_travel_trip_extras : {
                id : extraDatas,
                qty : extraValues
            }
        };
        apiFetch({
            url: `${wp_travel.ajaxUrl}?action=wp_travel_update_cart_item&_nonce=${_nonce}&cart_id=${form_key}`,
            method: 'POST',
            data : cartDatas
        }).then(res => { 
            console.log( 'respoces', res );
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
                } else {
                    setCartError( "Your cart isn't update due to server error." )
                }
            } else {
                setCartError( "Your cart isn't update due to server responce error." )
            }
         }).catch( err => alert( 'Your cart is not update due to some server error.' ) )
    }
    return <>
            <div className='wptravel-udate-cart-wrapper'>
            <button className='components-button' onClick={cartOpen == true ? cartUpdateClose : cartUpdateOpen} >{ cartOpen == true ? 'Close Cart' : 'View Cart' }</button>
        { cartOpen == true && <> 
            <div className="wptravel-on-page-booking-update-cart-section">
                { typeof priceCategoryList != 'undefined' && priceCategoryList.length > 0  && priceCategoryList.map( ( listed, index ) => {
                    const { title, catId, is_sale, regular_price, sale_price }  = listed;

                    return <>
                        <div className="wptrave-on-page-booking-cart-update-field">
                            <label>{title}</label>
                            <div className="wp-travel-on-page-cart-update-button">
                                
                                <button className='wptravel-page-cart-update-btn-increase' onClick={ () => paxDecreament( catId, is_sale, regular_price, sale_price )}>-</button>
                                <p>{paxCounts[catId]}</p>
                                <button className='wptravel-page-cart-update-btn-increase' onClick={ () => paxIncreament( catId, is_sale, regular_price, sale_price )}>+</button>
                                
                            </div>
                            <div className="wptravel-onpage-booking-cart-price">
                                <p>{currency_symbol}{updatePriceData[catId]}</p>
                            </div>
                            {/* <div className="wptravel-onpage-booking-cart-price">
                                <p>{currency_symbol}{updatePriceData[catId]}</p>
                            </div> */}
                        </div>
                    </>
                } )}
                <div className="wptravel-on-page-booking-cart-update-btn">
                    <button className='components-button' onClick={updateYouCart}>Update Cart</button>
                </div>
            </div>
        
        </>}
            </div>
        
    </>;
}