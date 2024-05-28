// import { useSelect, dispatch } from '@wordpress/data';
import he from 'he'
import parse from 'html-react-parser';
// const bookingStoreName = 'WPTravelFrontend/BookingData';
import { PanelBody, PanelRow } from '@wordpress/components'

export default ( { travelerData, trvOne = 'travelerOne' } ) => {
    // const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    // const { updateStore } = dispatch( bookingStoreName );
    const { label, type, name, id, wrapper_class } = travelerData
    // const { checkoutDetails } = bookingData
    const defaults = typeof travelerData != 'undefined' && typeof travelerData.default != undefined && travelerData.default || '';

    return <PanelBody>
        <PanelRow>
            <label >{typeof label != 'undefined' && label || '' }</label>
            <div id={id} className={wrapper_class} >{ parse( he.decode( defaults ) ) }</div>
        </PanelRow>
        
    </PanelBody>
}