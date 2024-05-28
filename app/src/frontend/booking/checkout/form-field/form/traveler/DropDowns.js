import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import {  PanelBody } from '@wordpress/components'

export default ( { travelerData, trvOne = 'travelerOne', pxKey = 1 } ) => {
    const countries = typeof _wp_travel != 'undefined' && typeof _wp_travel.checkout_field != 'undefined' && typeof _wp_travel.checkout_field.country != 'undefined' &&  _wp_travel.checkout_field.country || undefined;
    const countryKey = typeof countries != undefined && Object.keys( countries ) || []
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { label, type, name, id, wrapper_class } = travelerData
    const { checkoutDetails, error_list, requiredField } = bookingData
    const thisRequired = typeof requiredField[name] != 'undefined' && typeof requiredField[name][pxKey] != 'undefined' && requiredField[name][pxKey] || false;
    const errorData = typeof error_list[name] != 'undefined' && typeof error_list[name][pxKey] != 'undefined' &&  error_list[name][pxKey] || '';
    const travelerDataList = typeof checkoutDetails != 'undefined' && typeof checkoutDetails[trvOne] != 'undefined' && checkoutDetails[trvOne] || {};
    const travelerValue = typeof travelerDataList[name] != 'undefined' && travelerDataList[name] || '';
    
    return <><PanelBody>
        <label >{typeof label != 'undefined' && label || '' }{ thisRequired == true && <span className='wp-travel-in-page-required-field'>*</span> }</label>
        <select id={id} name={name} onClick={ (val ) => {
            const selectedValue = val.target.value;
            const newTraveler = {...travelerValue, [pxKey] : selectedValue}
            const newData = {...travelerDataList, [name] : newTraveler };
            const checkoutNewData = {...checkoutDetails, [trvOne] : newData }
            updateStore({...bookingData, checkoutDetails : checkoutNewData } )
        }}> 
            {
                countryKey.length > 0 && countryKey.map( ( list, index ) => {
                    return <option value={list}  key={index} >{countries[list]}</option>
                })
            }
        </select>
    </PanelBody><p className='wp-travel-in-page-error'>{errorData}</p></>
}