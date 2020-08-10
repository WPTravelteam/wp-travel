import { applyFilters, addFilter } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow,TextControl, ToggleControl, RadioControl, Notice, Button, Spinner } from '@wordpress/components';
import Select from 'react-select'
import {VersionCompare} from '../../fields/VersionCompare'
import apiFetch from '@wordpress/api-fetch';
import { useEffect, useState } from '@wordpress/element';

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

    const [ licenseApiData, setLicenseApiData ] = useState([]);

    const {premium_addons_keys, premium_addons_data} = allData
    console.log(allData)
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
                        {
                            ('valid' == license.status && false !== license.license_data ) ?
                                <Button isSecondary>{ __( 'Deactivate', 'wp-travel' ) }</Button>
                                : <Button isSecondary onClick={() => activateLicense(license)}>{ __( 'Activate', 'wp-travel' ) }</Button>
                        }
                        <p className="description">{__( `Enter license key for ${license.item_name} here.`, 'wp-travel' )}</p>
                    </PanelRow>
                    <div className="license_status">
                        {license.license_key && statusMsg(license)}
                        {
                            (license.license_key && license.license_data.expires) &&
                        'lifetime' !== license.license_data.expires ? <p>{__( 'Expires In : ' + licenseExpiryDate(license.license_data.expires), 'wp-travel' )}</p>      : ''
                        }
                    </div>
                    {console.log(licenseApiData)}
                </>
            }
        </>
    }

    const licenseExpiryDate = (expiryDate) => {
        var myDate = new Date(expiryDate)
        return myDate
    }

    const statusMsg = ( licenseData ) => {
        if ( '' !== licenseData ) {
            if ( licenseData.status == 'valid' ) {
                return <p className="howTo" style={{color:'green'}}>{ __( 'License Active', 'wp-travel' ) }<span className="dashicons dashicons-yes"></span></p>
            }
            else if ( licenseData.status == 'invalid' ) {
                return <p className="howTo" style={{color:'red'}}>{ __( 'License Invalid', 'wp-travel' ) }<span className="dashicons dashicons-no"></span></p>
            }
            else if ( licenseData.status == 'expired' ) {
                return <p className="howTo" style={{color:'red'}}>{ __( 'License Expired', 'wp-travel' ) }<span className="dashicons dashicons-no"></span></p>
            }
            else if ( licenseData.status == 'inactive' ) {
                return <p className="howTo" style={{color:'red'}}>{ __( 'License Inactive', 'wp-travel' ) }<span className="dashicons dashicons-no"></span></p>
            }
        }
    }

    const activateLicense = (license) => {
        apiFetch( 
            { 
                url: `${ajaxurl}?action=wp_travel_activate_license&_nonce=${_wp_travel._nonce}`,
                data: {
                    _option_prefix: license.option_prefix,
                    item_name: license.item_name,
                    wp_travel_mailchimp_key: license.license_key
                },
                method:'post' 
            } 
            ).then( res => {
                setLicenseApiData(res);
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