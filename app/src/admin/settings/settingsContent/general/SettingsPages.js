import React from 'react'
import { applyFilters } from '@wordpress/hooks';
import { useSelect, dispatch } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow } from '@wordpress/components';

import Select from './../../helpers/select-component/Select'

export default () => {
    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);
    const {
        // cart_page_id,
        checkout_page_id,
        dashboard_page_id,
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
                    {__("Pages Settings", "wp-travel")}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More pages settings according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <PanelRow>
                    <label>{__('Checkout Page', 'wp-travel')}</label>
                    <div className="wp-travel-field-value">
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
                        <p className="description">{__('Choose the page to use as checkout page for booking which contents checkout page shortcode [wp_travel_checkout].', 'wp-travel')}</p>
                    </div>
                </PanelRow>
                <PanelRow>
                    <label>{__('Dashboard Page', 'wp-travel')}</label>
                    <div className="wp-travel-field-value">
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
                        <p className="description">{__('Choose the page to use as dashboard page which contents dashboard page shortcode [wp_travel_user_account].', 'wp-travel')}</p>
                    </div>
                </PanelRow>
                {applyFilters('wp_travel_settings_after_general_fields', [])}
            </div>
        </>
    )
}
