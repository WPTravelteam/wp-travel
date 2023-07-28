import { applyFilters, addFilter } from '@wordpress/hooks';
import FinishedTab from './tabs/finished';
import { useSelect, select, dispatch } from '@wordpress/data'; 
import { useState } from "react";
import { ProgressBar } from "react-step-progress-bar";
import { PanelRow, ToggleControl, RangeControl, RadioControl, PanelBody, TextControl, TextareaControl, Button, Icon } from '@wordpress/components';
import { ReactSortable } from 'react-sortablejs';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';

import Select from 'react-select'

import "react-step-progress-bar/styles.css";
import { createStore } from 'state-pool';

const store = createStore();  // Create store for storing our global state
store.setState("stepCount", 0);  // Create "count" global state and add it to the store



let stepCountValue = 0;

const Body = () => {

	var [stepCount, setStepCount] = store.useState("stepCount");

	// var [wptravelsetuppage, setwptravelsetuppage] = useState(0);

 	switch (stepCount) {
    	case 0:
	      	stepCountValue = 14.3;
	      	if ( document.getElementById("ready-tab-item") ) {
	      		document.getElementById("currency-tab-item").classList.remove( 'active' );
	      		document.getElementById("ready-tab-item").classList.add( 'active' );
	      	}

    	break;

    	case 1:
	      	stepCountValue = 28.6;
	      	document.getElementById("ready-tab-item").classList.remove( 'active' );
	      	document.getElementById("page-tab-item").classList.remove( 'active' );
	      	document.getElementById("currency-tab-item").classList.add( 'active' );

      	break;

    	case 2:
	     	stepCountValue = 42.9;
	     	document.getElementById("currency-tab-item").classList.remove( 'active' );
	     	document.getElementById("email-tab-item").classList.remove( 'active' );
	      	document.getElementById("page-tab-item").classList.add( 'active' );
    
      	break;

    	case 3:
	      	stepCountValue = 57.2;
	      	document.getElementById("page-tab-item").classList.remove( 'active' );
	      	document.getElementById("payment-tab-item").classList.remove( 'active' );
	      	document.getElementById("email-tab-item").classList.add( 'active' );

	      break;

    	case 4:
      		stepCountValue = 71.5;
      		document.getElementById("email-tab-item").classList.remove( 'active' );
      		document.getElementById("theme-tab-item").classList.remove( 'active' );
	      	document.getElementById("payment-tab-item").classList.add( 'active' );

      	break;

    	case 5:
      		stepCountValue = 85.8;
      		document.getElementById("payment-tab-item").classList.remove( 'active' );
      		document.getElementById("finished-tab-item").classList.remove( 'active' );
	      	document.getElementById("theme-tab-item").classList.add( 'active' );

	    break;

	   	case 6:
      		stepCountValue = 100;
      		document.getElementById("theme-tab-item").classList.remove( 'active' );
	      	document.getElementById("finished-tab-item").classList.add( 'active' );

      	break;

	}
	
	// function manageNext() {
	//     if ( stepCount != 6 ) {
	//       setStepCount(stepCount + 1 );
	//     }
	//     else if ( setStepCount = 6 ) {
	//       setStepCount(6);
	//     }
	//     return stepCount;
	// }

	const settingsData = useSelect((select) => {
        return select('WPTravel/Admin').getSettings()
    }, []);
    

    const nextStep = () => {
    	setStepCount(stepCount + 1);
    	document.body.scrollTop = 0;
  		document.documentElement.scrollTop = 0;
    	// return stepCount;
    }

    const backStep = () => {
    	setStepCount(stepCount - 1);
    	document.body.scrollTop = 0;
  		document.documentElement.scrollTop = 0;
    	// return stepCount;
    }

    const importTrip = () => {

		document.getElementById("trip-import-loader").classList.add( 'active' );
    	document.getElementById("finished-tab-content").classList.add( 'inactive' );
    	document.getElementById("wptravel-site-ready").classList.add( 'inactive' );


    	apiFetch( { path: '/wp-travel/v1/trip-import/', method: 'POST' } ).then( ( response ) => {
    		
		    location.replace( _wp_travel.admin_url + 'edit.php?post_type=itineraries' );
		} );
    }

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
        checkout_page_id,
        dashboard_page_id,
        wp_travel_from_email,
        send_booking_email_to_admin,
        partial_payment,
        minimum_partial_payout,
        sorted_gateways,
        trip_tax_enable,
        trip_tax_price_inclusive,
        trip_tax_percentage,
        total_payout_fields,
        options } = allData;

    const { updateSettings } = dispatch('WPTravel/Admin');

    const handleSubmit = ( e ) => {
		e.preventDefault();

		document.getElementById("setup-page-loader").classList.add( 'active' );
		document.getElementById("setup-page-form").classList.add( 'inactive' );
		document.getElementById("btn-group").style.display = 'none';
		document.getElementById("setting-save-notice").classList.add( 'active' );

		apiFetch( { url: `${ajaxurl}?action=wp_travel_update_settings&_nonce=${_wp_travel._nonce}`, data:allData, method:'post' } ).then( res => {       
            if( res.success && "WP_TRAVEL_UPDATED_SETTINGS" === res.data.code){
               	document.getElementById("setup-page-loader").classList.remove( 'active' );
               	document.getElementById("setting-save-notice").classList.remove( 'active' );
               	
               	setStepCount(stepCount + 1);
               	document.getElementById("setup-page-form").classList.remove( 'inactive' );
               	document.getElementById("btn-group").style.display = 'flex';
               	// document.getElementById("next-step").style.margin = '20px auto';
            }
        } );
	}
    
    // Currency
    let currencyOptions = [];
    let currencyPositionOptions = [];

    if ( 'undefined' != typeof options ) {
        if ( 'undefined' != typeof options.currencies ) {
            currencyOptions = options.currencies
        }

        if ( 'undefined' != typeof options.currency_positions ) {
            currencyPositionOptions = options.currency_positions
        }
    }

    let selectedCurrency = currencyOptions.filter( opt => { return opt.value == currency } );
    let selectedCurrencyPosition = currencyPositionOptions.filter( opt => { return opt.value == currency_position } );

	// page

	let pageOptions = []

    if ( 'undefined' != typeof options ) {
        if ( 'undefined' != typeof options.page_list ) {
            pageOptions = options.page_list
        } 
    }

    let selectedCheckoutPage = pageOptions.filter( opt => { return opt.value == checkout_page_id } )
    let selectedDashboardPage = pageOptions.filter( opt => { return opt.value == dashboard_page_id } )


    // Email

    let sendBookingEmailToAdmin = 'undefined' != typeof send_booking_email_to_admin ? send_booking_email_to_admin : 'no';
    
    const updateEmailData = ( storeName, storeKey, value) => { // storeName[storeKey] = value

        updateSettings({ ...allData, [storeName]: { ...allData[storeName], [storeKey]: value } })
    }

    // Payment

    let _allData = allData

    if ( ( 'undefined' !== typeof partial_payment ) && ( 'undefined' !== typeof sorted_gateways ) && ( 'undefined' !== typeof minimum_partial_payout ) ) { 
   	
	    let partial_payouts = minimum_partial_payout;
	    if (  'undefined' != typeof minimum_partial_payout && minimum_partial_payout.length > 0 ) {
	        if ( 'string' == typeof minimum_partial_payout ) { // fixes for old partial payment saved as string.
	            partial_payouts = [minimum_partial_payout]
	        }
	    }   

		let enableAllGateway = true
	    sorted_gateways.map((gateway, index) => {
	        if ( enableAllGateway ) {
	            let payment_gateway_key = `payment_option_${gateway.key}`
	            let payment_gateway_enabled = 'undefined' != typeof allData[payment_gateway_key] && allData[payment_gateway_key] ? allData[payment_gateway_key] : 'no';
	            if ( 'no' == payment_gateway_enabled ) {
	                enableAllGateway = false
	            }
	        }
	    })

		const updatePayoutOption = (value, _tabIndex) => {

	        const { minimum_partial_payout } = _allData;

	        let _allPayouts = minimum_partial_payout;

	        if (  'undefined' != typeof _allPayouts && _allPayouts.length > 0 ) {
	            if ( 'string' == typeof _allPayouts ) { // fixes for old partial payment saved as string.
	                _allPayouts = [_allPayouts]
	            }
	        }
	        _allPayouts[_tabIndex] = value

	        let total_percent = 0
	        let partial_payouts = minimum_partial_payout;

	        if (  'undefined' != typeof minimum_partial_payout && minimum_partial_payout.length > 0 ) {
	            if ( 'string' == typeof minimum_partial_payout ) { // fixes for old partial payment saved as string.
	                partial_payouts = [minimum_partial_payout]
	            }
	            total_percent = partial_payouts.reduce(function(a,b){
	                return parseFloat(a) + parseFloat(b);
	            }, 0);
	        }

	        if ( total_percent > 100 ) {
	            let exceed_val = total_percent - 100;

	            if ( exceed_val > 0 ) {
	                value = value - exceed_val;
	            }

	        }
	        _allPayouts[_tabIndex] = value; // temp fixes. not recommended this direct approach

	        updateSettings({
	            ...allData,
	            // minimum_partial_payout: _allPayouts
	            minimum_partial_payout, _tabIndex: value
	        })
	    }
	   

		const sortGateways = ( sortedPricing) => {
	        updateSettings({
	            ...allData, // allData
	            sorted_gateways: sortedPricing
	        })
	    }
	    
	    const  array_move = (arr, old_index, new_index) => {
	        if (new_index >= arr.length) {
	            var k = new_index - arr.length + 1;
	            while (k--) {
	                arr.push(undefined);
	            }
	        }
	        arr.splice(new_index, 0, arr.splice(old_index, 1)[0]);
	        return arr; // for testing
	    };

	    function installTheme ( slug, title ){

	    	document.getElementById("setup-page-loader").classList.add( 'active' );
			document.getElementById("wp-travel-theme-lists").classList.add( 'inactive' );
			document.getElementById("btn-group").style.display = 'none';
			let targetDiv = document.getElementById('setting-save-notice');        
	        let noticeText = document.createTextNode ( __( 'Installing and activating ', 'wp-travel' ) + title + __( ' theme ...', 'wp-travel' ) );
	        targetDiv.appendChild(noticeText);
			document.getElementById("setting-save-notice").classList.add( 'active' );

	    	apiFetch( { path: '/wp-travel/v1/theme-install/'+slug } ).then( ( response ) => {
	    		document.getElementById("setting-save-notice").classList.remove( 'active' );
			    setStepCount(stepCount + 1);
			} );
	    }

	    const switchTheme = ( slug, title ) => {

	    	document.getElementById("setup-page-loader").classList.add( 'active' );
			document.getElementById("wp-travel-theme-lists").classList.add( 'inactive' );
			document.getElementById("btn-group").style.display = 'none';
			let targetDiv = document.getElementById('setting-save-notice');        
	        let noticeText = document.createTextNode ( __( 'Installing and activating ', 'wp-travel' ) + title + __( ' theme ...', 'wp-travel' ) );
	        targetDiv.appendChild(noticeText);
			document.getElementById("setting-save-notice").classList.add( 'active' );
			

	    	apiFetch( { path: '/wp-travel/v1/theme-switch/'+slug } ).then( ( response ) => {
	    		document.getElementById("setting-save-notice").classList.remove( 'active' );
			    setStepCount(stepCount + 1);
			} );
	    }

	    return(
			<div id="wp-travel-setup-page-body">
				<ProgressBar percent={stepCountValue} filledBackground="#079812">
		        </ProgressBar>
				<ul id="wp-travel-setup-page-tab-list">
					<li id="ready-tab-item" className="tab-item active">{ __('Ready To Setup', 'wp-travel') }</li>
					<li id="currency-tab-item" className="tab-item">{ __('Currency', 'wp-travel') }</li>
					<li id="page-tab-item" className="tab-item">{ __('Page', 'wp-travel') }</li>
					<li id="email-tab-item"  className="tab-item">{ __('Email', 'wp-travel') }</li>
					<li id="payment-tab-item" className="tab-item">{ __('Payment', 'wp-travel') }</li>
					<li id="theme-tab-item" className="tab-item">{ __('Compatible Themes', 'wp-travel') }</li>
					<li id="finished-tab-item" className="tab-item">{ __('Finished Setup', 'wp-travel') }</li>
				</ul>
				<div id="wp-travel-setup-page-tab">
					{ stepCount == 0 &&  
						<div id="ready-tab" className="tab active">
							<h1>{ __('The Ultimate Tour Operator Plugin for WordPress', 'wp-travel') }</h1>
							<p>{ __('Create, design and launch your very own powerful travel website for free! Build your travel tourism business in minutes using WP Travel, the best travel and tour operator plugin for WordPress.', 'wp-travel') }</p>
							<a className="dashboard-btn" href={ _wp_travel.admin_url }>{ __('Go To Dashboard', 'wp-travel') }</a>
							<button id="next-step" onClick={nextStep} >{ __("Let's Start", "wp-travel") }</button>
						</div>
					}	
					{ stepCount == 1 && 
						<div id="currency-tab" className="tab">
					        <div id="setting-save-notice">{ __('Saving currency setting ...', 'wp-travel') }</div>  
							<img id="setup-page-loader" src={ _wp_travel.plugin_url + 'assets/images/loader.gif' } />
							<form onSubmit={handleSubmit} id="setup-page-form">
								<h1>{ __('Currency', 'wp-travel') }</h1>
								<PanelRow>
					                <label>{ __( 'Currency', 'wp-travel' ) }</label>
					                <div className="wp-travel-field-value">
					                    <div className="wp-travel-select-wrapper">
					                        <Select
					                            options={currencyOptions}
					                            value={ 'undefined' != typeof selectedCurrency[0] && 'undefined' != typeof selectedCurrency[0].label ? selectedCurrency[0] : []}
					                            onChange={ ( data ) =>{
					                                if ( '' !== data ) {
					                                    updateSettings({
					                                        ...allData,
					                                        currency: data.value
					                                    })
					                                }
					                            }}
					                        />
					                    </div>
					                    <p className="description">{__( 'Choose currency you accept payments in.', 'wp-travel' )}</p>
					                </div>
					            </PanelRow>
					            <PanelRow>
					                <label>{ __( 'Use Currency Name', 'wp-travel' ) }</label>
					                <div className="wp-travel-field-value">
					                    <ToggleControl
					                        checked={ use_currency_name == 'yes' }
					                        onChange={ () => {
					                            updateSettings({
					                                ...allData,
					                                use_currency_name: 'yes' == use_currency_name ? 'no': 'yes'
					                            })
					                        } }
					                    />
					                    <p className="description">{__( 'This option will display currency name instead of symbol in frontend. ( E.g USD instead of $. )', 'wp-travel' )}</p>
					                </div>
					            </PanelRow>
					            <PanelRow>
					                <label>{ __( 'Currency Position', 'wp-travel' ) }</label>
					                <div className="wp-travel-field-value">
					                    <div className="wp-travel-select-wrapper">
					                        <Select
					                            options={currencyPositionOptions}
					                            value={ 'undefined' != typeof selectedCurrencyPosition[0] && 'undefined' != typeof selectedCurrencyPosition[0].label ? selectedCurrencyPosition[0] : []}
					                            onChange={( data )=>{
					                                if ( '' !== data ) {
					                                    updateSettings({
					                                        ...allData,
					                                        currency_position: data.value
					                                    })
					                                }
					                            }}
					                        />
					                    </div>
					                    <p className="description">{__( 'Choose currency position.', 'wp-travel' )}</p>
					                </div>
					            </PanelRow>
					            <PanelRow>
					                <label>{ __( 'Thousand separator', 'wp-travel' ) }</label>
					                <div className="wp-travel-field-value">
					                    <TextControl
					                        value={thousand_separator}
					                        onChange={ 
					                            ( value ) => {
					                                updateSettings({
					                                    ...allData,
					                                    thousand_separator: value
					                                })
					                            }
					                        }
					                    />
					                    <p className="description">{__( 'This sets the thousand separator of displayed prices.', 'wp-travel' )}</p>
					                </div>
					            </PanelRow>
					            <PanelRow>
					                <label>{ __( 'Decimal separator', 'wp-travel' ) }</label>
					                <div className="wp-travel-field-value">
					                    <TextControl
					                        value={ decimal_separator }
					                        onChange={ 
					                            ( value ) => {
					                                updateSettings({
					                                    ...allData,
					                                    decimal_separator: value
					                                })
					                            }
					                        }
					                    />
					                    <p className="description">{ __( 'This sets the decimal separator of displayed prices.', 'wp-travel' ) }</p>
					                </div>
					            </PanelRow>
					            <PanelRow>
					                <label>{ __( 'Number of decimals', 'wp-travel' ) }</label>
					                <div className="wp-travel-field-value">
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
					                    <p className="description">{__( 'This sets the number of decimal of displayed prices.', 'wp-travel' )}</p>
					                </div>
					            </PanelRow>
					            <button 
									type="submit"
									className="dashboard-btn"
								>
									{ 
										__( 'Continue', 'wp-travel-pro' )
									}
								</button>
							</form>
							<div id="btn-group">
								<button id="back-step" onClick={backStep} >{__( 'Go Back', 'wp-travel' )}</button>
								<button id="next-step" onClick={nextStep} >{__( 'Skip this step', 'wp-travel' )}</button>
							</div>							
						</div>
					}
					{ stepCount == 2 && 
						<div id="page-tab" className="tab">
					        <div id="setting-save-notice">{ __( 'Saving page setting ...', 'wp-travel' ) }</div>  
							<img id="setup-page-loader" src={ _wp_travel.plugin_url + 'assets/images/loader.gif' } />
							<form onSubmit={handleSubmit} id="setup-page-form">
								<h1>{ __( 'Page', 'wp-travel' ) }</h1>
								<PanelRow>
					                <label>{ __( 'Checkout Page', 'wp-travel' ) }</label>
					                <div className="wp-travel-field-value">
					                    <div className="wp-travel-select-wrapper">
					                        <Select
					                            options={pageOptions}
					                            value={ 'undefined' != typeof selectedCheckoutPage[0] && 'undefined' != typeof selectedCheckoutPage[0].label ? selectedCheckoutPage[0] : []}
					                            onChange={ ( data )=>{
					                                if ( '' !== data ) {
					                                    updateSettings({
					                                        ...allData,
					                                        checkout_page_id: data.value
					                                    })
					                                }
					                            }}
					                        />
					                    </div>
					                    <p className="description">{__( 'Choose the page to use as checkout page for booking which contents checkout page shortcode [wp_travel_checkout].', 'wp-travel' )}</p>
					                </div>
					            </PanelRow>
					            <PanelRow>
					                <label>{ __( 'Dashboard Page', 'wp-travel' ) }</label>
					                <div className="wp-travel-field-value">
					                    <div className="wp-travel-select-wrapper">
					                        <Select
					                            options={pageOptions}
					                            value={ 'undefined' != typeof selectedDashboardPage[0] && 'undefined' != typeof selectedDashboardPage[0].label ? selectedDashboardPage[0] : []}
					                            onChange={( data )=>{
					                                if ( '' !== data ) {
					                                    updateSettings({
					                                        ...allData,
					                                        dashboard_page_id: data.value
					                                    })
					                                }
					                            }}
					                        />
					                    </div>
					                    <p className="description">{__( 'Choose the page to use as dashboard page which contents dashboard page shortcode [wp_travel_user_account].', 'wp-travel' )}</p>
					                </div>
					            </PanelRow>  
					            <button 
									type="submit"
									className="dashboard-btn"
								>
									{ 
										__( 'Continue', 'wp-travel-pro' )
									}
								</button>
							</form>
							<div id="btn-group">
								<button id="back-step" onClick={backStep} >{__( 'Go Back', 'wp-travel' )}</button>
								<button id="next-step" onClick={nextStep} >{__( 'Skip this step', 'wp-travel' )}</button>
							</div>	
						</div>
					}
					{ stepCount == 3 && 
						<div id="email-tab" className="tab">
							<div id="setting-save-notice">{ __( 'Saving email setting ...', 'wp-travel' ) }</div>  
							<img id="setup-page-loader" src={ _wp_travel.plugin_url + 'assets/images/loader.gif' } />
					            
							<form onSubmit={handleSubmit} id="setup-page-form">
								<h1>{ __( 'Email', 'wp-travel' ) }</h1>
								<PanelRow>
					                <label>{ __( 'From Email', 'wp-travel' ) }</label>
					                <div className="wp-travel-field-value">
					                    <TextControl
					                        value={wp_travel_from_email}
					                        onChange={ 
					                            (value) => {
					                                updateSettings({
					                                    ...allData,
					                                    wp_travel_from_email: value
					                                })
					                            }
					                        }
					                    />
					                    <p className="description">{__( 'Email address to send email from.', 'wp-travel' )}<strong>{__( ' Preferred to use webmail like: sales@yoursite.com', 'wp-travel' )}</strong></p>
					                </div>
					            </PanelRow>
					            <PanelRow>
					                <label>{ __( 'Send Email', 'wp-travel' ) }</label>
					                <div className="wp-travel-field-value">
					                    <ToggleControl
					                        checked={sendBookingEmailToAdmin == 'yes'}
					                        onChange={  () => {
					                                updateSettings({
					                                    ...allData,
					                                    send_booking_email_to_admin: 'yes' == sendBookingEmailToAdmin ? 'no': 'yes'
					                                })
					                            }
					                        }
					                    />
					                    <p className="description">{__( 'Enable or disable Email notification to admin.', 'wp-travel' )}</p>
					                </div>
					            </PanelRow>
					            <button 
									type="submit"
									className="dashboard-btn"
								>
									{ 
										__( 'Continue', 'wp-travel-pro' )
									}
								</button>
							</form>
							<div id="btn-group">
								<button id="back-step" onClick={backStep} >{__( 'Go Back', 'wp-travel' )}</button>
								<button id="next-step" onClick={nextStep} >{__( 'Skip this step', 'wp-travel' )}</button>
							</div>	
						</div>
					}
					{ stepCount == 4 && ( 'undefined' !== typeof partial_payment ) && ( 'undefined' !== typeof sorted_gateways ) && ( 'undefined' !== typeof minimum_partial_payout ) &&
						<div id="payment-tab" className="tab">
							<div id="setting-save-notice">{ __( 'Saving payment setting ...', 'wp-travel' ) }</div>  
							<img id="setup-page-loader" src={ _wp_travel.plugin_url + 'assets/images/loader.gif' } />
							<form onSubmit={handleSubmit} id="setup-page-form">
								<h1>{ __( 'Payment', 'wp-travel' ) }</h1>
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
					            {applyFilters( 'wp_travel_before_minimum_partial_payout', [], allData )}
					            { 'yes' == partial_payment && partial_payouts.length > 0 ? 
					                <>
					                    
					                    { 'undefined' != typeof options && 'undefined' != options.has_partial_payment && options.has_partial_payment ? 
					                        <>
					                            {
					                                partial_payouts.length >= 1 && <>
					                                <PanelRow>
					                                    <label>Partial Payout 1 (%)</label>
					                                    <div className="wp-travel-field-value">
					                                        <RangeControl
					                                            value={ 'undefined' != typeof partial_payouts[0]  ? parseFloat(partial_payouts[0]) : 0}
					                                            onChange={
					                                                (value) => updatePayoutOption( value, 0 )
					                                            }
					                                            min={ 1.0 }
					                                            max={ 100.0 }
					                                            step={ 0.01 }
					                                        />
					                                        <p className="description">{__( 'Minimum percent of amount to pay while booking.', 'wp-travel' )}</p>
					                                    </div>
					                                </PanelRow>
					                            </>}
					                            {partial_payouts.length >= 2 && <>
					                                <PanelRow>
					                                    <label>Partial Payout 2 (%)</label>
					                                    <div className="wp-travel-field-value">
					                                        <RangeControl
					                                            value={ 'undefined' != typeof partial_payouts[1]  ? parseFloat(partial_payouts[1]) : 0}
					                                            onChange={
					                                                (value) => updatePayoutOption( value, 1 )
					                                            }
					                                            min={ 1.0 }
					                                            max={ 100.0 }
					                                            step={ 0.01 }
					                                        />
					                                        <p className="description">{__( 'Minimum percent of amount to pay while booking.', 'wp-travel' )}</p>
					                                    </div>
					                                </PanelRow>
					                            </>}
					                            {partial_payouts.length >= 3 && <>
					                                <PanelRow>
					                                    <label>Partial Payout 3 (%)</label>
					                                    <div className="wp-travel-field-value">
					                                        <RangeControl
					                                            value={ 'undefined' != typeof partial_payouts[2]  ? parseFloat(partial_payouts[2]) : 0}
					                                            onChange={
					                                                (value) => updatePayoutOption( value, 2 )
					                                            }
					                                            min={ 1.0 }
					                                            max={ 100.0 }
					                                            step={ 0.01 }
					                                        />
					                                        <p className="description">{__( 'Minimum percent of amount to pay while booking.', 'wp-travel' )}</p>
					                                    </div>
					                                </PanelRow>
					                            </>}
					                            {partial_payouts.length >= 4 && <>
					                                <PanelRow>
					                                    <label>Partial Payout 4 (%)</label>
					                                    <div className="wp-travel-field-value">
					                                        <RangeControl
					                                            value={ 'undefined' != typeof partial_payouts[3]  ? parseFloat(partial_payouts[3]) : 0}
					                                            onChange={
					                                                (value) => updatePayoutOption( value, 3 )
					                                            }
					                                            min={ 1.0 }
					                                            max={ 100.0 }
					                                            step={ 0.01 }
					                                        />
					                                        <p className="description">{__( 'Minimum percent of amount to pay while booking.', 'wp-travel' )}</p>
					                                    </div>
					                                </PanelRow>
					                            </>}
					                        </>
					                    :
					                    <PanelRow>
					                        <label>{ __( 'Minimum Payout (%)', 'wp-travel' ) }</label>
					                        <div key={1} className="wp-travel-field-value">
					                            <RangeControl
					                                value={ 'undefined' != typeof partial_payouts && 'undefined' != typeof partial_payouts[0] ? parseFloat(partial_payouts[0]) : 0 }
					                                onChange={
					                                    (value) => updatePayoutOption( value, 0 )
					                                }
					                                min={ 1.0 }
					                                max={ 100.0 }
					                                step={ 0.1 }
					                            />
					                            <p className="description">{__( 'Minimum percent of amount to pay while booking.', 'wp-travel' )}</p>
					                        </div>
					                    </PanelRow>
					                    }
					                </>
					                : '' }
					            {applyFilters( 'wp_travel_after_minimum_partial_payout', [], allData )} 
					            <h3>
					                {__( 'Payment Gateways', 'wp-travel' )}
					                <label>
					                    <ToggleControl
					                        checked={enableAllGateway }
					                        onChange={ (value) => {
					                            let gateway_key = null;
					                            let mapDataAction =  sorted_gateways.map((gateway, index) => {
					                                gateway_key = `payment_option_${gateway.key}`
					                                _allData[gateway_key] = value ? 'yes' :'no'

					                                // Additional settings for non consistant data [Need to improve in addons itself. For now temp fix from here]
					                                if ( 'payfast' == gateway.key || 'payu' == gateway.key || 'payhere' == gateway.key || 'payu_latam' == gateway.key ) {
					                                    let additionalArray = `wp_travel_${gateway.key}_settings`
					                                    if ( 'undefined' != typeof _allData[additionalArray] ) {
					                                        _allData[additionalArray][gateway_key] = value ? 'yes' :'no'
					                                    } else {
					                                        _allData[additionalArray] = {}
					                                        _allData[additionalArray][gateway_key] = value ? 'yes' :'no'
					                                    }
					                                }
					                            })

					                            // Wait for all mapDataAction, and then updateSettings
					                            Promise.all(mapDataAction).then(() => {
					                                updateSettings(_allData)
					                            });
					                        } }
					                    />
					                    <p className="description">{__( 'Enable/Disable All', 'wp-travel' )}</p>
					                </label>
					            </h3>
					            {
					                <div className="wp-travel-block-section wp-travel-block-sortable">
					                    <ReactSortable
					                        list={sorted_gateways}
					                        setList={sorted => sortGateways(sorted)}
					                        handle=".settings-general .components-panel__icon"
					                    >
					                        {
					                            sorted_gateways.map((gateway, tabIndex) => {
					                                let gateway_key = `payment_option_${gateway.key}`
					                                let gateway_enabled = 'undefined' != typeof allData[gateway_key] && allData[gateway_key] ? allData[gateway_key] : 'no';
					                                
					                                return <div style={{position:'relative'}}>
					                                    <PanelBody
					                                        title={ gateway.label }
					                                        initialOpen={false}
					                                        className={'no' == gateway_enabled ? 'ws-gateway gateway-disabled' : 'ws-gateway gateway-enabled' }
					                                    >
					                                    	{applyFilters( `wp_travel_payment_gateway_fields_${gateway.key}`, [], allData )}
					                                    </PanelBody>
					                                </div>
					                            } )
					                        }
					                    </ReactSortable>
					                </div>
					            }

					            <h3>{__( 'Tax Options', 'wp-travel' )}</h3>
					            <PanelRow>
					                <label>{ __( 'Enable Tax', 'wp-travel' ) }</label>
					                <div className="wp-travel-field-value">
					                    <ToggleControl
					                        checked={ trip_tax_enable == 'yes' }
					                        onChange={ () => {
					                            updateSettings({
					                                ...allData,
					                                trip_tax_enable: 'yes' == trip_tax_enable ? 'no': 'yes'
					                            })
					                        } }
					                    />
					                    <p className="description">{__( 'Check to enable Tax options for trips.', 'wp-travel' )}</p>
					                </div>
					            </PanelRow>

					            {'undefined' != typeof trip_tax_enable && 'yes' == trip_tax_enable &&
					                <>
					                    <PanelRow>
					                        <label>{ __( 'Tax on Trip prices', 'wp-travel' ) }</label>
					                        <div className="wp-travel-field-value">
					                            <RadioControl
					                                selected={ trip_tax_price_inclusive }
					                                options={ [
					                                    { label: __( 'Yes, I will enter trip prices inclusive of tax', 'wp-travel' ), value: 'yes' },
					                                    { label: __( 'No, I will enter trip prices exclusive of tax', 'wp-travel' ), value: 'no' },
					                                ] }
					                                onChange={ ( option ) => { 
					                                    updateSettings({
					                                        ...allData,
					                                        trip_tax_price_inclusive: option
					                                    })
					                                } }
					                            />
					                            <p className="description">{__( 'This option will affect how you enter trip prices.', 'wp-travel' )}</p>
					                        </div>
					                    </PanelRow>
					                    { 'undefined' != typeof trip_tax_price_inclusive && 'no' == trip_tax_price_inclusive &&
					                        <>
					                            <PanelRow>
					                                <label>{ __( 'Tax Percentage', 'wp-travel' ) }</label>
					                                <div className="wp-travel-field-value">
					                                    
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
					                                    <p className="description">{__( 'Trip Tax percentage added to trip price.', 'wp-travel' )}</p>
					                                </div>
					                            </PanelRow>
					                        </>
					                    }

					                </>
					            }
					            <button 
									type="submit"
									className="dashboard-btn"
								>
									{ 
										__( 'Continue', 'wp-travel-pro' )
									}
								</button>
							</form>
							<div id="btn-group">
								<button id="back-step" onClick={backStep} >{__( 'Go Back', 'wp-travel' )}</button>
								<button id="next-step" onClick={nextStep} >{__( 'Skip this step', 'wp-travel' )}</button>
							</div>	
						</div>
					}
					{ stepCount == 5 &&
						<div id="theme-tab" className="tab">
							<div id="setting-save-notice"></div>  
							<img id="setup-page-loader" src={ _wp_travel.plugin_url + 'assets/images/loader.gif' } />					            
							
							<div id="wp-travel-theme-lists">
								<h1>{ __('Compatible Themes', 'wp-travel') }</h1>
								{
									_wp_travel.theme_datas == 1 &&
											<div className="wp-travel-theme-item">
					                        	<p>{__( 'Need internet connection for this step', 'wp-travel' )}</p>
					                        </div>

											||
											<div className="wp-travel-theme-lists">		

												{ _wp_travel.theme_datas == 1 &&
														<div className="wp-travel-theme-item">
								                        	<p>{__( 'Need internet connection for this step', 'wp-travel' )}</p>
								                        </div>

														||

														_wp_travel.theme_datas.map( ( { title, slug, screenshot_url, is_active, is_installed, theme_page } ) => {
									                    return <div className="wp-travel-theme-item">
									                                <img id="theme-image" src={screenshot_url} />
									                                <div className="wp-travel-theme-item-wrapper">
									                                	<h3>{title}</h3>
									                                	<div className="btns">
									                                		{										                           				
									                                			is_active == 'yes' &&
									                                			<p>{ __('Curently Active', 'wp-travel') }</p>
									                                			||
									                                			is_installed == 'yes' &&
									                                			<button onClick={ () =>{
										                                			switchTheme( slug, title )
										                                			}
										                                		} >
										                                			{ __('Active', 'wp-travel') }
										                                		</button>
										                                		||
										                                		<button onClick={ () =>{
										                                			installTheme( slug, title )
										                                			}
										                                		} >
										                                			{ __('Install & Active', 'wp-travel') }
										                                		</button>
									                                		}
									                                		<a className="dashboard-btn" href={ theme_page } target="_blank">
									                                			{ __('Theme Page', 'wp-travel') }
									                                		</a>
									                                	</div>
									                                </div>
									                            </div>
								                    
								                } ) }
								            </div>
								}
								
							</div>
							<div id="btn-group">
								<button id="back-step" onClick={backStep} >{__( 'Go Back', 'wp-travel' )}</button>
								<button id="next-step" onClick={nextStep} >{__( 'Skip this step', 'wp-travel' )}</button>
							</div>	
						</div>
					}
					{ stepCount == 6 && 
						<div id="finished-tab" className="tab">
							<div id="trip-import-loader">
								<p>{ __('Importing demo trips ...', 'wp-travel') }</p>
								<img id="setup-page-loader" src={ _wp_travel.plugin_url + 'assets/images/loader.gif' } className="active" />				
							</div>
							<section id="wptravel-site-ready">
						        <div className="wptravel-wrapper">
						        	<img src={ _wp_travel.plugin_url + 'assets/images/travel-site-ready.png' } />
						            <h1 className="wp-entity-title">{ __('Your Site Is Ready!', 'wp-travel') }</h1>
						            <ul className="wp-travel-social-links">
						            	<li>
						            		<a href="https://www.facebook.com/wptravel.io/" target="_blank">
						            			<svg width="40" height="41" fill="none" xmlns="http://www.w3.org/2000/svg">
						            				<circle cx="19.858" cy="19.873" r="19.858" fill="#3B5998"/>
						            				<path d="M24.85 20.65h-3.544v12.981h-5.368v-12.98h-2.553v-4.563h2.553v-2.952c0-2.111 1.003-5.417 5.416-5.417l3.977.016v4.429h-2.885c-.474 0-1.14.236-1.14 1.243v2.685h4.013l-.47 4.558z" fill="#fff"/>
						            			</svg>
						            		</a>
						            	</li>
						            	<li>
						            		<a href="https://twitter.com/wptravel_io" target="_blank">
						            			<svg width="40" height="41" fill="none" xmlns="http://www.w3.org/2000/svg">
						            				<circle cx="20.188" cy="19.873" r="19.858" fill="#55ACEE"/>
						            				<path d="M32.352 14.286a9.47 9.47 0 01-2.726.747 4.759 4.759 0 002.087-2.626A9.502 9.502 0 0128.7 13.56a4.747 4.747 0 00-8.088 4.33 13.474 13.474 0 01-9.784-4.96 4.746 4.746 0 001.469 6.337 4.713 4.713 0 01-2.15-.594v.06a4.75 4.75 0 003.807 4.654 4.73 4.73 0 01-2.143.082 4.752 4.752 0 004.434 3.296 9.524 9.524 0 01-5.896 2.032c-.382 0-.76-.022-1.131-.066a13.427 13.427 0 007.275 2.132c8.73 0 13.505-7.232 13.505-13.505 0-.206-.004-.41-.014-.614a9.625 9.625 0 002.37-2.457z" fill="#F1F2F2"/>
						            			</svg>
						            		</a>
						            	</li>
						            	<li>
						            		<a href="https://www.youtube.com/channel/UCJx51UI1H73clCxBCTHuEjA" target="_blank">
						            			<svg width="40" height="41" fill="none" xmlns="http://www.w3.org/2000/svg">
						            				<circle cx="20.66" cy="20.015" r="20" fill="#D42428"/>
						            				<path d="M34.76 5.63c7.866 7.866 7.866 20.62 0 28.486s-20.62 7.866-28.487 0L34.76 5.629z" fill="#CC202D"/><path fill-rule="evenodd" clip-rule="evenodd" d="M28.145 12.998c1.68 0 3.042 1.375 3.042 3.072v8.24c0 1.698-1.362 3.074-3.042 3.074H13.877c-1.68 0-3.041-1.376-3.041-3.073v-8.24c0-1.697 1.361-3.073 3.04-3.073h14.27zm-9.17 2.93v7.739l5.813-3.87-5.812-3.87z" fill="#fff"/>
						            			</svg>
						            		</a>
						            	</li>
						            </ul>
						            <a className="dashboard-btn" href="https://wptravel.io/wp-travel-docs/" target="_blank">{ __('Documentation', 'wp-travel') }</a>
						            <a className="dashboard-btn" href={_wp_travel.admin_url + 'edit.php?post_type=itineraries'}>{ __('Create Trip', 'wp-travel') }</a>
						            <button  className="dashboard-btn" id="next-step" onClick={importTrip}>{ __('Import Trip', 'wp-travel') }</button>
						        </div>
						        <button id="back-step" onClick={backStep} >{__( 'Go Back', 'wp-travel' )}</button>
						        <a className="secondary-btn" href={ _wp_travel.admin_url }>{ __('Go To Dashboard', 'wp-travel') }</a>
						    </section>
			
							<FinishedTab /> 
						</div>
					}
				</div>
				
			</div>
		);
	}else{
   		return (
   			<div id="wp-travel-setup-page-body">
				<ProgressBar percent={stepCountValue} filledBackground="#159F84">
		        </ProgressBar>
				<ul id="wp-travel-setup-page-tab-list">
					<li id="ready-tab-item" className="tab-item active">{ __('Ready To Setup', 'wp-travel') }</li>
					<li id="currency-tab-item" className="tab-item">{ __('Currency', 'wp-travel') }</li>
					<li id="page-tab-item" className="tab-item">{ __('Page', 'wp-travel') }</li>
					<li id="email-tab-item"  className="tab-item">{ __('Email', 'wp-travel') }</li>
					<li id="payment-tab-item" className="tab-item">{ __('Payment', 'wp-travel') }</li>
					<li id="theme-tab-item" className="tab-item">{ __('Compatible Themes', 'wp-travel') }</li>
					<li id="finished-tab-item" className="tab-item">{ __('Finished Setup', 'wp-travel') }</li>
				</ul>
				<div id="wp-travel-setup-page-tab">
					{ stepCount == 0 &&  
						<div id="ready-tab" className="tab active">
							<h1>{ __('The Ultimate Tour Operator Plugin for WordPress', 'wp-travel') }</h1>
							<p>{ __('Create, design and launch your very own powerful travel website for free! Build your travel tourism business in minutes using WP Travel, the best travel and tour operator plugin for WordPress.', 'wp-travel') }</p>
							<a className="dashboard-btn" href={ _wp_travel.admin_url }>{ __('Go To Dashboard', 'wp-travel') }</a>
							<button id="next-step" onClick={nextStep} >{ __("Let's Start", "wp-travel") }</button>
						</div>

					}
				</div>				
			</div>
   		);
   	}


	
}

export default Body

