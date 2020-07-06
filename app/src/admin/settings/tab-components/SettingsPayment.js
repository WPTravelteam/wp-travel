import { applyFilters } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl, RangeControl, TextareaControl } from '@wordpress/components';
import Select from 'react-select'
import {VersionCompare} from '../../fields/VersionCompare'

import ErrorBoundary from '../../error/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);
    console.log(allData)

    const { updateSettings } = dispatch('WPTravel/Admin');
    const {
        partial_payment,
        minimum_partial_payout,
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
                
            {/* {applyFilters( 'wp_travel_below_debug_tab_fields', [] )} */}
        </ErrorBoundary>
    </div>
}