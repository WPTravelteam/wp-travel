import { useSelect, dispatch } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl } from '@wordpress/components';

import ErrorBoundary from '../../../ErrorBoundry/ErrorBoundry';

export default () => {
    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const { updateSettings } = dispatch('WPTravel/Admin');
    const { modules, options } = allData;
    const { default_settings, saved_settings } = options;
    const { modules:defaultModules } = default_settings;
    
    return <div className="wp-travel-ui wp-travel-ui-card settings-general">
        <h2>{ __( 'Modules Settings', 'wp-travel' ) }</h2>
        <p>{__( 'You can enable or disable modules features from here.', 'wp-travel' )}</p>
        <ErrorBoundary>
            <>
            {defaultModules && Object.keys( defaultModules ).length > 0 &&
               <>
                { Object.keys( defaultModules ).map( ( addonsKey, i ) => {

                    // enabledModule is temp varible to update in state.
                    // let enabledModule = false;

                    let enabledModule = 'yes' == modules[addonsKey].value; // Saved modules values.
                    // if ( saved_settings && 'undefined' !== typeof saved_settings.modules ) { // check If data is saved in new array structure.
                    // } else {
                    //     // fetch saved value from old structure. // Legacy
                    //     enabledModule = 'undefined' !== typeof allData[addonsKey] &&  'yes' == allData[addonsKey]; // Saved modules values.

                    //     // Exceptional cases
                    //     switch ( addonsKey ) {
                    //         case 'show_wp_travel_inventory_management':
                    //             enabledModule = 'undefined' !== typeof allData.show_wp_travel_inventory_magagement &&  'yes' == allData.show_wp_travel_inventory_magagement; // Typo error in old addons settings key.
                    //             break;
                            
                    //         // Previously have no settings to enabled disbled for [partial payment, Trip weather, Zapier, Invoice]
                    //         case 'show_wp_travel_partial_payment':
                    //         case 'show_wp_travel_trip_weather_forecast':
                    //         case 'show_wp_travel_zapier':
                    //         case 'show_wp_travel_invoice':
                    //             enabledModule = true;
                    //             break;

                    //         // Payment addons. [Note: Bank deposite and standard paypal is not addons ]
                    //         case 'show_wp_travel_authorize_net_checkout':
                    //             enabledModule = 'undefined' !== typeof allData.payment_option_authorizenet &&  'yes' == allData.payment_option_authorizenet;
                    //             break;
                    //         case 'show_wp_travel_paypal_express_checkout':
                    //             enabledModule = 'undefined' !== typeof allData.payment_option_express_checkout &&  'yes' == allData.payment_option_express_checkout;
                    //             break;
                    //         case 'show_wp_travel_khalti_checkout':
                    //             enabledModule = 'undefined' !== typeof allData.payment_option_khalti &&  'yes' == allData.payment_option_khalti;
                    //             break;
                    //         case 'show_wp_travel_payfast_checkout':
                    //             enabledModule = 'undefined' !== typeof allData.payment_option_payfast &&  'yes' == allData.payment_option_payfast;
                    //             break;
                    //         case 'show_wp_travel_payhere_checkout':
                    //             enabledModule = 'undefined' !== typeof allData.payment_option_payhere &&  'yes' == allData.payment_option_payhere;
                    //             break;
                            
                    //         case 'show_wp_travel_paystack_checkout':
                    //             enabledModule = 'undefined' !== typeof allData.payment_option_paystack &&  'yes' == allData.payment_option_paystack;
                    //             break;
                    //         case 'show_wp_travel_payu_checkout':
                    //             enabledModule = 'undefined' !== typeof allData.payment_option_payu &&  'yes' == allData.payment_option_payu;
                    //             break;
                    //         case 'show_wp_travel_razorpay_checkout':
                    //             enabledModule = 'undefined' !== typeof allData.payment_option_razorpay_checkout &&  'yes' == allData.payment_option_razorpay_checkout;
                    //             break;
                    //         case 'show_wp_travel_square_checkout':
                    //             enabledModule = 'undefined' !== typeof allData.payment_option_squareup_checkout &&  'yes' == allData.payment_option_squareup_checkout;
                    //             break;
                    //         case 'show_wp_travel_stripe':
                    //             enabledModule = 'undefined' !== typeof allData.payment_option_stripe &&  'yes' == allData.payment_option_stripe;
                    //             break;
                    //         case 'show_wp_travel_stripe_ideal_checkout':
                    //             enabledModule = 'undefined' !== typeof allData.payment_option_stripe_ideal &&  'yes' == allData.payment_option_stripe_ideal;
                    //             break;
                    //         case 'show_wp_travel_instamojo_checkout':
                    //             enabledModule = 'undefined' !== typeof allData.payment_option_instamojo_checkout &&  'yes' == allData.payment_option_instamojo_checkout;
                    //             break;
                    //         case 'show_wp_travel_payu_latam_checkout':
                    //             enabledModule = 'undefined' !== typeof allData.payment_option_payu_latam &&  'yes' == allData.payment_option_payu_latam;
                    //             break;
                    //     }
                    // }
                    

                    return <PanelRow key={i}>
                        <label>{modules[addonsKey].title}</label>
                        <div className="wp-travel-field-value">
                            <ToggleControl
                                checked={ enabledModule }
                                onChange={ ( val ) => {
                                    let _modules = modules;
                                    _modules[addonsKey].value = val ? 'yes' : 'no';
                                    console.log( '_modules', _modules );
                                    updateSettings({
                                        ...allData,
                                        modules : { ..._modules }
                                    })
                                } }
                            />
                            <p className="description">{__( 'Show all your "' + modules[addonsKey].title + '" settings and enable its feature', 'wp-travel' )}</p>
                        </div>
                    </PanelRow> 
                }  ) }
               </>
            }
            </>
        </ErrorBoundary>
    </div>
}