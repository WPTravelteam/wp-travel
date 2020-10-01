import { useSelect, dispatch } from '@wordpress/data';
import { PanelRow, Button, Snackbar } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

import { _n, __} from '@wordpress/i18n';


const SaveSettings = () => {
    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);
    const { updateRequestSending, updateStateChange, displaySavedMessage } = dispatch('WPTravel/Admin');

    const { has_state_changes, show_updated_message } = allData;
    
    setTimeout(() => {
        if ( typeof show_updated_message != 'undefined' && show_updated_message ) {
            displaySavedMessage(false)
        }
    }, 2000)
    return <>
        <PanelRow className="wp-travel-ui wp-travel-ui-card wp-travel-ui-card-no-border wp-travel-save-changes">
            <div>
                {has_state_changes&&<div className="wp-travel-save-notice">{__('* Please save the changes', 'wp-travel' )}</div>}
                {show_updated_message && <div>
                    <p class="text-success"><strong>{__('Settings Saved', 'wp-travel')}</strong></p>
                </div> }
            </div>
            <Button isPrimary onClick={()=>{
                updateRequestSending(true);
                
                
                
                apiFetch( { url: `${ajaxurl}?action=wp_travel_update_settings&_nonce=${_wp_travel._nonce}`, data:allData, method:'post' } ).then( res => {
                    updateRequestSending(false);
                    
                    if( res.success && "WP_TRAVEL_UPDATED_SETTINGS" === res.data.code){
                        updateStateChange(false)
                        displaySavedMessage(true)
                    }
                } );
                
            }}
            disabled={!has_state_changes}
            >{__('Save Settings', 'wp-travel' )}</Button>
        </PanelRow>
        <div className="wp-travel-setting-system-info">
            <a href="edit.php?post_type=itinerary-booking&page=sysinfo" title="View system information"><span className="dashicons dashicons-info"></span>System Information</a>
        </div>
    </>
}

export default SaveSettings;