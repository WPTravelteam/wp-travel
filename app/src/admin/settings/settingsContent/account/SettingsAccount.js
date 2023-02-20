import { applyFilters } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl, TextControl } from '@wordpress/components';
import Select from 'react-select'
import { VersionCompare } from '../../../fields/VersionCompare'

import ErrorBoundary from '../../../../ErrorBoundry/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const { updateSettings } = dispatch('WPTravel/Admin');
    const {
        enable_checkout_customer_registration,
        enable_my_account_customer_registration,
        create_user_while_booking,
        generate_username_from_email,
        generate_user_password,

        options } = allData;





    let enableCheckoutCustomerRegistration = 'undefined' != typeof enable_checkout_customer_registration ? enable_checkout_customer_registration : 'no'
    let enableMyAccountCustomerRegistration = 'undefined' != typeof enable_my_account_customer_registration ? enable_my_account_customer_registration : 'no'
    let createUserWhileBooking = 'undefined' != typeof create_user_while_booking ? create_user_while_booking : 'no'
    let generateUsernameFromEmail = 'undefined' != typeof generate_username_from_email ? generate_username_from_email : 'no'
    let generateUserPassword = 'undefined' != typeof generate_user_password ? generate_user_password : 'no'

    return (
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {__("Account", "wp-travel")}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More account according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <ErrorBoundary>
                    <PanelRow>
                        <label>{__('Require Login', 'wp-travel')}</label>
                        <div className="wp-travel-field-value">
                            <ToggleControl
                                checked={enableCheckoutCustomerRegistration == 'yes'}
                                onChange={() => {
                                    updateSettings({
                                        ...allData,
                                        enable_checkout_customer_registration: 'yes' == enableCheckoutCustomerRegistration ? 'no' : 'yes'
                                    })
                                }}
                            />
                            <p className="description">{__('Require Customer login or register before booking.', 'wp-travel')}</p>
                        </div>
                    </PanelRow>

                    <PanelRow>
                        <label>{__('Enable Registration', 'wp-travel')}</label>
                        <div className="wp-travel-field-value">
                            <ToggleControl
                                checked={enableMyAccountCustomerRegistration == 'yes'}
                                onChange={() => {
                                    updateSettings({
                                        ...allData,
                                        enable_my_account_customer_registration: 'yes' == enableMyAccountCustomerRegistration ? 'no' : 'yes'
                                    })
                                }}
                            />
                            <p className="description">{__('Enable customer registration on the "My Account" page.', 'wp-travel')}</p>
                        </div>
                    </PanelRow>
                    <PanelRow>
                        <label>{__('Create customer on booking', 'wp-travel')}</label>
                        <div className="wp-travel-field-value">
                            <ToggleControl
                                checked={createUserWhileBooking == 'yes'}
                                onChange={() => {
                                    updateSettings({
                                        ...allData,
                                        create_user_while_booking: 'yes' == createUserWhileBooking ? 'no' : 'yes'
                                    })
                                }}
                            />
                            <p className="description">{__('This will create WP Travel Customer while booking made', 'wp-travel')}</p>
                        </div>
                    </PanelRow>
                    {('yes' == enableMyAccountCustomerRegistration || 'yes' == createUserWhileBooking) &&
                        <>
                            <PanelRow>
                                <label>{__('Automatically generate username', 'wp-travel')}</label>
                                <div className="wp-travel-field-value">
                                    <ToggleControl
                                        checked={generateUsernameFromEmail == 'yes'}
                                        onChange={() => {
                                            updateSettings({
                                                ...allData,
                                                generate_username_from_email: 'yes' == generateUsernameFromEmail ? 'no' : 'yes'
                                            })
                                        }}
                                    />
                                    <p className="description">{__('Automatically generate username from customer email.', 'wp-travel')}</p>
                                </div>
                            </PanelRow>
                            <PanelRow>
                                <label>{__('Automatically generate password', 'wp-travel')}</label>
                                <div className="wp-travel-field-value">
                                    <ToggleControl
                                        checked={generateUserPassword == 'yes'}
                                        onChange={() => {
                                            updateSettings({
                                                ...allData,
                                                generate_user_password: 'yes' == generateUserPassword ? 'no' : 'yes'
                                            })
                                        }}
                                    />
                                    <p className="description">{__('Automatically generate customer password.', 'wp-travel')}</p>
                                </div>
                            </PanelRow>
                        </>
                    }
                </ErrorBoundary>
            </div>
        </>
    );
}