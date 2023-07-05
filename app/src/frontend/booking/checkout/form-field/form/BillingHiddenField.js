import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';

export default ( { names, values }  ) => {;
    return <input 
            value={ values }
            name={ names }
            type='hidden'
            className='wp-travel-hidden-input-field-submited'
        />
}