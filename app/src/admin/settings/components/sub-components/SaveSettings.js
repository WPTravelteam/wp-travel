import { useSelect, dispatch } from '@wordpress/data';
import { useRef, useState, useEffect, useMemo } from '@wordpress/element'
import { PanelRow, Button, Snackbar } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

import { _n, __ } from '@wordpress/i18n';

const __i18n = {
    ..._wp_travel_admin.strings
}

const adminUrl = _wp_travel.admin_url;

const SaveSettings = (props) => {
    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);
    const { updateSettings } = dispatch('WPTravel/Admin');
    const { updateRequestSending, updateStateChange, displaySavedMessage } = dispatch('WPTravel/Admin');

    const { has_state_changes, show_updated_message } = allData;

    setTimeout(() => {
        if (typeof show_updated_message != 'undefined' && show_updated_message) {
            displaySavedMessage(false)
            setSaveChangesActive(false)
        }
    }, 3000)

    let sysInfoUrl = 'edit.php?post_type=itinerary-booking&page=sysinfo';
    if (adminUrl) {
        sysInfoUrl = adminUrl + sysInfoUrl;
    }

    const panelRef = useRef();

    const [isSaveChangeActive, setSaveChangesActive] = useState(false)
    const [isSticky, setIsSticky] = useState(false)

    useEffect(() => {
        window.addEventListener('click', checkViewport)
        window.addEventListener('scroll', checkViewport)

        return () => {
            window.addEventListener('click', checkViewport)
            window.addEventListener('scroll', checkViewport)
        }
    }, [])


    const checkViewport = () => {
        if (panelRef.current) {
            let SaveSettingsBottom = panelRef.current.getBoundingClientRect().bottom
            SaveSettingsBottom >= window.innerHeight
                ? setIsSticky(true)
                : setIsSticky(false)

            props.settingsRef.current.getBoundingClientRect().bottom + (panelRef.current.getBoundingClientRect().bottom - panelRef.current.getBoundingClientRect().top) > window.innerHeight
                ? setIsSticky(true)
                : setIsSticky(false)
        }
    }

    return <>
        {has_state_changes && !show_updated_message &&
            <PanelRow ref={panelRef} className={`wp-travel-save-changes ${!isSaveChangeActive ? "is-active" : ""} ${isSticky ? "is-sticky" : ""} `}>
                <div className='wp-travel-save-notice-container'>
                    <div className="wp-travel-save-notice">
                        <p className="wp-travel-text-danger">
                            <i className="fa fa-exclamation-triangle" aria-hidden="true"></i>
                            {__('Unsaved changes', 'wp-travel')}
                        </p>
                    </div>
                </div>
                <Button
                    isPrimary
                    onClick={() => {
                        updateRequestSending(true);
                        apiFetch({ url: `${ajaxurl}?action=wp_travel_update_settings&_nonce=${_wp_travel._nonce}`, data: JSON.stringify( allData ), method: 'post' }).then(res => {
                            updateRequestSending(false);

                            if (res.success && "WP_TRAVEL_UPDATED_SETTINGS" === res.data.code) {
                                const wpml_enable = typeof res.data.settings != 'undefined' && typeof res.data.settings.wpml_enable != 'undefined' && res.data.settings.wpml_enable;
                                updateSettings({
                                    ...allData,
                                    wpml_enable: wpml_enable
                                })
                                updateStateChange(false)
                                displaySavedMessage(true)
                            }
                        });
                    }}
                    disabled={!has_state_changes}
                >
                    {__('Save Settings', 'wp-travel')}
                </Button>
            </PanelRow>
        }
        {show_updated_message &&
            <PanelRow ref={panelRef} className={`wp-travel-save-changes ${!isSaveChangeActive ? "is-active" : ""} ${isSticky ? "is-sticky" : ""} `}>
                <div className='wp-travel-save-notice-container'>
                    <div className="wp-travel-save-notice">
                        <p className="wp-travel-text-success">
                            <i className="fa fa-check" aria-hidden="true"></i>
                            {__('Settings Saved', 'wp-travel')}
                        </p>
                    </div>
                </div>
            </PanelRow>
        }

        {'bottom' == props.position &&
            <>
                { _wp_travel.is_pro_enable == "no" &&
                    <div id="wptravel-pro-interface">
                        <div class="wptravel-pro-interace-container">
                            <div class="wptravel-pro-upsell-head">
                                <h2 class="wptravel-pro-head-title">Upgrade to WP Travel Pro</h2>
                            </div>
                            <div class="wptravel-pro-upsell-body">
                                <div class="wptravel-pro-body-legend">
                                    <span>Premium Offerings</span>
                                </div>
                                <div class="wptravel-pro-upsell-content">
                                    <div class="wptravel-pro-features-highlight">
                                        <ul>
                                            <li>
                                                <i class="fa fa-check"></i>
                                                <span>
                                                    <strong>WP Travel Downloads: </strong>Allows users to download files
                                                </span>
                                            </li>
                                            <li>
                                                <i class="fa fa-check"></i>
                                                <span><strong>Multiple Currency: </strong>Set currency of your choice</span>
                                            </li>
                                            <li>
                                                <i class="fa fa-check"></i>
                                                <span><strong>Download Itinerary: </strong>Get trips(itineraries) in PDF</span>
                                            </li>
                                            <li>
                                                <i class="fa fa-check"></i>
                                                <span><strong>Multiple Cart</strong></span>
                                            </li>
                                            <li>
                                                <i class="fa fa-check"></i>
                                                <span><strong>Tour Extras: </strong>Extra services in fair packages</span>
                                            </li>
                                            <li>
                                                <i class="fa fa-check"></i>
                                                <span><strong>Group Discount: </strong>Discount tailored to group size</span>
                                            </li>
                                            <li>
                                                <i class="fa fa-check"></i>
                                                <span><strong>Travel Guide: </strong>Display Travel representatives</span>
                                            </li>
                                            <li>
                                                <i class="fa fa-check"></i>
                                                <span><strong>Google Calendar integration</strong></span>
                                            </li>
                                            <li>
                                                <i class="fa fa-check"></i>
                                                <span><strong>Get E-mail Support</strong></span>
                                            </li>
                                            <li>
                                                <i class="fa fa-check"></i>
                                                <span><strong>Send Invoice containing PDF</strong></span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="wptravel-pro-pricing-box-wrap">
                                        <div class="wptravel-pro-pricing-box">
                                            <div class="wptravel-pro-label-wrapper">
                                                <p class="wptravel-pro-starting-label">Starting From</p>
                                                <div class="wptravel-pro-price">
                                                    <span class="currency">$</span>
                                                    <span class="number">99.99</span>
                                                    <abbr title="United States Dollar">USD</abbr>
                                                </div>
                                            </div>
                                            <a href="https://wptravel.io/wp-travel-pro" target="_blank">
                                                <button class="wptravel-pro-btn-goto-pricing">
                                                    <span>Get WP Travel Pro</span>
                                                    <i class="fas fa-arrow-right"></i>
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                }
                <div className="wp-travel-setting-system-info">
                    <a href={sysInfoUrl} title={__i18n.view_system_information} ><span className="dashicons dashicons-info"></span>{__i18n.system_information}</a>
                </div>
            </>
        }
    </>
}

export default SaveSettings;