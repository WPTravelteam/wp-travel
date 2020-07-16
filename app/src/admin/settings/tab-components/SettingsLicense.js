import { applyFilters, addFilter } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow,TextControl, ToggleControl, RadioControl, Notice, Button } from '@wordpress/components';
import Select from 'react-select'
import {VersionCompare} from '../../fields/VersionCompare'
import apiFetch from '@wordpress/api-fetch';

import ErrorBoundary from '../../../ErrorBoundry/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);
    const {premium_addons_keys, premium_addons_data} = allData
    return <div className="wp-travel-ui wp-travel-ui-card settings-general">
        <h2>{ __( 'License Details', 'wp-travel' ) }</h2>
        <ErrorBoundary>
            {applyFilters( 'wp_travel_license_tab_fields', [], allData ) }
        </ErrorBoundary>
    </div>
}

addFilter('wp_travel_license_tab_fields', 'wp_travel', (content, allData) => {

    const {premium_addons_keys, premium_addons_data} = allData
    const { updateSettings } = dispatch('WPTravel/Admin');

    const updateLicenseData = (key, value, index) => {
        // console.log(allData)
        let {premium_addons_data} = allData
        
        let _premium_addons_data = premium_addons_data;
        _premium_addons_data[index][key] = value

        updateSettings({
            ...allData,
            premium_addons_data: [..._premium_addons_data]
        })
        
        // updateSettings({ ...allData, [storeName] : { ...allData[storeName], [storeKey]: value } })

    }

    let LicenseFields = ( props  ) => {
        let license =   'undefined' != typeof props.addons ? props.addons : []
        return <>
            {
                'undefined' != typeof license && 
                <>

                    <h3>{license.item_name}</h3>
                    <PanelRow>
                        <TextControl
                            placeholder={__( 'Enter License Key', 'wp-travel' )}
                            value={license.license_key}
                            onChange={(value) => {
                                updateLicenseData( 'license_key', value, props.index ) 
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
            
            if( res.success && "WP_TRAVEL_LICENSE_ACTIVATION" === res.data.code){
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

    { 'undefined' != typeof premium_addons_data && premium_addons_data.length > 0  &&
        <>
        {
            premium_addons_data.map((addons, index) => {

                content = [
                    ...content,
                    <LicenseFields addons={addons} index={index}  />
                ]

            }) 
        }
            
        </>
    }

    
    return content
});