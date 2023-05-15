import { PanelRow, TextControl }  from '@wordpress/components';
import { dispatch } from '@wordpress/data';
import { sprintf, _n, __} from '@wordpress/i18n';
const __i18n = {
	..._wp_travel_admin.strings
}
export default ( { allData } ) => {
    const { updateTripData } = dispatch('WPTravel/TripEdit');
    const { trip_duration, } = allData;
    const { duration_format } = typeof trip_duration != 'undefined' && trip_duration;
    const format = typeof duration_format != 'undefined' && duration_format || '';
    return <>
        { format == 'day' && 
        <PanelRow>
            <label>{__i18n.days}</label>
            <TextControl
                value={trip_duration.days}
                help={__i18n.days}
                onChange={(e) =>{
                    let _trip_duration = allData.trip_duration;
                    _trip_duration.days = e;

                    updateTripData({
                        ...allData,
                        trip_duration:{..._trip_duration}
                    })
                } }
            />
        </PanelRow> || format == 'night' && 
        <PanelRow>
            <label>{__i18n.nights}</label>
            <TextControl
                value={trip_duration.nights}
                help={__i18n.nights}
                onChange={(e) =>{
                    let _trip_duration = allData.trip_duration;
                    _trip_duration.nights = e;

                    updateTripData({
                        ...allData,
                        trip_duration:{..._trip_duration}
                    })
                } }
            />
        </PanelRow> || format == 'day_night' && <>
        <PanelRow>
            <label>{ __i18n.days }</label>
            <div className="wp-travel-trip-duration">
                <TextControl
                    value={trip_duration.days}
                    help={__i18n.days}
                    onChange={(e) =>{
                        let _trip_duration = allData.trip_duration;
                        _trip_duration.days = e;

                        updateTripData({
                            ...allData,
                            trip_duration:{..._trip_duration}
                        })
                    } }
                />
            </div>
        </PanelRow>
        <PanelRow>
            <label>{ __i18n.nights}</label>
            <div className="wp-travel-trip-duration">
                <TextControl
                    value={trip_duration.nights}
                    help={__i18n.nights}
                    onChange={(e) =>{
                        let _trip_duration = allData.trip_duration;
                        _trip_duration.nights = e;
                        updateTripData({
                            ...allData,
                            trip_duration:{..._trip_duration}
                        })
                    } }
                />
            </div>
        </PanelRow> </> || format == 'day_hour' && <>
        <PanelRow>
            <label>{ __i18n.days }</label>
            <div className="wp-travel-trip-duration">
                <TextControl
                    value={trip_duration.days}
                    help={__i18n.days}
                    onChange={(e) =>{
                        let _trip_duration = allData.trip_duration;
                        _trip_duration.days = e;

                        updateTripData({
                            ...allData,
                            trip_duration:{..._trip_duration}
                        })
                    } }
                />
            </div>
        </PanelRow>
        <PanelRow>
            <label>{ __i18n.hour }</label>
            <div className="wp-travel-trip-duration">
                <TextControl
                    value={typeof trip_duration.hours != 'undefined' && trip_duration.hours || '' }
                    help={ __i18n.hour}
                    onChange={(e) =>{
                        let _trip_duration = allData.trip_duration;
                        _trip_duration.hours = e;
                        updateTripData({
                            ...allData,
                            trip_duration:{..._trip_duration}
                        })
                    } }
                />
            </div>
        </PanelRow> </> || format == 'hour' && 
        <PanelRow>
            <label>{ __i18n.hour}</label>
            <TextControl
                value={typeof trip_duration.hours != 'undefined' && trip_duration.hours || ''}
                help={ __i18n.hour}
                onChange={(e) =>{
                    let _trip_duration = allData.trip_duration;
                    _trip_duration.hours = e;

                    updateTripData({
                        ...allData,
                        trip_duration:{..._trip_duration}
                    })
                } }
            />
        </PanelRow>  || format == 'hour_minute' && <>
        <PanelRow>
            <label>{ __i18n.hour }</label>
            <div className="wp-travel-trip-duration">
                <TextControl
                    value={typeof trip_duration.hours != 'undefined' && trip_duration.hours || ''}
                    help={__i18n.hour}
                    onChange={(e) =>{
                        let _trip_duration = allData.trip_duration;
                        _trip_duration.hours = e;

                        updateTripData({
                            ...allData,
                            trip_duration:{..._trip_duration}
                        })
                    } }
                />
            </div>
        </PanelRow>
        <PanelRow>
            <label>{ __i18n.minutes }</label>
            <div className="wp-travel-trip-duration">
                <TextControl
                    value={typeof trip_duration.minutes != 'undefined' && trip_duration.minutes || ''}
                    help={__i18n.minutes }
                    onChange={(e) =>{
                        let _trip_duration = allData.trip_duration;
                        _trip_duration.minutes = e;
                        updateTripData({
                            ...allData,
                            trip_duration:{..._trip_duration}
                        })
                    } }
            />
            </div>
        </PanelRow> </> || <>
        <PanelRow>
            <label>{ __i18n.days }</label>
            <div className="wp-travel-trip-duration">
                <TextControl
                    value={trip_duration.days}
                    help={__i18n.days}
                    onChange={(e) =>{
                        let _trip_duration = allData.trip_duration;
                        _trip_duration.days = e;

                        updateTripData({
                            ...allData,
                            trip_duration:{..._trip_duration}
                        })
                    } }
                />
            </div>
        </PanelRow>
        <PanelRow>
            <label>{ __i18n.nights}</label>
            <div className="wp-travel-trip-duration">
                <TextControl
                    value={trip_duration.nights}
                    help={__i18n.nights}
                    onChange={(e) =>{
                        let _trip_duration = allData.trip_duration;
                        _trip_duration.nights = e;
                        updateTripData({
                            ...allData,
                            trip_duration:{..._trip_duration}
                        })
                    } }
                />
            </div>
        </PanelRow> </> }
    </>
}