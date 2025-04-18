import { useSelect, select, dispatch } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl } from '@wordpress/components';
// import Select from 'react-select'

import ErrorBoundary from '../../../../../ErrorBoundry/ErrorBoundry';

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
                    {_wp_travel.setting_strings.account.account}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More account settings according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <ErrorBoundary>
                    <PanelRow>
                        <label>{_wp_travel.setting_strings.account.require_login}</label>
                        <div id="wp-travel-account-require-login" className="wp-travel-field-value">
                            <ToggleControl
                                checked={enableCheckoutCustomerRegistration == 'yes'}
                                onChange={() => {
                                    updateSettings({
                                        ...allData,
                                        enable_checkout_customer_registration: 'yes' == enableCheckoutCustomerRegistration ? 'no' : 'yes'
                                    })
                                }}
                            />
                            <p className="description">{_wp_travel.setting_strings.account.require_login_note}</p>
                        </div>
                    </PanelRow>

                    <PanelRow>
                        <label>{_wp_travel.setting_strings.account.enable_registration}</label>
                        <div id="wp-travel-account-enable-registration" className="wp-travel-field-value">
                            <ToggleControl
                                checked={enableMyAccountCustomerRegistration == 'yes'}
                                onChange={() => {
                                    updateSettings({
                                        ...allData,
                                        enable_my_account_customer_registration: 'yes' == enableMyAccountCustomerRegistration ? 'no' : 'yes'
                                    })
                                }}
                            />
                            <p className="description">{_wp_travel.setting_strings.account.enable_registration_note}</p>
                        </div>
                    </PanelRow>
                    <PanelRow>
                        <label>{_wp_travel.setting_strings.account.create_customer_on_booking}</label>
                        <div id="wp-travel-account-create-customer-booking" className="wp-travel-field-value">
                            <ToggleControl
                                checked={createUserWhileBooking == 'yes'}
                                onChange={() => {
                                    updateSettings({
                                        ...allData,
                                        create_user_while_booking: 'yes' == createUserWhileBooking ? 'no' : 'yes'
                                    })
                                }}
                            />
                            <p className="description">{_wp_travel.setting_strings.account.create_customer_on_booking_note}</p>
                        </div>
                    </PanelRow>
                    {('yes' == enableMyAccountCustomerRegistration || 'yes' == createUserWhileBooking) &&
                        <>
                            <PanelRow>
                                <label>{_wp_travel.setting_strings.account.automatically_generate_username}</label>
                                <div id="wp-travel-account-automatically-generate-username" className="wp-travel-field-value">
                                    <ToggleControl
                                        checked={generateUsernameFromEmail == 'yes'}
                                        onChange={() => {
                                            updateSettings({
                                                ...allData,
                                                generate_username_from_email: 'yes' == generateUsernameFromEmail ? 'no' : 'yes'
                                            })
                                        }}
                                    />
                                    <p className="description">{_wp_travel.setting_strings.account.automatically_generate_username_note}</p>
                                </div>
                            </PanelRow>
                            <PanelRow>
                                <label>{_wp_travel.setting_strings.account.automatically_generate_password}</label>
                                <div id="wp-travel-account-automatically-generate-password" className="wp-travel-field-value">
                                    <ToggleControl
                                        checked={generateUserPassword == 'yes'}
                                        onChange={() => {
                                            updateSettings({
                                                ...allData,
                                                generate_user_password: 'yes' == generateUserPassword ? 'no' : 'yes'
                                            })
                                        }}
                                    />
                                    <p className="description">{_wp_travel.setting_strings.account.automatically_generate_password_note}</p>
                                </div>
                            </PanelRow>
                        </>
                    }
                </ErrorBoundary>
            </div>
        </>
    );
}