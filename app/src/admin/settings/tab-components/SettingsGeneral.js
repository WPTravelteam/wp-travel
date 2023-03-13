import { applyFilters } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl, TextControl, Tooltip, Icon } from '@wordpress/components';
import {info, alignJustify } from '@wordpress/icons';

import Select from 'react-select'
import {VersionCompare} from '../../fields/VersionCompare'

import ErrorBoundary from '../../../ErrorBoundry/ErrorBoundry';
const __i18n = {
	..._wp_travel_admin.strings
}

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);
    const {
        wp_travel_switch_to_react, 
        currency, 
        currency_position,
        use_currency_name, 
        thousand_separator, 
        decimal_separator, 
        number_of_decimals,
        wp_travel_map, 
        google_map_api_key, 
        google_map_zoom_level, 
        // cart_page_id,
        checkout_page_id,
        dashboard_page_id,
        hide_plugin_archive_page_title,
        disable_admin_review,
        options } = allData;

    const { updateSettings } = dispatch('WPTravel/Admin');
    
    // options
    let currencyOptions = [];
    let currencyPositionOptions = []
    let mapOptions = []
    let pageOptions = []

    if ( 'undefined' != typeof options ) {
        if ( 'undefined' != typeof options.currencies ) {
            currencyOptions = options.currencies
        }

        if ( 'undefined' != typeof options.currency_positions ) {
            currencyPositionOptions = options.currency_positions
        }
        if ( 'undefined' != typeof options.maps ) {
            mapOptions = options.maps
        }
        if ( 'undefined' != typeof options.page_list ) {
            pageOptions = options.page_list
        } 
    }

    // selected options.
    let selectedCurrency = currencyOptions.filter( opt => { return opt.value == currency } )
    let selectedCurrencyPosition = currencyPositionOptions.filter( opt => { return opt.value == currency_position } )
    let selectedMap = mapOptions.filter( opt => { return opt.value == wp_travel_map } )
    // let selectedCartPage = pageOptions.filter( opt => { return opt.value == cart_page_id } )
    let selectedCheckoutPage = pageOptions.filter( opt => { return opt.value == checkout_page_id } )
    let selectedDashboardPage = pageOptions.filter( opt => { return opt.value == dashboard_page_id } )

    let switch_to_react = 'undefined' != typeof wp_travel_switch_to_react ? wp_travel_switch_to_react : 'no'
    
    return <div className="wp-travel-ui wp-travel-ui-card settings-general">
        <h2>{__i18n.general_setting}</h2>
        <ErrorBoundary>
            {/* { 'undefined' != typeof options && 'undefined' != typeof options.wp_travel_user_since && VersionCompare( options.wp_travel_user_since, '4.0.0', '<' ) &&
            
                <PanelRow>
                    <label>{ __( 'Switch to V4', 'wp-travel' ) }</label>
                    <div className="wp-travel-field-value">
                        <ToggleControl
                            // help={ __( 'This option will switch your trip edit page layout to new layout.', 'wp-travel' ) }
                            checked={ switch_to_react == 'yes' }
                            onChange={ () => {
                                updateSettings({
                                    ...allData,
                                    wp_travel_switch_to_react: 'yes' == switch_to_react ? 'no': 'yes'
                                })
                            } }
                        />
                        <p className="description">{__( 'This option will switch your trip edit page layout to new layout.', 'wp-travel' )}</p>
                    </div>
                </PanelRow>
            } */}
            <PanelRow>
                <label>{ __( 'Currency', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <div className="wp-travel-select-wrapper">
                        <Select
                            options={currencyOptions}
                            value={ 'undefined' != typeof selectedCurrency[0] && 'undefined' != typeof selectedCurrency[0].label ? selectedCurrency[0] : []}
                            onChange={(data)=>{
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
                            onChange={(data)=>{
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
                    <p className="description">{__( 'This sets the thousand separator of displayed prices.', 'wp-travel' )}</p>
                </div>
            </PanelRow>
            <PanelRow>
                <label>{ __( 'Decimal separator', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
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
                    <p className="description">{__( 'This sets the decimal separator of displayed prices.', 'wp-travel' )}</p>
                </div>
            </PanelRow>
            <PanelRow>
                <label>{ __( 'Number of decimals', 'wp-travel' ) }</label>
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
                    <p className="description">{__( 'This sets the number of decimal of displayed prices.', 'wp-travel' )}</p>
                </div>
            </PanelRow>



            <h4>{ __( 'Maps', 'wp-travel' ) }</h4>
            <PanelRow>
                <label>{ __( 'Select Map', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <div className="wp-travel-select-wrapper">
                        <Select
                            options={mapOptions}
                            value={ 'undefined' != typeof selectedMap[0] && 'undefined' != typeof selectedMap[0].label ? selectedMap[0] : []}
                            onChange={(data)=>{
                                if ( '' !== data ) {
                                    updateSettings({
                                        ...allData,
                                        wp_travel_map: data.value
                                    })
                                }
                            }}
                        />
                    </div>
                    <p className="description">{__( 'Choose your map provider to display map in site.', 'wp-travel' )}</p>
                </div>
            </PanelRow>

            { 'google-map' == wp_travel_map && <>
                <PanelRow>
                    <label>{ __( 'API Key', 'wp-travel' ) }
                        <Tooltip text={__( 'If you don\'t have API Key, you can use Map by using Lat/Lng or Location from location tab under trip edit page.', 'wp-travel' )}>
                            <span> <Icon icon={info} size="16" /></span>
                        </Tooltip>
                    </label>
                    <div className="wp-travel-field-value">
                        <TextControl
                            // help={__( 'To get your Google map API keys click here', 'wp-travel' )}
                            value={google_map_api_key}
                            onChange={ 
                                (value) => {
                                    updateSettings({
                                        ...allData,
                                        google_map_api_key: value
                                    })
                                }
                            }
                        />
                        <p className="description">{__( 'To get your Google map V3 API keys ', 'wp-travel' )} <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">{__( 'click here ', 'wp-travel' )}</a></p>
                        
                         
                    </div>
                </PanelRow>
                <PanelRow>
                    <label>{ __( 'Zoom Level', 'wp-travel' ) }</label>
                    <div className="wp-travel-field-value">
                        <TextControl
                            // help={__( 'Set default zoom level of map.', 'wp-travel' )}
                            type="number"
                            value={google_map_zoom_level}
                            onChange={ 
                                (value) => {
                                    updateSettings({
                                        ...allData,
                                        google_map_zoom_level: value
                                    })
                                }
                            }
                        />
                        <p className="description">{__( 'Set default zoom level of map.', 'wp-travel' )}</p>
                    </div>
                </PanelRow>
            </> }
            {applyFilters('wp_travel_settings_after_maps_fields', [] )}
            {applyFilters('wp_travel_settings_after_maps_upsell', [] )}

            <br/><br/>
            <h4>{ __( 'Pages', 'wp-travel' ) }</h4>
            {/* <PanelRow>
                <label>{ __( 'Cart Page', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <div className="wp-travel-select-wrapper">
                        <Select
                            options={pageOptions}
                            value={ 'undefined' != typeof selectedCartPage[0] && 'undefined' != typeof selectedCartPage[0].label ? selectedCartPage[0] : []}
                            onChange={(data)=>{
                                if ( '' !== data ) {
                                    updateSettings({
                                        ...allData,
                                        cart_page_id: data.value
                                    })
                                }
                            }}
                        />
                    </div>
                    <p className="description">{__( 'Choose the page to use as cart page for trip bookings which contents cart page shortcode [wp_travel_cart].', 'wp-travel' )}</p>
                </div>
            </PanelRow> */}
            <PanelRow>
                <label>{ __( 'Checkout Page', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <div className="wp-travel-select-wrapper">
                        <Select
                            options={pageOptions}
                            value={ 'undefined' != typeof selectedCheckoutPage[0] && 'undefined' != typeof selectedCheckoutPage[0].label ? selectedCheckoutPage[0] : []}
                            onChange={(data)=>{
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
                            onChange={(data)=>{
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
            {applyFilters('wp_travel_settings_after_general_fields', [] )}      
            <br/><br/>
            <h4>{ __( 'Archive Page title', 'wp-travel' ) }</h4>
            <PanelRow>
                <label>{ __( 'Hide Plugin Archive Page Title', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <ToggleControl
                        checked={ hide_plugin_archive_page_title == 'yes' }
                        onChange={ () => {
                            updateSettings({
                                ...allData,
                                hide_plugin_archive_page_title: 'yes' == hide_plugin_archive_page_title ? 'no': 'yes'
                            })
                        } }
                    />
                    <p className="description">{__( 'This option will hide archive title displaying from plugin.', 'wp-travel' )}</p>
                </div>
            </PanelRow>
             <PanelRow>
                <label>{ __( 'Disable Star Rating For Admin', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <ToggleControl
                        checked={ disable_admin_review == 'yes' }
                        onChange={ () => {
                            updateSettings({
                                ...allData,
                                disable_admin_review: 'yes' == disable_admin_review ? 'no': 'yes'
                            })
                        } }
                    />
                    <p className="description">{__( 'Enable to not allow star rating to admin', 'wp-travel' )}</p>
                </div>
            </PanelRow>
        </ErrorBoundary>
    </div>
}