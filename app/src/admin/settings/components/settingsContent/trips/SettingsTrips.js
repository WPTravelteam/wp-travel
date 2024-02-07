import { applyFilters } from '@wordpress/hooks';
import { useSelect, dispatch } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl, RadioControl, SelectControl } from '@wordpress/components';
import { VersionCompare } from '../../../../fields/VersionCompare'
import Select from '../../UI/Select';
import Tooltip from '../../UI/Tooltip';

import ErrorBoundary from '../../../../../ErrorBoundry/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const { updateSettings } = dispatch('WPTravel/Admin');
    const {
        wp_travel_switch_to_react,
        hide_related_itinerary,
        enable_trip_enquiry_option,
        enable_expired_trip_option,
        expired_trip_set_to,
        trip_pricing_options_layout,
        trip_date_listing,
        disable_admin_review,
        options
    } = allData;

    let switch_to_react = 'undefined' != typeof wp_travel_switch_to_react ? wp_travel_switch_to_react : 'no'
    return (
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {_wp_travel.setting_strings.trip_settings.trips_settings}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More trips settings according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <ErrorBoundary>
                    {applyFilters('wp_travel_tab_content_before_trips', [], allData)}
                    <PanelRow>
                        <label>{_wp_travel.setting_strings.trip_settings.enable_trip_enquiry}</label>
                        <div id="wp-travel-misc-enable-trip-inquiry" className="wp-travel-field-value">
                            <ToggleControl
                                checked={enable_trip_enquiry_option == 'yes'}
                                onChange={() => {
                                    updateSettings({
                                        ...allData,
                                        enable_trip_enquiry_option: 'yes' == enable_trip_enquiry_option ? 'no' : 'yes'
                                    })
                                }}
                            />
                            {/* <p className="description">{__( 'Enable test mode to make test payment.', 'wp-travel' )}</p> */}
                        </div>
                    </PanelRow>
                    <PanelRow>
                        <label>{_wp_travel.setting_strings.trip_settings.hide_related_trips}</label>
                        <div id="wp-travel-trips-settings-hide-related-trips" className="wp-travel-field-value">
                            <ToggleControl
                                checked={hide_related_itinerary == 'yes'}
                                onChange={() => {
                                    updateSettings({
                                        ...allData,
                                        hide_related_itinerary: 'yes' == hide_related_itinerary ? 'no' : 'yes'
                                    })
                                }}
                            />
                            <p className="description">{_wp_travel.setting_strings.trip_settings.hide_related_trips_note}</p>
                        </div>
                    </PanelRow>


                    {'undefined' != typeof options && 'undefined' != typeof options.wp_travel_user_since && VersionCompare(options.wp_travel_user_since, '4.0.0', '<') &&
                        <>

                            {'no' == switch_to_react &&
                                <PanelRow>
                                    <label>{__('Trip Pricing Options Listing', 'wp-travel')}</label>
                                    <div className="wp-travel-field-value">
                                        <RadioControl
                                            selected={trip_pricing_options_layout}
                                            options={[
                                                { label: __('List by pricing options ( Default )', 'wp-travel'), value: 'by-pricing-option' },
                                                { label: __('List by fixed departure dates', 'wp-travel'), value: 'by-date' },
                                            ]}
                                            onChange={(option) => {
                                                updateSettings({
                                                    ...allData,
                                                    trip_pricing_options_layout: option
                                                })
                                            }}
                                        />
                                        <p className="description">{__('This options will control how you display trip dates and prices.', 'wp-travel')}</p>
                                    </div>
                                </PanelRow>
                            }
                        </>
                    }
                    {/* {  _wp_travel.dev_mode && */}
                    <PanelRow>
                        <label>
                            {_wp_travel.setting_strings.trip_settings.trip_date_listing}
                            <Tooltip text="{_wp_travel.setting_strings.trip_settings.trip_date_listing_tooltip}">
                                <span><i className='fa fa-info-circle'></i></span>
                            </Tooltip>
                        </label>

                        <div id="wp-travel-trips-settings-trip-date-listing" className="wp-travel-field-value">
                            <SelectControl
                                value={trip_date_listing}
                                options={[
                                    {
                                        label: __('Calendar', 'wp-travel'),
                                        value: 'calendar'
                                    }, {
                                        label: __('Dates', 'wp-travel'),
                                        value: 'dates'
                                    }
                                ]}
                                onChange={(value) => {
                                    updateSettings({
                                        ...allData,
                                        trip_date_listing: value
                                    })
                                }}
                            />
                            <p className="description"></p>
                        </div>
                    </PanelRow>
                    {/* } */}
                    <PanelRow>
                        <label>{_wp_travel.setting_strings.trip_settings.enable_expired_trip_option}</label>
                        <div id="wp-travel-trips-settings-enable-expired-trip" className="wp-travel-field-value">
                            <ToggleControl
                                checked={enable_expired_trip_option == 'yes'}
                                onChange={() => {
                                    updateSettings({
                                        ...allData,
                                        enable_expired_trip_option: 'yes' == enable_expired_trip_option ? 'no' : 'yes'
                                    })
                                }}
                            />
                            <p className="description">{_wp_travel.setting_strings.trip_settings.enable_expired_trip_option_note}</p>
                        </div>
                    </PanelRow>
                    {'undefined' !== typeof enable_expired_trip_option && 'yes' === enable_expired_trip_option &&
                        <PanelRow>
                            <label>{_wp_travel.setting_strings.trip_settings.if_expired_trip_set_to_expired_delete}</label>
                            <div id="wp-travel-trips-settings-if-expired-trip" className="wp-travel-field-value">
                                <SelectControl
                                    value={expired_trip_set_to}
                                    options={[
                                        {
                                            label: __('Expired', 'wp-travel'),
                                            value: 'expired'
                                        }, {
                                            label: __('Delete', 'wp-travel'),
                                            value: 'delete'
                                        }
                                    ]}
                                    onChange={(value) => {
                                        updateSettings({
                                            ...allData,
                                            expired_trip_set_to: value
                                        })
                                    }}
                                />
                            </div>
                        </PanelRow>
                    }
                    <PanelRow>
                        <label>{_wp_travel.setting_strings.trip_settings.disable_star_rating_for_admin}</label>
                        <div id="wp-travel-trips-settings-disable-star-rating" className="wp-travel-field-value">
                            <ToggleControl
                                checked={disable_admin_review == 'yes'}
                                onChange={() => {
                                    updateSettings({
                                        ...allData,
                                        disable_admin_review: 'yes' == disable_admin_review ? 'no' : 'yes'
                                    })
                                }}
                            />
                            <p className="description">{_wp_travel.setting_strings.trip_settings.disable_star_rating_for_admin_note}</p>
                        </div>
                    </PanelRow>

                    {applyFilters('wp_travel_trip_setting_content_after_trips', [], allData)}
                </ErrorBoundary>
            </div>
        </>
    );
}