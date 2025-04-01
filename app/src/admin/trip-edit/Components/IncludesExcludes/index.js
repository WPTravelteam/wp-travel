import { PanelRow } from '@wordpress/components';
import { addFilter } from '@wordpress/hooks';
import { dispatch } from '@wordpress/data';
import { _n, __} from '@wordpress/i18n';
import WPEditor from '../../../fields/WPEditor';

const __i18n = {
	..._wp_travel_admin.strings
}

// Single Components for hook callbacks.
const TripIncludes = ({allData}) => {
    const { updateTripData } = dispatch('WPTravel/TripEdit');
    const { trip_include } = allData;
    return <>
        <PanelRow>
            <label>{__i18n.trip_includes}</label>
        </PanelRow>
        <PanelRow className="wp-travel-editor">
            {'undefined' !== typeof trip_include && <WPEditor id="wp-travel-trip-includes" value={trip_include.replace(/<p>\s*<\/p>/g, '<p><br /></p>')}
            onContentChange={(trip_include) => {
                updateTripData({
                    ...allData,
                    trip_include: trip_include
                })
            }} name="trip_include" />}
        </PanelRow>
    </>;
}

const TripExcludes = ({allData}) => {
    const { updateTripData } = dispatch('WPTravel/TripEdit');
    const { trip_exclude } = allData;
    return <>
        <PanelRow>
            <label>{__i18n.trip_excludes}</label>
        </PanelRow>
        <PanelRow className="wp-travel-editor">
            {'undefined' !== typeof trip_exclude && <WPEditor id="wp-travel-trip-excludes" value={trip_exclude.replace(/<p>\s*<\/p>/g, '<p><br /></p>')}
            onContentChange={(trip_exclude) => {
                updateTripData({
                    ...allData,
                    trip_exclude: trip_exclude
                })
            }} name="trip_exclude" />}
        </PanelRow>
    </>;
}

// Callbacks.
const TripIncludesCB = ( content, allData ) => {
    return [ ...content, <TripIncludes allData={allData} key="TripIncludes" /> ];
}

const TripExcludesCB = ( content, allData ) => {
    return [ ...content, <TripExcludes allData={allData} key="TripExcludes" /> ];
}

// Hooks.
addFilter( 'wptravel_trip_edit_tab_content_includes_excludes', 'WPTravel/TripEdit/TripIncludes', TripIncludesCB, 10 );
addFilter( 'wptravel_trip_edit_tab_content_includes_excludes', 'WPTravel/TripEdit/TripExcludes', TripExcludesCB, 20 );
