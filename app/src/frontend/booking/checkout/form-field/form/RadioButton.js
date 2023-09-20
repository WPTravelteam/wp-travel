import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { PanelBody, PanelRow } from '@wordpress/components'

export default ( { travelerData, trvOne = 'travelerOne', pmtFld = 'no' } ) => {

    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { label, type, name, id, wrapper_class, options } = travelerData
    const defaults = typeof travelerData != 'undefined' && typeof travelerData.default != 'undefined' && travelerData.default || ''
    const { checkoutDetails, error_list, requiredField } = bookingData
    const thisRequired = typeof requiredField[name] != 'undefined' && requiredField[name] || false;
    const travelerDataList = typeof checkoutDetails != 'undefined' && typeof checkoutDetails[trvOne] != 'undefined' && checkoutDetails[trvOne] || {};
    const travelerValue = typeof travelerDataList[name] != 'undefined' && travelerDataList[name] ||  defaults ;
    const errorData = typeof error_list[name] != 'undefined' && error_list[name]  || '';
    const optionKey = typeof options != 'undefined' && Object.keys( options ) || [];
    if ( pmtFld == 'yes' && typeof travelerDataList[name] == 'undefined' ) {
        const newData = {...travelerDataList, [name] : defaults };
        const checkoutNewData = {...checkoutDetails, [trvOne] : newData }
        updateStore({...bookingData, checkoutDetails : checkoutNewData } ) 
    }
    return optionKey.length > 0 && <><PanelBody>
                { pmtFld == 'no' &&<>
                <label >{typeof label != 'undefined' && label || '' }{ thisRequired == true && <span className='wp-travel-in-page-required-field'>*</span> }</label>  
                <div className='wptravel-onpage-radiobtn-handle'>
                 {optionKey.map( ( val, index ) => {
                    return  <div className='wptravel-single-page-gender-radio-btn'> <input 
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
                    </div>
                }) } </div> </> || <>
                <PanelRow>
                  <label >{typeof label != 'undefined' && label || '' }{ thisRequired == true && <span className='wp-travel-in-page-required-field'>*</span> }</label>  
                  <div className='wptravel-onpage-radiobtn-handle'>
                 {optionKey.map( ( val, index ) => {
                    if ( val == 'stripe' || val == 'authorizenet' || val == 'bank_deposit' || val == 'paypal' || val == 'express_checkout' || val == 'stripe_ideal' ) {
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
                })} </div> </PanelRow> </>  }
    </PanelBody><p className='wp-travel-in-page-error'>{errorData}</p></>
}