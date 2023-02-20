import { render, useState, useEffect, isValidElement } from '@wordpress/element'; // [ useeffect : used on onload, component update ]
import { TabPanel, Spinner, Notice } from '@wordpress/components';
import { useSelect, select, dispatch } from '@wordpress/data'; // redux [and also for hook / filter] | dispatch : send data to store
import { applyFilters, addFilter } from '@wordpress/hooks';
import { sprintf, _n, __ } from '@wordpress/i18n';
import domReady from '@wordpress/dom-ready';
import ErrorBoundary from '../../ErrorBoundry/ErrorBoundry';

import './store/settings-store';

import SaveSettings from './sub-components/SaveSettings'

// Tab Items.
import SettingsGeneral from './tab-components/SettingsGeneral';
import SettingsTrip from './tab-components/SettingsTrip';
import SettingsEmail from './tab-components/SettingsEmail';
import SettingsAccount from './tab-components/SettingsAccount';
// import SettingsTabs from './tab-components/SettingsTabs';
import SettingsPayment from './tab-components/SettingsPayment';
// import SettingsFacts from './tab-components/SettingsFacts';
// import SettingsFieldEditor from './tab-components/SettingsFieldEditor';
// import SettingsFaqs from './tab-components/SettingsFaqs';
import SettingsCartCheckout from './tab-components/SettingsCartCheckout';
import SettingsInvoice from './tab-components/SettingsInvoice';
import SettingsMisc from './tab-components/SettingsMisc';
import SettingsDebug from './tab-components/SettingsDebug';
import SettingsLicense from './tab-components/SettingsLicense';
import SettingsModules from './tab-components/SettingsModules';

//  Secondary Tabs
import SettingsCurrency from './settingsContent/general/SettingsCurrency';
import SettingsMaps from './settingsContent/general/SettingsMaps';
import SettingsPages from './settingsContent/general/SettingsPages';
import SettingsArchivePageTitle from './settingsContent/general/SettingsArchivePageTitle';
import SettingsFacts from './settingsContent/trips/SettingsFacts';
import SettingsFAQs from './settingsContent/trips/SettingsFAQs';
import SettingsFieldEditor from './settingsContent/trips/SettingsFieldEditor';
import SettingsTabs from './settingsContent/trips/SettingsTabs';
import SettingsTrips from './settingsContent/trips/SettingsTrips';

const WPTravelSettings = () => {
    const settingsData = useSelect((select) => {
        return select('WPTravel/Admin').getSettings()
    }, []);

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const { options } = allData

    let wrapperClasses = "wp-travel-block-tabs-wrapper wp-travel-trip-settings";
    wrapperClasses = allData.is_sending_request ? wrapperClasses + ' wp-travel-sending-request' : wrapperClasses;

    // Add filter to tabs.
    let subTabs = applyFilters('wp_travel_settings_tabs', [
        {
            name: 'currency',
            title: __('Currency', 'wp-travel'),
            className: 'tab-general',
            content: SettingsCurrency
        },
        {
            name: 'maps',
            title: __('Maps', 'wp-travel'),
            className: 'tab-general',
            content: SettingsMaps
        },
        {
            name: 'pages',
            title: __('Pages', 'wp-travel'),
            className: 'tab-general',
            content: SettingsPages
        },
        {
            name: 'archive-page-title',
            title: __('Archive Page Title', 'wp-travel'),
            className: 'tab-general',
            content: SettingsArchivePageTitle
        },
        // {
        //     name: 'facts',
        //     title: __('Facts', 'wp-travel'),
        //     className: 'tab-trip',
        //     content: SettingsFacts
        // },
        // {
        //     name: 'faqs',
        //     title: __('FAQs', 'wp-travel'),
        //     className: 'tab-trip',
        //     content: SettingsFAQs
        // },
        // {
        //     name: 'field-editor',
        //     title: __('Field Editor', 'wp-travel'),
        //     className: 'tab-trip',
        //     content: SettingsFieldEditor
        // },
        // {
        //     name: 'tabs',
        //     title: __('Global Tabs', 'wp-travel'),
        //     className: 'tab-trip',
        //     content: SettingsTabs
        // },
        // {
        //     name: 'trip-settings',
        //     title: __('Trip Settings', 'wp-travel'),
        //     className: 'tab-trip',
        //     content: SettingsTrips
        // },
        // {
        //     name: 'trip',
        //     title: __('Trips', 'wp-travel'),
        //     className: 'tab-trip',
        //     content: SettingsTrip
        // },
    ], allData);

    const [ settingComponent, setSettingComponent ] = useState( "Currency" );

    console.log( settingComponent );
    return (
        <div className={wrapperClasses}>
            {/* {allData.is_sending_request && <Spinner />}
            <TabPanel></TabPanel> */}
            <div className="wp-travel-main-container flex bg-slate-100 h-screen p-12">
                <div className="w-96 flex w-full">
                    <div className="Tabs p-6 rounded w-80 flex flex-col align-center">
                        <div className="logo-container mb-6 text-green-500 text-2xl font-bold text-center">WP Travel</div>
                        <div className="search-box bg-white flex border-1 shadow rounded px-3 py-1 align-center justify-center">
                            <i className="fa fa-search leading-6 text-slate-400" aria-hidden="true"></i>
                            <input type="text" className="ml-2 placeholder:text-sm" placeholder="Quick Search..." />
                        </div>
                        <div className="tabs mt-6">
                            <div className="primary-tabs">
                                <div className="bg-slate-200 px-3 py-1 rounded mb-2">
                                    <p className="font-bold text-slate-800 tracking-wide text-md">General</p>
                                </div>
                                <div className="secondary-tabs flex flex-col justify-end w-48 float-right mb-1">
                                    <div 
                                        className="text-sm font-bold text-slate-600 secondary-tab py-1 px-2 hover:bg-slate-200 cursor-pointer rounded mb-1"
                                        onClick={ () => setSettingComponent( "Currency" ) }
                                    >
                                        Currency
                                    </div>
                                </div>
                                <div className="secondary-tabs flex flex-col justify-end w-48 float-right mb-1">
                                    <div className="text-sm font-bold text-slate-600 secondary-tab py-1 px-2 hover:bg-slate-200 cursor-pointer rounded mb-1"
                                        onClick={ () => setSettingComponent( "Map" ) }
                                    >
                                        Maps
                                    </div>
                                </div>
                                <div className="secondary-tabs flex flex-col justify-end w-48 float-right mb-1">
                                    <div className="text-sm font-bold text-slate-600 secondary-tab py-1 px-2 hover:bg-slate-200 cursor-pointer rounded mb-1"
                                        onClick={ () => setSettingComponent( "Page" ) }
                                    >
                                        Pages
                                    </div>
                                </div>
                                <div className="secondary-tabs flex flex-col justify-end w-48 float-right mb-1"
                                    onClick={ () => setSettingComponent( "Archive" ) }
                                >
                                    <div className="text-sm font-bold text-slate-600 secondary-tab py-1 px-2 hover:bg-slate-200 cursor-pointer rounded mb-1">
                                        Archive Page Title
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="Content Settings bg-white border rounded w-full">
                        {
                            settingComponent == 'Currency' &&
                            <SettingsCurrency />
                            
                        }
                        {
                            settingComponent == 'Map' &&
                            <SettingsMaps />
                            
                        }
                        {
                            settingComponent == 'Page' &&
                            <SettingsPages />
                            
                        }
                    </div>
                </div>
            </div>
        </div>
    )
}

const WPTravelNetworkSettings = () => {
}

domReady(function () {
    if ('undefined' !== typeof document.getElementById('wp-travel-settings-block') && null !== document.getElementById('wp-travel-settings-block')) {
        render(<WPTravelSettings />, document.getElementById('wp-travel-settings-block'));
    }
});
domReady(function () {
    if ('undefined' !== typeof document.getElementById('wp-travel-network-settings-block') && null !== document.getElementById('wp-travel-network-settings-block')) {
        render(<WPTravelNetworkSettings />, document.getElementById('wp-travel-network-settings-block'));
    }
});