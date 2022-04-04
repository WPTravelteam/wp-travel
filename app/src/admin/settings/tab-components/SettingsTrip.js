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
        trip_date_listing,
        options
        } = allData;

    let switch_to_react = 'undefined' != typeof wp_travel_switch_to_react ? wp_travel_switch_to_react : 'no'
    return <div className="wp-travel-ui wp-travel-ui-card settings-general">
        <h2>{ __( 'Trip Settings', 'wp-travel' ) }</h2>
        <ErrorBoundary>
            {applyFilters( 'wp_travel_tab_content_before_trips', [], allData ) }
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
            
                    { 'no' == switch_to_react &&
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
                    }
                </>
            }
            {/* {  _wp_travel.dev_mode && */}
                <PanelRow>
                    <label>{ __( 'Trip date listing', 'wp-travel' ) }</label>
                    
                    <div className="wp-travel-field-value">
                        <SelectControl
                            value={ trip_date_listing }
                            options={ [
                                {
                                    label: __( 'Calendar', 'wp-travel' ),
                                    value:'calendar'
                                }, {
                                    label: __( 'Dates', 'wp-travel' ),
                                    value:'dates'
                                }
                            ] }
                            onChange={ ( value ) => {
                                updateSettings({
                                    ...allData,
                                    trip_date_listing: value
                                })
                            } }
                        />
                        <p className="description">{__( 'List date while booking or display calendar with available dates. Note: Date option only works for fixed departure trips.', 'wp-travel' )}</p>
                    </div>
                </PanelRow>
            {/* } */}
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
                    <p className="description">{__( 'This will enable expired trip set as Expired or delete.', 'wp-travel' )}</p>
                </div>
            </PanelRow>
            { 'undefined' !== typeof enable_expired_trip_option && 'yes' === enable_expired_trip_option &&
                <PanelRow>
                    <label>{ __( 'If expired, trip set to expired/delete', 'wp-travel' ) }</label>
                    <div className="wp-travel-field-value">
                    <SelectControl
                        value={ expired_trip_set_to }
                        options={ [
                            {
                                label: __( 'Expired', 'wp-travel' ),
                                value:'expired'
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
                    </div>
                </PanelRow>
            }

            {applyFilters( 'wp_travel_tab_content_after_trips', [] )}
        </ErrorBoundary>
    </div>
}