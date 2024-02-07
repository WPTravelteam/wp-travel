const i18n = {
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
import { useEffect, useState } from '@wordpress/element'
import { __ } from '@wordpress/i18n';

import ProgressBary from '../ProgressBary';
import SelectOption from './form/SelectOption';
import Heading from './form/Heading';

import { wpTravelFormat, objectSum, GetConvertedPrice } from '../../_wptravelFunctions';

export default ( ) => {
    // Booking Data/state.
    const [loaders, setLoaders] = useState(false)
    const [errorFound, setErrorFound] = useState('')
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { billing_form, error_list, checkoutDetails, price_list, currency_symbol,  cart_amount  } = bookingData;
    const { billing } = checkoutDetails;
    const billingData = typeof billing != 'undefined' && billing || {};
    const fieldKey  = typeof billing_form != 'undefined' && Object.keys( billing_form ) || [];

    const trip_price = typeof cart_amount != 'undefined' && typeof cart_amount.cart_total != 'undefined' && cart_amount.cart_total || 0
    useEffect( () => {
        const requiredField = {};
        if ( fieldKey.length > 0 ) {
            fieldKey.map( ( trvk, index ) => {
                const fieldCollect = typeof billing_form[trvk] != 'undefined' && billing_form[trvk] || {};
                const { validations, name, label } = fieldCollect;
                const requireds = typeof validations != 'undefined' && typeof validations.required != 'undefined' && validations.required || '0';
                const intRequiresd = requireds;
                if ( intRequiresd == 1 || intRequiresd == true ) {
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
                const fieldCollect = typeof billing_form[trvk] != 'undefined' && billing_form[trvk] || {};
                const { validations, name, label } = fieldCollect;
                const requireds = typeof validations != 'undefined' && typeof validations.required != 'undefined' && validations.required || '0';
                
                const intRequiresd = requireds;
                if ( intRequiresd == 1 || intRequiresd == true ) {
                    if ( Object.keys( billingData ).length < 1 ) {
                        errorss[name] = label + i18n.set_require_message
                    } else {
                        const travelData = typeof billingData[name] != 'undefined' && billingData[name] || '';
                        if ( travelData == '' ) {
                            errorss[name] = label + i18n.set_require_message
                        }
                    }
                }
            })
        }
        if ( Object.keys( errorss ).length < 1 ) {
            setLoaders(false);
            updateStore({...bookingData, error_list : {}, treipPaymentEnable : true , tripBillingEnable : false })
        } else {
            setErrorFound(i18n.set_require_empty );
            updateStore({...bookingData, error_list : errorss })
            setLoaders(false);
        }
    }

    return <>
    <div className="wptravel-billing-formfield animated-wp-travel  fadeIn-wp-travel">
        {
            fieldKey.length > 0 && fieldKey.map( ( trvKey, index ) => {
                const travelerData = typeof billing_form[trvKey] != 'undefined' && billing_form[trvKey] || undefined;
                const fieldTypes = typeof travelerData != 'undefined' && typeof travelerData.type != 'undefined' && travelerData.type || 'text';
                const fieldName = typeof travelerData != 'undefined' && typeof travelerData.name != 'undefined' && travelerData.name || '';
                return <div className={fieldTypes} key={ index }>{ ( fieldTypes == 'text' || fieldTypes == 'number' ) && <Texts travelerData={travelerData} trvOne={'billing'} /> || fieldTypes == 'email' && <Emails travelerData={travelerData} trvOne={'billing'} /> || fieldTypes == 'radio' && <RadioButton travelerData={travelerData} trvOne={'billing'} /> || fieldTypes == 'checkbox' && <CheckBoxs travelerData={travelerData} trvOne={'billing'} /> || fieldTypes == 'date' && <Dates travelerData={travelerData} trvOne={'billing'} /> || fieldTypes == 'country_dropdown' && <DropDowns travelerData={travelerData} trvOne={'billing'} /> || fieldTypes == 'select' && <SelectOption travelerData={travelerData} trvOne={'billing'} /> || fieldTypes == 'textarea' && <TextArea travelerData={travelerData} trvOne={'billing'} /> } </div>
            })
        }
        </div>
        <p className='wptravel-onepage-navigation-error'>{errorFound}</p>

        <div className='wptravel-onepage-navigation-btn'>
        
            <Button onClick={ () => { 
                updateStore({...bookingData, travelerInfo : true , tripBillingEnable : false })
            }} >{i18n.set_go_back}</Button>
            <div>
                <div className="wptravel-onpage-priceshow">
                    { trip_price != '' && <div className="onpage-traveler-field-price-show">
                        <p><span className='onpage-travel-price-display-label'>{i18n.set_cart_total_price}</span><span dangerouslySetInnerHTML={{ __html: wpTravelFormat( trip_price ) }}></span></p>
                    </div>}
                    <Button onClick={ validateTravelerData } >{i18n.set_next_btn }{loaders && <img className='wptravel-single-page-loader-btn' src={_wp_travel.loader_url } /> }</Button>
                </div>
                
            </div>
        </div>
        
        
    </>
}