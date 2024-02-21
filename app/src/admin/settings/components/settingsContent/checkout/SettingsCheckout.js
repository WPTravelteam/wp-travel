import { applyFilters, addFilter } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelBody, PanelRow, ToggleControl, TextControl, RadioControl, Notice } from '@wordpress/components';
import Select from 'react-select'
import { VersionCompare } from '../../../../fields/VersionCompare'
import { alignJustify } from '@wordpress/icons';

import ErrorBoundary from '../../../../../ErrorBoundry/ErrorBoundry';

export default () => {

    

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    let _allData = allData

    const { updateSettings } = dispatch('WPTravel/Admin');
    const { enable_multiple_travellers, sorted_gateways, enable_one_page_booking, enable_woo_checkout, options, modules } = allData;

    const { default_settings, saved_settings } = options;
    const { modules: defaultModules } = default_settings;
    let _modules = modules;
    if (!_modules.length) {
        _modules = defaultModules
    }
    // if (defaultModules && Object.keys(defaultModules).length > 0) { 
    const paymentModules = Object.keys(defaultModules).filter(k => 'Payment' === defaultModules[k].category);
    // }
    // console.log(paymentModules);
    return (
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {_wp_travel.setting_strings.checkout.checkout}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More checkout settings according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <ErrorBoundary>
                    {
                        _wp_travel.is_pro_enable == 'yes' && _wp_travel.is_woo_enable == "yes" &&
                        <PanelRow>
                            <label>{_wp_travel.setting_strings.checkout.enable_woo_checkout_label}</label>
                            <div id="wp-travel-checkout-enable-multiple-travelers" className="wp-travel-field-value">
                                <ToggleControl
                                    checked={enable_woo_checkout == 'yes'}
                                    onChange={( ) => {
                      
                                        _allData['enable_woo_checkout'] = 'yes' == enable_woo_checkout ? 'no' : 'yes'
                                        _allData['enable_one_page_booking'] = false
                                        _allData['enable_multiple_checkout'] = false
                                        _allData['enable_multiple_travellers'] = false
                                        
                                        let gateway_key = '';
                                        let mapPaymentGatewayDataAction = sorted_gateways.map((gateway, index) => {
                                            gateway_key = `payment_option_${gateway.key}`
                                            _allData[gateway_key] = 'no'

                                            // Additional settings for non consistant data [Need to improve in addons itself. For now temp fix from here]
                                            if ('payfast' == gateway.key || 'payu' == gateway.key || 'payhere' == gateway.key || 'payu_latam' == gateway.key) {
                                                let additionalArray = `wp_travel_${gateway.key}_settings`
                                                if ('undefined' != typeof _allData[additionalArray]) {
                                                    _allData[additionalArray][gateway_key] = 'no'
                                                } else {
                                                    _allData[additionalArray] = {}
                                                    _allData[additionalArray][gateway_key] = 'no'
                                                }
                                            }
                                        })

                                        let mapPaymentModuleDataAction = paymentModules.map((addonsKey, i) => {
                                            _modules[addonsKey].value = 'no'
                                        });

                                        // Wait for all mapDataAction, and then updateSettings
                                        Promise.all([mapPaymentGatewayDataAction,mapPaymentModuleDataAction]).then(() => {
                                            updateSettings({ ..._allData, modules: { ..._modules } })
                                        });
                                    }}
                                />
                                <p className="description">{_wp_travel.setting_strings.checkout.enable_woo_checkout_note}</p>
                            </div>
                        </PanelRow>
                    }
                    
                    {
                        enable_woo_checkout == 'no' &&
                        <>
                            {applyFilters('wp_travel_settings_tab_cart_checkout_fields', [], allData)}
                            <PanelRow>
                                <label>{_wp_travel.setting_strings.checkout.enable_multiple_travelers}</label>
                                <div id="wp-travel-checkout-enable-multiple-travelers" className="wp-travel-field-value">
                                    <ToggleControl
                                        checked={enable_multiple_travellers == 'yes'}
                                        onChange={() => {
                                            updateSettings({
                                                ...allData,
                                                enable_multiple_travellers: 'yes' == enable_multiple_travellers ? 'no' : 'yes'
                                            })
                                        }}
                                    />
                                    <p className="description">{_wp_travel.setting_strings.checkout.enable_multiple_travelers_note}</p>
                                </div>
                            </PanelRow>
                            <PanelRow>
                                <label>{_wp_travel.setting_strings.checkout.enable_on_page_booking}</label>
                                <div id="wp-travel-checkout-enable-multiple-travelers" className="wp-travel-field-value">
                                    <ToggleControl 
                                        checked={ typeof enable_one_page_booking != 'undefined' && enable_one_page_booking || false }
                                        onChange={ (value ) => {
                                            updateSettings({
                                                ...allData,
                                                enable_one_page_booking : value
                                            })
                                        }}
                                    />
                                    <p className="description">{_wp_travel.setting_strings.checkout.enable_on_page_booking_note}</p>
                                    <p className="description warning">{_wp_travel.setting_strings.checkout.enable_on_page_booking_tooltip}</p>
                                </div>
                            </PanelRow>
                        </>
                        
                    }
                    
                </ErrorBoundary>
            </div>
        </>
    )
}

// Checkout Notice
addFilter('wp_travel_settings_tab_cart_checkout_fields', 'WPTravel/Settings/Checkout/Notice', (content, allData) => {
    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Need Checkout options ?', 'wp-travel')}</strong>
                <br />
                {__('By upgrading to Pro, you can get checkout option features and more !', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]
    return content
});