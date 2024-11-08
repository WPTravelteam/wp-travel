import { useSelect, dispatch } from '@wordpress/data';
import { PanelBody } from '@wordpress/components';
const bookingStoreName = 'WPTravelFrontend/BookingData';
export default ({ travelerData, trvOne = 'travelerOne' }) => {
    // Safely retrieve countries using optional chaining
    const countries = _wp_travel?.checkout_field?.country || {};
    const countryKeys = Object.keys(countries);
    const bookingData = useSelect((select) => select(bookingStoreName).getAllStore(), []);
    const { updateStore } = dispatch(bookingStoreName);
    const { label, name, id } = travelerData;
    const { checkoutDetails, error_list, requiredField } = bookingData;
    const isRequired = requiredField?.[name] || false;
    const travelerDataList = checkoutDetails?.[trvOne] || {};
    const travelerValue = travelerDataList[name] || '';
    const errorData = error_list?.[name] || '';
    const handleChange = (event) => {
        const selectedValue = event.target.value;
        const newData = { ...travelerDataList, [name]: selectedValue };
        const updatedCheckoutDetails = { ...checkoutDetails, [trvOne]: newData };
        updateStore({ ...bookingData, checkoutDetails: updatedCheckoutDetails });
    };
    return (
        <>
            <PanelBody>
                <label>
                    {label || ''}
                    {isRequired && <span className='wp-travel-in-page-required-field'>*</span>}
                </label>
                <select id={id} name={name} onChange={handleChange}>
                    {countryKeys.map((key, index) => (
                        <option value={key} key={index}>
                            {countries[key]}
                        </option>
                    ))}
                </select>
            </PanelBody>
            {errorData && <p className='wp-travel-in-page-error'>{errorData}</p>}
        </>
    );
};