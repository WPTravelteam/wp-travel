import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { Button, Modal, PanelBody, PanelRow, TextControl } from '@wordpress/components'

export default ( { travelerData, trvOne = 'travelerOne' } ) => {
    const countries = typeof _wp_travel != 'undefined' && typeof _wp_travel.checkout_field != 'undefined' && typeof _wp_travel.checkout_field.country != 'undefined' &&  _wp_travel.checkout_field.country || undefined;
    const countryKey = typeof countries != undefined && Object.keys( countries ) || []
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { label, type, name, id, wrapper_class } = travelerData
    const { checkoutDetails } = bookingData
    const travelerDataList = typeof checkoutDetails != 'undefined' && typeof checkoutDetails[trvOne] != 'undefined' && checkoutDetails[trvOne] || {};
    const travelerValue = typeof travelerDataList[name] != 'undefined' && travelerDataList[name] || '';
    return <PanelBody>
        <label >{typeof label != 'undefined' && label || '' }</label>
        <select id={id} name={name} onClick={ (val ) => {
            const selectedValue = val.target.value;
            const newData = {...travelerDataList, [name] : selectedValue };
            const checkoutNewData = {...checkoutDetails, [trvOne] : newData }
            updateStore({...bookingData, checkoutDetails : checkoutNewData } )
        }}> 
            {
                countryKey.length > 0 && countryKey.map( ( list, index ) => {
                    return <option value={list}  key={index} >{countries[list]}</option>
                })
            }
        </select>
    </PanelBody>
}