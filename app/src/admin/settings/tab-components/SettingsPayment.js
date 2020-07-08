import { applyFilters, addFilter } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl, RangeControl, PanelBody, TextControl, TextareaControl, Button, Icon } from '@wordpress/components';
import Select from 'react-select'
import {VersionCompare} from '../../fields/VersionCompare'
import { ReactSortable } from 'react-sortablejs';
import {alignJustify } from '@wordpress/icons';
import ErrorBoundary from '../../error/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);
    // console.log(allData)

    const { updateSettings } = dispatch('WPTravel/Admin');
    const {
        partial_payment,
        minimum_partial_payout,
        sorted_gateways,
        options
        } = allData;
    

    const updatePayoutOption = (value, _tabIndex) => {

        const { minimum_partial_payout } = allData;

        let _allPayouts = minimum_partial_payout;
        _allPayouts[_tabIndex] = value

        updateSettings({
            ...allData,
            minimum_partial_payout: [..._allPayouts]
        })
    }

    const sortGateways = ( sortedPricing) => {
        updateSettings({
            ...allData, // allData
            sorted_gateways: sortedPricing
        })
    }
    return <div className="wp-travel-ui wp-travel-ui-card settings-general">
        <h2>{ __( 'Payment Settings', 'wp-travel' ) }</h2>
        <ErrorBoundary>
            <PanelRow>
                <label>{ __( 'Partial Payment', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <ToggleControl
                        checked={ partial_payment == 'yes' }
                        onChange={ () => {
                            updateSettings({
                                ...allData,
                                partial_payment: 'yes' == partial_payment ? 'no': 'yes'
                            })
                        } }
                    />
                    <p className="description">{__( 'Enable Partial Payment while booking.', 'wp-travel' )}</p>
                </div>
            </PanelRow>
            
            {applyFilters( 'wp_travel_before_minimum_partial_payout', [] )}
            {minimum_partial_payout.map( (minPayout, index ) => {

                return <PanelRow>
                    <label>{ __( 'Minimum Payout (%)', 'wp-travel' ) }</label>
                    <div className="wp-travel-field-value">
                        <RangeControl
                            value={ minPayout }
                            onChange={
                                (value) => updatePayoutOption( value, index )
                            }
                            min={ 1 }
                            max={ 100 }
                        />
                        <p className="description">{__( 'Minimum percent of amount to pay while booking.', 'wp-travel' )}</p>
                    </div>
                </PanelRow>
            } ) }
            {applyFilters( 'wp_travel_after_minimum_partial_payout', [] )}


            <h3>{__( 'Payment Gateways', 'wp-travel' )}</h3>
            {
                <ReactSortable
                    list={sorted_gateways}
                    setList={sorted => sortGateways(sorted)}
                    handle=".settings-general .components-panel__icon"
                >
                    {
                        sorted_gateways.map((gateway, index) => {
                            // console.log(gateway.key)
                            return <PanelBody
                                icon= {alignJustify}
                                title={ gateway.label }
                                initialOpen={false}
                            >

                                {applyFilters( `wp_travel_payment_gateway_fields_${gateway.key}`, [], allData )}
                            </PanelBody>
                        } )
                    }

                </ReactSortable>}
        
        </ErrorBoundary>
    </div>
}

// Standard Paypal
addFilter('wp_travel_payment_gateway_fields_paypal', 'wp_travel', (content, allData) => {
    const {payment_option_paypal, paypal_email} = allData
    const { updateSettings } = dispatch('WPTravel/Admin');
    content = [
        <>
            
            <PanelRow>
                <label>{ __( 'Enable Paypal', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <ToggleControl
                        checked={ payment_option_paypal == 'yes' }
                        onChange={ () => {
                            updateSettings({
                                ...allData,
                                payment_option_paypal: 'yes' == payment_option_paypal ? 'no': 'yes'
                            })
                        } }
                    />
                    <p className="description">{__( 'Check to enable Standard PayPal payment.', 'wp-travel' )}</p>
                </div>
            </PanelRow>
            {payment_option_paypal === 'yes' && 
            
                <PanelRow>
                    <label>{ __( 'Paypal Email', 'wp-travel' ) }</label>
                    <div className="wp-travel-field-value">
                        <TextControl
                                value={paypal_email}
                                // placeholder={tab.default_label }
                                onChange={ 
                                    (value) => { 
                                        updateSettings({
                                            ...allData,
                                            paypal_email: value
                                        })
                                    }
                                }
                            />
                        <p className="description">{__( 'PayPal email address that receive payment.', 'wp-travel' )}</p>
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
            wp_travel_bank_deposits:[...allBanks]
        })
    }

    const addNewAccount = () => {
        addNewBankDetail( {
            account_name: '',
            account_number: '',
            bank_name: '',
            sort_code: '',
            iban: '',
            swift: '',
            enable: 'no',
        } )
    }

    // Final Store Dispatcher.
    const sortBankDeposit = ( sortedList ) => {
        updateSettings({
            ...allData, // allData
            wp_travel_bank_deposits: sortedList
        })
    }
    content = [
        <>
            <PanelRow>
                <label>{ __( 'Enable', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <ToggleControl
                        checked={ payment_option_bank_deposit == 'yes' }
                        onChange={ () => {
                            updateSettings({
                                ...allData,
                                payment_option_bank_deposit: 'yes' == payment_option_bank_deposit ? 'no': 'yes'
                            })
                        } }
                    />
                    <p className="description">{__( 'Check to enable Bank deposit.', 'wp-travel' )}</p>
                </div>
            </PanelRow>
            {payment_option_bank_deposit === 'yes' && 
                <>
                    <PanelRow>
                        <label>{ __( 'Description', 'wp-travel' ) }</label>
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
                    <h3>{__( 'Account Detail', 'wp-travel' )}</h3>
                    {'undefined' != typeof wp_travel_bank_deposits && wp_travel_bank_deposits.length > 0 &&
                        <>
                            <ReactSortable
                            list={wp_travel_bank_deposits}
                            setList={sortedList => sortBankDeposit( sortedList)}
                            handle=".account-detail-sortable"
                                >
                                {/* <table>
                                    <tr>
                                        <th></th>
                                        <th>{__( 'Account Name', 'wp-travel' )}</th>
                                        <th>{__( 'Account Number', 'wp-travel' )}</th>
                                        <th>{__( 'Bank Name', 'wp-travel' )}</th>
                                        <th>{__( 'Sort Code', 'wp-travel' )}</th>
                                        <th>{__( 'IBAN', 'wp-travel' )}</th>
                                        <th>{__( 'BIC/Swift', 'wp-travel' )}</th>
                                        <th>{__( 'Action', 'wp-travel' )}</th>
                                    </tr> */}
                                    {wp_travel_bank_deposits.map( ( bankDeposite, index ) => {
                                        return <tr key={index}>
                                            <td><Icon icon={alignJustify} className="account-detail-sortable" /></td>
                                            <td>
                                                <TextControl
                                                    value={bankDeposite.account_name}
                                                    placeholder={__( 'Account Name', 'wp-travel' )}
                                                    onChange={ 
                                                        (value) => { updateBankDeposit( 'account_name', value, index ) }
                                                    }
                                                />
                                            </td>
                                            <td>
                                                <TextControl
                                                    value={bankDeposite.account_number}
                                                    placeholder={__( 'Account Number', 'wp-travel' )}
                                                    onChange={ 
                                                        (value) => { updateBankDeposit( 'account_number', value, index ) }
                                                    }
                                                />
                                            </td>
                                            <td>
                                                <TextControl
                                                    value={bankDeposite.bank_name}
                                                    placeholder={__( 'Bank Name', 'wp-travel' )}
                                                    onChange={ 
                                                        (value) => { updateBankDeposit( 'bank_name', value, index ) }
                                                    }
                                                />
                                            </td>
                                            <td>
                                                <TextControl
                                                    value={bankDeposite.sort_code}
                                                    placeholder={__( 'Sort Code', 'wp-travel' )}
                                                    onChange={ 
                                                        (value) => { updateBankDeposit( 'sort_code', value, index ) }
                                                    }
                                                />
                                            </td>
                                            <td>
                                                <TextControl
                                                    value={bankDeposite.iban}
                                                    placeholder={__( 'BIC/Swift', 'wp-travel' )}
                                                    onChange={ 
                                                        (value) => { updateBankDeposit( 'iban', value, index ) }
                                                    }
                                                />
                                            </td>
                                            <td>
                                                <TextControl
                                                    value={bankDeposite.swift}
                                                    placeholder={__( 'BIC/Swift', 'wp-travel' )}
                                                    onChange={ 
                                                        (value) => { updateBankDeposit( 'swift', value, index ) }
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
                                                <p className="description">{__( 'Enable', 'wp-travel' )}</p>
                                            </td>
                                            <td>
                                                <Button isDefault onClick={() => {
                                                    if (!confirm(__( 'Are you sure to delete Bank Detail?', 'wp-travel' ) )) {
                                                        return false;
                                                    }
                                                    let bankData = [];
                                                    bankData = wp_travel_bank_deposits.filter((data, newIndex) => {
                                                        return newIndex != index;
                                                    });
                                                    removeBankDeposit(bankData);
                                                }} className="wp-traval-button-danger">{__( '- Remove bank', 'wp-travel' )}</Button>
                                            </td>
                                        </tr>
                                    } )}
                                {/* </table> */}
                            </ReactSortable>
                        </>
                    }
                    <PanelRow className="wp-travel-action-section"><span></span><Button isDefault onClick={() => addNewAccount()}>{ __( '+ Add New', 'wp-travel' ) }</Button></PanelRow>

                </>
            }
        </>,
        ...content,
    ]
    return content
});