import { applyFilters } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelBody, PanelRow, ToggleControl, TextControl, RadioControl } from '@wordpress/components';
import Select from 'react-select'
import {VersionCompare} from '../../fields/VersionCompare'
import {alignJustify } from '@wordpress/icons';

import ErrorBoundary from '../../error/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const { updateSettings } = dispatch('WPTravel/Admin');
    const {
        global_tab_settings,
        options
        } = allData;

    const updateTabOption = (key, value, _tabIndex) => {

        const { global_tab_settings } = allData;

        let _allTabs = global_tab_settings;
        _allTabs[_tabIndex][key] = value

        updateSettings({
            ...allData,
            global_tab_settings: [..._allTabs]
        })
    }

    return <div className="wp-travel-ui wp-travel-ui-card settings-general">
        <h2>{ __( 'Trip Settings', 'wp-travel' ) }</h2>
        <ErrorBoundary>
            {applyFilters( 'wp_travel_custom_global_tabs', [] ) }

            {global_tab_settings.map(function (tab, tabIndex) {
                return <PanelBody
                    // icon= {alignJustify}
                    title={tab.label ? tab.label : tab.default_label }
                    initialOpen={false}
                >
                    <PanelRow>
                        <label>{__('Default Tab Title', 'wp-travel')}</label>
                        <TextControl
                            value={tab.default_label}
                            disabled={true}
                        />
                    </PanelRow>
                    <PanelRow>
                        <label>{__('Custom Tab Title', 'wp-travel')}</label>
                        <TextControl
                            value={tab.label}
                            placeholder={tab.default_label }
                            onChange={ 
                                (value) => { 
                                    updateTabOption( 'label', value, tabIndex ) 
                                }
                            }
                        />
                    </PanelRow>

                    <PanelRow>
                        <label>{__('Display', 'wp-travel')}</label>
                        <ToggleControl
                            checked={tab.show_in_menu == 'yes'}
                            onChange={
                                (e) => updateTabOption('show_in_menu', tab.show_in_menu == 'yes' ? 'no' : 'yes', tabIndex)
                            }
                        />
                    </PanelRow>

                </PanelBody>
            })}
          
            {applyFilters( 'wp_travel_tab_content_after_tabs', [] )}
        </ErrorBoundary>
    </div>
}