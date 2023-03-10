import React from 'react'
import { useSelect, dispatch } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl } from '@wordpress/components';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);
    const {
        hide_plugin_archive_page_title,
        options } = allData;

    const { updateSettings } = dispatch('WPTravel/Admin');

    return (
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {__("Archive Page Title Settings", "wp-travel")}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More archive page title settings according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
            <PanelRow>
                <label>{__('Hide Plugin Archive Page Title', 'wp-travel')}</label>
                <div id="wp-travel-hide-plugin-archive-page-title" className="wp-travel-field-value">
                    <ToggleControl
                        checked={hide_plugin_archive_page_title == 'yes'}
                        onChange={() => {
                            updateSettings({
                                ...allData,
                                hide_plugin_archive_page_title: 'yes' == hide_plugin_archive_page_title ? 'no' : 'yes'
                            })
                        }}
                    />
                    <p className="description">{__('This option will hide archive title displaying from plugin.', 'wp-travel')}</p>
                </div>
            </PanelRow>
            </div>
        </>
    )
}