
const __i18n = {
	..._wp_travel.strings
}
import CheckBoxs from '../form/CheckBoxs';
import Dates from '../form/Dates';
import DropDowns from '../form/DropDowns';
import Emails from '../form/Emails';
import RadioButton from '../form/RadioButton';
import Texts from '../form/Texts'
import { useSelect, dispatch } from '@wordpress/data';
const bookingStoreName = 'WPTravelFrontend/BookingData';
import { Button } from '@wordpress/components'
import TextArea from '../form/TextArea';
import OptionDrowpDown from '../form/OptionDrowpDown';
export default ( ) => {
    // Booking Data/state.
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { payment_form } = bookingData;
    const { payment_mode } = payment_form;
    // const fieldKey  = typeof booking_option != 'undefined' && Object.keys( booking_option ) || [];
    return <>
        { typeof payment_mode != 'undefined' && <OptionDrowpDown travelerData={ payment_mode } trvOne='booking_selected' /> }
    </>
}