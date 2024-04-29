import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { PanelBody } from '@wordpress/components'

export default ( { travelerData, trvOne = 'travelerOne' } ) => {
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    var { label, type, name, id, wrapper_class } = travelerData
    const { checkoutDetails, error_list, requiredField } = bookingData
    const thisRequired = typeof requiredField[name] != 'undefined' && requiredField[name] || false;
    const travelerDataList = typeof checkoutDetails != 'undefined' && typeof checkoutDetails[trvOne] != 'undefined' && checkoutDetails[trvOne] || {};
    var travelerValue = typeof travelerDataList[name] != 'undefined' && travelerDataList[name] || '';
    const errorData = typeof error_list[name] != 'undefined' && error_list[name]  || '';
    var readOnly = '';

    // if( id == 'billing_useremail' ){
    //     travelerValue = _wp_travel.checkout_field.checkout_user_email
    //     readOnly = 'readonly'
    // }

    // if( id == 'billing_userid' ){
    //     travelerValue = _wp_travel.checkout_field.checkout_user_id
    //     readOnly = 'readonly'
    // }

    if( id == 'billing_username' ){
        readOnly = 'readonly'
    }


    return <><PanelBody>
        {
            readOnly == 'readonly' &&
            <>
                <label >{typeof label != 'undefined' && label || '' }{ thisRequired == true && <span className='wp-travel-in-page-required-field'>*</span> }</label>
                <input 
                    value={ travelerValue }
                    id={ typeof id != 'undefined' && id || ''}
                    className={ typeof wrapper_class != 'undefined' && wrapper_class || ''}
                    type={ typeof type != 'undefined' && type || 'text' }  
                    // readonly= {readOnly}
                    onChange={ (e => {
                        const value = _wp_travel.checkout_field.checkout_user_name
                        const newData = {...travelerDataList, [name] : value };
                        const checkoutNewData = {...checkoutDetails, [trvOne] : newData }
                        updateStore({...bookingData, checkoutDetails : checkoutNewData } )
                    } ) }
                />
            </>
            ||
            <>
                <label >{typeof label != 'undefined' && label || '' }{ thisRequired == true && <span className='wp-travel-in-page-required-field'>*</span> }</label>
                <input 
                    value={ travelerValue }
                    id={ typeof id != 'undefined' && id || ''}
                    className={ typeof wrapper_class != 'undefined' && wrapper_class || ''}
                    type={ typeof type != 'undefined' && type || 'text' }  
                    onChange={ (e => {
                        const value = e.target.value
                        const newData = {...travelerDataList, [name] : value };
                        const checkoutNewData = {...checkoutDetails, [trvOne] : newData }
                        updateStore({...bookingData, checkoutDetails : checkoutNewData } )
                    } ) }
                />
            </>
            
        }
  
    </PanelBody><p className='wp-travel-in-page-error'>{errorData}</p></>
}