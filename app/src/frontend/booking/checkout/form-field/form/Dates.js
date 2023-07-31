import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { PanelBody } from '@wordpress/components'
// import { useState } from '@wordpress/element'
import DatePicker from "react-datepicker";

export default ( { travelerData, trvOne = 'travelerOne' } ) => {
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { label, type, name, id, attributes } = travelerData
    const backed = typeof attributes != 'undefined' && attributes['data-max-today'] || '';
    const keyDate = typeof attributes != 'undefined' && Object.keys( attributes ) || [];
    const { checkoutDetails, error_list, requiredField } = bookingData
    const thisRequired = typeof requiredField[name] != 'undefined' && requiredField[name] || false;
    const travelerDataList = typeof checkoutDetails != 'undefined' && typeof checkoutDetails[trvOne] != 'undefined' && checkoutDetails[trvOne] || {};
    const travelerValue = typeof travelerDataList[name] != 'undefined' && travelerDataList[name] || '';
    const selectedDate =  typeof travelerValue != 'undefined' && travelerValue != '' ? new Date( travelerValue ) : new Date();
    const datePickerParams =  {
        showMonthDropdown: true,
        showYearDropdown: "select",
        // seleted : new Date( travelerValue ),
        dropdownMode: "select",
        minDate: backed != 1 && keyDate.length > 0 && keyDate.includes( 'data-max-today' ) ? new Date() : null,
        maxDate: backed == 1 ?  new Date() : null,
    }
    const errorData = typeof error_list[name] != 'undefined' && error_list[name]  || '';
    return <><PanelBody>
        <label >{typeof label != 'undefined' && label || '' }{ thisRequired == true && <span className='wp-travel-in-page-required-field'>*</span> }</label>
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
                const newData = {...travelerDataList, [name] : finaldate };
                const checkoutNewData = {...checkoutDetails, [trvOne] : newData }
                updateStore({...bookingData, checkoutDetails : checkoutNewData } )
            }}
        />
        <input type='hidden' value={ travelerValue } id={id} name={name} />
    </PanelBody><p className='wp-travel-in-page-error'>{errorData}</p></>
}