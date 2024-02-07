import { applyFilters } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelBody, PanelRow, ToggleControl, TextControl, RadioControl } from '@wordpress/components';
import Select from 'react-select'
import { VersionCompare } from '../../../../fields/VersionCompare'
import { alignJustify } from '@wordpress/icons';
import { ReactSortable } from "react-sortablejs";
import { useRef } from '@wordpress/element'

import ErrorBoundary from '../../../../../ErrorBoundry/ErrorBoundry';

export default () => {
    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const { updateSettings, updateRequestSending } = dispatch('WPTravel/Admin');
    const {
        global_tab_settings,
        options
    } = allData;

    const updateTabOption = (key, value, _tabIndex) => {

        const { global_tab_settings } = allData;

        let _allTabs = global_tab_settings;
        _allTabs[_tabIndex][key] = value

        updateSettings({
            ...allData,
            global_tab_settings: [..._allTabs]
        })
    }
    const SortTabs = (sortedPricing) => {
        updateSettings({
            ...allData, // allData
            global_tab_settings: sortedPricing
        })
    }
    const array_move = (arr, old_index, new_index) => {
        if (new_index >= arr.length) {
            var k = new_index - arr.length + 1;
            while (k--) {
                arr.push(undefined);
            }
        }
        arr.splice(new_index, 0, arr.splice(old_index, 1)[0]);
        return arr; // for testing
    };
    // mount whatever plugins you'd like to. These are the only current options.
    return (
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {_wp_travel.setting_strings.tabs.tabs_settings}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More tabs settings according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <ErrorBoundary>
                    {applyFilters('wp_travel_custom_global_tabs', [], allData)}

                    <div id="wp-travel-tabs" className="wp-travel-block-section wp-travel-block-sortable">
                        <ReactSortable
                            list={global_tab_settings}
                            setList={sorted => SortTabs(sorted)}
                            // handle="span.dashicons.dashicons-menu"
                            handle=".wp-travel-block-sortable .components-panel__icon"
                        >
                            {global_tab_settings.map(function (tab, tabIndex) {
                                return <div className="wp-travel-block-section" style={{ position: 'relative' }}>
                                    {/* <span className="dashicons dashicons-menu"></span> */}
                                    <div className={`wptravel-swap-list`}>
                                        <button
                                            disabled={0 === tabIndex}
                                            onClick={(e) => {
                                                let sorted = array_move(global_tab_settings, tabIndex, tabIndex - 1)
                                                SortTabs(sorted)
                                                updateRequestSending(true); // Temp fixes to reload the content.
                                                updateRequestSending(false);
                                            }}><i className="dashicons dashicons-arrow-up"></i></button>
                                        <button
                                            disabled={(global_tab_settings.length - 1) === tabIndex}
                                            onClick={(e) => {
                                                let sorted = array_move(global_tab_settings, tabIndex, tabIndex + 1)
                                                SortTabs(sorted)
                                                updateRequestSending(true);
                                                updateRequestSending(false);
                                            }}><i className="dashicons dashicons-arrow-down"></i></button>
                                    </div>
                                    <PanelBody
                                        icon={alignJustify}
                                        title={tab.label ? tab.label : tab.default_label}
                                        initialOpen={false}
                                    >
                                        <PanelRow>
                                            <label>{_wp_travel.setting_strings.tabs.default_tab_title}</label>
                                            <TextControl
                                                value={tab.default_label}
                                                disabled={true}
                                            />
                                        </PanelRow>
                                        <PanelRow>
                                            <label>{_wp_travel.setting_strings.tabs.custom_tab_title}</label>
                                            <TextControl
                                                value={tab.label}
                                                placeholder={tab.default_label}
                                                onChange={
                                                    (value) => {
                                                        updateTabOption('label', value, tabIndex)
                                                    }
                                                }
                                            />
                                        </PanelRow>

                                        <PanelRow>
                                            <label>{_wp_travel.setting_strings.tabs.custom_tab_title}</label>
                                            <ToggleControl
                                                checked={tab.show_in_menu == 'yes'}
                                                onChange={
                                                    (e) => updateTabOption('show_in_menu', tab.show_in_menu == 'yes' ? 'no' : 'yes', tabIndex)
                                                }
                                            />
                                        </PanelRow>

                                    </PanelBody>
                                </div>
                            })}
                        </ReactSortable>
                    </div>

                    {applyFilters('wp_travel_tab_content_after_tabs', [])}
                </ErrorBoundary>
            </div>
        </>
    );
}