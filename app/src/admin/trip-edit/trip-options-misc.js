import { useState, useEffect } from '@wordpress/element';
import { TextControl, PanelRow, PanelBody, Button, TabPanel,Notice, ToggleControl, FormTokenField} from '@wordpress/components';
import { applyFilters, addFilter } from '@wordpress/hooks';
import { useSelect, dispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { sprintf, _n, __} from '@wordpress/i18n';
import ErrorBoundary from '../../ErrorBoundry/ErrorBoundry';
const __i18n = {
	..._wp_travel_admin.strings
}
const WPTravelTripOptionsMiscContent = () => {
    const allData = useSelect((select) => {
        return select('WPTravel/TripEdit').getAllStore()
    }, []);
    const { updateTripData } = dispatch('WPTravel/TripEdit');

    const { use_global_trip_enquiry_option, enable_trip_enquiry_option } = allData;

    return <ErrorBoundary>
        <div className="wp-travel-trip-misc">
            <h3>{ __i18n.trip_enquiry }</h3>
            {/* {applyFilters( 'wp_travel_itinerary_custom_tabs', '', id, allData )} */}
            <PanelRow>
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
            </PanelRow>
            {use_global_trip_enquiry_option !== 'yes' &&
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
           
        </div>
    </ErrorBoundary>;
}

const WPTravelTripOptionsMisc = () => {
    return <WPTravelTripOptionsMiscContent />
}

export default WPTravelTripOptionsMisc;

const TripEnquiryTitle = () => {
    return <h3>{ __i18n.trip_enquiry }</h3>
}

addFilter('wptravel_trip_edit_tab_content_misc', 'wp_travel', (content, allData) => {
    return [
        ...content,
        < TripEnquiryTitle  />
    ]
    
}, 10 );

const GlobalTripEnquiryOption = ({allData}) => {
    const { updateTripData } = dispatch('WPTravel/TripEdit');

    const { use_global_trip_enquiry_option, enable_trip_enquiry_option } = allData;
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
    </PanelRow>
}
addFilter('wptravel_trip_edit_tab_content_misc', 'wp_travel', (content, allData) => {
    return [
        ...content,
        < GlobalTripEnquiryOption allData={allData} />
    ]
    
}, 11 );