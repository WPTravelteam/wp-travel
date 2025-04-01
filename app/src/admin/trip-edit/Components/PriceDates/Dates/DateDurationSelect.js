import { PanelRow, SelectControl } from '@wordpress/components';
import { dispatch } from '@wordpress/data';
import { sprintf, _n, __} from '@wordpress/i18n';
import { applyFilters } from '@wordpress/hooks';
import TimePicker from 'react-time-picker';
const __i18n = {
	..._wp_travel_admin.strings
}
export default ( {allData } ) => {
    const { updateTripData } = dispatch('WPTravel/TripEdit');
    const { trip_duration, } = allData;
    const { duration_format, arrival_time, departure_time } = typeof trip_duration != 'undefined' && trip_duration;
    // const format = typeof duration_format != 'undefined' && duration_format || '';
    // const translatedSelectLabel = __i18n.duration_select_label;

    const timeFormating = {
        amPmAriaLabel: "Select AM/PM",
        clockIcon: false,
        clearIcon: null,
        disableClock: true,
        format: 'h:m a',
        hourAriaLabel: 'Hour',
        className: 'wp-travel-duration-arrival-time',
        minuteAriaLabel: 'minut',
        secondAriaLabel: 'Second',
        isOpen: true,
    }
    const arrival = typeof arrival_time != 'undefined' && arrival_time != '' && arrival_time || '00:00';
    const departure = typeof departure_time != 'undefined' && departure_time != '' && departure_time || '00:00';

    return <>

        { typeof __i18n.arrival_departure != 'undefined' && __i18n.arrival_departure && <>
            <PanelRow>
                <label>{ typeof __i18n.arrival_time != 'undefined' && __i18n.arrival_time || '' }</label>
                <TimePicker 
                    {...timeFormating}
                    value={[arrival]}
                    onChange={ value =>{
                        const old_duration = typeof allData.trip_duration != 'undefined' && allData.trip_duration
                        const new_duration = {...old_duration, arrival_time : value };
                        updateTripData({
                            ...allData,
                            trip_duration : new_duration
                        })
                    } } 
                />
            </PanelRow>
            <PanelRow>
                <label>{ typeof __i18n.departure_time != 'undefined' && __i18n.departure_time || '' }</label>
                <TimePicker 
                    {...timeFormating}
                    value={[departure]}
                    onChange={ value =>{
                        const old_duration = typeof allData.trip_duration != 'undefined' && allData.trip_duration
                        const new_duration = {...old_duration, departure_time : value };
                        updateTripData({
                            ...allData,
                            trip_duration : new_duration
                        })
                    } } 
                />
            </PanelRow>
        </> }
    </>;
}