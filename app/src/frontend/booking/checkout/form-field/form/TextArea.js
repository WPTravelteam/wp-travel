import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { TextareaControl, PanelBody, PanelRow, TextControl } from '@wordpress/components'

export default ( { travelerData, trvOne = 'travelerOne' } ) => {
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { label, type, name, id, wrapper_class } = travelerData
    const { checkoutDetails } = bookingData
    const travelerDataList = typeof checkoutDetails != 'undefined' && typeof checkoutDetails[trvOne] != 'undefined' && checkoutDetails[trvOne] || {};
    const travelerValue = typeof travelerDataList[name] != 'undefined' && travelerDataList[name] || '';
    return <PanelBody>
        <label >{typeof label != 'undefined' && label || '' }</label>
        <textarea 
            value={travelerValue }
            id={ typeof id != 'undefined' && id || ''}
            className={ typeof wrapper_class != 'undefined' && wrapper_class || ''}
            onChange={ ( e ) => {
                const value = e.target.value
                const newData = {...travelerDataList, [name] : value };
                const checkoutNewData = {...checkoutDetails, [trvOne] : newData }
                updateStore({...bookingData, checkoutDetails : checkoutNewData } )
            } }
            rows="4"
             cols="50"
        />
    </PanelBody>
}