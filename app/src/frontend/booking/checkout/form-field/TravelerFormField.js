const __i18n = {
	..._wp_travel.strings
}
import CheckBoxs from './form/CheckBoxs';
import Dates from './form/Dates';
import DropDowns from './form/DropDowns';
import Emails from './form/Emails';
import RadioButton from './form/RadioButton';
import Texts from './form/Texts'
import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { Button } from '@wordpress/components'
import TextArea from './form/TextArea';
export default ( ) => {
    // Booking Data/state.
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const multipleTraveler = typeof _wp_travel != 'undefined' && typeof _wp_travel.checkout_field != 'undefined' && typeof _wp_travel.checkout_field.form != 'undefined' && typeof _wp_travel.checkout_field.form.enable_multiple_travellers != 'undefined' && _wp_travel.checkout_field.form.enable_multiple_travellers || 'no';
    const { traveler_form, form_key } = bookingData;
    const fieldKey  = typeof traveler_form != 'undefined' && Object.keys( traveler_form ) || [];
    // console.log( 'hello dai')
    
    return <>
        {
            fieldKey.length > 0 && fieldKey.map( ( trvKey, index ) => {
                const travelerData = typeof traveler_form[trvKey] != 'undefined' && traveler_form[trvKey] || undefined;
                const fieldTypes = typeof travelerData != 'undefined' && typeof travelerData.type != 'undefined' && travelerData.type || 'text';
                const fieldName = typeof travelerData != 'undefined' && typeof travelerData.name != 'undefined' && travelerData.name || '';
                return <div key={ index }>{ ( fieldTypes == 'text' || fieldTypes == 'number' ) && <Texts travelerData={travelerData} trvOne={form_key} /> || fieldTypes == 'email' && <Emails travelerData={travelerData} trvOne={form_key} /> || fieldTypes == 'radio' && <RadioButton travelerData={travelerData} trvOne={form_key} /> || fieldTypes == 'checkbox' && <CheckBoxs travelerData={travelerData} trvOne={form_key} /> || fieldTypes == 'date' && <Dates travelerData={travelerData} trvOne={form_key} /> || fieldTypes == 'country_dropdown' && <DropDowns travelerData={travelerData} trvOne={form_key} /> || fieldTypes == 'textarea' && <TextArea travelerData={travelerData} trvOne={form_key} /> }</div>
            })
        }
        <Button onClick={ () => { 
           updateStore({...bookingData, tripBillingEnable : true , travelerInfo : false }) 
        }} >Go Billing </Button>
    </>
}