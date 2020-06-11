import { useState, useEffect } from '@wordpress/element';
import { TextControl, PanelBody, PanelRow, ToggleControl, Button, FormTokenField,SelectControl, Dropdown, RangeControl, DateTimePicker , Notice} from '@wordpress/components';
import { useSelect, dispatch } from '@wordpress/data';
import {format} from '@wordpress/date'
import apiFetch from '@wordpress/api-fetch';
import { sprintf, _n, __} from '@wordpress/i18n';
import { applyFilters } from '@wordpress/hooks';
import _ from 'lodash';

import TripDatesTimes from './dates-times';

const WPTravelTripDates = () => {
    
    const allData = useSelect((select) => {
        return select('WPTravel/TripEdit').getAllStore()
    }, []);
    const { updateTripData, updateRequestSending, setTripData, updateStateChange } = dispatch('WPTravel/TripEdit');

    const {is_fixed_departure, has_state_changes, is_multiple_dates, dates, trip_time, pricings, trip_duration } = allData;
    // console.log(trip_duration);
    

    let firstDate = dates.length>0?dates[0]:{
        start_date:format('Y-m-d', new Date() ),
        end_date:''
    };

    const [{stateHours, stateMinutes, enableTime}, setState] = useState({
        stateHours:0,
        stateMinutes:0,
        enableTime:false,
    });


    useEffect(()=>{
        // if(dates.length<1){
        //     let _firstDate = [{
        //         start_date:format('Y-m-d', new Date() ),
        //         end_date:''
        //     }];
        //     updateTripData({
        //         ...allData,
        //         dates:_firstDate
        //     })
        // }
        
        let _enableTime = 'undefined'!==typeof firstDate.trip_time && '' !== firstDate.trip_time?true:enableTime;
        setState((prevState)=>{
            return {...prevState, enableTime:_enableTime}
        })
        
    },[])

    let currentYear = format( 'Y', new Date() );
    let yearSuggestions = [];
    for (let index = 0; index < 10 ; index++) {
        yearSuggestions = [...yearSuggestions, `${parseInt( currentYear )+index}` ]
    }

    let selectedYears = 'undefined'!==typeof firstDate.years?firstDate.years.split(','): [];
    selectedYears = selectedYears.filter((year)=>{
        return yearSuggestions.includes(year);
    })


    let monthSuggestions = ["January","February","March","April","May","June","July",
    "August","September","October","November","December"];
    let _selectedMonths = 'undefined'!==typeof firstDate.months?firstDate.months.split(','): [];
    let selectedMonths = [];
    _selectedMonths.filter((month)=>{
        if ('undefined' !== typeof monthSuggestions[month-1] ) {
            selectedMonths = [...selectedMonths, monthSuggestions[month-1] ]
        }
    })

    let weekdaysSuggestions = ["Sunday","Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    let rruleWeekdays = ["SU","MO", "TU", "WE", "TH", "FR", "SA"];
    let selectedWeekdays = [];
    let _selectedWeekdays = 'undefined'!==typeof firstDate.days && 'every_days' !== firstDate.days?firstDate.days.split(','): [];
    _selectedWeekdays.filter((day)=>{
        let dIndex = _.indexOf(rruleWeekdays, day);
        if ('undefined' !== typeof weekdaysSuggestions[dIndex] ) {
            selectedWeekdays = [...selectedWeekdays, weekdaysSuggestions[dIndex] ]
        }
    })


    let dateDaysSuggestions = [];
    for (let index = 1; index < 33 ; index++) {
        dateDaysSuggestions = [...dateDaysSuggestions, `${index}` ]
    }

    let selectedDateDays = 'undefined'!==typeof firstDate.date_days && 'every_date_days' != firstDate.date_days?firstDate.date_days.split(','): [];
    selectedDateDays = _.without( selectedDateDays, "");
    selectedDateDays = selectedDateDays.filter((date_day)=>{
        return selectedDateDays.includes(date_day);
    })    

    let selectedTimes = 'undefined'!==typeof firstDate.trip_time && '' !== firstDate.trip_time?firstDate.trip_time.split(','): [];
    

    const updateDateTimes = (storeKey, data) => {
        let _allData = allData;
        _allData[storeKey] = [...data];
        updateTripData(_allData)
    }
    

    return <>
    <div className="wp-travel-ui wp-travel-ui-card wp-travel-ui-card-top-border">
        <h4>{ __( 'Date & Time', 'wp-travel' ) }</h4>
        <PanelRow>
            <label>{ __( 'Enable Fixed Departure', 'wp-travel' ) }</label>
            <ToggleControl
                checked={ is_fixed_departure }
                onChange={ () => {
                    updateTripData({
                        ...allData,
                        is_fixed_departure:!is_fixed_departure
                    })
                } }
            />
        </PanelRow>
        {applyFilters('wp_travel_before_dates_options', [])}
        {is_fixed_departure ?
            <TripDatesTimes dates={dates} storeKey="dates" onUpdate={updateDateTimes} pricings={pricings} /> :
            <PanelRow>
                <label>{ __( 'Trip Duration', 'wp-travel' ) }</label>
                <div className="wp-travel-trip-duration">
                    <TextControl
                        value={trip_duration.days}
                        help={__( 'Day(s)', 'wp-travel' )}
                        onChange={(e) =>{
                            let _trip_duration = allData.trip_duration;
                            _trip_duration.days = e;
                            updateTripData({
                                ...allData,
                                trip_duration:{..._trip_duration}
                            })
                        } }
                    />
                    <TextControl
                        value={trip_duration.nights}
                        help={__( 'Night(s)', 'wp-travel' )}
                        onChange={(e) =>{
                            let _trip_duration = allData.trip_duration;
                            _trip_duration.nights = e;
                            updateTripData({
                                ...allData,
                                trip_duration:{..._trip_duration}
                            })
                        } }
                />
                </div>
            </PanelRow>
        }
        {applyFilters('wp_travel_after_dates_options', [])}
    </div>
    {applyFilters('wp_travel_itinerary_price_tab_table_last_row', '', allData )}
    {/* <ExcludedDates /> */}
    <hr/>
    {/* <PanelRow>
        <span>
            {has_state_changes&&<div>* Please save changes.</div>}
        </span>
        <Button isPrimary onClick={()=>{
            updateRequestSending(true);

            // if( !is_multiple_dates ){
            //     let _pricingIds = [];
            //     allData.pricings.map((_price)=>{
            //         _pricingIds = (false !== _price.id) ?[..._pricingIds, _price.id]:_pricingIds;
            //     });
            //     allData.dates[0]['pricing_ids'] = _pricingIds.join(',');
            // }

            if ( allData.is_fixed_departure ) {
                let _pricingIds = [];
                allData.pricings.map((_price)=>{
                    _pricingIds = (false !== _price.id) ?[..._pricingIds, _price.id]:_pricingIds;
                });
                if( allData.dates.length>0 ) {
                    allData.dates.map((_dates, _datesIndex)=>{
                        allData.dates[_datesIndex]['pricing_ids'] = _pricingIds.join(',');
                        if ( !allData.dates[_datesIndex].is_recurring ) {
                            allData.dates[_datesIndex] = {...allData.dates[_datesIndex],...{
                                years:'',
                                months:'',
                                weeks:'',
                                days:'',
                                date_days:''
                            }}
                        }
                    })
                }
            } else {
                allData.dates = [];
            }

            if( !allData.enable_excluded_dates_times){
                allData.excluded_dates_times = [];
            }
            
            
            apiFetch( { url: `${ajaxurl}?action=wp_travel_update_trip&trip_id=${_wp_travel.postID}`, data:allData, method:'post' } ).then( res => {
                updateRequestSending(false);
                
                if( res.success && "WP_TRAVEL_UPDATED_TRIP" === res.data.code){
                    setTripData(res.data.trip);
                    updateStateChange(false)
                }
            } );
            
        }}
        disabled={!has_state_changes}
        >Save Changes</Button>
    </PanelRow> */}
    </>
}

export default WPTravelTripDates;