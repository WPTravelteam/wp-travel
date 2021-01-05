import { applyFilters } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl, RadioControl, SelectControl } from '@wordpress/components';
import Select from 'react-select'
import {VersionCompare} from '../../fields/VersionCompare'

import ErrorBoundary from '../../../ErrorBoundry/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const { updateSettings } = dispatch('WPTravel/Admin');
    const {
        wp_travel_switch_to_react,
        hide_related_itinerary,
        enable_expired_trip_option,
        expired_trip_set_to,
        trip_pricing_options_layout,
        calender_view,
        options
        } = allData;

    let switch_to_react = 'undefined' != typeof wp_travel_switch_to_react ? wp_travel_switch_to_react : 'no'
    return <div className="wp-travel-ui wp-travel-ui-card settings-general">
        <h2>{ __( 'Trip Settings', 'wp-travel' ) }</h2>
        <ErrorBoundary>
            {applyFilters( 'wp_travel_tab_content_before_trips', [] ) }
            <PanelRow>
                <label>{ __( 'Hide related trips', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <ToggleControl
                        checked={ hide_related_itinerary == 'yes' }
                        onChange={ () => {
                            updateSettings({
                                ...allData,
                                hide_related_itinerary: 'yes' == hide_related_itinerary ? 'no': 'yes'
                            })
                        } }
                    />
                    <p className="description">{__( 'This will hide your related trips.', 'wp-travel' )}</p>
                </div>
            </PanelRow>
            

            { 'undefined' != typeof options && 'undefined' != typeof options.wp_travel_user_since && VersionCompare( options.wp_travel_user_since, '4.0.0', '<' ) && 
                <>
            
                    { 'no' == switch_to_react ?
                        <PanelRow>
                            <label>{ __( 'Trip Pricing Options Listing', 'wp-travel' ) }</label>
                            <div className="wp-travel-field-value">
                                <RadioControl
                                    selected={ trip_pricing_options_layout }
                                    options={ [
                                        { label: __( 'List by pricing options ( Default )', 'wp-travel' ), value: 'by-pricing-option' },
                                        { label: __( 'List by fixed departure dates', 'wp-travel' ), value: 'by-date' },
                                    ] }
                                    onChange={ ( option ) => { 
                                        updateSettings({
                                            ...allData,
                                            trip_pricing_options_layout: option
                                        })
                                    } }
                                />
                                <p className="description">{__( 'This options will control how you display trip dates and prices.', 'wp-travel' )}</p>
                            </div>
                        </PanelRow>
                        :
                        <>
                        {/* Need to build frontend functionality to uncomment this feature. */}
                        {/* <PanelRow>
                            <label>{ __( 'Trip Dates Calendar View', 'wp-travel' ) }</label>
                            <div className="wp-travel-field-value">
                                <ToggleControl
                                    checked={ calender_view == 'yes' }
                                    onChange={ () => {
                                        updateSettings({
                                            ...allData,
                                            calender_view: 'yes' == calender_view ? 'no': 'yes'
                                        })
                                    } }
                                />
                                <p className="description">{__( 'Enable/Disable calender view on the booking tab of trip page.', 'wp-travel' )}</p>
                            </div>
                        </PanelRow> */}
                        </>
                    }
                </>
            }
            <PanelRow>
                <label>{ __( 'Enable Expired Trip Option', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <ToggleControl
                        checked={ enable_expired_trip_option == 'yes' }
                        onChange={ () => {
                            updateSettings({
                                ...allData,
                                enable_expired_trip_option: 'yes' == enable_expired_trip_option ? 'no': 'yes'
                            })
                        } }
                    />
                    <p className="description">{__( 'This will enable expired trip set as Draft or delete.', 'wp-travel' )}</p>
                </div>
            </PanelRow>
            { 'undefined' !== typeof enable_expired_trip_option && 'yes' === enable_expired_trip_option &&
                <PanelRow>
                    <label>{ __( 'If expired, trip set to draft/delete', 'wp-travel' ) }</label>
                    <SelectControl
                        value={ expired_trip_set_to }
                        options={ [
                            {
                                label: __( 'Draft', 'wp-travel' ),
                                value:'draft'
                            }, {
                                label: __( 'Delete', 'wp-travel' ),
                                value:'delete'
                            }
                        ] }
                        onChange={ ( value ) => {
                            updateSettings({
                                ...allData,
                                expired_trip_set_to: value
                            })
                        } }
                    />
                </PanelRow>
            }

            {applyFilters( 'wp_travel_tab_content_after_trips', [] )}
        </ErrorBoundary>
    </div>
}