import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { Button, Modal, PanelBody, PanelRow, TextControl, RadioControl } from '@wordpress/components'
import { useState, useEffect } from '@wordpress/element'
export default ( { travelerData, trvOne = 'travelerOne', pmtFld = 'no' } ) => {
    // const [ optionList, setOption ] = useState({})
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { label, type, name, id, wrapper_class, options } = travelerData
    const defaults = typeof travelerData != 'undefined' && typeof travelerData.default != 'undefined' && travelerData.default || ''
    const { checkoutDetails, error_list, requiredField } = bookingData
    const thisRequired = typeof requiredField[name] != 'undefined' && requiredField[name] || false;
    const travelerDataList = typeof checkoutDetails != 'undefined' && typeof checkoutDetails[trvOne] != 'undefined' && checkoutDetails[trvOne] || {};
    const travelerValue = typeof travelerDataList[name] != 'undefined' && travelerDataList[name] ||  defaults ;
    const errorData = typeof error_list[name] != 'undefined' && error_list[name]  || '';
    // console.log( )
    const optionKey = typeof options != 'undefined' && Object.keys( options ) || [];
    return optionKey.length > 0 && <><PanelBody>
        <PanelRow>
            <label >{typeof label != 'undefined' && label || '' }{ thisRequired == true && <span className='wp-travel-in-page-required-field'>*</span> }</label>
            <div>
                { pmtFld == 'no' && optionKey.map( ( val, index ) => {
                    // travelerValue == val && setOption( 'checked' );
                    // const vall = optionList[val];
                    return  <> <input 
                        name={name} 
                        type='radio' 
                        id={id} 
                        key={index}
                        defaultChecked={ travelerValue == val ? true : false} 
                        value={val}
                        className={ typeof wrapper_class != undefined && wrapper_class || ''}
                        onChange={ ( e ) => {
                            const value = e.target.value
                            const newData = {...travelerDataList, [name] : value };
                            const checkoutNewData = {...checkoutDetails, [trvOne] : newData }
                            updateStore({...bookingData, checkoutDetails : checkoutNewData } )
                        }}
                    /> 
                        <label htmlFor={val}>{ options[val] }</label> <br/>
                    </>
                }) || optionKey.map( ( val, index ) => {
                    // travelerValue == val && setOption( 'checked' );
                    // const vall = optionList[val];
                    if ( val == 'stripe' || val == 'authorizenet' || val == 'bank_deposit' || val == 'paypal'  ) {
                        return  <> <input 
                            name={name} 
                            type='radio' 
                            id={id} 
                            key={index}
                            defaultChecked={ travelerValue == val ? true : false} 
                            value={val}
                            className={ typeof wrapper_class != undefined && wrapper_class || ''}
                            onChange={ ( e ) => {
                                const value = e.target.value
                                const newData = {...travelerDataList, [name] : value };
                                const checkoutNewData = {...checkoutDetails, [trvOne] : newData }
                                updateStore({...bookingData, checkoutDetails : checkoutNewData } )
                            }}
                        /> 
                            <label htmlFor={val}>{ options[val] }</label> <br/>
                        </>
                    }
                })  }
            </div>
        </PanelRow>
    </PanelBody><p className='wp-travel-in-page-error'>{errorData}</p></>
}