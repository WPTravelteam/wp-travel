import { TextControl, PanelRow, PanelBody, Disabled, Notice, ToggleControl } from '@wordpress/components';
import { applyFilters, addFilter } from '@wordpress/hooks';
import { _n, __ } from '@wordpress/i18n';
import { withSelect, withDispatch } from '@wordpress/data';
import { compose } from '@wordpress/compose';

import { ReactSortable } from 'react-sortablejs';
import {alignJustify } from '@wordpress/icons';

import ErrorBoundary from '../../ErrorBoundry/ErrorBoundry';
const __i18n = {
	..._wp_travel_admin.strings
}
const WPTravelTripOptionsTabsContent = ({ allData, updateTripData }) => {
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
                <strong>{__i18n.notices.global_tab_option.title}</strong>
                <br />
                {__i18n.notices.global_tab_option.description}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__i18n.notice_button_text.get_pro}</a>
            </Notice><br />
        </>,
        ...content
    ]
    return content
}, 9);

const WPTravelTripOptionsTabs = ( props ) => {
    return <div className="wp-travel-ui wp-travel-ui-card wp-travel-ui-card-no-border"><WPTravelTripOptionsTabsContent { ...props } /></div>
}

export default compose([
	withSelect((select)=>{
		const allData = select('WPTravel/TripEdit').getAllStore();
		return {
			allData
		}
	}),
withDispatch((dispatch)=>{
	const { updateTripData } = dispatch('WPTravel/TripEdit');
	return {
		updateTripData
	}
})
])(WPTravelTripOptionsTabs);
