import { useState, useEffect } from '@wordpress/element';
import { TextControl, PanelRow, PanelBody, Button, TabPanel,Notice , FormTokenField, TextareaControl} from '@wordpress/components';
import { applyFilters, addFilter } from '@wordpress/hooks';
import { useSelect, dispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { sprintf, _n, __} from '@wordpress/i18n';
import WPEditor from '../fields/WPEditor';
import ErrorBoundary from '../../ErrorBoundry/ErrorBoundry';

const __i18n = {
	..._wp_travel_admin.strings
}
const WPTravelTripOptionsIncludesExcludesContent = () => {
    const allData = useSelect((select) => {
        return select('WPTravel/TripEdit').getAllStore()
    }, []);
    const { updateTripData, updateRequestSending, setTripData, updateStateChange } = dispatch('WPTravel/TripEdit');
    const { trip_include, trip_exclude } = allData;
    return <ErrorBoundary>
        <div className="wp-travel-trip-itinerary">
            <PanelRow>
                <label>{__i18n.trip_includes}</label>
            </PanelRow>
            <PanelRow className="wp-travel-editor">
                {'undefined' !== typeof trip_include && <WPEditor id="wp-travel-trip-includes" value={trip_include}
                onContentChange={(trip_include) => {
                    updateTripData({
                        ...allData,
                        trip_include: trip_include
                    })
                }} name="trip_include" />}
            </PanelRow>
            <PanelRow>
                <label>{__i18n.trip_excludes}</label>
            </PanelRow>
            <PanelRow className="wp-travel-editor">
                {'undefined' !== typeof trip_exclude && <WPEditor id="wp-travel-trip-excludes" value={trip_exclude}
                onContentChange={(trip_exclude) => {
                    updateTripData({
                        ...allData,
                        trip_exclude: trip_exclude
                    })
                }} name="trip_exclude" />}
            </PanelRow>
        </div>
    </ErrorBoundary>;
}

const WPTravelTripOptionsIncludesExcludes = () => {
    return <div className="wp-travel-ui wp-travel-ui-card wp-travel-ui-card-no-border"><WPTravelTripOptionsIncludesExcludesContent /></div>
}

export default WPTravelTripOptionsIncludesExcludes;