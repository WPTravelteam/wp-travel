import React from 'react'
import { applyFilters, addFilter } from '@wordpress/hooks';
import { useSelect, dispatch } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow } from '@wordpress/components';
import Tooltip from '../../UI/Tooltip';

import Select from '../../UI/Select'

export default () => {
    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);
    const {
        // cart_page_id,
        checkout_page_id,
        dashboard_page_id,
        enable_block,
        options } = allData;

    const { updateSettings } = dispatch('WPTravel/Admin');

    // options
    let pageOptions = []

    if ('undefined' != typeof options) {
        if ('undefined' != typeof options.page_list) {
            pageOptions = options.page_list
        }
    }

    // selected options.
    let selectedCheckoutPage = pageOptions.filter(opt => { return opt.value == checkout_page_id })
    let selectedDashboardPage = pageOptions.filter(opt => { return opt.value == dashboard_page_id })
    return (
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                {_wp_travel.setting_strings.pages.pages_setting}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More pages settings according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <PanelRow>
                    <label>
                        {_wp_travel.setting_strings.pages.checkout_page}
                        <Tooltip
                            text={_wp_travel.setting_strings.pages.checkout_page_tooltip}
                        >
                            <span>
                                <i className="fa fa-info-circle" aria-hidden="true"></i>
                            </span>
                        </Tooltip>
                    </label>
                    <div id="wp-travel-checkout-page" className="wp-travel-field-value">
                        <div className="wp-travel-select-wrapper">
                            <Select
                                options={pageOptions}
                                value={'undefined' != typeof selectedCheckoutPage[0] && 'undefined' != typeof selectedCheckoutPage[0].label ? selectedCheckoutPage[0] : []}
                                onChange={(data) => {
                                    if ('' !== data) {
                                        updateSettings({
                                            ...allData,
                                            checkout_page_id: data.value
                                        })
                                    }
                                }}
                            />
                        </div>
                    </div>
                </PanelRow>
                <PanelRow>
                    <label>
                        {_wp_travel.setting_strings.pages.dashboard_page}
                        <Tooltip
                            text={_wp_travel.setting_strings.pages.dashboard_page_tooltip}
                        >
                            <span>
                                <i className="fa fa-info-circle" aria-hidden="true"></i>
                            </span>
                        </Tooltip>
                    </label>
                    <div id="wp-travel-dashboard-page" className="wp-travel-field-value">
                        <div className="wp-travel-select-wrapper">
                            <Select
                                options={pageOptions}
                                value={'undefined' != typeof selectedDashboardPage[0] && 'undefined' != typeof selectedDashboardPage[0].label ? selectedDashboardPage[0] : []}
                                onChange={(data) => {
                                    if ('' !== data) {
                                        updateSettings({
                                            ...allData,
                                            dashboard_page_id: data.value
                                        })
                                    }
                                }}
                            />
                        </div>
                    </div>
                </PanelRow>
                {applyFilters('wp_travel_settings_after_general_fields', [])}
            </div>
        </>
    )
}

// Custom Tooltip for Thank You Page
addFilter('wp_travel_settings_after_pages_fields', 'wp_travel', () => {
    return (
        <Tooltip
            text={  _wp_travel.setting_strings.pages.thankyou_page_tooltip }
        >
            <span>
                <i className="fa fa-info-circle" aria-hidden="true"></i>
            </span>
        </Tooltip>
    )
})