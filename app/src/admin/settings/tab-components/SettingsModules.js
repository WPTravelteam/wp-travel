import { applyFilters } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelBody, PanelRow, ToggleControl, TextControl, RadioControl } from '@wordpress/components';

import ErrorBoundary from '../../../ErrorBoundry/ErrorBoundry';

export default () => {
    const settings = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const { updateSettings } = dispatch('WPTravel/Admin');
    const { modules } = settings;
    // console.log( 'modules', modules );
   
    return <div className="wp-travel-ui wp-travel-ui-card settings-general">
        <h2>{ __( 'Modules Settings', 'wp-travel' ) }</h2>
        <p>{__( 'You can enable or disable modules features from here.', 'wp-travel' )}</p>
        <ErrorBoundary>
            {modules && modules.length > 0 &&
               <>
                { modules.map( ( module, i ) => {
                    return <PanelRow key={i}>
                        <label>{module.title}</label>
                        <div className="wp-travel-field-value">
                            <ToggleControl
                                checked={ 'yes' == module.value }
                                onChange={ ( val ) => {
                                    console.log( 'val', val, 'module.value', module.value );
                                    let _settings = settings;
                                    let _modules = _settings.modules;
                                    _modules[i].value = val ? 'yes' : 'no';
                                    console.log( _modules );
                                    updateSettings({
                                        ...settings,
                                        // [modules][i][module.value]: module.value && 'yes' == module.value ? 'no': 'yes'
                                        modules : _modules
                                    })
                                } }
                            />
                            <p className="description">{__( 'Show all your "' + module.title + '" settings and enable its feature', 'wp-travel' )}</p>
                        </div>
                    </PanelRow> 
                }  ) }
               
               </>
            }
            
        </ErrorBoundary>
    </div>
}