import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import {  PanelBody } from '@wordpress/components'
import DatePicker from "react-datepicker";

export default ( { travelerData, trvOne = 'travelerOne', pxKey = 1 } ) => {
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { label, type, name, id, attributes } = travelerData
    const backed = typeof attributes != 'undefined' && attributes['data-max-today'] || '';
    const keyDate = typeof attributes != 'undefined' && Object.keys( attributes ) || [];
    const { checkoutDetails, error_list, requiredField } = bookingData
    const thisRequired = typeof requiredField[name] != 'undefined' && typeof requiredField[name][pxKey] != 'undefined' && requiredField[name][pxKey] || false;
    const errorData = typeof error_list[name] != 'undefined' && typeof error_list[name][pxKey] != 'undefined' &&  error_list[name][pxKey] || '';
    const travelerDataList = typeof checkoutDetails != 'undefined' && typeof checkoutDetails[trvOne] != 'undefined' && checkoutDetails[trvOne] || {};
    const travelerValue = typeof travelerDataList[name] != 'undefined' && travelerDataList[name] || {};
    const selectedDate =  typeof travelerValue != 'undefined' && typeof travelerValue[pxKey] != 'undefined' && travelerValue[pxKey] != '' ? new Date( travelerValue[pxKey] ) : new Date();

    
    const datePickerParams =  {
        showMonthDropdown: true,
        showYearDropdown: "select",
        dropdownMode: "select",
        minDate: backed != 1 && keyDate.length > 0 && keyDate.includes( 'data-max-today' ) ? new Date() : null,
        maxDate: backed == 1 ?  new Date() : null,
    }
    return <> <PanelBody>
        <label >{typeof label != 'undefined' && label || '' }{ thisRequired == true && <span className='wp-travel-in-page-required-field'>*</span> }</label>

        <DatePicker
        className= "wptravel-booking-datepicker"
            selected={selectedDate }
            { ...datePickerParams }
            value={ typeof travelerValue[pxKey] != 'undefined' && travelerValue[pxKey] || '' }
            onChange={ ( value ) => {
                const finaldate = moment(value).format('YYYY-MM-DD', value)
 
                const newTraveler = {...travelerValue, [pxKey] : finaldate}
                const newData = {...travelerDataList, [name] : newTraveler };
                const checkoutNewData = {...checkoutDetails, [trvOne] : newData }
           
                updateStore({...bookingData, checkoutDetails : checkoutNewData } )
            }}
        />
        <input type='hidden' value={ typeof travelerValue[pxKey] != 'undefined' && travelerValue[pxKey] || '' } id={id} name={name} />
    </PanelBody> <p className='wp-travel-in-page-error'>{errorData}</p></>
}