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

import {
    Accordion,
    AccordionItem,
    AccordionItemHeading,
    AccordionItemButton,
    AccordionItemPanel,
} from 'react-accessible-accordion';

export default ( ) => {
    // Booking Data/state.
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const multipleTraveler = typeof _wp_travel != 'undefined' && typeof _wp_travel.checkout_field != 'undefined' && typeof _wp_travel.checkout_field.enable_multiple_travellers != 'undefined' &&  _wp_travel.checkout_field.enable_multiple_travellers || 'no';
    const { traveler_form, form_key, paxCounts } = bookingData;
    const fieldKey  = typeof traveler_form != 'undefined' && Object.keys( traveler_form ) || [];
    const paxKey = Object.keys( paxCounts )
    console.log( 'multipleTraveler', multipleTraveler )
    return <>
        { multipleTraveler == 'yes' && <> { paxKey.length > 0 && paxKey.map( ( pKeys, index) => {
            const newdata = [];
            const paxC = paxCounts[pKeys];
            for ( i= 1; i <= paxC; i++ ) {
                newdata.push( i );
            }
        return newdata.length > 0 && newdata.map( ( finalPax, index ) => {
            console.log( 'finalPax', finalPax )
             return <Accordion allowZeroExpanded={true} key={ index }>
                <AccordionItem>
                    <AccordionItemHeading>
                        <AccordionItemButton>
                            <span>Traveler { finalPax }</span>
                        </AccordionItemButton>
                    </AccordionItemHeading>
                    <AccordionItemPanel>
                        <div className='wptravel-traveller-info-container'>
                        {   
                            fieldKey.length > 0 && fieldKey.map( ( trvKey, index ) => {
                                const travelerData = typeof traveler_form[trvKey] != 'undefined' && traveler_form[trvKey] || undefined;
                                const fieldTypes = typeof travelerData != 'undefined' && typeof travelerData.type != 'undefined' && travelerData.type || 'text';
                                const fieldName = typeof travelerData != 'undefined' && typeof travelerData.name != 'undefined' && travelerData.name || '';
                                return <div key={ index }>{ ( fieldTypes == 'text' || fieldTypes == 'number' ) && <Texts travelerData={travelerData} trvOne={form_key} pxKey={finalPax} /> || fieldTypes == 'email' && <Emails travelerData={travelerData} trvOne={form_key} pxKey={finalPax} /> || fieldTypes == 'radio' && <div className='wp-travel-new-gender-field'><RadioButton travelerData={travelerData} trvOne={form_key} pxKey={finalPax} /></div> || fieldTypes == 'checkbox' && <CheckBoxs travelerData={travelerData} trvOne={form_key} pxKey={finalPax} /> || fieldTypes == 'date' && <Dates travelerData={travelerData} trvOne={form_key} pxKey={finalPax} /> || fieldTypes == 'country_dropdown' && <DropDowns travelerData={travelerData} trvOne={form_key} pxKey={finalPax} /> || fieldTypes == 'textarea' && <TextArea travelerData={travelerData} trvOne={form_key} pxKey={finalPax} /> }</div>
                            })
                        } </div>
                    </AccordionItemPanel>
                </AccordionItem>
        </Accordion> }) }) }</>|| <div className='wptravel-traveller-info-container'>
        {   
            fieldKey.length > 0 && fieldKey.map( ( trvKey, index ) => {
                const travelerData = typeof traveler_form[trvKey] != 'undefined' && traveler_form[trvKey] || undefined;
                const fieldTypes = typeof travelerData != 'undefined' && typeof travelerData.type != 'undefined' && travelerData.type || 'text';
                const fieldName = typeof travelerData != 'undefined' && typeof travelerData.name != 'undefined' && travelerData.name || '';
                return <div key={ index }>{ ( fieldTypes == 'text' || fieldTypes == 'number' ) && <Texts travelerData={travelerData} trvOne={form_key} /> || fieldTypes == 'email' && <Emails travelerData={travelerData} trvOne={form_key} /> || fieldTypes == 'radio' && <div className='wp-travel-new-gender-field'><RadioButton travelerData={travelerData} trvOne={form_key} /></div> || fieldTypes == 'checkbox' && <CheckBoxs travelerData={travelerData} trvOne={form_key} /> || fieldTypes == 'date' && <Dates travelerData={travelerData} trvOne={form_key} /> || fieldTypes == 'country_dropdown' && <DropDowns travelerData={travelerData} trvOne={form_key} /> || fieldTypes == 'textarea' && <TextArea travelerData={travelerData} trvOne={form_key} /> }</div>
            })
        } </div> }
        <Button onClick={ () => { 
           updateStore({...bookingData, tripBillingEnable : true , travelerInfo : false }) 
        }} >Go Billing </Button>
    </>
}