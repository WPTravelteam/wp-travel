import {memo} from 'react'
import { useState } from '@wordpress/element'
import { _n, __ } from "@wordpress/i18n";

import Search from '../sub-components/Search/Search';

export default memo((props) => {
    const [showGeneralTab, setShowGeneralTab] = useState(true);
    const [showTripsTab, setShowTripsTab] = useState(false);
    const [showEmailTab, setShowEmailTab] = useState(false);
    const [showAccountTab, setShowAccountTab] = useState(false);
    const [showCheckoutTab, setShowCheckoutTab] = useState(false);
    const [showPaymentTab, setShowPaymentTab] = useState(false);
    const [showInvoiceTab, setShowInvoiceTab] = useState(false);
    const [showMiscTab, setShowMiscTab] = useState(false);
    const [showAdvancedTab, setShowAdvancedTab] = useState(false);

    const { activeTab, handleTabClick, tabs, className, isMobileNavOpen, closeMenu } = props

    return (
        <>
            {isMobileNavOpen && window.innerWidth < 768
                && <div id="wp-travel-backdrop" onClick={closeMenu}></div>
            }
            <div id="wp-travel-settings-menu" className={className}>
                {isMobileNavOpen && window.innerWidth < 768
                    && <div className="wp-travel-mobile-menu-close" onClick={closeMenu}><i className='fa fa-times wp-travel-icon-close'></i></div>
                }
                {/* <div className="wp-travel-logo-container">
                    <img id="wp-travel-logo" src={_wp_travel.plugin_url + "assets/images/wp-travel-log.png"}></img>
                    {__("WP Travel", "wp-travel")}
                </div> */}

                <Search handleTabClick={handleTabClick} />
                
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
                                        {_wp_travel.setting_strings.tab_name.general}
                                    </p>
                                </span>
                                <i
                                    className={`fa fa-chevron-${showGeneralTab ? "up" : "down"}`}
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
                                                        key={`wp-travel-tab-${tab.name}`}
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
                                        {_wp_travel.setting_strings.tab_name.trips}
                                    </p>
                                </span>
                                <i
                                    className={`fa fa-chevron-${showTripsTab ? "up" : "down"}`}
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
                                                        key={`wp-travel-tab-${tab.name}`}
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
                                        {_wp_travel.setting_strings.tab_name.email}
                                    </p>
                                </span>
                                <i
                                    className={`fa fa-chevron-${showEmailTab ? "up" : "down"}`}
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
                                                        key={`wp-travel-tab-${tab.name}`}
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
                                        {_wp_travel.setting_strings.tab_name.account}
                                    </p>
                                </span>
                                <i
                                    className={`fa fa-chevron-${showAccountTab ? "up" : "down"}`}
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
                                                        key={`wp-travel-tab-${tab.name}`}
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
                                        {_wp_travel.setting_strings.tab_name.checkout}
                                    </p>
                                </span>
                                <i
                                    className={`fa fa-chevron-${showCheckoutTab ? "up" : "down"}`}
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
                                                        key={`wp-travel-tab-${tab.name}`}
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
                                    <i className="fa fa-credit-card wp-travel-tab-icon"></i>
                                    <p className="wp-travel-primary-tab-title">
                                        {_wp_travel.setting_strings.tab_name.payment}
                                    </p>
                                </span>
                                <i
                                    className={`fa fa-chevron-${showPaymentTab ? "up" : "down"}`}
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
                                                        key={`wp-travel-tab-${tab.name}`}
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
                                    <i className="fa fa-receipt wp-travel-tab-icon"></i>
                                    <p className="wp-travel-primary-tab-title">
                                        {_wp_travel.setting_strings.tab_name.invoice}
                                    </p>
                                </span>
                                <i
                                    className={`fa fa-chevron-${showInvoiceTab ? "up" : "down"}`}
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
                                                        key={`wp-travel-tab-${tab.name}`}
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
                                    <i className="fa fa-folder-open wp-travel-tab-icon"></i>
                                    <p className="wp-travel-primary-tab-title">
                                        {_wp_travel.setting_strings.tab_name.miscellaneous}
                                    </p>
                                </span>
                                <i
                                    className={`fa fa-chevron-${showMiscTab ? "up" : "down"}`}
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
                                                        key={`wp-travel-tab-${tab.name}`}
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
                                    <i className="fa fa-wrench wp-travel-tab-icon"></i>
                                    <p className="wp-travel-primary-tab-title">
                                        {_wp_travel.setting_strings.tab_name.advanced}
                                    </p>
                                </span>
                                <i
                                    className={`fa fa-chevron-${showAdvancedTab ? "up" : "down"}`}
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
                                                        key={`wp-travel-tab-${tab.name}`}
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
})