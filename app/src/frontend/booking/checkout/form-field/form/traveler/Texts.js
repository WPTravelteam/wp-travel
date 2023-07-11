import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { Button, Modal, PanelBody, PanelRow, TextControl } from '@wordpress/components'

export default ( { travelerData, trvOne = 'travelerOne', pxKey = 1 } ) => {
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { label, type, name, id, wrapper_class } = travelerData
    const { checkoutDetails, error_list, requiredField } = bookingData
    const thisRequired = typeof requiredField[name] != 'undefined' && typeof requiredField[name][pxKey] != 'undefined' && requiredField[name][pxKey] || false;
    const errorData = typeof error_list[name] != 'undefined' && typeof error_list[name][pxKey] != 'undefined' &&  error_list[name][pxKey] || '';
    const travelerDataList = typeof checkoutDetails != 'undefined' && typeof checkoutDetails[trvOne] != 'undefined' && checkoutDetails[trvOne] || {};
    const travelerValue = typeof travelerDataList[name] != 'undefined' && travelerDataList[name] || {};
    return <><PanelBody>
        <label >{typeof label != 'undefined' && label || '' }{ thisRequired == true && <span className='wp-travel-in-page-required-field'>*</span> }</label>
        <input 
            value={ typeof travelerValue[pxKey] != 'undefined' && travelerValue[pxKey] || '' }
            id={ typeof id != 'undefined' && id || ''}
            className={ typeof wrapper_class != 'undefined' && wrapper_class || ''}
            type={ typeof type != 'undefined' && type || 'text' }
            onChange={ (e => {
                const value = e.target.value
                const newTraveler = {...travelerValue, [pxKey] : value}
                const newData = {...travelerDataList, [name] : newTraveler };
                const checkoutNewData = {...checkoutDetails, [trvOne] : newData }
                updateStore({...bookingData, checkoutDetails : checkoutNewData } )
            } ) }
        />
    </PanelBody> <p className='wp-travel-in-page-error'>{errorData}</p></>
}