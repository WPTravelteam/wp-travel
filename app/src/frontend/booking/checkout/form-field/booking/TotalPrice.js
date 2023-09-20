
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
import TextData from '../form/TextData';
export default ( ) => {
    // Booking Data/state.
    const bookingData  = useSelect((select) => { return select(bookingStoreName).getAllStore() }, []);
    const { updateStore } = dispatch( bookingStoreName );
    const { payment_form } = bookingData;
    const { trip_price_info } = payment_form;

    return <>
        { typeof trip_price_info != 'undefined' && <TextData travelerData={ trip_price_info } trvOne='price_print' /> }
    </>
}