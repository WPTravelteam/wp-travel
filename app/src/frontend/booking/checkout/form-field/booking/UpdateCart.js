import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { useState, useEffect } from '@wordpress/element'
import apiFetch from '@wordpress/api-fetch';

export default () => {
    const [ cartOpen, setCartOpen ]     = useState( false );
    const [ cartError, setCartError ]  = useState('')
    const [ cartDetail, setCartDetail ]    = useState( {} )
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { nomineePricingIds, priceCart, paxCounts, form_key } = bookingData;
    const priceCategoryList = typeof priceCart != 'undefined' && typeof priceCart.priceCategoryList != 'undefined' && priceCart.priceCategoryList || [];
    const prcMax = typeof priceCart != 'undefined' && typeof priceCart.max_pax != 'undefined' && priceCart.max_pax || 0;
    const prcMin = typeof priceCart != 'undefined' && typeof priceCart.min_pax != 'undefined' && priceCart.min_pax || 0;
    let tripData = 'undefined' !== typeof _wp_travel.trip_data ? _wp_travel.trip_data : {};
    const { pricings }  = tripData;
    console.log( 'nomineePricingIds', nomineePricingIds)
    // Open update cart field which is use to update cart pax 
    const cartUpdateOpen = () => {
        typeof priceCart == 'undefined' && typeof pricings != 'undefined' && pricings.length > 0 && pricings.forEach( ( priceList, index ) => {
            const { id, categories, max_pax, min_pax }    = priceList;
            console.log( 'bestign', nomineePricingIds.includes( id.toString() ), id, categories )
            if ( nomineePricingIds[0] == id ) {
                var prcCategory = {}
                if ( typeof categories != 'undefined' && categories.length > 0 ) {
                    console.log( 'in the file ' )
                    categories.forEach( ( priceCatList, ind ) => {
                        const { term_info } = priceCatList;
                        const catName = typeof term_info != 'undefined' && typeof term_info.title != 'undefined' && term_info.title || '';
                        const catId = typeof priceCatList.id != 'undefined' && priceCatList.id || 0;
                        const optionCat = { title : catName, catId : catId }
                        console.log( 'catde', optionCat );
                        prcCategory[catId]  = optionCat;
                        // setCartDetail( {...cartDetail, priceCategory : optionCat } );
                    })
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
    // Increament pax throught + icon
    const paxIncreament = ( categoryId ) => {
        const cat  = typeof paxCounts[categoryId] != 'undefined' && paxCounts[categoryId] || 0;
        const newPax = {...paxCounts, [categoryId] : cat >= prcMax ? prcMax : cat + 1 }
        updateStore( {...bookingData, paxCounts : newPax } );
    }
    // Decreament pax throught - icon
    const paxDecreament = ( categoryId ) => {
        const cat  = typeof paxCounts[categoryId] != 'undefined' && paxCounts[categoryId] || 0;
        const newPax = {...paxCounts, [categoryId] : cat > prcMin ? cat - 1 : prcMin }
        updateStore( {...bookingData, paxCounts : newPax } );
    }

    const updateYouCart = () => {
        const _nonce = typeof _wp_travel._nonce != 'undefined' && _wp_travel._nonce || '';
        const cartDatas = {
            pax : paxCounts,
            wp_travel_trip_extras : {
                id : [],
                qty : []
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
    // console.log( 'pricecate', cartDetail );
    return <>
            <div className='wptravel-udate-cart-wrapper'>
            <button className='components-button' onClick={cartUpdateOpen} >Edit Cart</button>
        { cartOpen == true && <> 
            <div className="wptravel-on-page-booking-update-cart-section">
                { typeof priceCategoryList != 'undefined' && priceCategoryList.length > 0  && priceCategoryList.map( ( listed, index ) => {
                    const { title, catId }  = listed;

                    return <>
                        <div className="wptrave-on-page-booking-cart-update-field">
                            <label>{title}</label>
                            <div className="wp-travel-on-page-cart-update-button">
                                
                                <button className='wptravel-page-cart-update-btn-increase' onClick={ () => paxDecreament( catId )}>-</button>
                                <p>{paxCounts[catId]}</p>
                                <button className='wptravel-page-cart-update-btn-increase' onClick={ () => paxIncreament( catId )}>+</button>
                                
                            </div>
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