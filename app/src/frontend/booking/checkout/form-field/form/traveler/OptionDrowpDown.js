import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { PanelBody, PanelRow } from '@wordpress/components'

export default ( { travelerData, trvOne = 'travelerOne', pxKey = 1 } ) => {
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { label, type, name, id, options } = travelerData
    const defaults = typeof travelerData != 'undefined' && typeof travelerData.default != undefined && travelerData.default || '';
    const { checkoutDetails, error_list, requiredField } = bookingData
    const thisRequired = typeof requiredField[name] != 'undefined' && typeof requiredField[name][pxKey] != 'undefined' && requiredField[name][pxKey] || false;
    const errorData = typeof error_list[name] != 'undefined' && typeof error_list[name][pxKey] != 'undefined' &&  error_list[name][pxKey] || '';
    const travelerDataList = typeof checkoutDetails != 'undefined' && typeof checkoutDetails[trvOne] != 'undefined' && checkoutDetails[trvOne] || {};
    const travelerValue = typeof travelerDataList[name] != 'undefined' && travelerDataList[name] || {};
    const optionKey = typeof options != undefined && Object.keys( options ) || []
    

    return <><PanelBody>
        <PanelRow>
            <label >{typeof label != 'undefined' && label || '' }{ thisRequired == true && <span className='wp-travel-in-page-required-field'>*</span> }</label>
            <select id={id} name={name} defaultValue={ typeof travelerValue[pxKey] != 'undefined' && travelerValue[pxKey] || defaults } 
                onClick={ (val ) => { 
                    const selectedValue = val.target.value;
                    const newTraveler = {...travelerValue, [pxKey] : selectedValue }
                    const newData = {...travelerDataList, [name] : newTraveler };
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
    </PanelBody> <p className='wp-travel-in-page-error'>{errorData}</p></>
}