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
                <h2 className="wp-travel-section-header-title">{__('Currency Settings', 'wp-travel')}</h2>
                <p className="wp-travel-section-header-description">{__('More general settings according to your choice.', 'wp-travel')}</p>
            </div>
            <div className='wp-travel-section-content'>
                <PanelRow>
                    <label>{__('Currency', 'wp-travel')}</label>
                    <div className="wp-travel-field-value">
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
                        <p className="description">{__('Choose currency you accept payments in.', 'wp-travel')}</p>
                    </div>
                </PanelRow>
                <PanelRow>
                    <label>
                        {__('Use Currency Name', 'wp-travel')}
                        <Tooltip text={__('This option will display currency name instead of symbol in frontend. ( E.g USD instead of $. )', 'wp-travel')}>
                            <span><i className='fa fa-info-circle'></i></span>
                        </Tooltip>
                    </label>
                    <div className="wp-travel-field-value">
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
                    <label>{__('Currency Position', 'wp-travel')}</label>
                    <div id="currency-position" className="wp-travel-field-value">
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
                        <p className="description">{__('Choose currency position.', 'wp-travel')}</p>
                    </div>
                </PanelRow>
                <PanelRow>
                    <label>{__('Thousand separator', 'wp-travel')}</label>
                    <div className="wp-travel-field-value">
                        <TextControl
                            // help={__( 'This sets the thousand separator of displayed prices.', 'wp-travel' )}
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
                        <p className="description">{__('This sets the thousand separator of displayed prices.', 'wp-travel')}</p>
                    </div>
                </PanelRow>
                <PanelRow>
                    <label>{__('Decimal separator', 'wp-travel')}</label>
                    <div id="decimal-separator" className="wp-travel-field-value">
                        <TextControl
                            // help={__( 'This sets the decimal separator of displayed prices.', 'wp-travel' )}
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
                        <p className="description">{__('This sets the decimal separator of displayed prices.', 'wp-travel')}</p>
                    </div>
                </PanelRow>
                <PanelRow>
                    <label>{__('Number of decimals', 'wp-travel')}</label>
                    <div className="wp-travel-field-value">
                        <TextControl
                            // help={__( 'This sets the number of decimal of displayed prices.', 'wp-travel' )}
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
                        <p className="description">{__('This sets the number of decimal of displayed prices.', 'wp-travel')}</p>
                    </div>
                </PanelRow>
            </div>
        </>
    )
})
