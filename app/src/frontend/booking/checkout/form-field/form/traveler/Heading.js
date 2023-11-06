import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';

export default ( { travelerData, trvOne = 'travelerOne' } ) => {
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { label, type, name, id, wrapper_class, attributes } = travelerData
    const tag = typeof attributes != 'undefined' && typeof attributes.heading_tag != 'undefined' ?attributes.heading_tag : 'h1';

    return  tag == 'h1' && <h1>{label}</h1> || 
            tag == 'h2' && <h2>{label}</h2> || 
            tag == 'h3' && <h3>{label}</h3> || 
            tag == 'h4' && <h4>{label}</h4> || 
            tag == 'h5' && <h5>{label}</h5> || 
            <h6>{label}</h6>
}