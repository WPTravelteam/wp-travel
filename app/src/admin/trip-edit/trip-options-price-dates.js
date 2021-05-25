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
                
                {/* <PanelRow>
                    <label>{__( 'Group Size(Pax)', 'wp-travel' )}</label>
                    <TextControl
                        help={__( 'It will be used as inventory size.', 'wp-travel' )}
                        value={ group_size }
                        type="number"
                        min="1"
                        autoComplete="off"
                        onChange={(gs) => {
                            updateTripData({
                                ...allData, // allData
                                group_size: gs
                            })
                        }}
                    />
                </PanelRow> */}
                {tripPrices.length > 0 ? <>
                
                    <Notice status="warning" isDismissible={false}>{__( 'Before making any changes in date, please make sure pricing is saved.', 'wp-travel')}</Notice>
                    <PanelRow className="wp-travel-action-section"><span></span><Button isDefault onClick={() => addTripPrice()}>{ __( '+ Add Price', 'wp-travel' ) }</Button></PanelRow>
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
                                title={ price.title ? `${price.title}` : __( 'Untitled Price', 'wp-travel' ) }
                                initialOpen={(tripPrices.length - 1 === priceIndex)}
                            >
                            <PanelRow>
                                <label>{__( 'Pricing Name', 'wp-travel' )}</label>
                                <TextControl
                                    placeholder={__( 'Pricing Name', 'wp-travel' )}
                                    value={price.title}
                                    onChange={(title) => {
                                        let priceData = price;
                                        priceData.title = title;
                                        updateTripPricing(priceData, priceIndex)
                                    }}
                                />
                            </PanelRow>
                            <PanelRow>
                                <label>{__( 'Min Pax', 'wp-travel' )}</label>
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
                                <label>{__( 'Max Pax', 'wp-travel' )}</label>
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
                                <label>{__( 'Price Categories', 'wp-travel' )}</label>
                            </PanelRow>
                            <PanelRow className="wp-travel-has-child-panel">
                                <div className="wp-travel-panel-wrap">
                                    <WPTravelTripPricingCategories priceIndex={priceIndex} />
                                </div>
                            </PanelRow>
                            {has_extras ? 
                                <PanelRow>
                                    <label>{__( 'Trip Extras', 'wp-travel' )}</label>
                                    <div className="wp-travel-field-value">
                                        <div className="wp-travel-select-wrapper">
                                            <Select options={tripExtrasData} onChange={(val)=>{
                                                let priceData = price;
                                                priceData.trip_extras = null!== val?val:[];

                                                updateTripPricing(priceData, priceIndex)
                                            }} isMulti
                                            noOptionsMessage={()=>__( 'No result found for searched term.', 'wp-travel' ) }
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
                                    <label>{__( 'Trip Extras', 'wp-travel' )}</label>
                                </PanelRow>
                                <PanelRow>
                                    <Notice isDismissible={false} actions={[{
                                        'label': __( 'Add Extras', 'wp-travel' ),
                                        onClick: () => {
                                            let url = _wp_travel.admin_url + 'post-new.php?post_type=tour-extras'
                                            window.location.href = url
                                        },
                                        noDefaultClasses: true,
                                        className: 'is-link'
                                    }]}>{ __( 'Please add extras first', 'wp-travel' ) }</Notice>
                                </PanelRow>
                                </>
                            }
                            {applyFilters('wp_travel_after_pricings_fields', [], priceIndex, allData)}
                            <hr />
                            <PanelRow className="wp-travel-action-section has-right-padding">
                                <span></span><Button isDefault onClick={() => {
                                    if (!confirm(__( 'Are you sure to delete this price?', 'wp-travel' ) )) {
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
                                }} className="wp-traval-button-danger">{__( '- Remove Price', 'wp-travel' ) }</Button>
                            </PanelRow>
                        </PanelBody>
                    })}
                    </ReactSortable>
                    </ div>
                {tripPrices.length > 1 && <PanelRow className="wp-travel-action-section"><span></span><Button isDefault onClick={() => addTripPrice()}>{ __( '+ Add Price', 'wp-travel' ) }</Button></PanelRow>}</>:<>
                <Notice isDismissible={false} actions={[{
                    'label': __( 'Add Price', 'wp-travel' ),
                    onClick:()=>{
                        addTripPrice()
                    },
                    noDefaultClasses:true,
                    className:'is-link'
                }]}>{__( 'No Pricings found.', 'wp-travel')}</Notice></>
                }
            </>}
            { settings.partial_payment == 'yes' &&
                <>
                    <PanelRow>
                    </PanelRow>
                    <h4>{ __( 'Minimum Payout ', 'wp-travel' ) }</h4>
                    <PanelRow>
                        <label>{ __( 'Use Global Payout', 'wp-travel' ) }</label>
                        <ToggleControl
                            checked={ minimum_partial_payout_use_global }
                            help={__( 'Note: In case of multiple cart items checkout, global payout will be used.', 'wp-travel' )}
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
                                let payout_label = __( 'Custom Min. Payout (%)', 'wp-travel' )
                                if ( payout_percentages.length > 1 ) {
                                    payout_label = __( `Custom Partial Payout ${i+1} (%)`, 'wp-travel' )
                                }
                                totalPayout += payout_percent;
                                return <PanelRow>
                                    <label>{payout_label}</label>
                                    <TextControl
                                        value={ payout_percent  }
                                        type="number"
                                        min="0"
                                        help={__( `Global partial payout: ${percent}%`, 'wp-travel' )}
                                        autoComplete="off"
                                        onChange={(value) => {
                                            let _minimum_partial_payout_percent = minimum_partial_payout_percent

                                            if ( ( totalPayout +1 ) > 100 ) { // Added +1 because state is updating after this totalPayout check.
                                                let exceed_val = ( totalPayout + 1 ) - 100;
                                    
                                                if ( exceed_val > 0 ) {
                                                    value = value - exceed_val;
                                                }
                                            }
                                            _minimum_partial_payout_percent[i] = value
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
                                <Notice status="error" isDismissible={false}>{__( 'Error: Total payout percent is not equals to 100%. Please update the trip once else global partial percent will be used as default.', 'wp-travel')}</Notice>
                            </PanelRow>
                        }
                        </>
                    }
                </>
            }

            {applyFilters('wp_travel_after_pricings_options', [], allData )}
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
                title: 'Prices',
                className: 'tab-one',
            },
            {
                name: 'dates',
                title: 'Dates',
                className: 'tab-two',
            },
        ]}>
        {
            (tab) => 'prices' == tab.name ? <ErrorBoundary> <WPTravelTripOptionsPricings /></ErrorBoundary> : <ErrorBoundary><WPTravelTripDates /></ErrorBoundary>
        }
    </TabPanel>;
}

export default WPTravelTripOptionsPriceDates;