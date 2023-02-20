import { render, useEffect, isValidElement } from '@wordpress/element'; // [ useeffect : used on onload, component update ]
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

const WPTravelTripSettings = () => {
    const settingsData = useSelect((select) => {
        return select('WPTravel/Admin').getSettings()
    }, []);
    
    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);
    
    const {options}= allData
   
    let wrapperClasses = "wp-travel-block-tabs-wrapper wp-travel-trip-settings";
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
        {
            name: 'facts',
            title: __('Facts', 'wp-travel'),
            className: 'tab-trip',
            content: SettingsFacts
        },
        {
            name: 'faqs',
            title: __('FAQs', 'wp-travel'),
            className: 'tab-trip',
            content: SettingsFAQs
        },
        {
            name: 'field-editor',
            title: __('Field Editor', 'wp-travel'),
            className: 'tab-trip',
            content: SettingsFieldEditor
        },
        {
            name: 'tabs',
            title: __('Global Tabs', 'wp-travel'),
            className: 'tab-trip',
            content: SettingsTabs
        },
        {
            name: 'trip-settings',
            title: __('Trip Settings', 'wp-travel'),
            className: 'tab-trip',
            content: SettingsTrips
        },
        {
            name: 'trip',
            title: __('Trips', 'wp-travel'),
            className: 'tab-trip',
            content: SettingsTrip
        },
        // {
        //     name: 'email',
        //     title: __('Email', 'wp-travel'),
        //     className: 'tab-email',
        //     content: SettingsEmail
        // },
        // {
        //     name: 'account',
        //     title: __('Account', 'wp-travel'),
        //     className: 'tab-account',
        //     content: SettingsAccount
        // },
        // {
        //     name: 'payment',
        //     title: __('Payment', 'wp-travel'),
        //     className: 'tab-payment',
        //     content: SettingsPayment
        // },
        // {
        //     name: 'cart-checkout',
        //     title: __('Checkout', 'wp-travel'), // cart & checkout label updated to checkout @since 4.4.3
        //     className: 'tab-cart-checkout',
        //     content: SettingsCartCheckout
        // },
        // {
        //     name: 'modules-settings',
        //     title: __('Modules Settings', 'wp-travel'),
        //     className: 'tab-modules-settings',
        //     content: SettingsModules
        // },
        // {
        //     name: 'invoice',
        //     title: __('Invoice', 'wp-travel'),
        //     className: 'tab-invoice',
        //     content: SettingsInvoice
        // },
        // {
        //     name: 'misc-options',
        //     title: __('Misc. Options', 'wp-travel'),
        //     className: 'tab-misc-options',
        //     content: SettingsMisc
        // },
        // {
        //     name: 'debug',
        //     title: __('Debug', 'wp-travel'),
        //     className: 'tab-debug',
        //     content: SettingsDebug
        // },
        
    ], allData );
    return <div className={wrapperClasses}>
        {allData.is_sending_request && <Spinner />}
        {/* <SaveSettings position="top" /> */}
        <TabPanel className="wp-travel-block-tabs"
            activeClass="active-tab"
            onSelect={() => false}
            tabs={tabs}>
            {
                (tab) =><ErrorBoundary>
                    { tab.content && isValidElement( <tab.content /> ) ? <tab.content /> : ''} {/* Need to remove this latter. add all content with filter instead */}
                    {applyFilters(
                        `wptravel_settings_tab_content_${tab.name.replaceAll(
                            "-",
                            "_"
                        )}`,
                        [],
                        allData
                    )}
                </ErrorBoundary>
            }
        </TabPanel>
        <SaveSettings position="bottom" />
    </div>
};

const WPTravelNetworkSettings = () => {
    const settingsData = useSelect((select) => {
        return select('WPTravel/Admin').getSettings()
    }, []);
    
    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);
    
    const {options}= allData
   
    let wrapperClasses = "wp-travel-block-tabs-wrapper wp-travel-trip-settings";
    wrapperClasses = allData.is_sending_request ? wrapperClasses + ' wp-travel-sending-request' : wrapperClasses;

    // Add filter to tabs.
    let tabs = applyFilters('wp_travel_network_settings_tabs', [
        {
            name: 'license',
            title: __('License', 'wp-travel'),
            className: 'tab-license',
            content: SettingsLicense
        }
        
    ], allData );
    return <div className={wrapperClasses}>
        {allData.is_sending_request && <Spinner />}
        <SaveSettings position="top" />
        <TabPanel className="wp-travel-block-tabs"
            activeClass="active-tab"
            onSelect={() => false}
            tabs={tabs}>
            {
                (tab) =>
                <ErrorBoundary>
                    { tab.content && isValidElement( <tab.content /> ) ? <tab.content /> : ''} {/* Need to remove this latter. add all content with filter instead */}
                    {applyFilters(
                        `wptravel_settings_tab_content_${tab.name.replaceAll(
                            "-",
                            "_"
                        )}`,
                        [],
                        allData
                    )}
                </ErrorBoundary>
            }
        </TabPanel>
        <SaveSettings position="bottom" />
    </div>
};


// Filters
addFilter('wp_travel_settings_tabs', 'wp_travel', (content, allData) => {
    const {options} = allData
    if ( 'undefined' != typeof options && ! options.is_multisite ) {
        content = [
            ...content,
            {
                name: 'license',
                title: __('License', 'wp-travel'),
                className: 'tab-license',
                content: SettingsLicense
            },
        ]
    }
    return content
});

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
            </Notice><br />
        </>,
        ...content,
    ]
    return content
});






addFilter('wp_travel_addons_setings_tab_fields', 'wp_travel', (content, allData) => {
    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Want to add more features in WP Travel?', 'wp-travel')}</strong>
                <br />
                {__('Get WP Travel Pro modules for Payment, Trip Extras, Inventory Management, Field Editor and other premium features.', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]
    return content
});

addFilter('wp_travel_after_payment_fields', 'wp_travel', (content, allData) => {
    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Need more payment gateway options ?', 'wp-travel')}</strong>
                <br />
                {/* {__('Get addon for Payment, Trip Extras, Inventory Management, Field Editor and other premium features.', 'wp-travel')} */}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
                    &nbsp;&nbsp;
                    <a className="button button-primary" target="_blank" href="http://wptravel.io/contact">{__('Request A new One', 'wp-travel')}</a>
                    &nbsp;&nbsp;
                    <a className="button button-primary" target="_blank" href="https://wptravel.io/downloads/category/payment-gateways/">{__('Check All Payment Gateways', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]
    return content
});

// Utilities Notices.
addFilter('wp_travel_tab_content_before_email', 'WPTravel/Settings/Email/Notice', (content, allData) => {
    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Want to get more e-mail customization options?', 'wp-travel')}</strong>
                <br />
                {__('By upgrading to Pro, you can get features like multiple email notifications, email footer powered by text removal options and more !', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]
    return content
});
addFilter('wp_travel_custom_global_tabs', 'WPTravel/Settings/Tabs/Notice', (content, allData) => {
    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Need Additional Tabs?', 'wp-travel')}</strong>
                <br />
                {__('By upgrading to Pro, you can get global custom tabs addition options with customized content and sorting !', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]
    return content
});
addFilter('wp_travel_settings_tab_faq_fields', 'WPTravel/Settings/Faqs/Notice', (content, allData) => {
    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Need Additional Global FAQs ?', 'wp-travel')}</strong>
                <br />
                {__('By upgrading to Pro, you can get Global FAQs to display it in trips !', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]
    return content
});
addFilter('wp_travel_settings_tab_cart_checkout_fields', 'WPTravel/Settings/Checkout/Notice', (content, allData) => {
    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Need Checkout options ?', 'wp-travel')}</strong>
                <br />
                {__('By upgrading to Pro, you can get checkout option features and more !', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]
    return content
});

// Field Editor Notice.
addFilter('wp_travel_settings_tab_field_editor_fields', 'WPTravel/Settings/FieldEditor/Notice', (content, allData) => {
    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Want to customize your Traveler fields, billing fields and more?', 'wp-travel')}</strong>
                <br />
                {__('By upgrading to Pro, you can customize your Fields for Trip enquiry, Billing and travelers fields.!', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]
    return content
});

// Invoice Notice.
addFilter('wp_travel_settings_tab_invoice_fields', 'WPTravel/Settings/Invoice/Notice', (content, allData) => {
    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Need invoice options ?', 'wp-travel')}</strong>
                <br />
                    {__('By upgrading to Pro, you can get invoice options and more !', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]
    return content
});

// Misc Notices.
// addFilter('wp_travel_settings_tab_misc_options_fields', 'WPTravel/Settings/Misc/Notices', (content, allData) => {

//     let miscNocice = {
//         currencyExchange: <>
//                 <h3>{ __( 'Currency Exchange Rate API', 'wp-travel' ) }</h3>
//                 <Notice isDismissible={false} status="informational">
//                     <strong>{__('Display current exchange rate in your site.', 'wp-travel')}</strong>
//                     <br />
//                     {__('You can display current exchange rate for different currency in pages or sidebar of your site. Checkout out', 'wp-travel')}
//                     <br />
//                     <br />
//                     <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('WP Travel Pro', 'wp-travel')}</a>
//                 </Notice><br />
//             </>,
//         mailchimp: <>
//                 <h3>{ __( 'Mailchimp Settings', 'wp-travel' ) }</h3>
//                 <Notice isDismissible={false} status="informational">
//                     <strong>{__('Using Mailchimp for email marketing?', 'wp-travel')}</strong>
//                     <br />
//                     {__('You can import customer email from booking and inquiry to Mailchimp. That help you grow your business.', 'wp-travel')}
//                     <br />
//                     <br />
//                     <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('WP Travel Pro', 'wp-travel')}</a>
//                 </Notice><br />
//             </>,
//         wishlists: <>
//                 <h3>{ __( 'Wishlists Options', 'wp-travel' ) }</h3>
//                 <Notice isDismissible={false} status="informational">
//                     <strong>{__('Allow customers to save trip for future.', 'wp-travel')}</strong>
//                     <br />
//                     {__('Whishlists helps user to save trip they like for future, so that they can book them later. ', 'wp-travel')}
//                     <br />
//                     <br />
//                     <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('WP Travel Pro', 'wp-travel')}</a>
//                 </Notice><br />
//             </>,
//     }

//     miscNocice = applyFilters( 'wp_travel_misc_addons_notices', miscNocice, allData );
//     if ( Object.keys(miscNocice).length > 0 ) {
//         let AllNotices = Object.keys(miscNocice).map( (index) => {
//             return miscNocice[ index ]
//         } )
//         content = [
//             AllNotices
//             ,
//             ...content,
//         ]
//     }
//     return content
// }); 

domReady(function () {
    if ('undefined' !== typeof document.getElementById('wp-travel-settings-block') && null !== document.getElementById('wp-travel-settings-block')) {
        render(<WPTravelTripSettings />, document.getElementById('wp-travel-settings-block'));
    }
});
domReady(function () {
    if ('undefined' !== typeof document.getElementById('wp-travel-network-settings-block') && null !== document.getElementById('wp-travel-network-settings-block')) {
        render(<WPTravelNetworkSettings />, document.getElementById('wp-travel-network-settings-block'));
    }
});


