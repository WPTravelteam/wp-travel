import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { Button, Modal, PanelBody, PanelRow, TextControl } from '@wordpress/components'

export default ( { names, values, index = '0', keys='2_44_55' }  ) => {
    // const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    // const wpKey = typeof bookingData != 'undefined' && typeof bookingData.form_key != 'undefined' && bookingData.form_key || '';
    const finaleName = names + '[' + keys.toString() + ']' + '[' + index.toString() + ']';
    return <input 
            value={ values }
            name={ finaleName }
            type='hidden'
            className='wp-travel-hidden-input-field-submited'
        />
}