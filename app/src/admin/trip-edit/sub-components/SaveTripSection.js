import { useSelect, dispatch } from '@wordpress/data';
import { PanelRow, Button } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

import { _n, __} from '@wordpress/i18n';

const __i18n = {
	..._wp_travel_admin.strings
}

const SaveTripSection = () => {
    const allData = useSelect((select) => {
        return select('WPTravel/TripEdit').getAllStore()
    }, []);
    const { updateRequestSending, setTripData, updateStateChange, displayUpdatedMessage } = dispatch('WPTravel/TripEdit');

    const { has_state_changes, show_updated_message } = allData;
    
    setTimeout(() => {
        if ( typeof show_updated_message != 'undefined' && show_updated_message ) {
            displayUpdatedMessage(false)
        }
    }, 2000)
    return <PanelRow className="wp-travel-ui wp-travel-ui-card wp-travel-ui-card-no-border wp-travel-save-changes">
        <div>
            {has_state_changes&&<div className="wp-travel-save-notice">{__i18n.messages.save_changes}</div>}
            {show_updated_message && <div>
                <p className="text-success"><strong>{__i18n.messages.trip_saved}</strong></p>
            </div> }
        </div>
        <Button variant="primary" onClick={()=>{
            updateRequestSending(true);

            if ( allData.is_fixed_departure ) {
                let _pricingIds = [];
                allData.pricings.map((_price)=>{
                    _pricingIds = (false !== _price.id) ?[..._pricingIds, _price.id]:_pricingIds;
                });
                if( allData.dates.length>0 ) {
                    allData.dates.map((_dates, _datesIndex)=>{
                        if ( !allData.dates[_datesIndex].is_recurring ) {
                            allData.dates[_datesIndex] = {...allData.dates[_datesIndex],...{
                                years:'',
                                months:'',
                                weeks:'',
                                days:'',
                                date_days:''
                            }}
                        }
                    })
                }
            } else {
                allData.dates = [];
            }

            if( !allData.enable_excluded_dates_times){
                allData.excluded_dates_times = [];
            }
            
            delete allData[ 'settings' ];

            apiFetch( { url: `${ajaxurl}?action=wp_travel_update_trip&trip_id=${_wp_travel.postID}&_nonce=${_wp_travel._nonce}`, data:allData, method:'post' } ).then( res => {
                updateRequestSending(false);
                
                if( res.success && "WP_TRAVEL_UPDATED_TRIP" === res.data.code){
                    setTripData(res.data.trip);
                    updateStateChange(false)
                    
                    // @todo: site url must be localize.
                    let url = window.location.href.split('?')[0];
                    if ( url.includes("post-new.php") ) {
                        let replaceString = "post.php?post=" + _wp_travel.postID + "&action=edit"
                        url  = url.replace("post-new.php", replaceString );
                        history.pushState(null, '', url);  

                        jQuery('#original_publish, #publish').val( __i18n.update )
                    }
                    displayUpdatedMessage(true)


                }
            } );
            
        }}
        disabled={!has_state_changes}
        >{__i18n.save_changes}</Button>
    </PanelRow>
}

export default SaveTripSection;