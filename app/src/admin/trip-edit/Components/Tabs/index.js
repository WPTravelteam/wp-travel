import { TextControl, PanelRow, PanelBody, Disabled, Notice, ToggleControl, Button } from '@wordpress/components';
import { addFilter } from '@wordpress/hooks';
import { dispatch } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';

import { ReactSortable } from 'react-sortablejs';
import {alignJustify } from '@wordpress/icons';

import ErrorBoundary from '../../../../ErrorBoundry/ErrorBoundry';
const __i18n = {
	..._wp_travel_admin.strings
}

// Single Components for hook callbacks.
const TripTabsNotice = () => {
    return <>
        <Notice isDismissible={false} status="informational">
            <strong>{__i18n.notices.global_tab_option.title}</strong>
            <br />
            {__i18n.notices.global_tab_option.description}
            <br />
            <br />
            <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__i18n.notice_button_text.get_pro}</a>
        </Notice><br />
    </>
}

const TripTabsUseGlobal = ( {allData} ) => {
    const {use_global_tabs} = allData;
    const { updateTripData } = dispatch('WPTravel/TripEdit');

    return <PanelRow>
        <label>{__i18n.use_global_tabs_layout}</label>
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
}

// Swap any array or object as per provided index.
const  swapList = (data, old_index, new_index) => {
    if ( 'object' === typeof data ) {
        if (new_index >= Object.keys(data).length) {
            var k = new_index - Object.keys(data).length + 1;
            while (k--) {
                data.push(undefined);
            }
        }
        data.splice(new_index, 0, data.splice(old_index, 1)[0]);
    }
    if ( 'array' === typeof data ) {
        if (new_index >= data.length) {
            var k = new_index - data.length + 1;
            while (k--) {
                data.push(undefined);
            }
        }
        data.splice(new_index, 0, data.splice(old_index, 1)[0]);
    }
    return data;
};

const TripTabs = ( {allData} ) => {

    const { updateTripData, updateRequestSending } = dispatch('WPTravel/TripEdit');

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
                        let index            = parseInt(tabIndex);
                        return <div style={{position:'relative'}}  data-index={index} key={index} >
                            <div className={`wptravel-swap-list`}>
                            <Button
                            disabled={0 == index}
                            onClick={(e) => {
                                let sorted = swapList( trip_tabs, index, index - 1 )
                                sortTabs(sorted)
                                updateRequestSending(true); // Temp fixes to reload the content.
                                updateRequestSending(false);
                            }}><i className="dashicons dashicons-arrow-up"></i></Button>
                            <Button
                            disabled={( Object.keys(trip_tabs).length - 1 ) === index}
                            onClick={(e) => {
                                let sorted = swapList( faqs, index, index + 1 )
                                sortTabs(sorted)
                                updateRequestSending(true);
                                updateRequestSending(false);
                            }}><i className="dashicons dashicons-arrow-down"></i></Button>
                        </div>
                        <PanelBody
                            icon= {alignJustify}
                            title={tab.label ? tab.label : tab.default_label }
                            initialOpen={false}
                        >
                            <PanelRow>
                                <label>{__i18n.global_trip_title}</label>
                                <TextControl
                                    value={tab.default_label}
                                    disabled={true}

                                />
                            </PanelRow>
                            <PanelRow>
                                <label>{__i18n.custom_trip_title}</label>
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
                                    <label>{__i18n.display}</label>
                                    <ToggleControl
                                        checked={ tab.show_in_menu == 'yes' || tab.show_in_menu === true }
                                        onChange={
                                            (e) => updateTabOption('show_in_menu', tab.show_in_menu == 'yes' || tab.show_in_menu === true ? false : true, tabIndex)
                                        }
                                        disabled={use_global_tabs == 'yes' ? true : false}
                                    />
                                </PanelRow>
                            }

                        </PanelBody>
                        </div>
                    })
                }
            </ReactSortable></div>
            )
        }

    }

    return <ErrorBoundary>
        <div className="wp-travel-trip-tabs">
            { 'yes' == use_global_tabs ?
                <Disabled>{tabsContent()}</Disabled> :
                tabsContent()
            }
        </div>
    </ErrorBoundary>;
}

// Callbacks.
const TripTabsNoticeCB = ( content ) => {
    return [ ...content, <TripTabsNotice /> ];
}

const TripTabsUseGlobalCB = ( content, allData ) => {
    return [ ...content, <TripTabsUseGlobal allData={allData} /> ];
}
const TripTabsCB = ( content, allData ) => {
    return [ ...content, <TripTabs allData={allData} /> ];
}

addFilter( 'wptravel_trip_edit_tab_content_tabs', 'WPTravel/TripEdit/TripTabsNotice', TripTabsNoticeCB, 10 );
addFilter( 'wptravel_trip_edit_tab_content_tabs', 'WPTravel/TripEdit/TripTabsUseGlobal', TripTabsUseGlobalCB, 20 );
addFilter( 'wptravel_trip_edit_tab_content_tabs', 'WPTravel/TripEdit/TripTabs', TripTabsCB, 30 );

addFilter( 'wptravel_trip_edit_block_tab_inspector_controls', 'WPTravel/TripEdit/Block/Tab/InspectorControls/TripTabsNotice', TripTabsNoticeCB );
addFilter( 'wptravel_trip_edit_block_tab_inspector_controls_tab_sort', 'WPTravel/TripEdit/Block/Tab/InspectorControls/TabSort/TripTabsUseGlobal', TripTabsUseGlobalCB );
addFilter( 'wptravel_trip_edit_block_tab_inspector_controls_tab_sort', 'WPTravel/TripEdit/Block/Tab/InspectorControls/TabSort/TripTabs', TripTabsCB );
