import { useSelect, dispatch } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl } from '@wordpress/components';

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
    let enableAallModules = true;
    Object.keys( defaultModules ).map( ( addonsKey, i ) => {
        if ( enableAallModules ) {
            let enabled = modules[addonsKey].value;
            if ( 'no' == enabled ) {
                enableAallModules = false;
            }
        }
    });
    return <div className="wp-travel-ui wp-travel-ui-card settings-modules">
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
                <>
                {defaultModules && Object.keys( defaultModules ).length > 0 &&
                <>
                    { Object.keys( defaultModules ).map( ( addonsKey, i ) => {
                        // Do not display pro.
                        if ( 'show_wp_travel_pro' === addonsKey ) {
                            return <></>
                        }
                        let enabledModule = 'yes' == modules[addonsKey].value; // Saved modules values.

                        return <PanelRow key={i}>
                            <div>
                                <div>
                                    <label>{modules[addonsKey].title}</label>
                                    <div className="wp-travel-field-value">
                                        <ToggleControl
                                            checked={ enabledModule }
                                            onChange={ ( val ) => {
                                                let _modules = modules;
                                                _modules[addonsKey].value = val ? 'yes' : 'no';
                                                updateSettings({
                                                    ...allData,
                                                    modules : { ..._modules }
                                                })
                                            } }
                                        />
                                    </div>
                                </div>
                                <p className="description">{__( 'Show all your "' + modules[addonsKey].title + '" settings and enable its feature', 'wp-travel' )}</p>
                            </div>
                        </PanelRow> 
                    }  ) }
                </>
                }
                </>
            </ErrorBoundary>
        </div>
    </div>
}