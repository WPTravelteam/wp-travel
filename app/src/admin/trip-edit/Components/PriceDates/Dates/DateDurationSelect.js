import { PanelRow, SelectControl } from '@wordpress/components';
import { dispatch } from '@wordpress/data';
import { sprintf, _n, __} from '@wordpress/i18n';
import { applyFilters } from '@wordpress/hooks';
const __i18n = {
	..._wp_travel_admin.strings
}
export default ( {allData } ) => {
    const { updateTripData } = dispatch('WPTravel/TripEdit');
    const { trip_duration, } = allData;
    const { duration_format } = typeof trip_duration != 'undefined' && trip_duration;
    const format = typeof duration_format != 'undefined' && duration_format || '';
    const translatedSelectLabel = __i18n.duration_select_label;
    const selectOption = [
        {label : translatedSelectLabel.day_night, value : 'day_night' },
        {label : translatedSelectLabel.hour, value : 'hour' },
        
    ];
    const newSelectOption = applyFilters( 'wp_travel_trip_duration_formate_select', selectOption);
    return <>
        <PanelRow>
            <label>{__i18n.trip_duration}</label>
            <SelectControl
            options={newSelectOption}
            value  ={ format }
            onChange={ (val)=> {
                const old_duration = typeof allData.trip_duration != 'undefined' && allData.trip_duration
                const new_duration = {...old_duration, duration_format : val };
                updateTripData({
                    ...allData,
                    trip_duration : new_duration
                })
            } }
            />
        </PanelRow>
    </>;
}