import { applyFilters, addFilter } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow,TextControl, ToggleControl, RadioControl, Notice, Button } from '@wordpress/components';
import Select from 'react-select'
import {VersionCompare} from '../../fields/VersionCompare'
import apiFetch from '@wordpress/api-fetch';

import ErrorBoundary from '../../error/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);
    return <div className="wp-travel-ui wp-travel-ui-card settings-general">
        <h2>{ __( 'License Details', 'wp-travel' ) }</h2>
        <ErrorBoundary>
            {applyFilters( 'wp_travel_license_tab_fields', [], allData ) }
        </ErrorBoundary>
    </div>
}

addFilter('wp_travel_license_tab_fields', 'wp_travel', (content, allData) => {

    const {options} = allData
    // console.log(options)

    let LicenseFields = ( props  ) => {
        
        const { options } = props.allData;
        let license =  'undefined' != typeof options && 'undefined' != typeof props.premiumAddonKey && 'undefined' != typeof options.premium_addons[props.premiumAddonKey] ? options.premium_addons[props.premiumAddonKey]: {}
        console.log(license)
        
        const { updateSettings } = dispatch('WPTravel/Admin');
        return <>
            {
                'undefined' != typeof options && 'undefined' != typeof license && 
                <>

                    <h3>{license.item_name}</h3>
                    <PanelRow>
                        <TextControl
                            placeholder={__( 'Enter License Key', 'wp-travel' )}
                            value={license.license_key}
                            onChange={(value) => {
                                // updateFact( 'name', value, index ) 
                            }}
                        />
                        <Button isDefault onClick={() => activateLicense(license)}>{ __( 'Activate', 'wp-travel' ) }</Button>
                        <p className="description">{__( `Enter license key for ${license.item_name} here.`, 'wp-travel' )}</p>

                        
                    </PanelRow>
                </>
            }
        </>
    }

    const activateLicense = (license) => {
        console.log(license)
        apiFetch( { url: `${ajaxurl}?action=wp_travel_activate_license&_nonce=${_wp_travel._nonce}`, data:license, method:'post' } ).then( res => {
            // updateRequestSending(false);
            
            if( res.success && "WP_TRAVEL_UPDATED_SETTINGS" === res.data.code){
                // updateStateChange(false)
                // displaySavedMessage(true)
            }
        } );
    }



    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Want to add more features in WP Travel?', 'wp-travel')}</strong>
                <br />
                {__('Get addon for payment, trip extras, Inventory management and other premium features.', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
                &nbsp;&nbsp;
                <a className="button button-primary" target="_blank" href="https://wptravel.io/downloads/">{__('Get WP Travel Addons', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]

    { 'undefined' != typeof options && 'undefined' != typeof options.premium_addons && Object.keys(options.premium_addons).length > 0  &&
        <>
        {
            Object.keys(options.premium_addons).map((addonKey, index) => {

                content = [
                    ...content,
                    <LicenseFields allData={allData} premiumAddonKey={addonKey} />
                ]

            }) 
        }
            
        </>
    }

    
    return content
});