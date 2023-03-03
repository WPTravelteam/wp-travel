import { useRef, useState, useEffect } from '@wordpress/element'
import { _n, __ } from "@wordpress/i18n";

import SearchModal from '../helpers/search-component/SearchModal';
import Select from 'react-select'

export default (props) => {
    const [showGeneralTab, setShowGeneralTab] = useState(true);
    const [showTripsTab, setShowTripsTab] = useState(false);
    const [showEmailTab, setShowEmailTab] = useState(false);
    const [showAccountTab, setShowAccountTab] = useState(false);
    const [showCheckoutTab, setShowCheckoutTab] = useState(false);
    const [showPaymentTab, setShowPaymentTab] = useState(false);
    const [showInvoiceTab, setShowInvoiceTab] = useState(false);
    const [showMiscTab, setShowMiscTab] = useState(false);
    const [showAdvancedTab, setShowAdvancedTab] = useState(false);

    const { ref, activeTab, handleTabClick, tabs, className, isMobileNavOpen, closeMenu } = props

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

    return (
        <>
            {isMobileNavOpen && window.innerWidth < 768
                && <div id="wp-travel-backdrop" onClick={closeMenu}></div>
            }
            <div id="wp-travel-settings-menu" className={className}>
                {isMobileNavOpen && window.innerWidth < 768
                    && <div className="wp-travel-mobile-menu-close" onClick={closeMenu}><i className='fa fa-times wp-travel-icon-close'></i></div>
                }
                <div className="wp-travel-logo-container">{__("WP Travel", "wp-travel")}</div>
                <SearchModal ref={ref}>
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
                    <Select
                        defaultValue={selectedOption}
                        onChange={e => {
                            // document.querySelectorAll('.all-tab-component').forEach(function (item) {
                            //     console.log("Item : ", item)
                            //     item.classList.remove('active');
                            // });
                            document.querySelectorAll('.wp-travel-primary-tab-container').forEach(function (item) {
                                // console.log("Item : ", item)
                                // item.classList.remove('active');
                            });
                            document.querySelectorAll('.components-panel__row label').forEach(function (item) {
                                console.log("Item : ", item.innerHTML)
                                // item.classList.remove('active');
                            });
                            // document.getElementById("wp-travel-settings-panel-currency").function (test) {
                            //     console.log(test)
                            // }
                            // console.log(document.getElementById(`wp-travel-settings-panel-${e.tab.tabName}`).innerHTML)
                            // console.log(document.querySelectorAll(".components-panel__row label"));
                            // console.log("The E :",e)
                            handleTabClick(e.tab)
                            // console.log(e)
                            // document.getElementById('tab-component-' + e.tab).classList.add('active');
                            document.getElementById('wp-travel-tab-' + e.tab).classList.add('is-active');

                            document.getElementById(e.value).scrollIntoView({ behavior: 'smooth' }, true)
                            options
                        }}
                        options={selectOptions}
                    />
                </SearchModal>
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
        </>
    )
}