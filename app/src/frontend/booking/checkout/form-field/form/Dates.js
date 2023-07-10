import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { Button, Modal, PanelBody, PanelRow, TextControl } from '@wordpress/components'
import { useState } from '@wordpress/element'
import DatePicker from "react-datepicker";

export default ( { travelerData, trvOne = 'travelerOne' } ) => {
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { label, type, name, id, attributes } = travelerData
    const backed = typeof attributes != 'undefined' && attributes['data-max-today'] || '';
    const keyDate = typeof attributes != 'undefined' && Object.keys( attributes ) || [];
    const { checkoutDetails } = bookingData
    const travelerDataList = typeof checkoutDetails != 'undefined' && typeof checkoutDetails[trvOne] != 'undefined' && checkoutDetails[trvOne] || {};
    const travelerValue = typeof travelerDataList[name] != 'undefined' && travelerDataList[name] || '';
    const selectedDate =  typeof travelerValue != 'undefined' && travelerValue != '' ? new Date( travelerValue ) : new Date();
    // console.log( 'dhfjdf oldd', new Date( travelerValue ) )
    const datePickerParams =  {
        showMonthDropdown: true,
        showYearDropdown: "select",
        // seleted : new Date( travelerValue ),
        dropdownMode: "select",
        minDate: backed != 1 && keyDate.length > 0 && keyDate.includes( 'data-max-today' ) ? new Date() : null,
        maxDate: backed == 1 ?  new Date() : null,
    }
    return <PanelBody>
        <label >{typeof label != 'undefined' && label || '' }</label>
        <DatePicker
        className= "wptravel-booking-datepicker"
            // dateFormat="yyyy-MM-dd"
            selected={selectedDate }
            { ...datePickerParams }
            value={ travelerValue }
            onChange={ ( value ) => {
                const createNewDate =  value;
                const month = createNewDate.getMonth() + 1 
                const years = createNewDate.getFullYear();
                const days = createNewDate.getDate() ;
                const finaldate = years + '-' + month + '-' + days;
                // console.log( 'dhfjdf', value)
                const newData = {...travelerDataList, [name] : finaldate };
                const checkoutNewData = {...checkoutDetails, [trvOne] : newData }
                // console.log( 'ths date', checkoutNewData )
                updateStore({...bookingData, checkoutDetails : checkoutNewData } )
            }}
        />
        <input type='hidden' value={ travelerValue } id={id} name={name} />
    </PanelBody>
}