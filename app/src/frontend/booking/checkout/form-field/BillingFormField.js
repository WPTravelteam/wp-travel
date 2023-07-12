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
import { useEffect, useState } from '@wordpress/element'
// import apiFetch from '@wordpress/api-fetch';
export default ( ) => {
    // Booking Data/state.
    const [loaders, setLoaders] = useState(false)
    const [errorFound, setErrorFound] = useState('')
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { billing_form, error_list, checkoutDetails } = bookingData;
    const { billing } = checkoutDetails;
    const billingData = typeof billing != 'undefined' && billing || {};
    const fieldKey  = typeof billing_form != 'undefined' && Object.keys( billing_form ) || [];
    useEffect( () => {
        const requiredField = {};
        if ( fieldKey.length > 0 ) {
            fieldKey.map( ( trvk, index ) => {
                console.log( 'trv key', trvk )
                const fieldCollect = typeof billing_form[trvk] != 'undefined' && billing_form[trvk] || {};
                const { validations, name, label } = fieldCollect;
                console.log( 'validations', validations );
                const requireds = typeof validations != 'undefined' && typeof validations.required != 'undefined' && validations.required || '0';
                const intRequiresd = parseInt( requireds );
                console.log( 'cick me id', intRequiresd );
                if ( intRequiresd == 1 ) {
                    requiredField[name] = true;
                }
            })
        }
        if ( Object.keys( requiredField ).length > 0 ) {
            updateStore({...bookingData, requiredField : requiredField })
        }
    }, [])
    const validateTravelerData = () => {
        setLoaders(true);
        const errorss = {};
        if ( fieldKey.length > 0 ) {
            fieldKey.map( ( trvk, index ) => {
                console.log( 'trv key', trvk )
                const fieldCollect = typeof billing_form[trvk] != 'undefined' && billing_form[trvk] || {};
                const { validations, name, label } = fieldCollect;
                console.log( 'validations', validations );
                const requireds = typeof validations != 'undefined' && typeof validations.required != 'undefined' && validations.required || '0';
                
                const intRequiresd = parseInt( requireds );
                console.log( 'cick me id', intRequiresd );
                if ( intRequiresd == 1 ) {
                    if ( Object.keys( billingData ).length < 1 ) {
                        console.log( 'error data', error_list );
                        errorss[name] = label + ' is required';
                    } else {
                        const travelData = typeof billingData[name] != 'undefined' && billingData[name] || '';
                        console.log( 'skdfjsdjfdlsfj', travelData );
                        if ( travelData == '' ) {
                            errorss[name] = label + ' is required';
                        }
                    }
                }
            })
        }
        if ( Object.keys( errorss ).length < 1 ) {
            setLoaders(false);
            updateStore({...bookingData, error_list : {}, treipPaymentEnable : true , tripBillingEnable : false })
        } else {
            setErrorFound('Required field is empty' );
            updateStore({...bookingData, error_list : errorss })
            setLoaders(false);
        }
    }
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
        {/* <PanelBody>
            <PanelRow> */}
        <div className='wptravel-onepage-navigation-btn'>
        
            <Button onClick={ () => { 
                updateStore({...bookingData, travelerInfo : true , tripBillingEnable : false })
            }} >Go Back</Button>
            <div><p className='wptravel-onepage-navigation-error'>{errorFound}</p><Button onClick={ validateTravelerData } >Next{loaders && <img src={_wp_travel.loader_url } /> }</Button>
                
            </div>
        </div>
            {/* </PanelRow>
        </PanelBody> */}
        
        
    </>
}