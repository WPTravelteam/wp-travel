const __i18n = {
	..._wp_travel.strings
}
import CheckBoxs from './form/traveler/CheckBoxs';
import Dates from './form/traveler/Dates';
import DropDowns from './form/traveler/DropDowns';
import Emails from './form/traveler/Emails';
import RadioButton from './form/traveler/RadioButton';
import Texts from './form/traveler/Texts'
import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { Button } from '@wordpress/components'
import TextArea from './form/traveler/TextArea';
import 'react-accessible-accordion/dist/fancy-example.css';
import { useEffect, useState } from '@wordpress/element'
import {
    Accordion,
    AccordionItem,
    AccordionItemHeading,
    AccordionItemButton,
    AccordionItemPanel,
} from 'react-accessible-accordion';
// import ProgressBary from '../ProgressBary';

export default ( ) => {
    const [loaders, setLoaders] = useState(false)
    const [errorFound, setErrorFound] = useState('')
    // const [ paxSize, setPaxSize ] = useState(0);
    // Booking Data/state.
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const multipleTraveler = typeof _wp_travel != 'undefined' && typeof _wp_travel.checkout_field != 'undefined' && typeof _wp_travel.checkout_field.enable_multiple_travellers != 'undefined' &&  _wp_travel.checkout_field.enable_multiple_travellers || 'no';
    const { traveler_form, form_key, paxCounts, checkoutDetails, error_list, paxSize } = bookingData;
    const fieldKey  = typeof traveler_form != 'undefined' && Object.keys( traveler_form ) || [];
    // const paxValue = Object.values( paxCounts )

    const travelerEnter = typeof checkoutDetails[form_key] != 'undefined' && checkoutDetails[form_key] || {};
    const validateEmail = ( input ) => {
        var validRegex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if ( input.match( validRegex ) ) return true;
        else return false;
    }
    /** 
     * When load this component, this function set validation of required field 
     * 
     */
    useEffect( () => {
        const requiredField = {};
        if ( fieldKey.length > 0 ) {
            fieldKey.map( ( trvk, index ) => {
                const fieldCollect = typeof traveler_form[trvk] != 'undefined' && traveler_form[trvk] || {};
                const { validations, name, label } = fieldCollect;
                const requireds = typeof validations != 'undefined' && typeof validations.required != 'undefined' && validations.required || '0';
                const requiredAll = typeof validations != 'undefined' && typeof validations.required_for_all != 'undefined' && validations.required_for_all || '0';
                const intRequiredAll =  requiredAll;
                const intRequiresd = requireds;
                if ( intRequiresd == 1 || intRequiredAll == true ) {
                    const newKeyRequired = { 1 : true }
                    requiredField[name] = newKeyRequired;
                }

                if ( multipleTraveler == 'yes' && ( intRequiredAll == 1 || intRequiredAll == true ) ) {
                    // var paxTotal = 0
                    // paxKey.length > 0 && paxKey.map( ( pKeys, index) => {
                    //     const paxC = paxCounts[pKeys];
                    //     paxTotal = paxTotal + paxC;
                    // })
                    var pxReq = {}
                    if ( paxSize > 1 ) {
                        for( i = 0 ; i < paxSize ; i++ ) {
                            pxReq[i + 1 ] = true
                        }
                        if ( Object.keys( pxReq ).length > 0 ) {
                            requiredField[name] = pxReq;
                        }
                    }
                }
            })
        }
        if ( Object.keys( requiredField ).length > 0 ) {
            updateStore({...bookingData, requiredField : requiredField })
        }
    },[])
    /** 
     * When use click next button this function check required field validation 
     * 
     */
    const validateTravelerData = () => {
        setLoaders(true)
        const errorss = {};
        const emailValidate = {}
        if ( fieldKey.length > 0 ) {
            fieldKey.map( ( trvk, index ) => {
                const fieldCollect = typeof traveler_form[trvk] != 'undefined' && traveler_form[trvk] || {};
                const { validations, name, label, type } = fieldCollect;
                const requireds = typeof validations != 'undefined' && typeof validations.required != 'undefined' && validations.required || '0';
                const requiredAll = typeof validations != 'undefined' && typeof validations.required_for_all != 'undefined' && validations.required_for_all || '0';
                const intRequiredAll = requiredAll;
                const intRequiresd = requireds;
                const travelData = typeof travelerEnter[name] != 'undefined' && travelerEnter[name] || {};
                var requiredListed = {}
                if ( type == 'email' ) {
                    const emailData = typeof travelData[1] != 'undefined' && travelData[1] || '';
                    if ( emailData != '' && ! validateEmail( emailData ) ) {
                        const newEmailError = {1 : 'Email is not valide'}
                        emailValidate[name] = newEmailError;
                    }

                }
                if ( intRequiresd == 1 || intRequiresd == true ) {
                    if ( Object.keys( travelerEnter ).length < 1 ) {
                        const newCreateError = { 1 : label + ' is required' }
                        requiredListed[1] = label + ' is required';
                        errorss[name] = newCreateError;
                    } else {
                        const finalData = typeof travelData[1] != 'undefined' && travelData[1] || '';
                        if ( finalData == '' ) {
                            const newCreateError = { 1 : label + ' is required' }
                            requiredListed[1] = label + ' is required';
                            errorss[name] = newCreateError;
                        }
                    }
                } 
                if ( multipleTraveler == 'yes' ) {
                    // var paxTotal = 0
                    // paxKey.length > 0 && paxKey.map( ( pKeys, index) => {
                    //     const paxC = paxCounts[pKeys];
                    //     paxTotal = paxTotal + paxC;
                    // })
                    var pxReq = {}
                    var emailErrors = {}
                    if ( paxSize > 1 ) {
                        for( i = 0 ; i < paxSize ; i++ ) {
                            const finalDatas = typeof travelData[i+1] != 'undefined' && travelData[i+1] || '';
                            if ( type == 'email' && finalDatas != '' && ! validateEmail( finalDatas ) ) {
                                emailErrors[i+1] = 'Email is not valide';
                            }
                            if ( finalDatas == '' ) {
                                pxReq[i + 1 ] = label + ' is required';
                            }
                        }
                        if ( ( intRequiredAll == true || intRequiredAll == 1 ) && Object.keys( pxReq ).length > 0 ) {
                            errorss[name] = {...requiredListed, ...pxReq };
                        }
                        if ( Object.keys( emailErrors).length > 0 ) {
                            emailValidate[name] = emailErrors;
                        }
                    }
                }
            })
        }
        if ( Object.keys( errorss ).length < 1 && Object.keys( emailValidate ).length < 1 ) {
            setLoaders( false );
            updateStore({...bookingData, error_list : {}, tripBillingEnable : true , travelerInfo : false })
        } else {
            const newRequiredError = {...errorss, email_validation : emailValidate }
            setErrorFound('Required field is empty' );
            updateStore({...bookingData, error_list : newRequiredError })
            setLoaders(false);
        }
    }
    const paxKey = [paxSize];
    return <>
        {/* <ProgressBary statusText={`Progress: Fill Up Traveller Details`} value={30} max={100} /> */}
        { multipleTraveler == 'yes' && <> { paxKey.length > 0 && paxKey.map( ( pKeys, ind) => {
            const newdata = [];
            // const paxC = pKeys;
            for ( i= 1; i <= pKeys; i++ ) {
                newdata.push( i );
            }
        return <div key={ind * 9 } >
            {/* When Multiple traveler enable this accordion show */}
            <Accordion allowZeroExpanded={true} preExpanded={[1]}  >{ newdata.length > 0 && newdata.map( ( finalPax, index ) => {
             return <AccordionItem key={ index + 10 } uuid={finalPax + ind } >
                    <AccordionItemHeading>
                        <AccordionItemButton>
                            <span>{ finalPax == 1 ? "Lead Traveler" : 'Traveler ' } { finalPax > 1 && finalPax } </span>
                        </AccordionItemButton>
                    </AccordionItemHeading>
                    <AccordionItemPanel>
                        <div className='wptravel-traveller-info-container'>
                        {   
                            fieldKey.length > 0 && fieldKey.map( ( trvKey, index ) => {
                                const travelerData = typeof traveler_form[trvKey] != 'undefined' && traveler_form[trvKey] || undefined;
                                const validated = typeof travelerData != 'undefined' && typeof travelerData.remove_field != 'undefined' && travelerData.remove_field || '';
                                const fieldTypes = typeof travelerData != 'undefined' && typeof travelerData.type != 'undefined' && travelerData.type || 'text';
                                const fieldName = typeof travelerData != 'undefined' && typeof travelerData.name != 'undefined' && travelerData.name || '';
                                if ( finalPax == 1 ) {
                                    return <div key={ trvKey }>{ ( fieldTypes == 'text' || fieldTypes == 'number' ) && <Texts travelerData={travelerData} trvOne={form_key} pxKey={finalPax} /> || fieldTypes == 'email' && <Emails travelerData={travelerData} trvOne={form_key} pxKey={finalPax} /> || fieldTypes == 'radio' && <div className='wp-travel-new-gender-field'><RadioButton travelerData={travelerData} trvOne={form_key} pxKey={finalPax} /></div> || fieldTypes == 'checkbox' && <CheckBoxs travelerData={travelerData} trvOne={form_key} pxKey={finalPax} /> || fieldTypes == 'date' && <Dates travelerData={travelerData} trvOne={form_key} pxKey={finalPax} /> || fieldTypes == 'country_dropdown' && <DropDowns travelerData={travelerData} trvOne={form_key} pxKey={finalPax} /> || fieldTypes == 'textarea' && <TextArea travelerData={travelerData} trvOne={form_key} pxKey={finalPax} /> }</div>
                                } else {
                                    if ( validated == '' ) {
                                        return <div key={ trvKey }>{ ( fieldTypes == 'text' || fieldTypes == 'number' ) && <Texts travelerData={travelerData} trvOne={form_key} pxKey={finalPax} /> || fieldTypes == 'email' && <Emails travelerData={travelerData} trvOne={form_key} pxKey={finalPax} /> || fieldTypes == 'radio' && <div className='wp-travel-new-gender-field'><RadioButton travelerData={travelerData} trvOne={form_key} pxKey={finalPax} /></div> || fieldTypes == 'checkbox' && <CheckBoxs travelerData={travelerData} trvOne={form_key} pxKey={finalPax} /> || fieldTypes == 'date' && <Dates travelerData={travelerData} trvOne={form_key} pxKey={finalPax} /> || fieldTypes == 'country_dropdown' && <DropDowns travelerData={travelerData} trvOne={form_key} pxKey={finalPax} /> || fieldTypes == 'textarea' && <TextArea travelerData={travelerData} trvOne={form_key} pxKey={finalPax} /> }</div>

                                    }
                                }
                            })
                        } </div>
                    </AccordionItemPanel>
                </AccordionItem>
         }) } </Accordion> </div> }) }</>|| <div className='wptravel-traveller-info-container'>
            {/* When disable multiple traveler, Execute this code and show traveler detail without accordion */}
        {   
            fieldKey.length > 0 && fieldKey.map( ( trvKey, index ) => {
                const travelerData = typeof traveler_form[trvKey] != 'undefined' && traveler_form[trvKey] || undefined;
                const fieldTypes = typeof travelerData != 'undefined' && typeof travelerData.type != 'undefined' && travelerData.type || 'text';
                const fieldName = typeof travelerData != 'undefined' && typeof travelerData.name != 'undefined' && travelerData.name || '';
                return <div key={ index }>{ ( fieldTypes == 'text' || fieldTypes == 'number' ) && <Texts travelerData={travelerData} trvOne={form_key} /> || fieldTypes == 'email' && <Emails travelerData={travelerData} trvOne={form_key} /> || fieldTypes == 'radio' && <div className='wp-travel-new-gender-field'><RadioButton travelerData={travelerData} trvOne={form_key} /></div> || fieldTypes == 'checkbox' && <CheckBoxs travelerData={travelerData} trvOne={form_key} /> || fieldTypes == 'date' && <Dates travelerData={travelerData} trvOne={form_key} /> || fieldTypes == 'country_dropdown' && <DropDowns travelerData={travelerData} trvOne={form_key} /> || fieldTypes == 'textarea' && <TextArea travelerData={travelerData} trvOne={form_key} /> }</div>
            })
        } </div> }
        <p className='wp-travel-in-page-error'>{errorFound}</p>
        <div className='wptrave-singlepage-initial-nextbtn'>
        
        <Button onClick={validateTravelerData} >Next{loaders && <img className='wptravel-single-page-loader-btn' src={_wp_travel.loader_url } /> }</Button>
        </div>
        
    </>
}