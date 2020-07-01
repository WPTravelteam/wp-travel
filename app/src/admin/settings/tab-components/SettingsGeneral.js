import { applyFilters } from '@wordpress/hooks';
import { useSelect, select, dispatch } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl, TextControl } from '@wordpress/components';
import Select from 'react-select'

import ErrorBoundary from '../../error/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const { updateSettings } = dispatch('WPTravel/Admin');
    const {wp_travel_switch_to_react, currency, currency_position, thousand_separator, decimal_separator, number_of_decimals,wp_travel_map, options } = allData;
    
    // options
    let currencyOptions = [];
    let currencyPositionOptions = []
    let mapOptions = []

    if ( 'undefined' != typeof options ) {
        if ( 'undefined' != typeof options.currencies ) {
            currencyOptions = options.currencies
        }

        if ( 'undefined' != typeof options.currency_positions ) {
            currencyPositionOptions = options.currency_positions
        }
        if ( 'undefined' != typeof options.maps ) {
            mapOptions = options.maps
        } 
    }

    // selected options.
    let selectedCurrency = currencyOptions.filter( opt => { return opt.value == currency } )
    let selectedCurrencyPosition = currencyPositionOptions.filter( opt => { return opt.value == currency_position } )
    let selectedMap = mapOptions.filter( opt => { return opt.value == wp_travel_map } )

    let switch_to_react = 'undefined' != typeof wp_travel_switch_to_react ? wp_travel_switch_to_react : 'no'
    
    return <div className="wp-travel-ui wp-travel-ui-card settings-general">
        <h4>{ __( 'General Settings', 'wp-travel' ) }</h4>
        <ErrorBoundary>
            <PanelRow>
                <label>{ __( 'Switch to V4', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
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
                </div>
            </PanelRow>
            <PanelRow>
                <label>{ __( 'Currency', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <div className="wp-travel-select-wrapper">
                        <Select
                            options={currencyOptions}
                            value={ 'undefined' != typeof selectedCurrency[0] && 'undefined' != typeof selectedCurrency[0].label ? selectedCurrency[0] : []}
                            onChange={(data)=>{
                                if ( '' !== data ) {
                                    updateSettings({
                                        ...allData,
                                        currency: data.value
                                    })
                                }
                            }}
                        />
                    </div>
                    <p class="description">
                        {__( 'Choose currency you accept payments in.', 'wp-travel' )}
                    </p>
                </div>
            </PanelRow>
            <PanelRow>
                <label>{ __( 'Currency Position', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <div className="wp-travel-select-wrapper">
                        <Select
                            options={currencyPositionOptions}
                            value={ 'undefined' != typeof selectedCurrencyPosition[0] && 'undefined' != typeof selectedCurrencyPosition[0].label ? selectedCurrencyPosition[0] : []}
                            onChange={(data)=>{
                                if ( '' !== data ) {
                                    updateSettings({
                                        ...allData,
                                        currency_position: data.value
                                    })
                                }
                            }}
                        />
                    </div>
                    <p class="description">{__( 'Choose currency position.', 'wp-travel' )}</p>
                </div>
            </PanelRow>
            <PanelRow>
                <label>{ __( 'Thousand separator', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <TextControl
                        help={__( 'This sets the thousand separator of displayed prices.', 'wp-travel' )}
                        value={thousand_separator}
                        onChange={ 
                            (value) => {
                                updateSettings({
                                    ...allData,
                                    thousand_separator: value
                                })
                            }
                        }
                    />
                </div>
            </PanelRow>
            <PanelRow>
                <label>{ __( 'Decimal separator', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <TextControl
                        help={__( 'This sets the decimal separator of displayed prices.', 'wp-travel' )}
                        value={decimal_separator}
                        onChange={ 
                            (value) => {
                                updateSettings({
                                    ...allData,
                                    decimal_separator: value
                                })
                            }
                        }
                    />
                </div>
            </PanelRow>
            <PanelRow>
                <label>{ __( 'Number of decimals', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <TextControl
                        help={__( 'This sets the number of decimal of displayed prices.', 'wp-travel' )}
                        value={number_of_decimals}
                        type="number"
                        onChange={ 
                            (value) => {
                                updateSettings({
                                    ...allData,
                                    number_of_decimals: value
                                })
                            }
                        }
                    />
                </div>
            </PanelRow>



            <h4>{ __( 'Maps', 'wp-travel' ) }</h4>
            <PanelRow>
                <label>{ __( 'Select Map', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <div className="wp-travel-select-wrapper">
                        <Select
                            options={mapOptions}
                            value={ 'undefined' != typeof selectedMap[0] && 'undefined' != typeof selectedMap[0].label ? selectedMap[0] : []}
                            onChange={(data)=>{
                                if ( '' !== data ) {
                                    updateSettings({
                                        ...allData,
                                        wp_travel_map: data.value
                                    })
                                }
                            }}
                        />
                    </div>
                    <p class="description">{__( 'Choose your map provider to display map in site.', 'wp-travel' )}</p>
                </div>
            </PanelRow>
                
        </ErrorBoundary>
    </div>
}