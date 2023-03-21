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
                            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                            {__('Unsaved changes', 'wp-travel')}
                        </p>
                    </div>
                </div>
                <Button
                    isPrimary
                    onClick={() => {
                        updateRequestSending(true);
                        apiFetch({ url: `${ajaxurl}?action=wp_travel_update_settings&_nonce=${_wp_travel._nonce}`, data: allData, method: 'post' }).then(res => {
                            updateRequestSending(false);

                            if (res.success && "WP_TRAVEL_UPDATED_SETTINGS" === res.data.code) {
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
                            <i class="fa fa-check" aria-hidden="true"></i>
                            {__('Settings Saved', 'wp-travel')}
                        </p>
                    </div>
                </div>
            </PanelRow>
        }
        {'bottom' == props.position &&
            <div className="wp-travel-setting-system-info">
                <a href={sysInfoUrl} title={__i18n.view_system_information} ><span className="dashicons dashicons-info"></span>{__i18n.system_information}</a>
            </div>
        }
    </>
}

export default SaveSettings;