import { PanelRow, ToggleControl, TextControl,TextareaControl, SelectControl, Dropdown, DateTimePicker, Notice } from '@wordpress/components';
import { useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { useSelect, select, dispatch, withSelect, forwardRef } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { render, useEffect } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';

import './store/enquiry-store';
import SaveEnquiry from './sub-components/SaveEnquiry'

//for disabling the publish button before saving the enquiry
const toggleDisablePostUpdate = (isDisabled = false) => {
    if (jQuery('#submitpost').find('#wp-travel-post-disable-message').length < 1 && isDisabled) {
        jQuery('#submitpost').append(`<div id="wp-travel-post-disable-message">${__('* Please save enquiry options first.')}</div>`)
        jQuery('#major-publishing-actions #publishing-action input#publish').attr('disabled', 'disabled')
        jQuery('#minor-publishing #save-action input#save-post').attr('disabled', 'disabled')
    } else if (!isDisabled) {
        jQuery('#submitpost').find('#wp-travel-post-disable-message').remove();
        jQuery('#major-publishing-actions #publishing-action input#publish').removeAttr('disabled')
        jQuery('#minor-publishing #save-action input#save-post').removeAttr('disabled')
    }
}
const App = () => {
    useEffect(() => {
        const { setEnquiry } = select('WPTravel/Enquiry');
        setEnquiry(_wp_travel.postID);
    }, []);

    // Get All Data.
    const allData = useSelect((select) => {
        return select('WPTravel/Enquiry').getAllStore()
}, []);

    //change the publish button state as per the data changes
    toggleDisablePostUpdate(allData.has_state_changes);

    const { trips, wp_travel_enquiry_name, wp_travel_enquiry_email, wp_travel_enquiry_query,wp_travel_trip_id } = allData;
    let allTrips = 'undefined' != typeof trips ? trips : [];
    let tripNames = []
    if (allTrips.length > 0) {
        tripNames = allTrips.map(allTrip => {
            return { id: allTrip.id, title: allTrip.title }
        })
    }
    let tripnames = tripNames.map(tripSuggestion => { return { label: tripSuggestion.title, value: tripSuggestion.id } }); 
    const { updateEnquiry } = dispatch('WPTravel/Enquiry'); 
    return (
        <div>

            <PanelRow>
                <label><strong>{__('Trip', 'wp-travel')}</strong></label>
                <div >
                    <SelectControl
                        value={wp_travel_trip_id}
                        options={tripnames}
                        onChange={ 
                            (value) => {
                                updateEnquiry({
                                    ...allData,
                                    wp_travel_trip_id: value
                                })
                            }
                        }
                       
                    />
                </div>
            </PanelRow>
            <PanelRow>
                <label><strong>{__('*Full Name', 'wp-travel')}</strong></label>
                <div >
                    <TextControl
                        value={wp_travel_enquiry_name} required

                        onChange={
                            (value) => {
                                updateEnquiry({
                                    ...allData,
                                    wp_travel_enquiry_name: value
                                })
                            }
                        }

                    />
                </div>
            </PanelRow>
            <PanelRow>
                <label><strong>{__('*Email', 'wp-travel')}</strong></label>
                <div >
                    <TextControl value={wp_travel_enquiry_email} required 
                        onChange={
                            (value) => {
                                updateEnquiry({
                                    ...allData,
                                    wp_travel_enquiry_email: value
                                })
                            }
                        }
                    />
                </div>
            </PanelRow>
            <PanelRow>
                <label><strong>{__('*Enquiry Message', 'wp-travel')}</strong></label>
                <div >
                    <TextareaControl value={wp_travel_enquiry_query} required rows ="6"
                        onChange={
                            (value) => {
                                updateEnquiry({
                                    ...allData,
                                    wp_travel_enquiry_query: value
                                })
                            }
                        }

                    />
                </div>
            </PanelRow>
            {allData.is_sending_request}
            <SaveEnquiry position="bottom" />
        </div>
    )
};

domReady(function () {
    if ('undefined' !== typeof document.getElementById('wp_travel_enquiries') && null !== document.getElementById('wp_travel_enquiries')) {
        render(<App />, document.getElementById('wp_travel_enquiries'));
    }
});