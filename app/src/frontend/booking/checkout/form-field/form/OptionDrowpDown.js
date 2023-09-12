import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { PanelBody, PanelRow } from '@wordpress/components'

export default ( { travelerData, trvOne = 'travelerOne', partials = 'no' } ) => {
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { label, type, name, id, options } = travelerData
    const defaults = typeof travelerData != 'undefined' && typeof travelerData.default != undefined && travelerData.default || '';
    const { checkoutDetails, error_list, requiredField } = bookingData
    const thisRequired = typeof requiredField[name] != 'undefined' && requiredField[name] || false;
    const travelerDataList = typeof checkoutDetails != 'undefined' && typeof checkoutDetails[trvOne] != 'undefined' && checkoutDetails[trvOne] || {};
    const travelerValue = typeof travelerDataList[name] != 'undefined' && travelerDataList[name] || '';
    const optionKey = typeof options != undefined && Object.keys( options ) || []

    if ( typeof travelerDataList[name] == 'undefined' ) {
        const newData = {...travelerDataList, [name] : defaults };
        const checkoutNewData = {...checkoutDetails, [trvOne] : newData }
        updateStore({...bookingData, checkoutDetails : checkoutNewData } )  
    }
    const errorData = typeof error_list[name] != 'undefined' && error_list[name]  || '';
    return <><PanelBody >
        <PanelRow className='wptravel-singlepage-booking-options'>
            <label >{typeof label != 'undefined' && label || '' }{ thisRequired == true && <span className='wp-travel-in-page-required-field'>*</span> }</label>
            <select id={id} name={name} defaultValue={ typeof travelerValue != 'undefined' && travelerValue != '' && travelerValue || defaults } 
                onClick={ (val ) => { 
                    const selectedValue = val.target.value;
                    const newData = {...travelerDataList, [name] : selectedValue };
                    const checkoutNewData = {...checkoutDetails, [trvOne] : newData }
                    updateStore({...bookingData, checkoutDetails : checkoutNewData } )
            }}> 
                {
                    optionKey.length > 0 && optionKey.map( ( list, index ) => {
                        return <option value={list} key={index} defaultChecked={ travelerValue == list ? true : false} >{options[list]}</option>
                    })
                }
            </select>
        </PanelRow>
    </PanelBody><p className='wp-travel-in-page-error'>{errorData}</p></>
}