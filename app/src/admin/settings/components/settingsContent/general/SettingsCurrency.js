import { useRef, forwardRef } from '@wordpress/element'
import { PanelRow, ToggleControl, TextControl } from '@wordpress/components';
import { _n, __ } from '@wordpress/i18n';
import { useSelect, dispatch } from '@wordpress/data'; // redux [and also for hook / filter] | dispatch : send data to store

import Tooltip from '../../UI/Tooltip';
import Select from '../../UI/Select';

export default forwardRef((props) => {
    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const {
        currency,
        currency_position,
        use_currency_name,
        thousand_separator,
        decimal_separator,
        number_of_decimals,
        options } = allData;

    const { updateSettings } = dispatch('WPTravel/Admin');

    // options
    let currencyOptions = [];
    let currencyPositionOptions = []

    if ('undefined' != typeof options) {
        if ('undefined' != typeof options.currencies) {
            currencyOptions = options.currencies
        }
        if ('undefined' != typeof options.currency_positions) {
            currencyPositionOptions = options.currency_positions
        }

    }

    // selected options.
    let selectedCurrency = currencyOptions.filter(opt => { return opt.value == currency })
    let selectedCurrencyPosition = currencyPositionOptions.filter(opt => { return opt.value == currency_position });

    const currencyRef = useRef([]);

    return (
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">{_wp_travel.setting_strings.currency.currency_settings}</h2>
                <p className="wp-travel-section-header-description">{__('More general settings according to your choice.', 'wp-travel')}</p>
            </div>
            <div className='wp-travel-section-content'>
                <PanelRow>
                    <label>{_wp_travel.setting_strings.currency.currency}</label>
                    <div id="wp-travel-currency" className="wp-travel-field-value">
                        <div className="wp-travel-select-wrapper">
                            <Select
                                ref={ref => !currencyRef.current.includes(ref) && currencyRef.current.push(ref)}
                                options={currencyOptions}
                                value={'undefined' != typeof selectedCurrency[0] && 'undefined' != typeof selectedCurrency[0].label ? selectedCurrency[0] : []}
                                onChange={(data) => {
                                    if ('' !== data) {
                                        updateSettings({
                                            ...allData,
                                            currency: data.value
                                        })
                                    }
                                }}
                            />
                        </div>
                        <p className="description">{_wp_travel.setting_strings.currency.currency_note}</p>
                    </div>
                </PanelRow>
                <PanelRow>
                    <label>
                        {_wp_travel.setting_strings.currency.use_currency_name}
                        <Tooltip text={_wp_travel.setting_strings.currency.use_currency_name_tooltip}>
                            <span><i className='fa fa-info-circle'></i></span>
                        </Tooltip>
                    </label>
                    <div id="wp-travel-use-currency-name" className="wp-travel-field-value">
                        <ToggleControl
                            checked={use_currency_name == 'yes'}
                            onChange={() => {
                                updateSettings({
                                    ...allData,
                                    use_currency_name: 'yes' == use_currency_name ? 'no' : 'yes'
                                })
                            }}
                        />
                        <p className="description">
                        </p>
                    </div>
                </PanelRow>
                <PanelRow>
                    <label>{_wp_travel.setting_strings.currency.currency_position}</label>
                    <div id="wp-travel-currency-position" className="wp-travel-field-value">
                        <div className="wp-travel-select-wrapper">
                            <Select
                                options={currencyPositionOptions}
                                value={'undefined' != typeof selectedCurrencyPosition[0] && 'undefined' != typeof selectedCurrencyPosition[0].label ? selectedCurrencyPosition[0] : []}
                                onChange={(data) => {
                                    if ('' !== data) {
                                        updateSettings({
                                            ...allData,
                                            currency_position: data.value
                                        })
                                    }
                                }}
                            />
                        </div>
                        <p className="description">{_wp_travel.setting_strings.currency.currency_position_note}</p>
                    </div>
                </PanelRow>
                <PanelRow>
                    <label>{_wp_travel.setting_strings.currency.thousand_separator}</label>
                    <div id="wp-travel-thousand-separator" className="wp-travel-field-value">
                        <TextControl
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
                        <p className="description">{_wp_travel.setting_strings.currency.thousand_separator_note}</p>
                    </div>
                </PanelRow>
                <PanelRow>
                    <label>{_wp_travel.setting_strings.currency.decimal_separator}</label>
                    <div id="wp-travel-decimal-separator" className="wp-travel-field-value">
                        <TextControl                            
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
                        <p className="description">{_wp_travel.setting_strings.currency.decimal_separator_note}</p>
                    </div>
                </PanelRow>
                <PanelRow>
                    <label>{_wp_travel.setting_strings.currency.number_decimals}</label>
                    <div id="wp-travel-number-decimals" className="wp-travel-field-value">
                        <TextControl                            
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
                        <p className="description">{_wp_travel.setting_strings.currency.number_decimals_note}</p>
                    </div>
                </PanelRow>
            </div>
        </>
    )
})
