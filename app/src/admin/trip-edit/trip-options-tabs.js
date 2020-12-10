import { useState, useEffect } from '@wordpress/element';
import { TextControl, PanelRow, PanelBody, Button, TabPanel, Disabled, Notice, ToggleControl, FormTokenField } from '@wordpress/components';
import { applyFilters, addFilter } from '@wordpress/hooks';
import { useSelect, dispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { sprintf, _n, __ } from '@wordpress/i18n';

import { ReactSortable } from 'react-sortablejs';
import {alignJustify } from '@wordpress/icons';

import ErrorBoundary from '../../ErrorBoundry/ErrorBoundry';

const WPTravelTripOptionsTabsContent = () => {
    const allData = useSelect((select) => {
        return select('WPTravel/TripEdit').getAllStore()
    }, []);
    const { updateTripData } = dispatch('WPTravel/TripEdit');

    const updateTabOption = (key, value, _tabIndex) => {

        const { trip_tabs } = allData;

        let _allTabs = trip_tabs;
        _allTabs[_tabIndex][key] = value

        updateTripData({
            ...allData,
            trip_tabs: [..._allTabs]
        })
    }
    const { trip_tabs, id, use_global_tabs } = allData;
    const sortTabs = (sortedTabs) => {
        updateTripData({
            ...allData, // allData
            trip_tabs: sortedTabs
        })
    }
    let tabsContent = () => {
        if ( typeof trip_tabs !== 'undefined' ) {
            return ( <div className="wp-travel-sortable-component"><ReactSortable
                list={trip_tabs}
                setList={sortedTabs => sortTabs(sortedTabs)}
                handle=".components-panel__icon"
                >
                {
                    trip_tabs.map(function (tab, tabIndex) {
                        return <PanelBody
                            icon= {alignJustify}
                            title={tab.label ? tab.label : tab.default_label }
                            initialOpen={false}
                        >
                            <PanelRow>
                                <label>{__('Global Trip Title', 'wp-travel')}</label>
                                <TextControl
                                    value={tab.default_label}
                                    disabled={true}

                                />
                            </PanelRow>
                            <PanelRow>
                                <label>{__('Custom Trip Title', 'wp-travel')}</label>
                                <TextControl
                                    value={tab.label}
                                    placeholder={tab.default_label }
                                    onChange={
                                        (e) => updateTabOption('label', e, tabIndex)
                                    }
                                    disabled={use_global_tabs == 'yes' ? true : false}
                                />
                            </PanelRow>
                            {use_global_tabs !== 'yes' && 
                                <PanelRow>
                                    <label>{__('Display', 'wp-travel')}</label>
                                    <ToggleControl
                                        checked={tab.show_in_menu == 'yes'}
                                        onChange={
                                            (e) => updateTabOption('show_in_menu', tab.show_in_menu == 'yes' ? 'no' : 'yes', tabIndex)
                                        }
                                        disabled={use_global_tabs == 'yes' ? true : false}
                                    />
                                </PanelRow>
                            }

                        </PanelBody>
                    })
                }
            </ReactSortable></div>
            )
        }

    }
    
    return <ErrorBoundary>
        <div className="wp-travel-trip-tabs">

            {applyFilters('wp_travel_itinerary_custom_tabs', '', id, allData, updateTripData)}
            <PanelRow>
                <label>{__('Use Global Tabs Layout', 'wp-travel')}</label>
                <ToggleControl
                    value={use_global_tabs}
                    checked={use_global_tabs == 'yes' ? true : false}
                    onChange={
                        (use_global_tabs) => {
                            updateTripData({
                                ...allData,
                                use_global_tabs: use_global_tabs ? 'yes' : 'no'
                            })
                        }
                    }
                />
            </PanelRow>
            { 'yes' == use_global_tabs ? 
                <Disabled>{tabsContent()}</Disabled> :
                tabsContent()
            }
        </div>
    </ErrorBoundary>;
}

addFilter('wp_travel_itinerary_custom_tabs', 'wp_travel', (content, id, allData) => {
    const { trip_code } = allData;

    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Need Additional Tabs ?', 'wp-travel')}</strong>
                <br />
                {__('By upgrading to Pro, you can get trip specific custom tabs addition options with customized content and sorting !', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
                        &nbsp;&nbsp;
                        <a className="button button-primary" target="_blank" href="https://wptravel.io/downloads/wp-travel-utilities/">{__('Get WP Travel Utilities Addon', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content
    ]
    return content
}, 9);

const WPTravelTripOptionsTabs = () => {
    return <div className="wp-travel-ui wp-travel-ui-card wp-travel-ui-card-no-border"><WPTravelTripOptionsTabsContent /></div>
}

export default WPTravelTripOptionsTabs;