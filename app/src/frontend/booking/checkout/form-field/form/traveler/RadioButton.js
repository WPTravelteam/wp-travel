import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { PanelBody } from '@wordpress/components'

export default ( { travelerData, trvOne = 'travelerOne', pxKey = 1 } ) => {

    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { label, type, name, id, wrapper_class, options } = travelerData
    const defaults = typeof travelerData != 'undefined' && typeof travelerData.default != 'undefined' && travelerData.default || ''
    const { checkoutDetails, error_list, requiredField } = bookingData
    const thisRequired = typeof requiredField[name] != 'undefined' && typeof requiredField[name][pxKey] != 'undefined' && requiredField[name][pxKey] || false;
    const errorData = typeof error_list[name] != 'undefined' && typeof error_list[name][pxKey] != 'undefined' &&  error_list[name][pxKey] || '';
    const travelerDataList = typeof checkoutDetails != 'undefined' && typeof checkoutDetails[trvOne] != 'undefined' && checkoutDetails[trvOne] || {};
    const travelerValue = typeof travelerDataList[name] != 'undefined' && travelerDataList[name] || {} ;

    const optionKey = typeof options != 'undefined' && Object.keys( options ) || [];
    return optionKey.length > 0 && <> <PanelBody>

        <label >{typeof label != 'undefined' && label || '' }{ thisRequired == true && <span className='wp-travel-in-page-required-field'>*</span> }</label>
        <div className='wptravel-onpage-radiobtn-handle' >
            { optionKey.map( ( val, index ) => {
                return  <div className='wptravel-single-page-gender-radio-btn'> <input 
                    name={name + pxKey.toString()} 
                    type='radio' 
                    id={id} 
                    key={index}
                    defaultChecked={typeof travelerValue[pxKey] != 'undefined' && travelerValue[pxKey] == val ? true : false} 
                    value={options[val]}
                    className={ typeof wrapper_class != undefined && wrapper_class || ''}
                    onChange={ ( e ) => {
                        const value = e.target.value
                        const newTraveler = {...travelerValue, [pxKey] : value}
                        const newData = {...travelerDataList, [name] : newTraveler };
                        const checkoutNewData = {...checkoutDetails, [trvOne] : newData }
                        updateStore({...bookingData, checkoutDetails : checkoutNewData } )
                    }}
                /> 
                    <label htmlFor={val}>{ options[val] }</label> <br/>
                </div>
            })  }
        </div>

    </PanelBody> <p className='wp-travel-in-page-error'>{errorData}</p></>
}