import { applyFilters } from '@wordpress/hooks';
import { useSelect, select, dispatch } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl, SelectControl } from '@wordpress/components';

import ErrorBoundary from '../../error/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const { updateSettings } = dispatch('WPTravel/Admin');
    const {wp_travel_switch_to_react, currency, options } = allData;
    
    let currencyOptions = 'undefined' != typeof options && 'undefined' != typeof options.currencies ? options.currencies : []
    let selectedCurrencyOption = currencyOptions.filter( opt => { return opt.value == currency } )

    // console.log(currency)
    // console.log(selectedCurrencyOption)

    let switch_to_react = 'undefined' != typeof wp_travel_switch_to_react ? wp_travel_switch_to_react : 'no'
    
    return <div className="wp-travel-ui wp-travel-ui-card settings-general">
        <h4>{ __( 'General Settings', 'wp-travel' ) }</h4>
        <ErrorBoundary>
            <PanelRow>
                <label>{ __( 'Switch to V4', 'wp-travel' ) }</label>
                <ToggleControl
                    help={ __( 'This options will switch your trip edit page layout to new layout.', 'wp-travel' ) }
                    checked={ switch_to_react == 'yes' }
                    onChange={ () => {
                        updateSettings({
                            ...allData,
                            wp_travel_switch_to_react: 'yes' == switch_to_react ? 'no': 'yes'
                        })
                    } }
                />
            </PanelRow>
            <PanelRow>
                <label>{ __( 'Currency', 'wp-travel' ) }</label>
                <SelectControl
                    value={ currency }
                    options={ currencyOptions}
                    onChange={ ( currency ) => {
                        updateSettings({
                            ...allData,
                            currency: currency
                        })
                    } }
                />
            </PanelRow>
        </ErrorBoundary>
    </div>
}