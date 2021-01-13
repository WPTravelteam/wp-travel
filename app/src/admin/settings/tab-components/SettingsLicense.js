import { applyFilters, addFilter } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow,TextControl, ToggleControl, RadioControl, Notice, Button, Spinner } from '@wordpress/components';
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
            <div className="wp-travel-license-details">
                {applyFilters( 'wp_travel_license_tab_fields', [], allData ) }
            </div>
        </ErrorBoundary>
    </div>
}

addFilter('wp_travel_license_tab_fields', 'wp_travel', (content, allData) => {

    const [ isProcessingApi, setIsProcessingApi ] = useState(null);
    
    const {premium_addons_keys, premium_addons_data} = allData
    const { updateSettings } = dispatch('WPTravel/Admin');

    const updateLicenseData = (key, value, index) => {
        let {premium_addons_data} = allData
        
        let _premium_addons_data = premium_addons_data;
        _premium_addons_data[index][key] = value

        updateSettings({
            ...allData,
            premium_addons_data: [..._premium_addons_data]
        })
        
        // updateSettings({ ...allData, [storeName] : { ...allData[storeName], [storeKey]: value } })

    }

    let LicenseFields = ( addons, index  ) => {

        let props = {
            addons:addons,
            index:index
        }

        let license =   'undefined' != typeof props.addons ? props.addons : []

        return <>
            {
                'undefined' != typeof license && 
                <div className={`license-details__item license-details__item__${'undefined' != typeof license._option_prefix ? license._option_prefix : '' }`}>

                    <h3>{license.item_name}</h3>
                    { 'undefined' !== typeof license.host && 'tp' == license.host &&
                        <>
                            <PanelRow>
                                <TextControl
                                    placeholder={__( 'Enter License Key', 'wp-travel' )}
                                    value={license.license_key}
                                    onChange={(value) => {
                                        updateLicenseData( 'license_key', value, props.index ) 
                                    }}
                                />
                                {
                                    // Spinner will be trigger only if props index value of button matched with current state value.
                                    isProcessingApi == props.index ? <Spinner /> : ''
                                }
                                {
                                    ('valid' == license.status && false !== license.license_data ) ?
                                        <Button id={props.index} isSecondary onClick={() => deactivateLicense(license,props)}>{ __( 'Deactivate', 'wp-travel' ) }</Button>
                                        : <Button isSecondary onClick={() => activateLicense(license,props)}>{ __( 'Activate', 'wp-travel' ) }</Button>
                                }
                                <p className="description">{__( `Enter license key for ${license.item_name} here.`, 'wp-travel' )}</p>
                            </PanelRow>
                            <div className="license_status">
                                { license.license_key && statusMsg(license)}
                                {
                                    'valid' == license.status &&
                                    'lifetime' !== license.license_data.expires ? <p><strong>{__( 'Expires In : ' + licenseExpiryDate(license.license_data.expires), 'wp-travel' )}</strong></p>      : ''
                                }
                            </div>
                        </>
                    }

                    { 'undefined' !== typeof license.host && 'freemius' == license.host &&
                        <>
                            {license.status && 'valid' == license.status ? 
                                <a href={license.account_link} title="Manage License">Manage License</a>
                                :
                                <a href={license.license_link} title="Add License">Add License</a>
                            }
                        </>
                    }
                </div>
            }
        </>
    }

    /**
     * Return expiry date of license on MM DD , YYYY (January 1, 2022) format.
     * @param {Expiry date of license} expiryDate 
     */
    const licenseExpiryDate = (expiryDate) => {
        var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        var myDate = new Date(expiryDate);
        var year = myDate.getFullYear();
        var month = months[myDate.getMonth()];
        var day = myDate.getDate();
        return (month + ' ' + day + ',' + ' ' + year);
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

    /**
     * Ajax request for getting License Data.
     * @param {License Activation} license 
     */
    const activateLicense = (license, props) => {
        setIsProcessingApi(props.index);
        if ( '' == license.license_key ) {
            alert('Please Enter License Key!!');
            setIsProcessingApi(null);
            return;
        }
        let requestLicenseData = {};
        var item_key = license._option_prefix + 'license_key' // Prefix key.

        // Request Data.
        requestLicenseData = {
            _option_prefix: license._option_prefix + 'license_',
            item_name: license.item_name,
        }
        requestLicenseData[item_key] = license.license_key; // Adding prefix key on request data.
        apiFetch( 
            { 
                url: `${ajaxurl}?action=wp_travel_activate_license&_nonce=${_wp_travel._nonce}`,
                data: requestLicenseData,
                method:'post' 
            } 
            ).then( res => {
                if ( res.success && "WP_TRAVEL_LICENSE_ACTIVATION" == res.data.code ) {
                    updateLicenseData( 'license_data', res.data.license, props.index )
                    updateLicenseData( 'status', res.data.license.license, props.index )
                }
                setIsProcessingApi(null)
        } );
    }

    /**
     * Ajax request for getting License Data.
     * @param {License Deactivation} license 
     */
    const deactivateLicense = (license, props) => {
        setIsProcessingApi(props.index)
        let requestLicenseData = {};
        var item_key = license._option_prefix + 'license_key'// Prefix key.

        // Request Data.
        requestLicenseData = {
            _option_prefix: license._option_prefix + 'license_',
            item_name: license.item_name,
        }
        requestLicenseData[item_key] = license.license_key; // Adding prefix key on request data.
        apiFetch( 
            { 
                url: `${ajaxurl}?action=wp_travel_deactivate_license&_nonce=${_wp_travel._nonce}`,
                data: requestLicenseData,
                method:'post' 
            } 
            ).then( res => {
                if ( res.success && "WP_TRAVEL_LICENSE_ACTIVATION" == res.data.code ) { 
                    updateLicenseData( 'license_data', res.data.license, props.index )
                    updateLicenseData( 'status', res.data.license.license, props.index )
                }
                setIsProcessingApi(null)
        } );
    }

    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Want to add more features in WP Travel?', 'wp-travel')}</strong>
                <br />
                {__('Get WP Travel Pro modules for payment, trip extras, Inventory management and other premium features.', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]

    { premium_addons_data && 'undefined' != typeof premium_addons_data && premium_addons_data.length > 0  &&
        <>
        {
        
            premium_addons_data.map((addons, index) => {

                content = [
                    ...content,
                    LicenseFields(addons, index)
                ]

            }) 
        }
            
        </>
    }

    
    return content
});