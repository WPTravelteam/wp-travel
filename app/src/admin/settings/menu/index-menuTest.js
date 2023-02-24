import {
    render,
    useState,
    useEffect,
    useRef,
    isValidElement,
    createPortal,
} from "@wordpress/element"; // [ useeffect : used on onload, component update ]
import { TabPanel, Spinner, Notice } from "@wordpress/components";
import { useSelect, select, dispatch } from "@wordpress/data"; // redux [and also for hook / filter] | dispatch : send data to store
import { applyFilters, addFilter } from "@wordpress/hooks";
import { sprintf, _n, __ } from "@wordpress/i18n";
import domReady from "@wordpress/dom-ready";
import ErrorBoundary from "../../ErrorBoundry/ErrorBoundry";
import Select from 'react-select'

import "./store/settings-store";

import SaveSettings from "./sub-components/SaveSettings";

// Menu
import Menu from './menu'

import AllComponent from "./settingsContent/AllComponent";
import SettingsCurrency from "./settingsContent/general/SettingsCurrency";
import SettingsMaps from "./settingsContent/general/SettingsMaps";
import SettingsPages from "./settingsContent/general/SettingsPages";
import SettingsArchivePageTitle from "./settingsContent/general/SettingsArchivePageTitle";
import SettingsFacts from "./settingsContent/trips/SettingsFacts";
import SettingsFAQs from "./settingsContent/trips/SettingsFAQs";
import SettingsFieldEditor from "./settingsContent/trips/SettingsFieldEditor";
import SettingsTabs from "./settingsContent/trips/SettingsTabs";
import SettingsTrips from "./settingsContent/trips/SettingsTrips";
import SettingsEmailTemplates from "./settingsContent/email/SettingsEmailTemplates";
import SettingsGeneralEmail from "./settingsContent/email/SettingsGeneralEmail";
import SettingsAccount from "./settingsContent/account/SettingsAccount";
import SettingsCheckout from "./settingsContent/checkout/SettingsCheckout";
import SettingsPayment from "./settingsContent/payment/SettingsPayment";
import SettingsInvoice from "./settingsContent/invoice/SettingsInvoice";
import SettingsMisc from "./settingsContent/misc/SettingsMisc";
import SettingsThirdParty from "./settingsContent/misc/SettingsThirdParty";
import SettingsModules from "./settingsContent/advanced/SettingsModules";
import SettingsDebug from "./settingsContent/advanced/SettingsDebug";
import SettingsLicense from "./settingsContent/license/SettingsLicense";

import SearchButton from "./helpers/search-component/SearchButton";
import SettingsAdvancedGallery from "./settingsContent/misc/SettingsAdvancedGallery";
import SettingsReCaptchaV2 from "./settingsContent/misc/SettingsReCaptchaV2";

const WPTravelSettings = () => {
    const settingsData = useSelect((select) => {
        return select("WPTravel/Admin").getSettings();
    }, []);

    const { updateSettings } = dispatch('WPTravel/Admin');

    const allData = useSelect((select) => {
        return select("WPTravel/Admin").getAllStore();
    }, []);

    // console.log(allData);

    const { options } = allData;

    let wrapperClasses = "wp-travel-block-tabs-wrapper wp-travel-trip-settings";
    wrapperClasses = allData.is_sending_request
        ? wrapperClasses + " wp-travel-sending-request"
        : wrapperClasses;

    // Add filter to tabs.
    let tabs = applyFilters(
        "wp_travel_secondary_tabs",
        [
            {
                name: "currency",
                title: __("Currency", "wp-travel"),
                className: "tab-general",
                content: SettingsCurrency,
            },
            {
                name: "maps",
                title: __("Maps", "wp-travel"),
                className: "tab-general",
                content: SettingsMaps,
            },
            {
                name: "pages",
                title: __("Pages", "wp-travel"),
                className: "tab-general",
                content: SettingsPages,
            },
            {
                name: "archive-page-title",
                title: __("Archive Page Title", "wp-travel"),
                className: "tab-general",
                content: SettingsArchivePageTitle,
            },
            {
                name: "facts",
                title: __("Facts", "wp-travel"),
                className: "tab-trip",
                content: SettingsFacts,
            },
            {
                name: "faqs",
                title: __("FAQs", "wp-travel"),
                className: "tab-trip",
                content: SettingsFAQs,
            },
            {
                name: "trip-settings",
                title: __("Trips Settings", "wp-travel"),
                className: "tab-trip",
                content: SettingsTrips,
            },
            {
                name: "field-editor",
                title: __("Field Editor", "wp-travel"),
                className: "tab-trip",
                content: SettingsFieldEditor,
            },
            {
                name: "tabs",
                title: __("Tabs", "wp-travel"),
                className: "tab-trip",
                content: SettingsTabs,
            },
            {
                name: "general-email-settings",
                title: __("General Email Settings", "wp-travel"),
                className: "tab-email",
                content: SettingsGeneralEmail,
            },
            {
                name: "email-templates",
                title: __("Email Templates", "wp-travel"),
                className: "tab-email",
                content: SettingsEmailTemplates,
            },
            {
                name: "account",
                title: __("Account", "wp-travel"),
                className: "tab-account",
                content: SettingsAccount,
            },
            {
                name: "checkout",
                title: __("Checkout", "wp-travel"),
                className: "tab-checkout",
                content: SettingsCheckout,
            },
            {
                name: "payment",
                title: __("Payment", "wp-travel"),
                className: "tab-payment",
                content: SettingsPayment,
            },
            {
                name: "invoice",
                title: __("Invoice", "wp-travel"),
                className: "tab-invoice",
                content: SettingsInvoice,
            },
            {
                name: "misc-options",
                title: __("Miscellaneous Options", "wp-travel"),
                className: "tab-misc",
                content: SettingsMisc,
            },
            {
                name: "advanced-gallery",
                title: __("Advanced Gallery", "wp-travel"),
                className: "tab-misc",
                content: SettingsAdvancedGallery,
            },
            {
                name: "recaptcha-v2",
                title: __("reCaptcha V2", "wp-travel"),
                className: "tab-misc",
                content: SettingsReCaptchaV2,
            },
            {
                name: "third-party",
                title: __("Third Party Integrations", "wp-travel"),
                className: "tab-misc",
                content: SettingsThirdParty,
            },
            {
                name: "modules-settings",
                title: __("Modules Settings", "wp-travel"),
                className: "tab-advanced",
                content: SettingsModules,
            },
            {
                name: "debug",
                title: __("Debug", "wp-travel"),
                className: "tab-advanced",
                content: SettingsDebug,
            },
        ],
        allData
    );

    const [activeTab, setActiveTab] = useState("currency");

    const handleTabClick = (tab) => {
        setActiveTab(tab);
    };

    const selectOptions = [
        {
            label: 'Currency',
            options: [
                // { value: 'currency', label: 'Currency', tab: 'currency' },
                { value: 'decimal-separator', label: 'Decimal separator', tab: 'currency', tabName: 'Currency' },
                { value: 'currency-position', label: 'Currency Position', tab: 'currency', tabName: 'Currency Position' },
            ]
        },
        {
            label: 'Maps',
            options: [
                { value: 'select-map', label: 'Select Map', tab: 'maps', tabName: 'Select Map' },
                { value: 'api-key', label: 'Api Key', tab: 'maps', tabName: 'API Key' },
            ]
        },
        {
            label: 'Trips Settings',
            options: [
                { value: 'enable-expired-trip', label: 'Enable Expired Trip Options', tab: 'trips-settings' },
            ]
        },
        // { value: 'decimal-separator', label: 'Decimal separator', tab: 'currency' },
        // { value: 'currency-position', label: 'Currency Position', tab: 'currency' },
        // { value: 'select-map', label: 'Select Map', tab: 'maps' },
        // { value: 'api-key', label: 'Api Key', tab: 'maps' },
    ];

    const [selectedOption, setSelectedOption] = useState({ value: 'currency', label: 'Currency' });

    const [isSticky, setIsSticky] = useState(false);

    const myRef = useRef();
    const stickyRef = useRef();

    useEffect(() => {
        window.addEventListener("scroll", handleScroll);

        return () => {
            window.addEventListener("scroll", handleScroll);
        }
    }, [])

    const [isMobileNavOpen, setIsMobileNavOpen] = useState(false);

    const handleScroll = () => {
        console.log(window.innerWidth)
        window.innerWidth < 768 ?
            window.scrollY >= stickyRef.current.getBoundingClientRect().top + 30
                ? setIsSticky(true)
                : setIsSticky(false)
            :
            setIsSticky(false)
    }

    const handleMenuOpen = () => {
        setIsMobileNavOpen(true)
        document.body.style.overflow = "hidden"
        if (window.innerWidth < 768) {
            createPortal(
                <Menu
                    ref={myRef}
                    className={`${window.innerWidth < 768 ? "wp-travel-active" : ""}`}
                    tabs={tabs}
                    handleTabClick={handleTabClick}
                    isMobileNavOpen={isMobileNavOpen}
                    setNav={setIsMobileNavOpen}
                />,
                document.querySelector('body'))
        }

    }

    return (
        <>
            <div id="wp-travel-mobile-navbar" className={isSticky ? "wp-travel-nav-sticky" : "wp-travel-nav"}>
                <button className="wp-travel-nav-menu-button" onClick={() => setIsMobileNavOpen(!isMobileNavOpen)}><i class="fa fa-bars" aria-hidden="true"></i></button>
                {isMobileNavOpen && handleMenuOpen}
            </div>
            <div className={wrapperClasses}>
                <div ref={stickyRef} className="wp-travel-main-container">
                    <div className="wp-travel-settings-container">
                        {/* Side Menu */}
                        <Menu ref={myRef} tabs={tabs} handleTabClick={handleTabClick} />

                        {/* Settings Section */}
                        <div className="wp-travel-settings-section-wrapper">
                            <div className="wp-travel-settings-section">
                                {/* Render tab contents from the tabs object */}
                                {tabs.map((tab) => (
                                    <ErrorBoundary>
                                        {activeTab == tab.name &&
                                            tab.content &&
                                            isValidElement(<tab.content />) && <tab.content />}
                                    </ErrorBoundary>
                                ))}
                                <SaveSettings position="bottom" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

// const WPTravelNetworkSettings = () => {
//     const settingsData = useSelect((select) => {
//         return select("WPTravel/Admin").getSettings();
//     }, []);

//     const allData = useSelect((select) => {
//         return select("WPTravel/Admin").getAllStore();
//     }, []);

//     const { options } = allData;

//     let wrapperClasses = "wp-travel-block-tabs-wrapper wp-travel-trip-settings";
//     wrapperClasses = allData.is_sending_request
//         ? wrapperClasses + " wp-travel-sending-request"
//         : wrapperClasses;

//     // Add filter to tabs.
//     let tabs = applyFilters(
//         "wp_travel_network_settings_tabs",
//         [
//             {
//                 name: "license",
//                 title: __("License", "wp-travel"),
//                 className: "tab-license",
//                 content: SettingsLicense,
//             },
//         ],
//         allData
//     );
//     return (
//         <div className={wrapperClasses}>
//             {allData.is_sending_request && <Spinner />}
//             <SaveSettings position="top" />
//             <TabPanel
//                 className="wp-travel-block-tabs"
//                 activeClass="active-tab"
//                 onSelect={() => false}
//                 tabs={tabs}
//             >
//                 {(tab) => (
//                     <ErrorBoundary>
//                         {tab.content && isValidElement(<tab.content />) ? (
//                             <tab.content />
//                         ) : (
//                             ""
//                         )}{" "}
//                         {/* Need to remove this latter. add all content with filter instead */}
//                         {applyFilters(
//                             `wptravel_settings_tab_content_${tab.name.replaceAll("-", "_")}`,
//                             [],
//                             allData
//                         )}
//                     </ErrorBoundary>
//                 )}
//             </TabPanel>
//             <SaveSettings position="bottom" />
//         </div>
//     );
// };

// Filters

addFilter('wp_travel_secondary_tabs', 'wp_travel', (content, allData) => {
    const { options } = allData
    if ('undefined' != typeof options && !options.is_multisite) {
        content = [
            ...content,
            {
                name: 'license',
                title: __('License', 'wp-travel'),
                className: 'tab-general',
                content: SettingsLicense
            },
        ]
    }
    return content
});

addFilter('wp_travel_settings_downloads_section_header', 'wp-travel', () => {
    return (
        <div className="wp-travel-section-header">
            <h2 className="wp-travel-section-header-title">
                {__("Downloads", "wp-travel")}
            </h2>
            <p className="wp-travel-section-header-description">
                {__("More downloads settings according to your choice.", "wp-travel")}
            </p>
        </div>
    )
})

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

domReady(function () {
    if (
        "undefined" !==
        typeof document.getElementById("wp-travel-settings-block") &&
        null !== document.getElementById("wp-travel-settings-block")
    ) {
        render(
            <WPTravelSettings />,
            document.getElementById("wp-travel-settings-block")
        );
    }
});

// domReady(function () {
//     if (
//         "undefined" !==
//         typeof document.getElementById("wp-travel-network-settings-block") &&
//         null !== document.getElementById("wp-travel-network-settings-block")
//     ) {
//         render(
//             <WPTravelNetworkSettings />,
//             document.getElementById("wp-travel-network-settings-block")
//         );
//     }
// });
