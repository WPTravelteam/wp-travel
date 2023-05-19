import {
    render,
    useState,
    useEffect,
    useRef,
    isValidElement,
    createPortal,
} from "@wordpress/element"; // [ useeffect : used on onload, component update ]
import { Spinner, Notice } from "@wordpress/components";
import { useSelect, select, dispatch } from "@wordpress/data"; // redux [and also for hook / filter] | dispatch : send data to store
import { applyFilters, addFilter } from "@wordpress/hooks";
import { _n, __ } from "@wordpress/i18n";
import domReady from "@wordpress/dom-ready";
import ErrorBoundary from "../../ErrorBoundry/ErrorBoundry";

import SettingsCurrency from "./components/settingsContent/general/SettingsCurrency";
import SettingsMaps from "./components/settingsContent/general/SettingsMaps";
import SettingsPages from "./components/settingsContent/general/SettingsPages";
import SettingsArchivePageTitle from "./components/settingsContent/general/SettingsArchivePageTitle";
import SettingsLicense from "./components/settingsContent/license/SettingsLicense";
import SettingsFacts from "./components/settingsContent/trips/SettingsFacts";
import SettingsFAQs from "./components/settingsContent/trips/SettingsFAQs";
import SettingsFieldEditor from "./components/settingsContent/trips/SettingsFieldEditor";
import SettingsTabs from "./components/settingsContent/trips/SettingsTabs";
import SettingsTrips from "./components/settingsContent/trips/SettingsTrips";
import SettingsEmailTemplates from "./components/settingsContent/email/SettingsEmailTemplates";
import SettingsGeneralEmail from "./components/settingsContent/email/SettingsGeneralEmail";
import SettingsAccount from "./components/settingsContent/account/SettingsAccount";
import SettingsCheckout from "./components/settingsContent/checkout/SettingsCheckout";
import SettingsPayment from "./components/settingsContent/payment/SettingsPayment";
import SettingsConditionalPayment from "./components/settingsContent/payment/SettingsConditionalPayment";
import SettingsInvoice from "./components/settingsContent/invoice/SettingsInvoice";
import SettingsMisc from "./components/settingsContent/misc/SettingsMisc";
import SettingsAdvancedGallery from "./components/settingsContent/misc/SettingsAdvancedGallery";
import SettingsReCaptchaV2 from "./components/settingsContent/misc/SettingsReCaptchaV2";
import SettingsThirdParty from "./components/settingsContent/misc/SettingsThirdParty";
import SettingsModules from "./components/settingsContent/advanced/SettingsModules";
import SettingsPWA from "./components/settingsContent/advanced/SettingsPWA";
import SettingsDebug from "./components/settingsContent/advanced/SettingsDebug";

// Settings from Redux Store
import "./store/settings-store";

// Menu Component
import Menu from './components/menu'

// Save Changes Component
import SaveSettings from "./components/sub-components/SaveSettings";

// UI Components
import Transition from "./components/UI/Transition";
import Tooltip from "./components/UI/Tooltip";

const WPTravelSettings = () => {
    const settingsData = useSelect((select) => {
        return select("WPTravel/Admin").getSettings();
    }, []);

    const { updateSettings } = dispatch('WPTravel/Admin');

    const allData = useSelect((select) => {
        return select("WPTravel/Admin").getAllStore();
    }, []);

    const { options } = allData;

    let blockTab = {}
    let downloadsTab = {}

    if (_wp_travel.is_blocks_enable) {
        const SettingBlocks = () => {
            return (
                <>
                    <div className="wp-travel-section-header">
                        <h2 className="wp-travel-section-header-title">
                            {__("Block Settings", "wp-travel")}
                        </h2>
                        <p className="wp-travel-section-header-description">
                            {__("WP Travel Block settings according to your choice.", "wp-travel")}
                        </p>
                    </div>
                    <div className='wp-travel-section-content'>
                        {applyFilters('wptravel_settings_tab_content_block_settings', [], allData)}
                    </div>
                </>
            )
        }

        blockTab = {
            name: "block-settings",
            title: __("Block Setting", "wp-travel"),
            className: "tab-advanced",
            content: SettingBlocks,
        }
    }

    if (_wp_travel.pro_version == null || _wp_travel.pro_version < 5.4) {
        downloadsTab = {
            name: "downloads",
            title: __("Downloads", "wp-travel"),
            className: "tab-trip",
            content: SettingsDownloadsTemp,
        }
    }

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
                name: "conditional-payment",
                title: __("Conditional Payment", "wp-travel"),
                className: "tab-payment",
                content: SettingsConditionalPayment,
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
                name: "pwa",
                title: __("PWA", "wp-travel"),
                className: "tab-advanced",
                content: SettingsPWA,
            },
            {
                name: "debug",
                title: __("Debug", "wp-travel"),
                className: "tab-advanced",
                content: SettingsDebug,
            },
            blockTab,
            downloadsTab
        ],
        allData
    );

    const [activeTab, setActiveTab] = useState("currency");

    const handleTabClick = (tab) => {
        setActiveTab(tab);
        if (window.innerWidth < 768) {
            setIsMobileNavOpen(false)
            document.body.style.overflow = "unset"
        }
    };

    const [isSticky, setIsSticky] = useState(false);

    const stickyRef = useRef();
    const settingsRef = useRef();

    useEffect(() => {
        window.addEventListener("scroll", handleScroll);

        return () => {
            window.addEventListener("scroll", handleScroll);
        }
    }, [])

    const [isMobileNavOpen, setIsMobileNavOpen] = useState(false);

    const handleScroll = () => {
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
    }

    const handleMenuClose = () => {
        setIsMobileNavOpen(false)
        document.body.style.overflow = "unset"
    }

    return (
        <>
            {allData.is_sending_request && <div className="wp-travel-spinner-overlay"><Spinner /></div>}
            <div id="wp-travel-mobile-navbar" className={isSticky ? "wp-travel-nav-sticky" : "wp-travel-nav"}>
                <button className="wp-travel-nav-menu-button" onClick={handleMenuOpen}><i className="fa fa-bars" aria-hidden="true"></i></button>
                {/* Mobile Menu */}
                {isMobileNavOpen && window.innerWidth < 768 && createPortal(
                    <Menu
                        className={`${window.innerWidth < 768 ? "wp-travel-active" : ""}`}
                        tabs={tabs}
                        handleTabClick={handleTabClick}
                        isMobileNavOpen={isMobileNavOpen}
                        closeMenu={handleMenuClose}
                        activeTab={activeTab}
                    />,
                    document.getElementById('wpwrap')
                )}
            </div>
            <div className="wp-travel-block-tabs-wrapper wp-travel-trip-settings">
                <div ref={stickyRef} className="wp-travel-main-container">
                    <div className="wp-travel-settings-container">
                        {/* Side Menu */}
                        <Menu tabs={tabs} handleTabClick={handleTabClick} activeTab={activeTab} />

                        {/* Settings Section */}
                        <div className="wp-travel-settings-section-wrapper">
                            <div ref={settingsRef} className="wp-travel-settings-section">
                                {/* Render tab contents from the tabs object */}
                                {tabs.map((tab) => (
                                    <ErrorBoundary key={`err-boundary-${tab.name}`}>
                                        {activeTab == tab.name &&
                                            tab.content &&
                                            isValidElement(<tab.content />) &&
                                            <Transition zIndex={30} duration={300} translateX={0} translateY={25}>
                                                <tab.content key={tab.className + "-" + tab.name} />
                                            </Transition>
                                        }
                                    </ErrorBoundary>
                                ))}
                            </div>
                            <div id="wp-travel-save-changes-container">
                                <SaveSettings position="bottom" settingsRef={settingsRef} />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

/**
 * remove network error
 * @since 6.7.0
 */
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

const SettingsDownloadsTemp = () => {
    return (
        _wp_travel.pro_version < 5.4 && _wp_travel.pro_version != null &&
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {__("Downloads", "wp-travel")}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More Downloads settings according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <Notice isDismissible={false} status="informational">
                    <strong>{__('Looks like you haven\'t updated your WP Travel Pro plugin.', 'wp-travel')}</strong>
                    <br />
                    {__('Update WP Travel Pro to gain access to additional settings.', 'wp-travel')}
                    <br />
                    <br />
                    <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Update WP Travel Pro', 'wp-travel')}</a>
                </Notice>
            </div>
        </>
        ||
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {__("Downloads", "wp-travel")}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More Downloads settings according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <Notice isDismissible={false} status="informational">
                    <strong>{__('Want features for Itinerary Downloads?', 'wp-travel')}</strong>
                    <br />
                    {__('By upgrading to Pro, you will get access to additional features including itinerary downloads.', 'wp-travel')}
                    <br />
                    <br />
                    <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('WP Travel Pro', 'wp-travel')}</a>
                </Notice><br />
            </div>
        </>
    )
}

// Tooltips
addFilter('wp_travel_submodule_downloads_use_relative_path', 'wp_travel', () => {
    return (
        <Tooltip
            text={__('Use image path as var/www/html... instead of http to generate pdf itinerary.', 'wp-travel-authorizenet')}
        >
            <span>
                <i className="fa fa-info-circle" aria-hidden="true"></i>
            </span>
        </Tooltip>
    )
})

addFilter('wp_travel_submodule_currency_exchange_use_api_layer_tooltip', 'wp_travel', () => {
    return (
        <Tooltip
            text={__('Requires API Layer Fixer API Key instead of regular Fixer API Key', 'wp-travel-authorizenet')}
        >
            <span>
                <i className="fa fa-info-circle" aria-hidden="true"></i>
            </span>
        </Tooltip>
    )
})

addFilter('wp_travel_submodule_google_calendar_redirectUrl_tooltip', 'wp_travel', () => {
    return (
        <Tooltip
            text={__(`Your redirect url i.e: ${window.location.href}`, 'wp-travel')}
        >
            <span>
                <i className="fa fa-info-circle" aria-hidden="true"></i>
            </span>
        </Tooltip>
    )
})

addFilter('wp_travel_submodule_multiple_currency_use_geolocation_tooltip', 'wp_travel', () => {
    return (
        <Tooltip
            text={__('If enabled, the manual currency selector option will be disabled from the frontend.', 'wp-travel-multiple-currency')}
        >
            <span>
                <i className="fa fa-info-circle" aria-hidden="true"></i>
            </span>
        </Tooltip>
    )
})

addFilter('wp_travel_submodule_multiple_currency_menu_location_tooltip', 'wp_travel', () => {
    return (
        <Tooltip
            text={__('Select the menu location where you want to display the currency selector.', 'wp-travel-multiple-currency')}
        >
            <span>
                <i className="fa fa-info-circle" aria-hidden="true"></i>
            </span>
        </Tooltip>
    )
})

addFilter('wp_travel_submodule_multiple_currency_reset_cache_tooltip', 'wp_travel', () => {
    return (
        <Tooltip
            text={__('Cache automatically replaced with new data in every 4 hours, to force reset click "Reset" button.', 'wp-travel-multiple-currency')}
        >
            <span>
                <i className="fa fa-info-circle" aria-hidden="true"></i>
            </span>
        </Tooltip>
    )
})

addFilter('wp_travel_submodule_mailchimp_optin_tooltip', 'wp_travel', () => {
    return (
        <Tooltip
            text={__('Enabling this option will enable the Mailchimp double opt-in option i.e sends contact an opt-in confirmation email when they subscribe.', 'wp-travel-mailchimp')}
        >
            <span>
                <i className="fa fa-info-circle" aria-hidden="true"></i>
            </span>
        </Tooltip>
    )
})

addFilter('wp_travel_submodule_multiple_currency_reset_cache_tooltip', 'wp_travel', () => {
    return (
        <Tooltip
            text={__('Cache automatically replaced with new data in every 4 hours, to force reset click "Reset" button.', 'wp-travel-multiple-currency')}
        >
            <span>
                <i className="fa fa-info-circle" aria-hidden="true"></i>
            </span>
        </Tooltip>
    )
})

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
/**
 * Rendering network 
 * @since 6.7.0
 */
domReady(function () {
    if ('undefined' !== typeof document.getElementById('wp-travel-network-settings-block') && null !== document.getElementById('wp-travel-network-settings-block')) {
        render(<WPTravelNetworkSettings />, document.getElementById('wp-travel-network-settings-block'));
    }
});
