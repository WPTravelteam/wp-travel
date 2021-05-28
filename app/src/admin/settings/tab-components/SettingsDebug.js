import { applyFilters } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl, TextControl } from '@wordpress/components';
import Select from 'react-select'
import {VersionCompare} from '../../fields/VersionCompare'

import ErrorBoundary from '../../../ErrorBoundry/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const { updateSettings } = dispatch('WPTravel/Admin');
    const {
        wt_test_mode,
        wt_test_email,
        wt_load_optimized_script,
        load_minified_scripts,

        options
        } = allData;

    console.log( 'load_minified_scripts', load_minified_scripts );
    return <div className="wp-travel-ui wp-travel-ui-card settings-general">
        <h2>{ __( 'Debug Options', 'wp-travel' ) }</h2>
        <ErrorBoundary>
            <h3>{__( 'Test Payment' )}</h3>
            <PanelRow>
                <label>{ __( 'Test Mode', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <ToggleControl
                        checked={ wt_test_mode == 'yes' }
                        onChange={ () => {
                            updateSettings({
                                ...allData,
                                wt_test_mode: 'yes' == wt_test_mode ? 'no': 'yes'
                            })
                        } }
                    />
                    <p className="description">{__( 'Enable test mode to make test payment.', 'wp-travel' )}</p>
                </div>
            </PanelRow>
            <PanelRow>
                <label>{ __( 'Test Email', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <TextControl
                        value={wt_test_email}
                        onChange={ 
                            (value) => {
                                updateSettings({
                                    ...allData,
                                    wt_test_email: value
                                })
                            }
                        }
                    />
                    <p className="description">{__( 'Test email address will get test mode payment emails.', 'wp-travel' )}</p>
                </div>
            </PanelRow>
            
            <h3>{__( 'Optimized Scripts and Styles', 'wp-travel' )}</h3>
            <PanelRow>
                <label>{ __( 'Load Combined Scripts', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <ToggleControl
                        checked={ wt_load_optimized_script == 'yes' }
                        onChange={ () => {
                            updateSettings({
                                ...allData,
                                wt_load_optimized_script: 'yes' == wt_load_optimized_script ? 'no': 'yes'
                            })
                        } }
                    />
                    <p className="description">{__( 'Enabling this will load the bundled scripts files.', 'wp-travel' )}</p>
                </div>
            </PanelRow>
            <PanelRow>
                <label>{ __( 'Load Minified Scripts', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <ToggleControl
                        checked={ load_minified_scripts == 'yes' }
                        onChange={ () => {
                            updateSettings({
                                ...allData,
                                load_minified_scripts: 'yes' == load_minified_scripts ? 'no': 'yes'
                            })
                        } }
                    />
                    <p className="description">{__( 'Enabling this will load minified scripts.', 'wp-travel' )}</p>
                </div>
            </PanelRow>
            {applyFilters( 'wp_travel_below_debug_tab_fields', [] )}
        </ErrorBoundary>
    </div>
}