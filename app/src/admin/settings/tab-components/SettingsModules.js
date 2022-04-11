import { useSelect, dispatch } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl, Notice, PanelBody } from '@wordpress/components';

import ErrorBoundary from '../../../ErrorBoundry/ErrorBoundry';

export default () => {
    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const { updateSettings } = dispatch('WPTravel/Admin');
    const { modules, options } = allData;
    const { default_settings, saved_settings } = options;
    const { modules:defaultModules } = default_settings;

    let _modules          = modules;
    if ( ! _modules.length ) {
        _modules = defaultModules
    }
    let enableAallModules = true;
    Object.keys( defaultModules ).map( ( addonsKey, i ) => {
        if ( enableAallModules ) {
            let enabled = false;
            if ( 'undefined' !== typeof modules[addonsKey] ) {
                enabled =  modules[addonsKey].value;
            } else {
                enabled =  defaultModules[addonsKey].value;
            }
            if ( 'no' == enabled ) {
                enableAallModules = false;
            }
        }
    });

    // All Modules
    if ( defaultModules && Object.keys( defaultModules ).length > 0 ) {

        const proModules = Object.keys( defaultModules ).filter( k => 'Pro' === defaultModules[k].category );
        const paymentModules = Object.keys( defaultModules ).filter( k => 'Payment' === defaultModules[k].category );
        const mapModules = Object.keys( defaultModules ).filter( k => 'Map' === defaultModules[k].category );
        
        return <div className="wp-travel-ui wp-travel-ui-card settings-modules">
            <Notice isDismissible={false} status="warning">
                <p><b>Note:</b> Please reload page after enabling/disabling modules.</p>
            </Notice><br />
            <div className="wptravel-ui-title-container">
                <div className="wptravel-ui-title">
                    <h2>{ __( 'Modules Settings', 'wp-travel' ) }</h2>
                    <p>{__( 'You can enable or disable modules features from here.', 'wp-travel' )}</p>
                </div>
                
                <label>
                    <ToggleControl
                        checked={enableAallModules }
                        onChange={ (value) => {
                            let mapDataAction = Object.keys( defaultModules ).map( ( addonsKey, i ) => {
                                _modules[addonsKey].value = value ? 'yes' :'no'
                            });
                            // Wait for all mapDataAction, and then updateSettings
                            Promise.all(mapDataAction).then(() => {
                                updateSettings({
                                    ...allData,
                                    modules: {..._modules}
                                })
                            });
                        } }
                    />
                    <p className="description">{__( 'Enable/Disable All', 'wp-travel' )}</p>
                </label>
                
            </div>
            <div className="wptravel-ui-content wptravel-modules" >
                <ErrorBoundary>
                    { proModules.length > 0 &&
                        <PanelBody
                            title="Pro Modules"
                            initialOpen={false}
                        >
                            <div className="wptravel-modules-list">

                            { proModules.map( ( addonsKey, i ) => {
                                // Do not display pro.
                                if ( 'show_wp_travel_pro' === addonsKey ) {
                                    return <></>
                                }
                                let enabledModule = false;
                                if ( 'undefined' !== typeof modules[addonsKey] ) {
                                    enabledModule = 'yes' == modules[addonsKey].value; // Saved modules values.
                                } else {
                                    enabledModule = 'yes' == defaultModules[addonsKey].value;
                                }
                                let moduleName = defaultModules[addonsKey].title.replace( 'WP Travel ', ' ' );
                                return <PanelRow key={i}>
                                    <div className="wptravel-modules-row">
                                        <div className="wptravel-modules-input">
                                            <label><strong>{moduleName}</strong></label>
                                            <div className="wp-travel-field-value">
                                                <ToggleControl
                                                    checked={ enabledModule }
                                                    onChange={ ( val ) => {
                                                        // let _modules = modules;
                                                        _modules[addonsKey].value = val ? 'yes' : 'no';
                                                        updateSettings({
                                                            ...allData,
                                                            modules : { ..._modules }
                                                        })
                                                    } }
                                                />
                                            </div>
                                        </div>
                                        <p className="description">{__( 'Show all your "' + moduleName + '" settings and enable its feature', 'wp-travel' )}</p>
                                    </div>
                                </PanelRow> 
                            }  ) }
                            </div>
                        </PanelBody>
                    }
                    { paymentModules.length > 0 &&
                        <PanelBody
                            title="Payment Modules"
                            initialOpen={false}
                        >
                            <div className="wptravel-modules-list">
                            { paymentModules.map( ( addonsKey, i ) => {
                                // Do not display pro.
                                if ( 'show_wp_travel_pro' === addonsKey ) {
                                    return <></>
                                }
                                let enabledModule = false;
                                if ( 'undefined' !== typeof modules[addonsKey] ) {
                                    enabledModule = 'yes' == modules[addonsKey].value; // Saved modules values.
                                } else {
                                    enabledModule = 'yes' == defaultModules[addonsKey].value;
                                }
                                let moduleName = defaultModules[addonsKey].title.replace( 'WP Travel ', ' ' );
                                return <PanelRow key={i}>
                                    <div className="wptravel-modules-row">
                                        <div className="wptravel-modules-input">
                                            <label><strong>{moduleName}</strong></label>
                                            <div className="wp-travel-field-value">
                                                <ToggleControl
                                                    checked={ enabledModule }
                                                    onChange={ ( val ) => {
                                                        // let _modules = modules;
                                                        _modules[addonsKey].value = val ? 'yes' : 'no';
                                                        updateSettings({
                                                            ...allData,
                                                            modules : { ..._modules }
                                                        })
                                                    } }
                                                />
                                            </div>
                                        </div>
                                        <p className="description">{__( 'Show all your "' + moduleName + '" settings and enable its feature', 'wp-travel' )}</p>
                                    </div>
                                </PanelRow> 
                            }  ) }
                            </div>
                        </PanelBody>
                    }

                    { mapModules.length > 0 &&
                        <PanelBody
                            title="Map Modules"
                            initialOpen={false}
                        >
                            <div className="wptravel-modules-list">
                            { mapModules.map( ( addonsKey, i ) => {
                                // Do not display pro.
                                if ( 'show_wp_travel_pro' === addonsKey ) {
                                    return <></>
                                }
                                let enabledModule = false;
                                if ( 'undefined' !== typeof modules[addonsKey] ) {
                                    enabledModule = 'yes' == modules[addonsKey].value; // Saved modules values.
                                } else {
                                    enabledModule = 'yes' == defaultModules[addonsKey].value;
                                }
                                let moduleName = defaultModules[addonsKey].title.replace( 'WP Travel ', ' ' );
                                return <PanelRow key={i}>
                                    <div className="wptravel-modules-row">
                                        <div className="wptravel-modules-input">
                                            <label><strong>{moduleName}</strong></label>
                                            <div className="wp-travel-field-value">
                                                <ToggleControl
                                                    checked={ enabledModule }
                                                    onChange={ ( val ) => {
                                                        // let _modules = modules;
                                                        _modules[addonsKey].value = val ? 'yes' : 'no';
                                                        updateSettings({
                                                            ...allData,
                                                            modules : { ..._modules }
                                                        })
                                                    } }
                                                />
                                            </div>
                                        </div>
                                        <p className="description">{__( 'Show all your "' + moduleName + '" settings and enable its feature', 'wp-travel' )}</p>
                                    </div>
                                </PanelRow> 
                            }  ) }
                            </div>
                        </PanelBody>
                    }
                    
                </ErrorBoundary>
            </div>
            <Notice isDismissible={false} status="warning">
                <p><b>Note:</b> Please reload page after enabling/disabling modules.</p>
            </Notice><br />
        </div>
    } else {
        return <div className="wp-travel-ui wp-travel-ui-card settings-modules">
            <div className="wptravel-ui-title-container">
                <div className="wptravel-ui-title">
                    <h2>{ __( 'Modules Settings', 'wp-travel' ) }</h2>
                </div>
            </div>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Want to add more features in WP Travel?', 'wp-travel')}</strong>
                <br />
                {__('Get WP Travel Pro modules for Payment, Trip Extras, Inventory Management, Field Editor and other premium features.', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
            </Notice><br />
        </div>
    }
}