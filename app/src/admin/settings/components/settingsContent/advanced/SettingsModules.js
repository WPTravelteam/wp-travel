import { useSelect, dispatch } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl, Notice, PanelBody } from '@wordpress/components';

import ErrorBoundary from '../../../../../ErrorBoundry/ErrorBoundry';

export default () => {
    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const { updateSettings } = dispatch('WPTravel/Admin');
    const { modules, options, enable_woo_checkout } = allData;
    const { default_settings } = options;
    const { modules: defaultModules } = default_settings;

    let _modules = modules;
    if (!_modules.length) {
        _modules = defaultModules
    }
    let enableAllModules = true;
    Object.keys(defaultModules).map((addonsKey, i) => {
        if (enableAllModules) {
            let enabled = false;
            if ('undefined' !== typeof modules[addonsKey]) {
                enabled = modules[addonsKey].value;
            } else {
                enabled = defaultModules[addonsKey].value;
            }
            if ('no' == enabled) {
                enableAllModules = false;
            }
        }
    });

    // All Modules
    if (defaultModules && Object.keys(defaultModules).length > 0) {

        const proModules = Object.keys(defaultModules).filter(k => (!defaultModules[k].category || 'Pro' === defaultModules[k].category));
        const paymentModules = Object.keys(defaultModules).filter(k => 'Payment' === defaultModules[k].category);
        const mapModules = Object.keys(defaultModules).filter(k => 'Map' === defaultModules[k].category);


        let enableProModules = true;
        proModules.map((addonsKey, i) => {
            if (enableProModules) {
                let enabled = false;
                if ('undefined' !== typeof modules[addonsKey]) {
                    enabled = modules[addonsKey].value;
                } else {
                    enabled = defaultModules[addonsKey].value;
                }
                if ('no' == enabled) {
                    enableProModules = false;
                }
            }
        });
        let enablePaymentModules = true;
        paymentModules.map((addonsKey, i) => {
            if (enablePaymentModules) {
                let enabled = false;
                if ('undefined' !== typeof modules[addonsKey]) {
                    enabled = modules[addonsKey].value;
                } else {
                    enabled = defaultModules[addonsKey].value;
                }
                if ('no' == enabled) {
                    enablePaymentModules = false;
                }
            }
        });

        return (
            <>
                <div className="wp-travel-section-header wp-travel-custom-section-header">
                    <div className='wp-travel-section-info'>
                        <h2 className="wp-travel-section-header-title">
                            {_wp_travel.setting_strings.advance.modules}
                        </h2>
                        <p className="wp-travel-section-header-description">
                            {__('You can enable or disable modules features from here.', 'wp-travel')}

                        </p>
                    </div>
                    <label className='wp-travel-section-header-label'>
                        <ToggleControl
                            checked={enableAllModules}
                            onChange={(value) => {
                                let mapDataAction = Object.keys(defaultModules).map((addonsKey, i) => {
                                    _modules[addonsKey].value = value ? 'yes' : 'no'
                                });
                                // Wait for all mapDataAction, and then updateSettings
                                Promise.all(mapDataAction).then(() => {
                                    updateSettings({
                                        ...allData,
                                        modules: { ..._modules }
                                    })
                                });
                            }}
                        />
                    </label>
                </div>
                <div className='wp-travel-section-content'>
                    <div className="wptravel-ui-content wptravel-modules" >
                        <ErrorBoundary>
                            {proModules.length > 0 &&
                                <PanelBody
                                    title={_wp_travel.setting_strings.advance.pro_modules}
                                    initialOpen={false}
                                >
                                    <label id="wp-travel-modules-pro" className="enable-all-btn">
                                        <p className="description">{_wp_travel.setting_strings.advance.enable_disable_all_pro_modules}</p>
                                        <ToggleControl
                                            checked={enableProModules}
                                            onChange={(value) => {
                                                let mapDataAction = proModules.map((addonsKey, i) => {
                                                    _modules[addonsKey].value = value ? 'yes' : 'no'
                                                });
                                                // Wait for all mapDataAction, and then updateSettings
                                                Promise.all(mapDataAction).then(() => {
                                                    updateSettings({
                                                        ...allData,
                                                        modules: { ..._modules }
                                                    })
                                                });
                                            }}
                                        />
                                    </label>
                                    <div className="wptravel-modules-list">

                                        {proModules.map((addonsKey, i) => {
                                            // Do not display pro.
                                            if ('show_wp_travel_pro' === addonsKey) {
                                                return <></>
                                            }
                                            let enabledModule = false;
                                            if ('undefined' !== typeof modules[addonsKey]) {
                                                enabledModule = 'yes' == modules[addonsKey].value; // Saved modules values.
                                            } else {
                                                enabledModule = 'yes' == defaultModules[addonsKey].value;
                                            }
                                            let moduleName = defaultModules[addonsKey].title.replace('WP Travel ', ' ');
                                            return <PanelRow key={i}>
                                                <div className="wptravel-modules-row">
                                                    <div className="wptravel-modules-input">
                                                        <label><strong>{moduleName}</strong></label>
                                                        <div className="wp-travel-field-value">
                                                            <ToggleControl
                                                                checked={enabledModule}
                                                                onChange={(val) => {
                                                                    _modules[addonsKey].value = val ? 'yes' : 'no';
                                                                    updateSettings({
                                                                        ...allData,
                                                                        modules: { ..._modules }
                                                                    })
                                                                }}
                                                            />
                                                        </div>
                                                    </div>
                                                    <p className="description">{ _wp_travel.setting_strings.advance.pro_modules_note + moduleName + _wp_travel.setting_strings.advance.pro_modules_note2}</p>
                                                </div>
                                            </PanelRow>
                                        })}
                                    </div>
                                </PanelBody>
                            }
                            {paymentModules.length > 0 && enable_woo_checkout == 'no' &&
                                
                                <PanelBody
                                    title={_wp_travel.setting_strings.advance.payment_modules}
                                    initialOpen={false}
                                >
                                    <label id="wp-travel-modules-payment" className="enable-all-btn">
                                        <p className="description">{_wp_travel.setting_strings.advance.enable_disable_all_payment_modules}</p>
                                        <ToggleControl
                                            checked={enablePaymentModules}
                                            onChange={(value) => {
                                                let mapDataAction = paymentModules.map((addonsKey, i) => {
                                                    _modules[addonsKey].value = value ? 'yes' : 'no'
                                                });
                                                // Wait for all mapDataAction, and then updateSettings
                                                Promise.all(mapDataAction).then(() => {
                                                    updateSettings({
                                                        ...allData,
                                                        modules: { ..._modules }
                                                    })
                                                });
                                            }}
                                        />
                                    </label>

                                    <div className="wptravel-modules-list">
                                        {paymentModules.map((addonsKey, i) => {
                                            // Do not display pro.
                                            if ('show_wp_travel_pro' === addonsKey) {
                                                return <></>
                                            }
                                            let enabledModule = false;
                                            if ('undefined' !== typeof modules[addonsKey]) {
                                                enabledModule = 'yes' == modules[addonsKey].value; // Saved modules values.
                                            } else {
                                                enabledModule = 'yes' == defaultModules[addonsKey].value;
                                            }
                                            let moduleName = defaultModules[addonsKey].title.replace('WP Travel ', ' ');
                                            return <PanelRow key={i}>
                                                <div className="wptravel-modules-row">
                                                    <div className="wptravel-modules-input">
                                                        <label><strong>{moduleName}</strong></label>
                                                        <div className="wp-travel-field-value">
                                                            <ToggleControl
                                                                checked={enabledModule}
                                                                onChange={(val) => {
                                                                    // let _modules = modules;
                                                                    _modules[addonsKey].value = val ? 'yes' : 'no';
                                                                    updateSettings({
                                                                        ...allData,
                                                                        modules: { ..._modules }
                                                                    })
                                                                }}
                                                            />
                                                        </div>
                                                    </div>
                                                    <p className="description">{ _wp_travel.setting_strings.advance.pro_modules_note + moduleName + _wp_travel.setting_strings.advance.pro_modules_note2}</p>
                                                </div>
                                            </PanelRow>
                                        })}
                                    </div>
                                </PanelBody>
                            }

                            {mapModules.length > 0 &&
                                <PanelBody
                                    title={_wp_travel.setting_strings.advance.map_modules}
                                    initialOpen={false}
                                >
                                    <div id="wp-travel-modules-map" className="wptravel-modules-list">
                                        {mapModules.map((addonsKey, i) => {
                                            // Do not display pro.
                                            if ('show_wp_travel_pro' === addonsKey) {
                                                return <></>
                                            }
                                            let enabledModule = false;
                                            if ('undefined' !== typeof modules[addonsKey]) {
                                                enabledModule = 'yes' == modules[addonsKey].value; // Saved modules values.
                                            } else {
                                                enabledModule = 'yes' == defaultModules[addonsKey].value;
                                            }
                                            let moduleName = defaultModules[addonsKey].title.replace('WP Travel ', ' ');
                                            return <PanelRow key={i}>
                                                <div className="wptravel-modules-row">
                                                    <div className="wptravel-modules-input">
                                                        <label><strong>{moduleName}</strong></label>
                                                        <div className="wp-travel-field-value">
                                                            <ToggleControl
                                                                checked={enabledModule}
                                                                onChange={(val) => {
                                                                    _modules[addonsKey].value = val ? 'yes' : 'no';
                                                                    updateSettings({
                                                                        ...allData,
                                                                        modules: { ..._modules }
                                                                    })
                                                                }}
                                                            />
                                                        </div>
                                                    </div>
                                                    <p className="description">{ _wp_travel.setting_strings.advance.pro_modules_note + moduleName + _wp_travel.setting_strings.advance.pro_modules_note2}</p>
                                                </div>
                                            </PanelRow>
                                        })}
                                    </div>
                                </PanelBody>
                            }

                        </ErrorBoundary>
                    </div>
                    <Notice isDismissible={false} status="warning">
                        <p>{_wp_travel.setting_strings.advance.advance_modules_note}</p>
                    </Notice><br />
                </div>
            </>
        )
    } else {
        return (
            <>
                <div className="wp-travel-section-header">
                    <div className='wp-travel-section-info'>
                        <h2 className="wp-travel-section-header-title">
                            {__("Modules", "wp-travel")}
                        </h2>
                        <p className="wp-travel-section-header-description">
                            {__('You can enable or disable modules features from here.', 'wp-travel')}

                        </p>
                    </div>
                </div>
                <div className='wp-travel-section-content'>
                    <Notice isDismissible={false} status="informational">
                        <strong>{__('Want to add more features in WP Travel?', 'wp-travel')}</strong>
                        <br />
                        {__('Get WP Travel Pro modules for Payment, Trip Extras, Inventory Management, Field Editor and other premium features.', 'wp-travel')}
                        <br />
                        <br />
                        <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
                    </Notice><br />
                </div>
            </>
        )
    }
}