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
    
    return <div className="wp-travel-ui wp-travel-ui-card settings-general">
        <h2>{ __( 'Modules Settings', 'wp-travel' ) }</h2>
        <p>{__( 'You can enable or disable modules features from here.', 'wp-travel' )}</p>
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
                        <label>{modules[addonsKey].title}</label>
                        <div className="wp-travel-field-value">
                            <ToggleControl
                                checked={ enabledModule }
                                onChange={ ( val ) => {
                                    let _modules = modules;
                                    _modules[addonsKey].value = val ? 'yes' : 'no';
                                    console.log( '_modules', _modules );
                                    updateSettings({
                                        ...allData,
                                        modules : { ..._modules }
                                    })
                                } }
                            />
                            <p className="description">{__( 'Show all your "' + modules[addonsKey].title + '" settings and enable its feature', 'wp-travel' )}</p>
                        </div>
                    </PanelRow> 
                }  ) }
               </>
            }
            </>
        </ErrorBoundary>
    </div>
}