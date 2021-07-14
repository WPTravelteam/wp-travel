import { useState, useEffect } from '@wordpress/element';
import { TextControl, PanelRow, PanelBody, Button, TabPanel,Notice , FormTokenField, ToggleControl} from '@wordpress/components';
import { applyFilters } from '@wordpress/hooks';
import { useSelect, dispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { sprintf, _n, __} from '@wordpress/i18n';
import {alignJustify } from '@wordpress/icons';

import { ReactSortable } from 'react-sortablejs';
import Select from 'react-select'

import WPTravelTripPricingCategories from './trip-pricings-categories';
import WPTravelTripDates from './trip-dates';

import ErrorBoundary from '../../ErrorBoundry/ErrorBoundry';
const __i18n = {
	..._wp_travel_admin.strings
}
const WPTravelTripOptionsPricings = () => {
    const allData = useSelect((select) => {
        return select('WPTravel/TripEdit').getAllStore()
    }, []);

    const [{tripExtrasData}, setState] = useState({
        tripExtrasData:[]
    });
    const settings = useSelect((select) => {
        return select('WPTravel/TripEdit').getSettings()
    }, []);

    const { pricing_type, pricings, has_state_changes, is_multiple_dates, group_size, has_extras, dates, minimum_partial_payout_use_global, minimum_partial_payout_percent } = allData;
    const {options} = settings
    const {updateStateChange, updateTripPricing, addTripPricing, updateTripPrices, updateTripData, setTripData, updateRequestSending } = dispatch('WPTravel/TripEdit');

    let tripPrices = 'undefined' != typeof pricings ? pricings : [];
    
    const addTripPrice = () => {
        addTripPricing({
            id: false,
            title: '',
            categories: []
        })
    }
    const sortPricings = ( sortedPricing) => {
        updateTripData({
            ...allData, // allData
            pricings: sortedPricing
        })
    }
    let totalPayout = 0

    //Fixes
    let payout_percentages = [];

    if ( ! minimum_partial_payout_use_global && settings.minimum_partial_payout.length > 0 ) {
        // Pro enabled case.
        if ( 'undefined' != typeof options && 'undefined' != options.has_partial_payment && options.has_partial_payment ) {
            payout_percentages = settings.minimum_partial_payout; 
        } else {
            payout_percentages = [ settings.minimum_partial_payout[0] ];
        }
    }
    return <ErrorBoundary>
        <div className="wp-travel-trip-pricings">
            {applyFilters('wp_travel_before_pricings_options', [], allData)}
            {'multiple-price' === pricing_type && <>
                {tripPrices.length > 0 ? <>
                
                    <Notice status="warning" isDismissible={false}>{__i18n.messages.pricing_message}</Notice>
                    <PanelRow className="wp-travel-action-section"><span></span><Button isDefault onClick={() => addTripPrice()}>{__i18n.add_price }</Button></PanelRow>
                    <div className="wp-travel-sortable-component">
                    <ReactSortable
                        list={tripPrices}
                        setList={sortedPricing => sortPricings(sortedPricing)}
                        handle=".wp-travel-trip-pricings .components-panel__icon"
                    >
                    {tripPrices.map((price, priceIndex) => {
                        let tripExtrasDefaultData = [];
                        
                        if( 'undefined' !== typeof price.trip_extras && price.trip_extras.length >0 ) {
                            tripExtrasDefaultData = price.trip_extras.map( s => ({...s,value:s.id, label:s.title}) );
                        }
                        return <PanelBody
                                icon= {alignJustify}
                                title={ price.title ? `${price.title}` : __i18n.pricing_name }
                                initialOpen={(tripPrices.length - 1 === priceIndex)}
                            >
                            <PanelRow>
                                <label>{__i18n.pricing_name}</label>
                                <TextControl
                                    placeholder={__i18n.pricing_name}
                                    value={price.title}
                                    onChange={(title) => {
                                        let priceData = price;
                                        priceData.title = title;
                                        updateTripPricing(priceData, priceIndex)
                                    }}
                                />
                            </PanelRow>
                            <PanelRow>
                                <label>{__i18n.min_pax}</label>
                                <TextControl
                                    value={price.min_pax ? price.min_pax : ''}
                                    type="number"
                                    min="0"
                                    autoComplete="off"
                                    onChange={(min_pax) => {
                                        let priceData = price;
                                        priceData.min_pax = min_pax;
                                        updateTripPricing(priceData, priceIndex)
                                    }}
                                />
                            </PanelRow>
                            <PanelRow>
                                <label>{__i18n.max_pax}</label>
                                <TextControl
                                    value={price.max_pax ? price.max_pax : ''}
                                    type="number"
                                    min="0"
                                    autoComplete="off"
                                    onChange={(max_pax) => {
                                        let priceData = price;
                                        priceData.max_pax = max_pax;
                                        updateTripPricing(priceData, priceIndex)
                                    }}
                                />
                            </PanelRow>
                            <hr />
                            <PanelRow className="wp-travel-has-child-panel">
                                <label>{__i18n.price_category}</label>
                            </PanelRow>
                            <PanelRow className="wp-travel-has-child-panel">
                                <div className="wp-travel-panel-wrap">
                                    <WPTravelTripPricingCategories priceIndex={priceIndex} />
                                </div>
                            </PanelRow>
                            {has_extras ? 
                                <PanelRow>
                                    <label>{__i18n.bookings.trip_extras}</label>
                                    <div className="wp-travel-field-value">
                                        <div className="wp-travel-select-wrapper">
                                            <Select options={tripExtrasData} onChange={(val)=>{
                                                let priceData = price;
                                                priceData.trip_extras = null!== val?val:[];

                                                updateTripPricing(priceData, priceIndex)
                                            }} isMulti
                                            noOptionsMessage={()=>__i18n.empty_results.extras }
                                            onInputChange={(val)=>{
                                                if ( '' !== val ) {
                                                    apiFetch( { url: `${ajaxurl}?action=wp_travel_search_trip_extras&keyword=${val}&_nonce=${_wp_travel._nonce}` } ).then( res => {
                                                        
                                                        if( res.success && "WP_TRAVEL_TRIP_EXTRAS" === res.data.code){
                                                            var output = res.data.trip_extras.map( s => ({...s,value:s.id, label:s.title}) );
                                                            setState({
                                                                tripExtrasData:output
                                                            })
                                                        } else {
                                                            setState({
                                                                tripExtrasData:[]
                                                            })
                                                        }
                                                    } );
                                                    
                                                }
                                            }}

                                            defaultValue={tripExtrasDefaultData}
                                            />
                                        </div>
                                        {applyFilters('wp_travel_trip_extras_notice', [])}
                                    </div>
                                </PanelRow>
                            
                            : 
                                <>
                                <PanelRow>
                                    <label>{__i18n.bookings.trip_extras}</label>
                                </PanelRow>
                                <PanelRow>
                                    <Notice isDismissible={false} actions={[{
                                        'label': __i18n.add_extras,
                                        onClick: () => {
                                            let url = _wp_travel.admin_url + 'post-new.php?post_type=tour-extras'
                                            window.location.href = url
                                        },
                                        noDefaultClasses: true,
                                        className: 'is-link'
                                    }]}>{ __i18n.empty_results.add_extras }</Notice>
                                </PanelRow>
                                </>
                            }
                            {applyFilters('wp_travel_after_pricings_fields', [], priceIndex, allData)}
                            <hr />
                            <PanelRow className="wp-travel-action-section has-right-padding">
                                <span></span><Button isDefault onClick={() => {
                                    if (!confirm(__i18n.alert.remove_price )) {
                                        return false;
                                    }
                                    
                                    let pricesData = [];
                                    pricesData = tripPrices.filter((pricing, newPriceIndex) => {
                                        return newPriceIndex != priceIndex;
                                    });

                                    if ( 'undefined' !== typeof tripPrices[priceIndex] && false === tripPrices[priceIndex].id ) {
                                        updateTripPrices(pricesData);
                                    } else if ( 'undefined' !== typeof tripPrices[priceIndex] && false !== tripPrices[priceIndex].id ) {
                                        apiFetch( { url: `${ajaxurl}?action=wp_travel_remove_trip_pricing&pricing_id=${tripPrices[priceIndex].id}&_nonce=${_wp_travel._nonce}` } ).then( res => {                                           
                                            if( res.success && "WP_TRAVEL_REMOVED_TRIP_PRICING" === res.data.code){
                                                updateTripPrices(pricesData);
                                            }
                                        } );
                                    }
                                }} className="wp-traval-button-danger">{__i18n.remove_price}</Button>
                            </PanelRow>
                        </PanelBody>
                    })}
                    </ReactSortable>
                    </ div>
                {tripPrices.length > 1 && <PanelRow className="wp-travel-action-section"><span></span><Button isDefault onClick={() => addTripPrice()}>{__i18n.add_price }</Button></PanelRow>}</>:<>
                <Notice isDismissible={false} actions={[{
                    'label': __i18n.add_price,
                    onClick:()=>{
                        addTripPrice()
                    },
                    noDefaultClasses:true,
                    className:'is-link'
                }]}>{__i18n.empty_results.pricing}</Notice></>
                }
            </>}
            {applyFilters('wp_travel_after_pricings_options', [], allData )}

            { settings.partial_payment == 'yes'  && 'custom-booking' !== pricing_type &&
                <>
                    <PanelRow>
                    </PanelRow>
                    <h4>{ __i18n.minimum_payout }</h4>
                    <PanelRow>
                        <label>{__i18n.use_global_payout}</label>
                        <ToggleControl
                            checked={ minimum_partial_payout_use_global }
                            help={__i18n.help_text.use_global_payout}
                            onChange={ () => {
                                updateTripData({
                                    ...allData,
                                    minimum_partial_payout_use_global:! minimum_partial_payout_use_global
                                })
                            } }
                        />
                    </PanelRow>

                    { ( ! minimum_partial_payout_use_global && settings.minimum_partial_payout.length > 0 ) &&
                        <>
                            {payout_percentages.map( ( percent, i ) => {
                                let payout_percent = 'undefined' != typeof minimum_partial_payout_percent && 'undefined' != typeof minimum_partial_payout_percent[i] ? minimum_partial_payout_percent[i] : percent
                                if ( payout_percent ) {
                                    payout_percent = parseInt(payout_percent)
                                }
                                let payout_label = __i18n.custom_min_payout
                                if ( payout_percentages.length > 1 ) {
                                    payout_label = __( `${__i18n.global_partial_payout} ${i+1} (%)`, 'wp-travel' )
                                }
                                totalPayout += payout_percent;
                                return <PanelRow>
                                    <label>{payout_label}</label>
                                    <TextControl
                                        value={ payout_percent  }
                                        type="number"
                                        min="0"
                                        help={__( `${__i18n.global_partial_payout} ${percent}%`, 'wp-travel' )}
                                        autoComplete="off"
                                        onChange={(value) => {
                                            let _minimum_partial_payout_percent = minimum_partial_payout_percent

                                            let newVal = value ? parseInt( value ) : 0;
                                            let newTotalPayout = parseInt( totalPayout ) - parseInt( _minimum_partial_payout_percent[i] ) + parseInt( newVal );

                                            if ( newTotalPayout > 100 ) {
                                                let exceed_val = newTotalPayout - 100;
                                                if ( exceed_val > 0 ) {
                                                    value = value - exceed_val;
                                                    if ( value < 0 ) {
                                                        value = 0;
                                                    }
                                                }
                                            }
                                            _minimum_partial_payout_percent[i] = parseInt( value )
                                            updateTripData({
                                                ...allData,
                                                minimum_partial_payout_percent: [..._minimum_partial_payout_percent]
                                            })
                                        }}
                                    />
                                </PanelRow>
                                
                            } ) }

                        { ( payout_percentages.length > 1 && totalPayout != 100 ) &&
                            <PanelRow>
                                <Notice status="error" isDismissible={false}>{__i18n.messages.total_payout}</Notice>
                            </PanelRow>
                        }
                        </>
                    }
                </>
            }

        </div>
    </ErrorBoundary>;
}

const WPTravelTripOptionsPriceDates = () => {
    return <TabPanel className="wp-travel-trip-edit-menu wp-travel-trip-edit-menu-horizontal wp-travel-trip-edit-menu-add-gap"
        activeClass="active-tab"
        onSelect={() => false}
        tabs={[
            {
                name: 'prices',
                title: __i18n.prices,
                className: 'tab-one',
            },
            {
                name: 'dates',
                title: __i18n.dates,
                className: 'tab-two',
            },
        ]}>
        {
            (tab) => 'prices' == tab.name ? <ErrorBoundary> <WPTravelTripOptionsPricings /></ErrorBoundary> : <ErrorBoundary><WPTravelTripDates /></ErrorBoundary>
        }
    </TabPanel>;
}

export default WPTravelTripOptionsPriceDates;