import { applyFilters, addFilter } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl, RangeControl, PanelBody, TextControl, TextareaControl } from '@wordpress/components';
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
                            console.log(gateway.key)
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
    const {payment_option_bank_deposit, wp_travel_bank_deposit_description} = allData
    const { updateSettings } = dispatch('WPTravel/Admin');
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
            }
        </>,
        ...content,
    ]
    return content
});