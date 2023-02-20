import {
    render,
    useState,
    useEffect,
    useRef,
    isValidElement,
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

// Tabs

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
        "wp_travel_settings_tabs",
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
    const [showGeneralTab, setShowGeneralTab] = useState(true);
    const [showTripsTab, setShowTripsTab] = useState(false);
    const [showEmailTab, setShowEmailTab] = useState(false);
    const [showAccountTab, setShowAccountTab] = useState(false);
    const [showCheckoutTab, setShowCheckoutTab] = useState(false);
    const [showPaymentTab, setShowPaymentTab] = useState(false);
    const [showInvoiceTab, setShowInvoiceTab] = useState(false);
    const [showMiscTab, setShowMiscTab] = useState(false);
    const [showAdvancedTab, setShowAdvancedTab] = useState(false);

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

    const myRef = useRef(null);
    const stickyRef = useRef(null);

    const [isSticky, setIsSticky] = useState(false);

    useEffect(() => {
        window.addEventListener("scroll", handleScroll);

        return () => {
            window.removeEventListener("scroll", handleScroll);
        };
    }, []);

    function scrollToElement() {
        console.log(myRef)
        myRef.current?.scrollIntoView({ behavior: "smooth", block: "center", inline: "nearest" });
        myRef.current.focus();
    }

    // useEffect(() => {
    //     window.onscroll = function () { myFunction() }
    //     return () => {
    //         second
    //     }
    // }, [third])

    // function myFunction() {
    //     if (window.pageYOffset >= sticky) {
    //         navbar.classList.add("sticky")
    //     } else {
    //         navbar.classList.remove("sticky");
    //     }
    // }

    //   handleTabClick( selectedOption.value );
    return (
        <>
            <div id="wp-travel-mobile-navbar">
                <button className="wp-travel-nav-menu-button"><i class="fa fa-bars" aria-hidden="true"></i></button>
            </div>
            <div className={wrapperClasses}>
                <div ref={stickyRef} className="wp-travel-main-container">
                    <div className="wp-travel-settings-container">
                        <div className="wp-travel-settings-menu">
                            <div className="wp-travel-logo-container">{__("WP Travel", "wp-travel")}</div>
                            <SearchButton ref={myRef} />
                            {/* 
                        <button className="wp-travel-quick-search">
                            <i
                                className="fa fa-search wp-travel-search-icon"
                                aria-hidden="true"
                            ></i>
                            <span
                                id="wp-travel-quick-search-text"
                                >
                                {__("Quick Search...", "wp-travel")}
                            </span>
                        </button> 
                        */}
                            {/* <div>
                            <Select
                                defaultValue={selectedOption}
                                onChange={e => {
                                    // // document.querySelectorAll('.all-tab-component').forEach(function (item) {
                                    // //     console.log("Item : ", item)
                                    // //     item.classList.remove('active');
                                    // // });
                                    // document.querySelectorAll('.wp-travel-primary-tab-container').forEach(function (item) {
                                    //     // console.log("Item : ", item)
                                    //     // item.classList.remove('active');
                                    // });
                                    // document.querySelectorAll('.components-panel__row label').forEach(function (item) {
                                    //     console.log("Item : ", item.innerHTML)
                                    //     // item.classList.remove('active');
                                    // });
                                    // // document.getElementById("wp-travel-settings-panel-currency").function (test) {
                                    // //     console.log(test)
                                    // // }
                                    // // console.log(document.getElementById(`wp-travel-settings-panel-${e.tab.tabName}`).innerHTML)
                                    // // console.log(document.querySelectorAll(".components-panel__row label"));
                                    // // console.log("The E :",e)
                                    // handleTabClick(e.tab)
                                    // // console.log(e)
                                    // // document.getElementById('tab-component-' + e.tab).classList.add('active');
                                    // document.getElementById('wp-travel-tab-' + e.tab).classList.add('is-active');

                                    // document.getElementById(e.value).scrollIntoView({ behavior: 'smooth' }, true)
                                    options
                                }}
                                options={selectOptions}
                            />
                        </div> */}

                            <div className="wp-travel-tabs-container">
                                <div className="wp-travel-tabs">
                                    {/* General */}
                                    <div className="wp-travel-primary-tab-container">
                                        <button
                                            className="wp-travel-primary-tab"
                                            onClick={() => setShowGeneralTab(!showGeneralTab)}
                                        >
                                            <span className="wp-travel-primary-tab-info">
                                                <i className="fa fa-desktop wp-travel-tab-icon"></i>
                                                <p className="wp-travel-primary-tab-title">
                                                    {__("General", "wp-travel")}
                                                </p>
                                            </span>
                                            <i
                                                class={`fa fa-chevron-${showGeneralTab ? "up" : "down"}`}
                                            ></i>
                                        </button>
                                        <div className="wp-travel-secondary-tabs-container">
                                            {/* Render tab conditionally with respect to the className in tabs object */}
                                            {showGeneralTab && (
                                                <div>
                                                    {tabs.map(
                                                        (tab) =>
                                                            tab.className == "tab-general" && (
                                                                <div
                                                                    id={`wp-travel-tab-${tab.name}`}
                                                                    className={`wp-travel-secondary-tab ${activeTab == tab.name ? "is-active" : ""
                                                                        }`}
                                                                    onClick={() => handleTabClick(tab.name)}
                                                                >
                                                                    {tab.title}
                                                                </div>
                                                            )
                                                    )}
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                    {/* Trips */}
                                    <div className="wp-travel-primary-tab-container">
                                        <button
                                            className="wp-travel-primary-tab"
                                            onClick={() => setShowTripsTab(!showTripsTab)}
                                        >
                                            <span className="wp-travel-primary-tab-info">
                                                <i className="fa fa-plane wp-travel-tab-icon"></i>
                                                <p className="wp-travel-primary-tab-title">
                                                    {__("Trips", "wp-travel")}
                                                </p>
                                            </span>
                                            <i
                                                class={`fa fa-chevron-${showTripsTab ? "up" : "down"}`}
                                            ></i>
                                        </button>
                                        <div className="wp-travel-secondary-tabs-container">
                                            {/* Render tab conditionally with respect to the className in tabs object */}
                                            {showTripsTab && (
                                                <div>
                                                    {tabs.map(
                                                        (tab) =>
                                                            tab.className == "tab-trip" && (
                                                                <div
                                                                    className={`wp-travel-secondary-tab ${activeTab == tab.name ? "is-active" : ""
                                                                        }`}
                                                                    onClick={() => handleTabClick(tab.name)}
                                                                >
                                                                    {tab.title}
                                                                </div>
                                                            )
                                                    )}
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                    {/* Email */}
                                    <div className="wp-travel-primary-tab-container">
                                        <button
                                            className="wp-travel-primary-tab"
                                            onClick={() => setShowEmailTab(!showEmailTab)}
                                        >
                                            <span className="wp-travel-primary-tab-info">
                                                <i className="fa fa-envelope wp-travel-tab-icon"></i>
                                                <p className="wp-travel-primary-tab-title">
                                                    {__("Email", "wp-travel")}
                                                </p>
                                            </span>
                                            <i
                                                class={`fa fa-chevron-${showEmailTab ? "up" : "down"}`}
                                            ></i>
                                        </button>
                                        <div className="wp-travel-secondary-tabs-container">
                                            {/* Render tab conditionally with respect to the className in tabs object */}
                                            {showEmailTab && (
                                                <div>
                                                    {tabs.map(
                                                        (tab) =>
                                                            tab.className == "tab-email" && (
                                                                <div
                                                                    className={`wp-travel-secondary-tab ${activeTab == tab.name ? "is-active" : ""
                                                                        }`}
                                                                    onClick={() => handleTabClick(tab.name)}
                                                                >
                                                                    {tab.title}
                                                                </div>
                                                            )
                                                    )}
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                    {/* Account */}
                                    <div className="wp-travel-primary-tab-container">
                                        <button
                                            className="wp-travel-primary-tab"
                                            onClick={() => setShowAccountTab(!showAccountTab)}
                                        >
                                            <span className="wp-travel-primary-tab-info">
                                                <i className="fa fa-user wp-travel-tab-icon"></i>
                                                <p className="wp-travel-primary-tab-title">
                                                    {__("Account", "wp-travel")}
                                                </p>
                                            </span>
                                            <i
                                                class={`fa fa-chevron-${showAccountTab ? "up" : "down"}`}
                                            ></i>
                                        </button>
                                        <div className="wp-travel-secondary-tabs-container">
                                            {/* Render tab conditionally with respect to the className in tabs object */}
                                            {showAccountTab && (
                                                <div>
                                                    {tabs.map(
                                                        (tab) =>
                                                            tab.className == "tab-account" && (
                                                                <div
                                                                    className={`wp-travel-secondary-tab ${activeTab == tab.name ? "is-active" : ""
                                                                        }`}
                                                                    onClick={() => handleTabClick(tab.name)}
                                                                >
                                                                    {tab.title}
                                                                </div>
                                                            )
                                                    )}
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                    {/* Checkout */}
                                    <div className="wp-travel-primary-tab-container">
                                        <button
                                            className="wp-travel-primary-tab"
                                            onClick={() => setShowCheckoutTab(!showCheckoutTab)}
                                        >
                                            <span className="wp-travel-primary-tab-info">
                                                <i className="fa fa-shopping-cart wp-travel-tab-icon"></i>
                                                <p className="wp-travel-primary-tab-title">
                                                    {__("Checkout", "wp-travel")}
                                                </p>
                                            </span>
                                            <i
                                                class={`fa fa-chevron-${showCheckoutTab ? "up" : "down"}`}
                                            ></i>
                                        </button>
                                        <div className="wp-travel-secondary-tabs-container">
                                            {/* Render tab conditionally with respect to the className in tabs object */}
                                            {showCheckoutTab && (
                                                <div>
                                                    {tabs.map(
                                                        (tab) =>
                                                            tab.className == "tab-checkout" && (
                                                                <div
                                                                    className={`wp-travel-secondary-tab ${activeTab == tab.name ? "is-active" : ""
                                                                        }`}
                                                                    onClick={() => handleTabClick(tab.name)}
                                                                >
                                                                    {tab.title}
                                                                </div>
                                                            )
                                                    )}
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                    {/* Payment */}
                                    <div className="wp-travel-primary-tab-container">
                                        <button
                                            className="wp-travel-primary-tab"
                                            onClick={() => setShowPaymentTab(!showPaymentTab)}
                                        >
                                            <span className="wp-travel-primary-tab-info">
                                                <i class="fa fa-credit-card wp-travel-tab-icon"></i>
                                                <p className="wp-travel-primary-tab-title">
                                                    {__("Payment", "wp-travel")}
                                                </p>
                                            </span>
                                            <i
                                                class={`fa fa-chevron-${showPaymentTab ? "up" : "down"}`}
                                            ></i>
                                        </button>
                                        <div className="wp-travel-secondary-tabs-container">
                                            {/* Render tab conditionally with respect to the className in tabs object */}
                                            {showPaymentTab && (
                                                <div>
                                                    {tabs.map(
                                                        (tab) =>
                                                            tab.className == "tab-payment" && (
                                                                <div
                                                                    className={`wp-travel-secondary-tab ${activeTab == tab.name ? "is-active" : ""
                                                                        }`}
                                                                    onClick={() => handleTabClick(tab.name)}
                                                                >
                                                                    {tab.title}
                                                                </div>
                                                            )
                                                    )}
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                    {/* Invoice */}
                                    <div className="wp-travel-primary-tab-container">
                                        <button
                                            className="wp-travel-primary-tab"
                                            onClick={() => setShowInvoiceTab(!showInvoiceTab)}
                                        >
                                            <span className="wp-travel-primary-tab-info">
                                                <i class="fa fa-receipt wp-travel-tab-icon"></i>
                                                <p className="wp-travel-primary-tab-title">
                                                    {__("Invoice", "wp-travel")}
                                                </p>
                                            </span>
                                            <i
                                                class={`fa fa-chevron-${showInvoiceTab ? "up" : "down"}`}
                                            ></i>
                                        </button>
                                        <div className="wp-travel-secondary-tabs-container">
                                            {/* Render tab conditionally with respect to the className in tabs object */}
                                            {showInvoiceTab && (
                                                <div>
                                                    {tabs.map(
                                                        (tab) =>
                                                            tab.className == "tab-invoice" && (
                                                                <div
                                                                    className={`wp-travel-secondary-tab ${activeTab == tab.name ? "is-active" : ""
                                                                        }`}
                                                                    onClick={() => handleTabClick(tab.name)}
                                                                >
                                                                    {tab.title}
                                                                </div>
                                                            )
                                                    )}
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                    {/* Miscellaneous */}
                                    <div className="wp-travel-primary-tab-container">
                                        <button
                                            className="wp-travel-primary-tab"
                                            onClick={() => setShowMiscTab(!showMiscTab)}
                                        >
                                            <span className="wp-travel-primary-tab-info">
                                                <i class="fa fa-folder-open wp-travel-tab-icon"></i>
                                                <p className="wp-travel-primary-tab-title">
                                                    {__("Miscellaneous", "wp-travel")}
                                                </p>
                                            </span>
                                            <i
                                                class={`fa fa-chevron-${showMiscTab ? "up" : "down"}`}
                                            ></i>
                                        </button>
                                        <div className="wp-travel-secondary-tabs-container">
                                            {/* Render tab conditionally with respect to the className in tabs object */}
                                            {showMiscTab && (
                                                <div>
                                                    {tabs.map(
                                                        (tab) =>
                                                            tab.className == "tab-misc" && (
                                                                <div
                                                                    className={`wp-travel-secondary-tab ${activeTab == tab.name ? "is-active" : ""
                                                                        }`}
                                                                    onClick={() => handleTabClick(tab.name)}
                                                                >
                                                                    {tab.title}
                                                                </div>
                                                            )
                                                    )}
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                    {/* Advanced */}
                                    <div className="wp-travel-primary-tab-container">
                                        <button
                                            className="wp-travel-primary-tab"
                                            onClick={() => setShowAdvancedTab(!showAdvancedTab)}
                                        >
                                            <span className="wp-travel-primary-tab-info">
                                                <i class="fa fa-wrench wp-travel-tab-icon"></i>
                                                <p className="wp-travel-primary-tab-title">
                                                    {__("Advanced", "wp-travel")}
                                                </p>
                                            </span>
                                            <i
                                                class={`fa fa-chevron-${showAdvancedTab ? "up" : "down"}`}
                                            ></i>
                                        </button>
                                        <div className="wp-travel-secondary-tabs-container">
                                            {/* Render tab conditionally with respect to the className in tabs object */}
                                            {showAdvancedTab && (
                                                <div>
                                                    {tabs.map(
                                                        (tab) => (
                                                            tab.className == "tab-advanced" && (
                                                                <div
                                                                    className={`wp-travel-secondary-tab ${activeTab == tab.name ? "is-active" : ""
                                                                        }`}
                                                                    onClick={() => handleTabClick(tab.name)}
                                                                >
                                                                    {tab.title}
                                                                </div>
                                                            )
                                                        )
                                                    )}
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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

const WPTravelNetworkSettings = () => {
    const settingsData = useSelect((select) => {
        return select("WPTravel/Admin").getSettings();
    }, []);

    const allData = useSelect((select) => {
        return select("WPTravel/Admin").getAllStore();
    }, []);

    const { options } = allData;

    let wrapperClasses = "wp-travel-block-tabs-wrapper wp-travel-trip-settings";
    wrapperClasses = allData.is_sending_request
        ? wrapperClasses + " wp-travel-sending-request"
        : wrapperClasses;

    // Add filter to tabs.
    let tabs = applyFilters(
        "wp_travel_network_settings_tabs",
        [
            {
                name: "license",
                title: __("License", "wp-travel"),
                className: "tab-license",
                content: SettingsLicense,
            },
        ],
        allData
    );
    return (
        <div className={wrapperClasses}>
            {allData.is_sending_request && <Spinner />}
            <SaveSettings position="top" />
            <TabPanel
                className="wp-travel-block-tabs"
                activeClass="active-tab"
                onSelect={() => false}
                tabs={tabs}
            >
                {(tab) => (
                    <ErrorBoundary>
                        {tab.content && isValidElement(<tab.content />) ? (
                            <tab.content />
                        ) : (
                            ""
                        )}{" "}
                        {/* Need to remove this latter. add all content with filter instead */}
                        {applyFilters(
                            `wptravel_settings_tab_content_${tab.name.replaceAll("-", "_")}`,
                            [],
                            allData
                        )}
                    </ErrorBoundary>
                )}
            </TabPanel>
            <SaveSettings position="bottom" />
        </div>
    );
};

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

domReady(function () {
    if (
        "undefined" !==
        typeof document.getElementById("wp-travel-network-settings-block") &&
        null !== document.getElementById("wp-travel-network-settings-block")
    ) {
        render(
            <WPTravelNetworkSettings />,
            document.getElementById("wp-travel-network-settings-block")
        );
    }
});
