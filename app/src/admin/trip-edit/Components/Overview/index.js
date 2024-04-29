import { PanelRow } from "@wordpress/components";
import { addFilter } from "@wordpress/hooks";
import { dispatch } from "@wordpress/data";
import { _n, __ } from "@wordpress/i18n";

import WPEditor from "../../../fields/WPEditor";
const __i18n = {
	..._wp_travel_admin.strings,
};

// Single Components for hook callbacks.
const TripOverview = ({ allData }) => {
	const { trip_overview } = allData;
	const { updateTripData } = dispatch("WPTravel/TripEdit");
	return (
		<>
			{/* <PanelRow>
            <label htmlFor="wp-travel-trip-overview">{__i18n.overview}</label>
        </PanelRow> */}
			<PanelRow className="wp-travel-editor">
				{"undefined" !== typeof trip_overview && (
					<WPEditor
						id="wp-travel-trip-overview"
						value={trip_overview}
						onContentChange={(trip_overview) => {
							updateTripData({
								...allData,
								trip_overview: trip_overview,
							});
						}}
						name="trip_overview"
					/>
				)}
				
			</PanelRow>
		</>
	);
};

// Callbacks.
const TripOverviewCB = (content, allData) => {
	return [...content, <TripOverview allData={allData} key="TripOverview" />];
};

// Hooks.
addFilter(
	"wptravel_trip_edit_tab_content_overview",
	"WPTravel/TripEdit/TripOverview",
	TripOverviewCB,
	10
);
