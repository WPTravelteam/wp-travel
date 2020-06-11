import { useSelect, dispatch } from '@wordpress/data';
import { PanelRow, Button, Snackbar } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';

import { _n, __} from '@wordpress/i18n';


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
            {has_state_changes&&<div className="wp-travel-save-notice">{__('* Please save the changes', 'wp-travel' )}</div>}
            {show_updated_message && <div>
                <p class="text-success"><strong>{__('Trip Saved', 'wp-travel')}</strong></p>
            </div> }
        </div>
        <Button isPrimary onClick={()=>{
            updateRequestSending(true);

            // if( !is_multiple_dates ){
            //     let _pricingIds = [];
            //     allData.pricings.map((_price)=>{
            //         _pricingIds = (false !== _price.id) ?[..._pricingIds, _price.id]:_pricingIds;
            //     });
            //     allData.dates[0]['pricing_ids'] = _pricingIds.join(',');
            // }

            if ( allData.is_fixed_departure ) {
                let _pricingIds = [];
                allData.pricings.map((_price)=>{
                    _pricingIds = (false !== _price.id) ?[..._pricingIds, _price.id]:_pricingIds;
                });
                if( allData.dates.length>0 ) {
                    allData.dates.map((_dates, _datesIndex)=>{
                        // allData.dates[_datesIndex]['pricing_ids'] = _pricingIds.join(','); // Need this for what a life
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

                        jQuery('#original_publish, #publish').val('Update')
                    }
                    displayUpdatedMessage(true)


                }
            } );
            
        }}
        disabled={!has_state_changes}
        >{__('Save Changes', 'wp-travel' )}</Button>
    </PanelRow>
}

export default SaveTripSection;