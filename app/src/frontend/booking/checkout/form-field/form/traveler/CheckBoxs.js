import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { Button, Modal, PanelBody, PanelRow, CheckboxControl} from '@wordpress/components'
import { useState, useEffect } from '@wordpress/element'
export default ( { travelerData, trvOne = 'travelerOne', pxKey = 1 } ) => {
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { label, type, name, id, wrapper_class, options } = travelerData
    const { checkoutDetails } = bookingData
    const travelerDataList = typeof checkoutDetails != 'undefined' && typeof checkoutDetails[trvOne] != 'undefined' && checkoutDetails[trvOne] || {};
    const travelerValue = typeof travelerDataList[name] != 'undefined' && travelerDataList[name] || {};
    const finalTravelerData = typeof travelerValue[pxKey] != 'undefined' && travelerValue[pxKey] || [];
    return <PanelBody>
        <PanelRow>
            <label >{typeof label != 'undefined' && label || '' }</label>
            {
               typeof options != 'undefined' && options.length > 0 && options.map( ( value, index ) => {
                return <div key={index}><CheckboxControl
                    key={index}
                    label={value}
                    name={name}
                    checked={ typeof finalTravelerData != 'undefined' && finalTravelerData.length > 0 && finalTravelerData.includes( value ) || false }
                    onChange={ ( values ) => {
                        if ( values == true ) {
                            // const newVal = [...]
                            const newTrData = [...finalTravelerData, values ]
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
        </PanelRow>
    </PanelBody>
}

