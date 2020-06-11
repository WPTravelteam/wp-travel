import { useState, useEffect } from '@wordpress/element';
import { TextControl, PanelRow, PanelBody, Button, TabPanel,Notice, ToggleControl, FormTokenField} from '@wordpress/components';
import { applyFilters, addFilter } from '@wordpress/hooks';
import { useSelect, dispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { sprintf, _n, __} from '@wordpress/i18n';

const WPTravelTripOptionsMiscContent = () => {
    const allData = useSelect((select) => {
        return select('WPTravel/TripEdit').getAllStore()
    }, []);
    const { updateTripData } = dispatch('WPTravel/TripEdit');

    
    // console.log( allData )
    const { use_global_trip_enquiry_option, enable_trip_enquiry_option } = allData;

    return <>
        <div className="wp-travel-trip-misc">
            <h3>{__( 'Trip Enquiry', 'wp-travel' ) }</h3>
            {/* {applyFilters( 'wp_travel_itinerary_custom_tabs', '', id, allData )} */}
            <PanelRow>
                <label>{ __( 'Global Trip Enquiry Option', 'wp-travel' ) }</label>
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
                    <label>{ __( 'Trip Enquiry', 'wp-travel' ) }</label>
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
    </>;
}

const WPTravelTripOptionsMisc = () => {
    return <div className="wp-travel-ui wp-travel-ui-card wp-travel-ui-card-no-border"><WPTravelTripOptionsMiscContent /></div>
}

export default WPTravelTripOptionsMisc;