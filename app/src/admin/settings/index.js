import { render, useEffect } from '@wordpress/element'; // [ useeffect : used on onload, component update ]
import { TabPanel, Spinner, Notice } from '@wordpress/components';
import { useSelect, select, dispatch } from '@wordpress/data'; // redux [and also for hook / filter] | dispatch : send data to store
import { applyFilters, addFilter } from '@wordpress/hooks';
import { sprintf, _n, __ } from '@wordpress/i18n';
import domReady from '@wordpress/dom-ready';
import ErrorBoundary from '../error/ErrorBoundry';

import './store/settings-store';

import SaveSettings from './sub-components/SaveSettings'

// Tab Items.
import SettingsGeneral from './tab-components/SettingsGeneral';
import SettingsTrip from './tab-components/SettingsTrip';
import SettingsEmail from './tab-components/SettingsEmail';
import SettingsAccount from './tab-components/SettingsAccount';


const WPTravelTripSettings = () => {
    const settingsData = useSelect((select) => {
        return select('WPTravel/Admin').getSettings()
    }, []);
    
    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);
    

   
    let wrapperClasses = "wp-travel-trip-pricings";
    wrapperClasses = allData.is_sending_request ? wrapperClasses + ' wp-travel-sending-request' : wrapperClasses;

    // Add filter to tabs.
    let tabs = applyFilters('wp_travel_settings_tabs', [
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
            content: SettingsTrip
        },
        {
            name: 'email',
            title: __('Email', 'wp-travel'),
            className: 'tab-email',
            content: SettingsEmail
        },
        {
            name: 'account',
            title: __('Account', 'wp-travel'),
            className: 'tab-account',
            content: SettingsAccount
        },
        {
            name: 'tabs',
            title: __('Tabs', 'wp-travel'),
            className: 'tab-tabs',
            content: 'a'
        },
        {
            name: 'payment',
            title: __('Payment', 'wp-travel'),
            className: 'tab-payment',
            content: 'a'
        },
        {
            name: 'facts',
            title: __('Facts', 'wp-travel'),
            className: 'tab-facts',
            content: 'a'
        },
        {
            name: 'field-editor',
            title: __('Field Editor', 'wp-travel'),
            className: 'tab-field-editor',
            content: 'a'
        },
        {
            name: 'faqs',
            title: __('FAQs', 'wp-travel'),
            className: 'tab-faqs',
            content: 'a'
        },
        {
            name: 'cart-checkout',
            title: __('Cart & Checkout', 'wp-travel'),
            className: 'tab-cart-checkout',
            content: 'a'
        },
        {
            name: 'addons-settings',
            title: __('Addons Settings', 'wp-travel'),
            className: 'tab-addons-settings',
            content: 'a'
        },
        {
            name: 'misc-options',
            title: __('Misc. Options', 'wp-travel'),
            className: 'tab-misc-options',
            content: 'a'
        },
        {
            name: 'debug',
            title: __('Debug', 'wp-travel'),
            className: 'tab-debug',
            content: 'a'
        },
        
    ]);
    return <div className={wrapperClasses}>
        {allData.is_sending_request && <Spinner />}
        <SaveSettings />
        <TabPanel className="wp-travel-block-tabs"
            activeClass="active-tab"
            onSelect={() => false}
            tabs={tabs}>
            {
                (tab) => 'undefined' !== typeof tab.content ? <ErrorBoundary><tab.content /></ErrorBoundary> : <>{__('Error', 'wp-travel')}</>
            }
        </TabPanel>
        <SaveSettings />
    </div>
};


// Filters
addFilter('wp_travel_settings_after_maps_upsell', 'wp_travel', (content, allData) => {
    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Need alternative maps?', 'wp-travel')}</strong>
                <br />
                {__('If you need alternative to current map then you can get free or pro maps for WP Travel.', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
                    &nbsp;&nbsp;
                    <a className="button button-primary" target="_blank" href="https://wptravel.io/downloads/category/map/">{__('View WP Travel Map addons', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]
    return content
});

addFilter('wp_travel_tab_content_before_email', 'wp_travel', (content, allData) => {
    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Want to get more e-mail customization options?', 'wp-travel')}</strong>
                <br />
                {__('By upgrading to Pro, you can get features like multiple email notifications, email footer powered by text removal options and more !', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
                    &nbsp;&nbsp;
                    <a className="button button-primary" target="_blank" href="https://wptravel.io/downloads/wp-travel-utilities/">{__('Get WP Travel Utilities Addon', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]
    return content
});


domReady(function () {
    if ('undefined' !== typeof document.getElementById('wp-travel-settings-block') && null !== document.getElementById('wp-travel-settings-block')) {
        render(<WPTravelTripSettings />, document.getElementById('wp-travel-settings-block'));
    }
});