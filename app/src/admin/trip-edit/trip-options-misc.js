import { PanelRow, ToggleControl } from '@wordpress/components';
import { addFilter } from '@wordpress/hooks';
import { dispatch } from '@wordpress/data';
import { _n, __} from '@wordpress/i18n';
const __i18n = {
	..._wp_travel_admin.strings
}

// @todo Need to remove this in future.
const WPTravelTripOptionsMisc = () => {
    return <></>
}
export default WPTravelTripOptionsMisc;

// Single Components for hook callbacks.
const TripEnquiryTitle = () => {
    return <h3>{ __i18n.trip_enquiry }</h3>;
}

const EnableGlobalTripEnquiry = ({allData}) => {
    const { updateTripData } = dispatch('WPTravel/TripEdit');
    const { use_global_trip_enquiry_option } = allData;
    return <PanelRow>
        <label>{ __i18n.global_trip_enquiry }</label>
        <ToggleControl
            value={use_global_trip_enquiry_option}
            checked={ use_global_trip_enquiry_option == 'yes' ? true : false }
            onChange={ 
                (use_global_trip_enquiry_option) => {
                    updateTripData({
                        ...allData,
                        use_global_trip_enquiry_option:use_global_trip_enquiry_option ? 'yes' : 'no'
                    })
                }
            }
        />
    </PanelRow>;
}
const EnableTripEnquiry = ({allData}) => {
    const { updateTripData } = dispatch('WPTravel/TripEdit');
    const { use_global_trip_enquiry_option, enable_trip_enquiry_option } = allData;
    return <>
        { use_global_trip_enquiry_option !== 'yes' &&
            <PanelRow>
                <label>{ __i18n.trip_enquiry }</label>
                <ToggleControl
                    value={enable_trip_enquiry_option}
                    checked={ enable_trip_enquiry_option == 'yes' ? true : false }
                    onChange={ 
                        (enable_trip_enquiry_option) => {
                            updateTripData({
                                ...allData,
                                enable_trip_enquiry_option:enable_trip_enquiry_option ? 'yes' : 'no'
                            })
                        }
                    }
                />
            </PanelRow>
        }
    </>;
}

// Hooks.
addFilter('wptravel_trip_edit_tab_content_misc', 'wp_travel', (content ) => { return [ ...content, < TripEnquiryTitle  />] }, 10 );
addFilter('wptravel_trip_edit_tab_content_misc', 'wp_travel', (content, allData ) => { return [ ...content, < EnableGlobalTripEnquiry allData={allData} /> ] }, 11 );
addFilter('wptravel_trip_edit_tab_content_misc', 'wp_travel', (content, allData ) => { return [ ...content, < EnableTripEnquiry allData={allData} /> ] }, 12 );