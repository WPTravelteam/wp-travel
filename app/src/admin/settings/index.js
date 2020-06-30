import { render, useEffect } from '@wordpress/element'; // [ useeffect : used on onload, component update ]
import { TabPanel, Spinner, Notice } from '@wordpress/components';
import { useSelect, select, dispatch } from '@wordpress/data'; // redux [and also for hook / filter] | dispatch : send data to store
import { applyFilters, addFilter } from '@wordpress/hooks';
import { sprintf, _n, __ } from '@wordpress/i18n';
import domReady from '@wordpress/dom-ready';

import './store/settings-store';

import SaveSettings from './sub-components/SaveSettings'

// Tab Items.
import SettingsGeneral from './tab-components/SettingsGeneral';


const WPTravelTripSettings = () => {
    const settingsData = useSelect((select) => {
        return select('WPTravel/Settings').getSettings()
    }, []);
    
    const allData = useSelect((select) => {
        return select('WPTravel/Settings').getAllStore()
    }, []);
    

   
    let wrapperClasses = "wp-travel-trip-pricings";
    wrapperClasses = allData.is_sending_request ? wrapperClasses + ' wp-travel-sending-request' : wrapperClasses;

    // Add filter to tabs.
    let tabs = applyFilters('wp_travel_trip_options_tabs', [
        {
            name: 'general',
            title: __('General', 'wp-travel'),
            className: 'tab-general',
            content: SettingsGeneral
        },
        {
            name: 'trip',
            title: __('Trip', 'wp-travel'),
            className: 'tab-trip',
            content: SettingsGeneral
        },
        
    ]);
    return <div className={wrapperClasses}>
        {allData.is_sending_request && <Spinner />}
        <TabPanel className="wp-travel-block-tabs"
            activeClass="active-tab"
            onSelect={() => false}
            tabs={tabs}>
            {
                (tab) => 'undefined' !== typeof tab.content ? <><tab.content /></> : <>{__('Error', 'wp-travel')}</>
            }
        </TabPanel>
        <SaveSettings />
    </div>
};

domReady(function () {
    if ('undefined' !== typeof document.getElementById('wp-travel-settings-block') && null !== document.getElementById('wp-travel-settings-block')) {
        render(<WPTravelTripSettings />, document.getElementById('wp-travel-settings-block'));
    }
});