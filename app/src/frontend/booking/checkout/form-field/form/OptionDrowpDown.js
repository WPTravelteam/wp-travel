import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { Button, Modal, PanelBody, PanelRow, TextControl } from '@wordpress/components'

export default ( { travelerData, trvOne = 'travelerOne' } ) => {
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { label, type, name, id, options } = travelerData
    const defaults = typeof travelerData != 'undefined' && typeof travelerData.default != undefined && travelerData.default || '';
    const { checkoutDetails } = bookingData
    const travelerDataList = typeof checkoutDetails != 'undefined' && typeof checkoutDetails[trvOne] != 'undefined' && checkoutDetails[trvOne] || {};
    const travelerValue = typeof travelerDataList[name] != 'undefined' && travelerDataList[name] || "booking_only";
    const optionKey = typeof options != undefined && Object.keys( options ) || []
    if ( typeof travelerDataList[name] == undefined ) {
        const newData = {...travelerDataList, [name] : "booking_only" };
        const checkoutNewData = {...checkoutDetails, [trvOne] : newData }
        updateStore({...bookingData, checkoutDetails : checkoutNewData } )  
    }
    return <PanelBody>
        <PanelRow>
            <label >{typeof label != 'undefined' && label || '' }</label>
            <select id={id} name={name} defaultValue={ typeof travelerValue != 'undefined' && travelerValue != '' && travelerValue || defaults } 
                onClick={ (val ) => { 
                    const selectedValue = val.target.value;
                    const newData = {...travelerDataList, [name] : selectedValue };
                    const checkoutNewData = {...checkoutDetails, [trvOne] : newData }
                    updateStore({...bookingData, checkoutDetails : checkoutNewData } )
            }}> 
                {
                    optionKey.length > 0 && optionKey.map( ( list, index ) => {
                        return <option value={list} key={index} >{options[list]}</option>
                    })
                }
            </select>
        </PanelRow>
    </PanelBody>
}