import { useSelect, dispatch } from '@wordpress/data';
import { PanelRow, Button, Snackbar } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

import { _n, __} from '@wordpress/i18n';

const __i18n = {
	..._wp_travel_admin.strings
}
const SaveCoupons = (props) => {
    const allData = useSelect((select) => {
        return select('WPTravel/Coupon').getAllStore()
    }, []);
  
    const { updateRequestSending, updateStateChange, displaySavedMessage } = dispatch('WPTravel/Coupon');

    const { has_state_changes, show_updated_message, disable_save } = allData;
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
                    <p className="text-success"><strong>{__('Coupon Saved !', 'wp-travel')}</strong></p>
                </div> }
            </div>
            <Button isPrimary onClick={()=>{
                 
                updateRequestSending(true);
                apiFetch( { url: `${ajaxurl}?action=wptravel_update_coupon&_nonce=${_wp_travel._nonce}&coupon_id=${_wp_travel.postID}`, data:allData, method:'post' } ).then( res => {
                    updateRequestSending(false);
                    
                    if( res.success && "WP_TRAVEL_UPDATED_COUPON" === res.data.code){
                        updateStateChange(false)
                        displaySavedMessage(true)
                        disable_save(true)
                    }
                } );
            }}
            disabled={!has_state_changes || disable_save }
            >{__('Save Coupon', 'wp-travel' )}</Button>
        </PanelRow>
    </>
}

export default SaveCoupons;