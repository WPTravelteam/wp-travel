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
import { Button, PanelBody, PanelRow } from '@wordpress/components'
import TextArea from './form/TextArea';
// import { useEffect } from '@wordpress/element'
// import apiFetch from '@wordpress/api-fetch';
export default ( ) => {
    // Booking Data/state.
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { billing_form } = bookingData;
    const fieldKey  = typeof billing_form != 'undefined' && Object.keys( billing_form ) || [];
    // useEffect( () => {
    //     apiFetch( {
    //         url: `${wp_travel.ajaxUrl}?action=wptravel_get_payment_field&_nonce=${_wp_travel._nonce}`,
    //         method: 'GET',
    //     }).then( settingData => {
    //         console.log( 'sdf', typeof settingData, settingData )
    //         if ( typeof settingData != 'undefined' && typeof settingData.success != 'undefined' && typeof settingData.data != 'undefined' ) {

    //             if ( settingData.success === true && settingData.data != '' ) {
    //                 console.log( 'data', settingData.data )
    //                 updateStore( {...bookingData, payment_form : settingData.data.payment, form_key : settingData.data.form_key } )
    //             }

    //         } else {
    //             console.log( 'setting not get!' )
    //         }
    //     }).catch(error => {
    //         console.log( 'You can not use one page checkout because setting not loaded!' );
    //     })
    // },[])
    return <>
    <div className="wptravel-billing-formfield">
        {
            fieldKey.length > 0 && fieldKey.map( ( trvKey, index ) => {
                const travelerData = typeof billing_form[trvKey] != 'undefined' && billing_form[trvKey] || undefined;
                const fieldTypes = typeof travelerData != 'undefined' && typeof travelerData.type != 'undefined' && travelerData.type || 'text';
                const fieldName = typeof travelerData != 'undefined' && typeof travelerData.name != 'undefined' && travelerData.name || '';
                if ( fieldTypes != 'heading' ) {
                return <div  key={ index }>{ ( fieldTypes == 'text' || fieldTypes == 'number' ) && <Texts travelerData={travelerData} trvOne={'billing'} /> || fieldTypes == 'email' && <Emails travelerData={travelerData} trvOne={'billing'} /> || fieldTypes == 'radio' && <RadioButton travelerData={travelerData} trvOne={'billing'} /> || fieldTypes == 'checkbox' && <CheckBoxs travelerData={travelerData} trvOne={'billing'} /> || fieldTypes == 'date' && <Dates travelerData={travelerData} trvOne={'billing'} /> || fieldTypes == 'country_dropdown' && <DropDowns travelerData={travelerData} trvOne={'billing'} /> || fieldTypes == 'textarea' && <TextArea travelerData={travelerData} trvOne={'billing'} /> } </div>
                }
            })
        }
        </div>
        <PanelBody>
            <PanelRow>
                <Button onClick={ () => { 
                    updateStore({...bookingData, travelerInfo : true , tripBillingEnable : false })
                }} >Go Back</Button>
                <Button onClick={ () => { 
                    updateStore({...bookingData, treipPaymentEnable : true , tripBillingEnable : false })
                }} >Next</Button>
            </PanelRow>
        </PanelBody>
        
    </>
}