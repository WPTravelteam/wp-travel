import { applyFilters } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl, RadioControl } from '@wordpress/components';
import Select from 'react-select'
import {VersionCompare} from '../../fields/VersionCompare'

import ErrorBoundary from '../../error/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const { updateSettings } = dispatch('WPTravel/Admin');
    const {
        wp_travel_switch_to_react,
        hide_related_itinerary,
        enable_multiple_travellers,
        trip_pricing_options_layout,
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
            <PanelRow>
                <label>{ __( 'Enable multiple travelers', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <ToggleControl
                        checked={ enable_multiple_travellers == 'yes' }
                        onChange={ () => {
                            updateSettings({
                                ...allData,
                                enable_multiple_travellers: 'yes' == enable_multiple_travellers ? 'no': 'yes'
                            })
                        } }
                    />
                    <p className="description">{__( 'Collect multiple travelers information from checkout page.', 'wp-travel' )}</p>
                </div>
            </PanelRow>

            { 'no' == switch_to_react && 'undefined' != typeof options && 'undefined' != typeof options.wp_travel_user_since && VersionCompare( options.wp_travel_user_since, '4.0.0', '<' ) &&
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
            {applyFilters( 'wp_travel_tab_content_after_trips', [] )}
        </ErrorBoundary>
    </div>
}