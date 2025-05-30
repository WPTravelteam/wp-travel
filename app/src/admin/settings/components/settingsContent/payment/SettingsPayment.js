import { applyFilters, addFilter } from '@wordpress/hooks';
import { useSelect, dispatch } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl, RangeControl, RadioControl, PanelBody, TextControl, TextareaControl, Button, Icon } from '@wordpress/components';
import { ReactSortable } from 'react-sortablejs';
import { alignJustify } from '@wordpress/icons';
import ErrorBoundary from '../../../../../ErrorBoundry/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);
    let _allData = allData

    const { updateSettings, updateRequestSending } = dispatch('WPTravel/Admin');
    const {
        partial_payment,
        partial_payment_amount,
        partial_amount,
        minimum_partial_payout,
        sorted_gateways,
        trip_tax_enable,
        trip_tax_price_inclusive,
        trip_tax_percentage,
        options,
        total_payout_fields,
    } = allData;

    let partial_payouts = minimum_partial_payout;
    if ('undefined' != typeof minimum_partial_payout && minimum_partial_payout.length > 0) {
        if ('string' == typeof minimum_partial_payout) { // fixes for old partial payment saved as string.
            partial_payouts = [minimum_partial_payout]
        }
    }

    let enableAllGateway = true
    sorted_gateways.map((gateway, index) => {
        if (enableAllGateway) {
            let payment_gateway_key = `payment_option_${gateway.key}`
            let payment_gateway_enabled = 'undefined' != typeof allData[payment_gateway_key] && allData[payment_gateway_key] ? allData[payment_gateway_key] : 'no';
            if ('no' == payment_gateway_enabled) {
                enableAllGateway = false
            }
        }
    })

    const updatePayoutOption = (value, _tabIndex) => {

        const { minimum_partial_payout } = _allData;

        let _allPayouts = minimum_partial_payout;

        if ('undefined' != typeof _allPayouts && _allPayouts.length > 0) {
            if ('string' == typeof _allPayouts) { // fixes for old partial payment saved as string.
                _allPayouts = [_allPayouts]
            }
        }
        _allPayouts[_tabIndex] = value

        let total_percent = 0
        let partial_payouts = minimum_partial_payout;

        if ('undefined' != typeof minimum_partial_payout && minimum_partial_payout.length > 0) {
            if ('string' == typeof minimum_partial_payout) { // fixes for old partial payment saved as string.
                partial_payouts = [minimum_partial_payout]
            }
            total_percent = partial_payouts.reduce(function (a, b) {
                return parseFloat(a) + parseFloat(b);
            }, 0);
        }

        if (total_percent > 100) {
            let exceed_val = total_percent - 100;

            if (exceed_val > 0) {
                value = value - exceed_val;
            }

        }
        _allPayouts[_tabIndex] = value; // temp fixes. not recommended this direct approach

        updateSettings({
            ...allData,
            minimum_partial_payout, _tabIndex: value
        })
    }

    const sortGateways = (sortedPricing) => {
        updateSettings({
            ...allData, // allData
            sorted_gateways: sortedPricing
        })
    }
    const array_move = (arr, old_index, new_index) => {
        if (new_index >= arr.length) {
            var k = new_index - arr.length + 1;
            while (k--) {
                arr.push(undefined);
            }
        }
        arr.splice(new_index, 0, arr.splice(old_index, 1)[0]);
        return arr; // for testing
    };

    let x = 1;

    return (
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {_wp_travel.setting_strings.payment.payment}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More payment settings according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content' id='wp-travel-partial-payment-pays'>
                <ErrorBoundary>
                    <PanelRow>
                        <label>{_wp_travel.setting_strings.payment.partial_payment}</label>
                        <div id="wp-travel-payment-partial" className="wp-travel-field-value">
                            <ToggleControl
                                checked={partial_payment == 'yes'}
                                onChange={() => {
                                    updateSettings({
                                        ...allData,
                                        partial_payment: 'yes' == partial_payment ? 'no' : 'yes'
                                    })
                                }}
                            />
                            <p className="description">{_wp_travel.setting_strings.payment.partial_payment_note}</p>
                        </div>
                    </PanelRow>

                    {'yes' == partial_payment && _wp_travel.is_pro_enable == 'yes' &&
                        <PanelRow>
                            <label>{_wp_travel.setting_strings.payment.in_amount}</label>
                            <div id="wp-travel-payment-partial" className="wp-travel-field-value">
                                <ToggleControl
                                    checked={partial_payment_amount == 'yes'}
                                    onChange={() => {
                                        updateSettings({
                                            ...allData,
                                            partial_payment_amount: 'yes' == partial_payment_amount ? 'no' : 'yes'
                                        })
                                    }}
                                />
                            </div>
                        </PanelRow>
                    }  

                    {'yes' == partial_payment && 'yes' == partial_payment_amount && _wp_travel.is_pro_enable == 'yes' &&
                        <PanelRow>
                        <label>{_wp_travel.setting_strings.payment.partial_amount}</label>
                        <div id="wp-travel-payment-partial-amount" className="wp-travel-field-value">
                            <TextControl
                                type="number"
                                value={partial_amount}
                                onChange={(value) => {
                                    updateSettings({
                                    ...allData,
                                    partial_amount: value,
                                    });
                                }}
                            />  
                        </div>
                        </PanelRow>
                    } 

                    {applyFilters('wp_travel_before_minimum_partial_payout', [], allData)}
                    
                    {'yes' == partial_payment && partial_payment_amount == 'no' && partial_payouts.length > 0 ?
                        <>

                            {'undefined' != typeof options && 'undefined' != options.has_partial_payment && options.has_partial_payment ?
                                <>
                                    {
                                        partial_payouts.length >= 1 && <>
                                            <PanelRow>
                                                <label>{_wp_travel.setting_strings.payment.partial_payout_one}</label>
                                                <div id="wp-travel-payment-partial-payout" className="wp-travel-field-value">
                                                    <RangeControl
                                                        value={'undefined' != typeof partial_payouts[0] ? parseFloat(partial_payouts[0]) : 0}
                                                        onChange={
                                                            (value) => updatePayoutOption(value, 0)
                                                        }
                                                        min={1.0}
                                                        max={100.0}
                                                        step={0.01}
                                                    />
                                                    <p className="description">{_wp_travel.setting_strings.payment.partial_payout_note}</p>
                                                </div>
                                            </PanelRow>
                                        </>}
                                    {partial_payouts.length >= 2 && <>
                                        <PanelRow>
                                            <label>{_wp_travel.setting_strings.payment.partial_payout_two}</label>
                                            <div className="wp-travel-field-value">
                                                <RangeControl
                                                    value={'undefined' != typeof partial_payouts[1] ? parseFloat(partial_payouts[1]) : 0}
                                                    onChange={
                                                        (value) => updatePayoutOption(value, 1)
                                                    }
                                                    min={1.0}
                                                    max={100.0}
                                                    step={0.01}
                                                />
                                                <p className="description">{_wp_travel.setting_strings.payment.partial_payout_note}</p>
                                            </div>
                                        </PanelRow>
                                    </>}
                                    {partial_payouts.length >= 3 && <>
                                        <PanelRow>
                                            <label>{_wp_travel.setting_strings.payment.partial_payout_three}</label>
                                            <div className="wp-travel-field-value">
                                                <RangeControl
                                                    value={'undefined' != typeof partial_payouts[2] ? parseFloat(partial_payouts[2]) : 0}
                                                    onChange={
                                                        (value) => updatePayoutOption(value, 2)
                                                    }
                                                    min={1.0}
                                                    max={100.0}
                                                    step={0.01}
                                                />
                                                <p className="description">{_wp_travel.setting_strings.payment.partial_payout_note}</p>
                                            </div>
                                        </PanelRow>
                                    </>}
                                    {partial_payouts.length >= 4 && <>
                                        <PanelRow>
                                            <label>{_wp_travel.setting_strings.payment.partial_payout_four}</label>
                                            <div className="wp-travel-field-value">
                                                <RangeControl
                                                    value={'undefined' != typeof partial_payouts[3] ? parseFloat(partial_payouts[3]) : 0}
                                                    onChange={
                                                        (value) => updatePayoutOption(value, 3)
                                                    }
                                                    min={1.0}
                                                    max={100.0}
                                                    step={0.01}
                                                />
                                                <p className="description">{_wp_travel.setting_strings.payment.partial_payout_note}</p>
                                            </div>
                                        </PanelRow>
                                    </>}
                                    {partial_payouts.length >= 5 && <>
                                        <PanelRow>
                                            <label>{_wp_travel.setting_strings.payment.partial_payout_five}</label>
                                            <div className="wp-travel-field-value">
                                                <RangeControl
                                                    value={'undefined' != typeof partial_payouts[4] ? parseFloat(partial_payouts[4]) : 0}
                                                    onChange={
                                                        (value) => updatePayoutOption(value, 4)
                                                    }
                                                    min={1.0}
                                                    max={100.0}
                                                    step={0.01}
                                                />
                                                <p className="description">{_wp_travel.setting_strings.payment.partial_payout_note}</p>
                                            </div>
                                        </PanelRow>
                                    </>}
                                    {partial_payouts.length >= 6 && <>
                                        <PanelRow>
                                            <label>{_wp_travel.setting_strings.payment.partial_payout_six}</label>
                                            <div className="wp-travel-field-value">
                                                <RangeControl
                                                    value={'undefined' != typeof partial_payouts[5] ? parseFloat(partial_payouts[5]) : 0}
                                                    onChange={
                                                        (value) => updatePayoutOption(value, 5)
                                                    }
                                                    min={1.0}
                                                    max={100.0}
                                                    step={0.01}
                                                />
                                                <p className="description">{_wp_travel.setting_strings.payment.partial_payout_note}</p>
                                            </div>
                                        </PanelRow>
                                    </>}
                                    {partial_payouts.length >= 7 && <>
                                        <PanelRow>
                                            <label>{_wp_travel.setting_strings.payment.partial_payout_seven}</label>
                                            <div className="wp-travel-field-value">
                                                <RangeControl
                                                    value={'undefined' != typeof partial_payouts[6] ? parseFloat(partial_payouts[6]) : 0}
                                                    onChange={
                                                        (value) => updatePayoutOption(value, 6)
                                                    }
                                                    min={1.0}
                                                    max={100.0}
                                                    step={0.01}
                                                />
                                                <p className="description">{_wp_travel.setting_strings.payment.partial_payout_note}</p>
                                            </div>
                                        </PanelRow>
                                    </>}
                                    {partial_payouts.length >= 8 && <>
                                        <PanelRow>
                                            <label>{_wp_travel.setting_strings.payment.partial_payout_eight}</label>
                                            <div className="wp-travel-field-value">
                                                <RangeControl
                                                    value={'undefined' != typeof partial_payouts[7] ? parseFloat(partial_payouts[7]) : 0}
                                                    onChange={
                                                        (value) => updatePayoutOption(value, 7)
                                                    }
                                                    min={1.0}
                                                    max={100.0}
                                                    step={0.01}
                                                />
                                                <p className="description">{_wp_travel.setting_strings.payment.partial_payout_note}</p>
                                            </div>
                                        </PanelRow>
                                    </>}
                                    {partial_payouts.length >= 9 && <>
                                        <PanelRow>
                                            <label>{_wp_travel.setting_strings.payment.partial_payout_nine}</label>
                                            <div className="wp-travel-field-value">
                                                <RangeControl
                                                    value={'undefined' != typeof partial_payouts[8] ? parseFloat(partial_payouts[8]) : 0}
                                                    onChange={
                                                        (value) => updatePayoutOption(value, 8)
                                                    }
                                                    min={1.0}
                                                    max={100.0}
                                                    step={0.01}
                                                />
                                                <p className="description">{_wp_travel.setting_strings.payment.partial_payout_note}</p>
                                            </div>
                                        </PanelRow>
                                    </>}
                                    {partial_payouts.length >= 10 && <>
                                        <PanelRow>
                                            <label>{_wp_travel.setting_strings.payment.partial_payout_ten}</label>
                                            <div className="wp-travel-field-value">
                                                <RangeControl
                                                    value={'undefined' != typeof partial_payouts[10] ? parseFloat(partial_payouts[10]) : 0}
                                                    onChange={
                                                        (value) => updatePayoutOption(value, 10)
                                                    }
                                                    min={1.0}
                                                    max={100.0}
                                                    step={0.01}
                                                />
                                                <p className="description">{_wp_travel.setting_strings.payment.partial_payout_note}</p>
                                            </div>
                                        </PanelRow>
                                    </>}
                                </>
                                :
                                <PanelRow>
                                    <label>{_wp_travel.setting_strings.payment.minimum_payout}</label>
                                    <div key={1} className="wp-travel-field-value">
                                        <RangeControl
                                            value={'undefined' != typeof partial_payouts && 'undefined' != typeof partial_payouts[0] ? parseFloat(partial_payouts[0]) : 0}
                                            onChange={
                                                (value) => updatePayoutOption(value, 0)
                                            }
                                            min={1.0}
                                            max={100.0}
                                            step={0.1}
                                        />
                                        <p className="description">{_wp_travel.setting_strings.payment.partial_payout_note}</p>
                                    </div>
                                </PanelRow>
                            }
                        </>
                        : <></>}
                    {applyFilters('wp_travel_after_minimum_partial_payout', [], allData)}


                    <PanelRow>
                        <h3>
                            {_wp_travel.setting_strings.payment.payment_gateways}
                        </h3>
                        <label id="wp-travel-payment-gateways" className="wp-travel-section-header-label">
                            <ToggleControl
                                checked={enableAllGateway}
                                onChange={(value) => {
                                    let gateway_key = null;
                                    let mapDataAction = sorted_gateways.map((gateway, index) => {
                                        gateway_key = `payment_option_${gateway.key}`
                                        _allData[gateway_key] = value ? 'yes' : 'no'

                                        // Additional settings for non consistant data [Need to improve in addons itself. For now temp fix from here]
                                        if ('payfast' == gateway.key || 'payu' == gateway.key || 'payhere' == gateway.key || 'payu_latam' == gateway.key) {
                                            let additionalArray = `wp_travel_${gateway.key}_settings`
                                            if ('undefined' != typeof _allData[additionalArray]) {
                                                _allData[additionalArray][gateway_key] = value ? 'yes' : 'no'
                                            } else {
                                                _allData[additionalArray] = {}
                                                _allData[additionalArray][gateway_key] = value ? 'yes' : 'no'
                                            }
                                        }
                                    })

                                    // Wait for all mapDataAction, and then updateSettings
                                    Promise.all(mapDataAction).then(() => {
                                        updateSettings(_allData)
                                    });
                                }}
                            />
                            <p className="description">{_wp_travel.setting_strings.payment.enable_disable}</p>
                        </label>
                    </PanelRow>

                    {
                        <div className="wp-travel-block-section wp-travel-block-sortable">
                            <ReactSortable
                                list={sorted_gateways}
                                setList={sorted => sortGateways(sorted)}
                                handle=".wp-travel-block-sortable .components-panel__icon"
                            >
                                {
                                    sorted_gateways.map((gateway, tabIndex) => {
                                        let gateway_key = `payment_option_${gateway.key}`
                                        let gateway_enabled = 'undefined' != typeof allData[gateway_key] && allData[gateway_key] ? allData[gateway_key] : 'no';

                                        return <div style={{ position: 'relative' }}>
                                            <div className={`wptravel-swap-list`}>
                                                <button
                                                    disabled={0 === tabIndex}
                                                    onClick={(e) => {
                                                        let sorted = array_move(sorted_gateways, tabIndex, tabIndex - 1)
                                                        sortGateways(sorted)
                                                        updateRequestSending(true); // Temp fixes to reload the content.
                                                        updateRequestSending(false);
                                                    }}><i className="dashicons dashicons-arrow-up"></i></button>
                                                <button
                                                    disabled={(sorted_gateways.length - 1) === tabIndex}
                                                    onClick={(e) => {
                                                        let sorted = array_move(sorted_gateways, tabIndex, tabIndex + 1)
                                                        sortGateways(sorted)
                                                        updateRequestSending(true);
                                                        updateRequestSending(false);
                                                    }}><i className="dashicons dashicons-arrow-down"></i></button>
                                            </div>
                                            <PanelBody
                                                icon={alignJustify}
                                                title={gateway.label}
                                                initialOpen={false}
                                                className={'no' == gateway_enabled ? 'ws-gateway gateway-disabled' : 'ws-gateway gateway-enabled'}
                                            >

                                                {applyFilters(`wp_travel_payment_gateway_fields_${gateway.key}`, [], allData)}
                                            </PanelBody>
                                        </div>
                                    })
                                }

                            </ReactSortable>
                        </div>
                    }
                    {applyFilters('wp_travel_after_payment_fields', [], allData)}

                    <h3>{_wp_travel.setting_strings.payment.tax_options}</h3>
                    <PanelRow>
                        <label>{_wp_travel.setting_strings.payment.tax_options_enable_tax}</label>
                        <div id="wp-travel-payment-tax-options" className="wp-travel-field-value">
                            <ToggleControl
                                checked={trip_tax_enable == 'yes'}
                                onChange={() => {
                                    updateSettings({
                                        ...allData,
                                        trip_tax_enable: 'yes' == trip_tax_enable ? 'no' : 'yes'
                                    })
                                }}
                            />
                            <p className="description">{_wp_travel.setting_strings.payment.tax_options_enable_tax_note}</p>
                        </div>
                    </PanelRow>

                    {'undefined' != typeof trip_tax_enable && 'yes' == trip_tax_enable &&
                        <>
                            <PanelRow>
                                <label>{_wp_travel.setting_strings.payment.tax_options_tax_on_trip_prices}</label>
                                <div id="wp-travel-payment-tax-on-trip" className="wp-travel-field-value">
                                    <RadioControl
                                        selected={trip_tax_price_inclusive}
                                        options={[
                                            { label: _wp_travel.setting_strings.payment.tax_options_tax_on_trip_prices_option_1, value: 'yes' },
                                            { label: _wp_travel.setting_strings.payment.tax_options_tax_on_trip_prices_option_2, value: 'no' },
                                        ]}
                                        onChange={(option) => {
                                            updateSettings({
                                                ...allData,
                                                trip_tax_price_inclusive: option
                                            })
                                        }}
                                    />
                                    <p className="description">{_wp_travel.setting_strings.payment.tax_options_tax_on_trip_prices_note}</p>
                                </div>
                            </PanelRow>
                            {'undefined' != typeof trip_tax_price_inclusive && 'no' == trip_tax_price_inclusive &&
                                <>
                                    <PanelRow>
                                        <label>{_wp_travel.setting_strings.payment.tax_options_tax_percentage}</label>
                                        <div id="wp-travel-payment-tax-percentage" className="wp-travel-field-value">

                                            <TextControl
                                                type="number"
                                                value={trip_tax_percentage}
                                                onChange={
                                                    (value) => {
                                                        updateSettings({
                                                            ...allData,
                                                            trip_tax_percentage: value
                                                        })
                                                    }
                                                }
                                            />
                                            <p className="description">{_wp_travel.setting_strings.payment.tax_options_tax_percentage_note}</p>
                                        </div>
                                    </PanelRow>
                                </>
                            }
                        </>
                    }
                </ErrorBoundary>
            </div>
        </>
    )
}

// Standard Paypal
addFilter('wp_travel_payment_gateway_fields_paypal', 'wp_travel', (content, allData) => {
    const { payment_option_paypal, paypal_email } = allData
    const { updateSettings } = dispatch('WPTravel/Admin');
    content = [
        <>

            <PanelRow>
                <label>{_wp_travel.setting_strings.payment.enable_paypal}</label>
                <div className="wp-travel-field-value">
                    <ToggleControl
                        checked={payment_option_paypal == 'yes'}
                        onChange={() => {
                            updateSettings({
                                ...allData,
                                payment_option_paypal: 'yes' == payment_option_paypal ? 'no' : 'yes'
                            })
                        }}
                    />
                    <p className="description">{_wp_travel.setting_strings.payment.enable_paypal_note}</p>
                </div>
            </PanelRow>
            {payment_option_paypal === 'yes' &&

                <PanelRow>
                    <label>{_wp_travel.setting_strings.payment.paypal_email}</label>
                    <div className="wp-travel-field-value">
                        <TextControl
                            value={paypal_email}
                            onChange={
                                (value) => {
                                    updateSettings({
                                        ...allData,
                                        paypal_email: value
                                    })
                                }
                            }
                        />
                        <p className="description">{_wp_travel.setting_strings.payment.paypal_email_note}</p>
                    </div>
                </PanelRow>
            }
        </>,
        ...content,
    ]
    return content
});


// Bank deposite
addFilter('wp_travel_payment_gateway_fields_bank_deposit', 'wp_travel', (content, allData) => {
    const {
        payment_option_bank_deposit,
        wp_travel_bank_deposit_description,
        wp_travel_bank_deposits
    } = allData
    const { updateSettings, addNewBankDetail } = dispatch('WPTravel/Admin');


    const updateBankDeposit = (key, value, _tabIndex) => {

        const { wp_travel_bank_deposits } = allData;

        let _allDeposit = wp_travel_bank_deposits;
        _allDeposit[_tabIndex][key] = value

        updateSettings({
            ...allData,
            wp_travel_bank_deposits: [..._allDeposit]
        })
    }

    const removeBankDeposit = (allBanks) => { // Remove
        updateSettings({
            ...allData,
            wp_travel_bank_deposits: [...allBanks]
        })
    }

    const addNewAccount = () => {
        addNewBankDetail({
            account_name: '',
            account_number: '',
            bank_name: '',
            sort_code: '',
            iban: '',
            swift: '',
            routing_number: '',
            enable: 'no',
        })
    }

    // Final Store Dispatcher.
    const sortBankDeposit = (sortedList) => {
        updateSettings({
            ...allData, // allData
            wp_travel_bank_deposits: sortedList
        })
    }
    content = [
        <>
            <PanelRow>
                <label>{_wp_travel.setting_strings.payment.bank_deposit_enable}</label>
                <div className="wp-travel-field-value">
                    <ToggleControl
                        checked={payment_option_bank_deposit == 'yes'}
                        onChange={() => {
                            updateSettings({
                                ...allData,
                                payment_option_bank_deposit: 'yes' == payment_option_bank_deposit ? 'no' : 'yes'
                            })
                        }}
                    />
                    <p className="description">{_wp_travel.setting_strings.payment.bank_deposit_enable_note}</p>
                </div>
            </PanelRow>
            {payment_option_bank_deposit === 'yes' &&
                <>
                    <PanelRow>
                        <label>{_wp_travel.setting_strings.payment.bank_deposit_enable_description}</label>
                        <div className="wp-travel-field-value">
                            <TextareaControl
                                value={wp_travel_bank_deposit_description}
                                onChange={
                                    (value) => {
                                        updateSettings({
                                            ...allData,
                                            wp_travel_bank_deposit_description: value
                                        })
                                    }
                                }
                            />
                        </div>
                    </PanelRow>
                    <h3>{_wp_travel.setting_strings.payment.account_detail}</h3>
                    {'undefined' != typeof wp_travel_bank_deposits && wp_travel_bank_deposits.length > 0 &&
                        <>
                            <ReactSortable
                                list={wp_travel_bank_deposits}
                                setList={sortedList => sortBankDeposit(sortedList)}
                                handle=".account-detail-sortable"
                            >
                                {wp_travel_bank_deposits.map((bankDeposite, index) => {
                                    return <PanelRow className="flex-wrap jsutify-content-end"><table><tr key={index}>
                                        <td><Icon icon={alignJustify} className="account-detail-sortable" /></td>
                                        <td>
                                            <TextControl
                                                value={bankDeposite.account_name}
                                                placeholder={__('Account Name', 'wp-travel')}
                                                onChange={
                                                    (value) => { updateBankDeposit('account_name', value, index) }
                                                }
                                            />
                                        </td>
                                        <td>
                                            <TextControl
                                                value={bankDeposite.account_number}
                                                placeholder={__('Account Number', 'wp-travel')}
                                                onChange={
                                                    (value) => { updateBankDeposit('account_number', value, index) }
                                                }
                                            />
                                        </td>
                                        <td>
                                            <TextControl
                                                value={bankDeposite.bank_name}
                                                placeholder={__('Bank Name', 'wp-travel')}
                                                onChange={
                                                    (value) => { updateBankDeposit('bank_name', value, index) }
                                                }
                                            />
                                        </td>
                                        <td>
                                            <TextControl
                                                value={bankDeposite.sort_code}
                                                placeholder={__('Sort Code', 'wp-travel')}
                                                onChange={
                                                    (value) => { updateBankDeposit('sort_code', value, index) }
                                                }
                                            />
                                        </td>
                                        <td>
                                            <TextControl
                                                value={bankDeposite.iban}
                                                placeholder={__('IBAN', 'wp-travel')}
                                                onChange={
                                                    (value) => { updateBankDeposit('iban', value, index) }
                                                }
                                            />
                                        </td>
                                        <td>
                                            <TextControl
                                                value={bankDeposite.swift}
                                                placeholder={__('BIC/Swift', 'wp-travel')}
                                                onChange={
                                                    (value) => { updateBankDeposit('swift', value, index) }
                                                }
                                            />
                                        </td>
                                        <td>
                                            <TextControl
                                                value={bankDeposite.routing_number}
                                                placeholder={__('Routing Number', 'wp-travel')}
                                                onChange={
                                                    (value) => { updateBankDeposit('routing_number', value, index) }
                                                }
                                            />
                                        </td>
                                        <td>
                                            <ToggleControl
                                                checked={bankDeposite.enable == 'yes'}
                                                onChange={
                                                    (e) => updateBankDeposit('enable', bankDeposite.enable == 'yes' ? 'no' : 'yes', index)
                                                }
                                            />
                                            <p className="description">{_wp_travel.setting_strings.payment.bank_deposit_enable}</p>
                                        </td>
                                    </tr>
                                    </table>
                                        <PanelRow className="wp-travel-action-section">
                                            <Button variant="secondary" onClick={() => {
                                                if (!confirm(_wp_travel.setting_strings.payment.remove_bank_note)) {
                                                    return false;
                                                }
                                                let bankData = [];
                                                bankData = wp_travel_bank_deposits.filter((data, newIndex) => {
                                                    return newIndex != index;
                                                });
                                                removeBankDeposit(bankData);
                                            }} className="wp-traval-button-danger">{_wp_travel.setting_strings.payment.remove_bank}</Button>
                                        </PanelRow>
                                    </PanelRow>
                                })}
                            </ReactSortable>
                        </>
                    }
                    <PanelRow className="wp-travel-action-section"><span></span><Button variant="secondary" onClick={() => addNewAccount()}>{_wp_travel.setting_strings.payment.add_new}</Button></PanelRow>

                </>
            }
        </>,
        ...content,
    ]
    return content
});