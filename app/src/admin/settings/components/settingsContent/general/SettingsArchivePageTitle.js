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
                    {_wp_travel.setting_strings.archive_page_title.archive_page_title_settings}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More archive page title settings according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
            <PanelRow>
                <label>{_wp_travel.setting_strings.archive_page_title.hide_plugin_archive_page_title}</label>
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
                    <p className="description">{_wp_travel.setting_strings.archive_page_title.hide_plugin_archive_page_title_note}</p>
                </div>
            </PanelRow>
            </div>
        </>
    )
}