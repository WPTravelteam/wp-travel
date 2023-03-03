import { applyFilters } from '@wordpress/hooks';
import { useSelect, dispatch } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl, RadioControl, SelectControl } from '@wordpress/components';
import { VersionCompare } from '../../../fields/VersionCompare'
import Select from '../../UI/Select';
import Tooltip from '../../UI/Tooltip';

import ErrorBoundary from '../../../../ErrorBoundry/ErrorBoundry';

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
        disable_admin_review,
        options
    } = allData;

    // let tripDataListingOptions = [];

    // if ("undefined" != typeof options) {
    //     if ("undefined" != typeof options.trips) {
    //         tripDataListingOptions = options.trips;
    //     }
    // }
    // console.log(options)

    // let selectedTripDataListing = tripDataListingOptions.filter((opt) => {
    //     return opt.value == trip_date_listing;
    // });

    // const returnParent = () => {
    //         this.child.parent = this;
    //         delete this.init;
    //         return this;
    // }

    let switch_to_react = 'undefined' != typeof wp_travel_switch_to_react ? wp_travel_switch_to_react : 'no'
    return (
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {__("Trips Settings", "wp-travel")}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More trips settings according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <ErrorBoundary>
                    {applyFilters('wp_travel_tab_content_before_trips', [], allData)}
                    <PanelRow>
                        <label>{__('Hide related trips', 'wp-travel')}</label>
                        <div className="wp-travel-field-value">
                            <ToggleControl
                                checked={hide_related_itinerary == 'yes'}
                                onChange={() => {
                                    updateSettings({
                                        ...allData,
                                        hide_related_itinerary: 'yes' == hide_related_itinerary ? 'no' : 'yes'
                                    })
                                }}
                            />
                            <p className="description">{__('This will hide your related trips.', 'wp-travel')}</p>
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
                            {__('Trip date listing', 'wp-travel')}
                            <Tooltip text={__('List date while booking or display calendar with available dates. Note: Date option only works for fixed departure trips.', 'wp-travel')}>
                                <span><i className='fa fa-info-circle'></i></span>
                            </Tooltip>
                        </label>

                        <div className="wp-travel-field-value">
                            <Select
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
                            {/* <Select
                                theme={(theme) => ({
                                    ...theme,
                                    borderRadius: ".5rem",
                                    colors: {
                                        ...theme.colors,
                                        primary25: 'rgb(231, 236, 243)',
                                        primary50: 'rgb(174, 186, 202)',
                                        primary: 'rgb(31, 150, 75)',
                                    },
                                })}
                                options={[
                                    {
                                        label: __('Calendar', 'wp-travel'),
                                        value: 'calendar',
                                        init : returnParent
                                    }, {
                                        label: __('Dates', 'wp-travel'),
                                        value: 'dates',
                                        init : returnParent
                                    }
                                ]}
                                value={trip_date_listing == "dates" && trip_date_listing}
                                onChange={(value) => {
                                        updateSettings({
                                            ...allData,
                                            trip_date_listing: value
                                        })
                                    }
                                }
                            /> */}
                            <p className="description"></p>
                        </div>
                    </PanelRow>
                    {/* } */}
                    <PanelRow>
                        <label>{__('Enable Expired Trip Option', 'wp-travel')}</label>
                        <div className="wp-travel-field-value">
                            <ToggleControl
                                checked={enable_expired_trip_option == 'yes'}
                                onChange={() => {
                                    updateSettings({
                                        ...allData,
                                        enable_expired_trip_option: 'yes' == enable_expired_trip_option ? 'no' : 'yes'
                                    })
                                }}
                            />
                            <p className="description">{__('This will enable expired trip set as Expired or delete.', 'wp-travel')}</p>
                        </div>
                    </PanelRow>
                    {'undefined' !== typeof enable_expired_trip_option && 'yes' === enable_expired_trip_option &&
                        <PanelRow>
                            <label>{__('If expired, trip set to expired/delete', 'wp-travel')}</label>
                            <div className="wp-travel-field-value">
                                <Select
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
                        <label>{__('Disable Star Rating For Admin', 'wp-travel')}</label>
                        <div className="wp-travel-field-value">
                            <ToggleControl
                                checked={disable_admin_review == 'yes'}
                                onChange={() => {
                                    updateSettings({
                                        ...allData,
                                        disable_admin_review: 'yes' == disable_admin_review ? 'no' : 'yes'
                                    })
                                }}
                            />
                            <p className="description">{__('Enable to not allow star rating to admin', 'wp-travel')}</p>
                        </div>
                    </PanelRow>

                    {applyFilters('wp_travel_tab_content_after_trips', [])}
                </ErrorBoundary>
            </div>
        </>
    );
}