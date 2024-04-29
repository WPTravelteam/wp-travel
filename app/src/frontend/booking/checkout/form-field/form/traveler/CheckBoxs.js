import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { Button, Modal, PanelBody, PanelRow, CheckboxControl} from '@wordpress/components'
import { useState, useEffect } from '@wordpress/element'
export default ( { travelerData, trvOne = 'travelerOne', pxKey = 1 } ) => {
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { label, type, name, id, wrapper_class, options } = travelerData
    const { checkoutDetails, error_list, requiredField } = bookingData
    const thisRequired = typeof requiredField[name] != 'undefined' && typeof requiredField[name][pxKey] != 'undefined' && requiredField[name][pxKey] || false;
    const errorData = typeof error_list[name] != 'undefined' && typeof error_list[name][pxKey] != 'undefined' &&  error_list[name][pxKey] || '';
    const travelerDataList = typeof checkoutDetails != 'undefined' && typeof checkoutDetails[trvOne] != 'undefined' && checkoutDetails[trvOne] || {};
    const travelerValue = typeof travelerDataList[name] != 'undefined' && travelerDataList[name] || {};
    const finalTravelerData = typeof travelerValue[pxKey] != 'undefined' && travelerValue[pxKey] || [];
    
    return <div className='wptravel-onpage-checkbox-container'>
    <PanelBody>

        <label >{typeof label != 'undefined' && label || '' }{ thisRequired == true && <span className='wp-travel-in-page-required-field'>*</span> }</label>
        <div className="wptravel-onpage-checkbox-wrapper">
            {
               typeof options != 'undefined' && options.length > 0 && options.map( ( value, index ) => {
                return <div className='wtravel-onpage-boking-checkbox' key={index}><CheckboxControl
                    key={index}
                    label={value}
                    name={name}
                    checked={ typeof finalTravelerData != 'undefined' && finalTravelerData.length > 0 && finalTravelerData.includes( value ) || false }
                    onChange={ ( values ) => {
                        if ( values == true ) {
    
                            const newTrData = [...finalTravelerData, value ]
                            const newDataSelect = {...travelerValue, [pxKey] : newTrData }
                            const newData = {...travelerDataList, [name] : newDataSelect };
                            const checkoutNewData = {...checkoutDetails, [trvOne] : newData }
                            updateStore({...bookingData, checkoutDetails : checkoutNewData } )
                            
                        } else {
                            const filterOptions = typeof finalTravelerData != 'undefined' && finalTravelerData.length > 0 && finalTravelerData.filter( val => val != value ) || [];
                            const newDataSelect = {...travelerValue, [pxKey] : filterOptions }
                            const newData = {...travelerDataList, [name] : newDataSelect };
                            const checkoutNewData = {...checkoutDetails, [trvOne] : newData }
                            updateStore({...bookingData, checkoutDetails : checkoutNewData } )
                        }
                    } }
                />
                </div>
               } )
            }
        </div>

    </PanelBody>
     <p className='wp-travel-in-page-error'>{errorData}</p></div>
}

