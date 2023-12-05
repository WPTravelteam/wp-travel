import { applyFilters } from '@wordpress/hooks';
import { useState} from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { useSelect, select, dispatch, withSelect, forwardRef } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl, TextControl, SelectControl, Dropdown, DateTimePicker, Notice } from '@wordpress/components';
import Select from 'react-select'
import {VersionCompare} from '../../fields/VersionCompare'
// import DatePicker, {registerLocale} from "react-datepicker";
import ErrorBoundary from '../../../ErrorBoundry/ErrorBoundry';

export default () => {
    const allData = useSelect((select) => {
        return select('WPTravel/Coupon').getAllStore()
    }, []);

    const {general} = allData

    const status             = 'undefined' !== typeof general && 'undefined' !== typeof general.status ? general.status : '';
    const coupon_code        = 'undefined' !== typeof general && 'undefined' !== typeof general.coupon_code ? general.coupon_code : '';
    const coupon_type        = 'undefined' !== typeof general && 'undefined' !== typeof general.coupon_type ? general.coupon_type : 'fixed';
    const coupon_value       = 'undefined' !== typeof general && 'undefined' !== typeof general.coupon_value ? general.coupon_value : '';
    const coupon_expiry_date = 'undefined' !== typeof general && 'undefined' !== typeof general.coupon_expiry_date ? general.coupon_expiry_date : '';
    // Update Values
    const { updateCoupon, disableSave } = dispatch('WPTravel/Coupon');
    // Local States
    const initialState = {
        code_exists: false
    }
    const [{ code_exists } , setState] = useState(initialState)
    const updateState = data => {
		setState(state => ({ ...state, ...data }))
	}

    return <div className="wp-travel-ui wp-travel-ui-card coupon-general">
        <h3>{ __( 'General Settings', 'wp-travel' ) }</h3>
        <ErrorBoundary>
            {status&&
                <PanelRow>
                    <label>{ __( 'Coupon Status', 'wp-travel' ) }</label>
                    <div className="wp-travel-field-value">
                    <span className="wp-travel-info-msg">{'Active' === status ? status : 'Expired'}</span>
                    </div>
                </PanelRow>
            }

            <PanelRow>
                <label>{ __( 'Coupon Code', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <TextControl
                        value={coupon_code}
                        onChange={
                            (value) => {

                                updateCoupon({
                                    ...allData,
                                    general: {...general, coupon_code: value }
                                })
                                // Checks whether coupon already exists or not.
                                const url = `${ajaxurl}?action=wp_travel_check_coupon_code&_nonce=${_wp_travel._nonce}&coupon_id=${_wp_travel.postID}&coupon_code=${value}`;
                                apiFetch( { url: url } ).then( ( result ) => {
                                    if ( ! result.success ) {
                                        updateState({
                                            code_exists:true
                                        });
                                        disableSave(true)
                                    } else {
                                        updateState({
                                            code_exists:false
                                        });
                                        disableSave(false)
                                    }
                                } );
                            }
                        }
                    />
                </div>
            </PanelRow>
            {code_exists&& <Notice status="warning" isDismissible={false}>{__( 'Coupon Code already used. Please choose a unique coupon code', 'wp-travel')}</Notice>}
            <PanelRow>
                <label>{ __( 'Coupon Type', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <SelectControl
                        value={ coupon_type }
                        options={ [
                            {
                                label: 'Fixed Discount',
                                value:'fixed'
                            }, {
                                label: 'Percentage Discount',
                                value:'percentage'
                            }
                        ] }
                        onChange={ ( value ) => {
                            updateCoupon({
                                ...allData,
                                general: {...general, coupon_type: value }
                            })
                        } }
                    />
                </div>
            </PanelRow>
            <PanelRow>
                <label>{ __( 'Coupon Value', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <TextControl
                        value={coupon_value}
                        type="number"
                        onChange={ 
                            (value) => {
                                updateCoupon({
                                    ...allData,
                                    general: {...general, coupon_value: value }
                                })
                            }
                        }
                    />
                </div>
            </PanelRow>
            <PanelRow>
                <label>Coupon Expiry Date</label>
                <div className="wp-travel-field-value">
                    <Dropdown
                        className="my-container-class-name"
                        contentClassName="my-popover-content-classname"
                        popoverProps={ { placement: 'bottom-right' } }
                        renderToggle={({ isOpen, onToggle }) => {
                            var couponExpiryDate = moment(coupon_expiry_date);
                            return <TextControl value={couponExpiryDate.isValid() ? coupon_expiry_date : ''} onFocus={onToggle} aria-expanded={isOpen} onChange={() => false} autoComplete="off" />
                        }}
                        renderContent={() => {
                            {
                                let _coupon_expiry_date = moment(coupon_expiry_date)
                                _coupon_expiry_date = _coupon_expiry_date.isValid() ? _coupon_expiry_date.toDate() : new Date();

                                return (
                                    <div className="wp-travel-dropdown-content-wrap wp-travel-datetimepicker wp-travel-datetimepicker-hide-time">
                                        <DateTimePicker
                                            currentDate={_coupon_expiry_date}
                                            minDate={new Date()}
                                            isInvalidDate={ (date) =>{
                                                if (!moment(date).isAfter(new Date())) {
                                                    return true;
                                                }
                                                return false;
                                            } }
                                            onChange={(date) => {
                                                updateCoupon({
                                                    ...allData,
                                                    general: {...general, coupon_expiry_date: moment(date).format('YYYY-MM-DD', date) }
                                                })

                                            }}
                                        />
                                    </div>
                                )
                            }
                        }}
                    />
                </div>
            </PanelRow>
        </ErrorBoundary>
    </div>
}