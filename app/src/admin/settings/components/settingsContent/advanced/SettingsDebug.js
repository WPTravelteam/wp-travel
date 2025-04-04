import { applyFilters } from '@wordpress/hooks';
import { useSelect, dispatch } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl, TextControl, CheckboxControl, Spinner, Button, Disabled } from '@wordpress/components';
import { useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

import { VersionCompare } from '../../../../fields/VersionCompare'

import ErrorBoundary from '../../../../../ErrorBoundry/ErrorBoundry';

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

    const { wp_travel_user_since } = options;
    const initialState = {
        forceMigrateToV4: false,
        migrating: false,
        showMigrateCompleteNotice: false
    }
    const [{ forceMigrateToV4, migrating, showMigrateCompleteNotice }, setState] = useState(initialState)
    const updateState = data => {
        setState(state => ({ ...state, ...data }))
    }
    setTimeout(() => {
        if ('undefined' !== typeof showMigrateCompleteNotice && showMigrateCompleteNotice) {
            updateState({
                showMigrateCompleteNotice: false
            })
        }
    }, 5000)
    const [valWPML , setValWPML ] = useState( typeof allData.wpml_migrations != 'undefined' && allData.wpml_migrations || false );
    const [ loader, setLoader ]     = useState({display : 'none', wpml_active : false });
    const updateMigrate = () => {
        const responce = '';
        setLoader({ display : 'block'});
        apiFetch({ url: `${ajaxurl}?action=wptravel_wpml_migrate&_nonce=${_wp_travel_admin._nonce}`, data: allData, method: 'post' }).then(res => {
            if ( typeof res['success'] != 'undefined' && res['success'] ) {
                setLoader({ display : 'none'});
            }
        });
    }
    const plugins = typeof _wp_travel.activated_plugins != 'undefined' && _wp_travel.activated_plugins != '' && ( Object.values(_wp_travel.activated_plugins) ).length > 0 && Object.values(_wp_travel.activated_plugins)
    const wpml_plugins = plugins.includes( "sitepress-multilingual-cms/sitepress.php" ) || plugins.includes( "wpml-string-translation/plugin.php" ) || plugins.includes( "wpml-media-translation/plugin.php" ) && true || false;
    return (
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {_wp_travel.setting_strings.debug.debug}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More debug settings according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <ErrorBoundary>
                    <h3 className='wp-travel-option-header'>{_wp_travel.setting_strings.debug.debug_test_payment}</h3>
                    <PanelRow>
                        <label>{_wp_travel.setting_strings.debug.debug_test_mode}</label>
                        <div id="wp-travel-debug-test-mode" className="wp-travel-field-value">
                            <ToggleControl
                                checked={wt_test_mode == 'yes'}
                                onChange={() => {
                                    updateSettings({
                                        ...allData,
                                        wt_test_mode: 'yes' == wt_test_mode ? 'no' : 'yes'
                                    })
                                }}
                            />
                            <p className="description">{_wp_travel.setting_strings.debug.debug_test_mode_note}</p>
                        </div>
                    </PanelRow>
                    <PanelRow>
                        <label>{_wp_travel.setting_strings.debug.debug_test_email}</label>
                        <div id="wp-travel-debug-test-email" className="wp-travel-field-value">
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
                            <p className="description">{_wp_travel.setting_strings.debug.debug_test_email_note}</p>
                        </div>
                    </PanelRow>

                    <h3 className='wp-travel-option-header'>{_wp_travel.setting_strings.debug.optimized_scripts_styles}</h3>
                    <PanelRow>
                        <label>{_wp_travel.setting_strings.debug.load_combined_scripts}</label>
                        <div id="wp-travel-debug-load-combined-scripts" className="wp-travel-field-value">
                            <ToggleControl
                                checked={wt_load_optimized_script == 'yes'}
                                onChange={() => {
                                    updateSettings({
                                        ...allData,
                                        wt_load_optimized_script: 'yes' == wt_load_optimized_script ? 'no' : 'yes'
                                    })
                                }}
                            />
                            <p className="description">{_wp_travel.setting_strings.debug.load_combined_scripts_note}</p>
                        </div>
                    </PanelRow>
                    <PanelRow>
                        <label>{_wp_travel.setting_strings.debug.load_minified_scripts}</label>
                        <div id="wp-travel-debug-load-minified-scripts" className="wp-travel-field-value">
                            <ToggleControl
                                checked={load_minified_scripts == 'yes'}
                                onChange={() => {
                                    updateSettings({
                                        ...allData,
                                        load_minified_scripts: 'yes' == load_minified_scripts ? 'no' : 'yes'
                                    })
                                }}
                            />
                            <p className="description">{_wp_travel.setting_strings.debug.load_minified_scripts_note}</p>
                        </div>
                    </PanelRow>
                    { wpml_plugins && <PanelRow>
                        <label>{ typeof _wp_travel.wpml_label != 'undefined' && _wp_travel.wpml_label }</label>
                        <div className="wp-travel-field-value">
                            <ToggleControl
                                checked={  valWPML  }
                                onChange={ () => {
                                    const wpml = valWPML == true ? false : true;
                                    setValWPML( wpml );
                                    updateSettings({
                                        ...allData,
                                        wpml_migrations: wpml
                                    })
                                } }
                            />
                            <label>{ typeof _wp_travel.wpml_migratio_dicription != 'undefined' && _wp_travel.wpml_migratio_dicription }</label>
                            { typeof allData.wpml_migrations != 'undefined' && allData.wpml_migrations == true && <div className='wp-travel-wpml-migrations-spinner'>{ typeof allData.wpml_enable != 'undefined' && allData.wpml_enable && <Button 
                                className='wp-travel-wpml-migrate-button' 
                                variant="primary" 
                                onClick={ () => {
                                    updateMigrate()
                                }}>{ typeof _wp_travel.wpml_btn_label != 'undefined' && _wp_travel.wpml_btn_label }</Button> || <Disabled>
                                    <Button 
                                        className='wp-travel-wpml-migrate-button disables' 
                                        variant="primary" 
                                        onClick={ () => {
                                            updateMigrate()
                                        }}>
                                    { typeof _wp_travel.wpml_btn_label != 'undefined' && _wp_travel.wpml_btn_label }</Button>
                                    <br /><label>{ typeof _wp_travel.diable_wpml_text != 'undefined' && _wp_travel.diable_wpml_text }</label>
                                </Disabled> }
                                <img id="wp-travel-migratios-loader" src={ _wp_travel.plugin_url + 'assets/images/Spinner.gif' } style={{ display: typeof loader.display != 'undefined' && loader.display }} /> </div>
                            }
                        </div>
                    </PanelRow> }
                    {VersionCompare(wp_travel_user_since, '4.0.0', '<') &&
                        <PanelRow>
                            <label>{__('Migrate Pricing and Date', 'wp-travel')} {migrating && <Spinner />}{showMigrateCompleteNotice && <p className="text-success" >Migration completed! Please Do not check it again</p>}</label>
                            <div className="wp-travel-field-value">
                                <CheckboxControl
                                    checked={forceMigrateToV4}
                                    onChange={() => {
                                        if (confirm('Are your sure to migrate your trips to v4 trips?')) {
                                            updateState({
                                                migrating: true
                                            })

                                            apiFetch({ url: `${ajaxurl}?action=wptravel_force_migrate&_nonce=${_wp_travel_admin._nonce}`, data: { force_migrate_to_v4: true }, method: 'post' }).then(res => {

                                                updateState({
                                                    forceMigrateToV4: !forceMigrateToV4,
                                                    migrating: false,
                                                    showMigrateCompleteNotice: true
                                                })
                                            });
                                        }
                                    }}
                                />
                                <p className="description">{__('Enabling this will migrate all your v3 trips to v4 trips. Also, the existing v4 trips can be overridden as well. So, before enabling you have to be clear regarding migration process.', 'wp-travel')}</p>
                            </div>
                        </PanelRow>
                    }

                    {applyFilters('wp_travel_below_debug_tab_fields', [])}
                </ErrorBoundary>
            </div>
        </>
    )
}