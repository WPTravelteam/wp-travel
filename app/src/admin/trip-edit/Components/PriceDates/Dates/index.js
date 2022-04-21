import { useState, useEffect } from '@wordpress/element';
import { TextControl, PanelBody, PanelRow, ToggleControl, Button, FormTokenField,SelectControl, Dropdown, RangeControl, DateTimePicker , Notice} from '@wordpress/components';
import { useSelect, dispatch } from '@wordpress/data';
import {format} from '@wordpress/date'
import apiFetch from '@wordpress/api-fetch';
import { sprintf, _n, __} from '@wordpress/i18n';
import { applyFilters, addFilter } from '@wordpress/hooks';
import _ from 'lodash';

import TripDatesTimes from './dates-times';


import ErrorBoundary from '../../../../../ErrorBoundry/ErrorBoundry';

const __i18n = {
	..._wp_travel_admin.strings
}
const Dates = ( {allData} ) => {
    const { updateTripData, updateRequestSending, setTripData, updateStateChange } = dispatch('WPTravel/TripEdit');

    const {is_fixed_departure, has_state_changes, is_multiple_dates, dates, trip_time, pricings, trip_duration } = allData;

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
        updateTripData({
            ...allData,
            [storeKey]:[...data]
        })
    }
    

    return <ErrorBoundary key="1">
    <div className="wp-travel-ui wp-travel-ui-card wp-travel-ui-card-top-border">
        <h4>{ __i18n.date_time }</h4>
        <PanelRow>
            <label>{ __i18n.enable_fixed_departure }</label>
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
            <>
                <PanelRow>
                    <label>{ __i18n.trip_duration }</label>
                    <div className="wp-travel-trip-duration">
                        <TextControl
                            value={trip_duration.days}
                            help={__i18n.days}
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
                            help={__i18n.nights}
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
                {/* <Notice status="warning" isDismissible={false}>{__( 'Inventory option will only work in fixed departure date.', 'wp-travel')}</Notice> */}

            </>
        }
        {applyFilters('wp_travel_after_dates_options', [], allData)}
    </div>
    {applyFilters('wp_travel_itinerary_price_tab_table_last_row', '', allData )}
    {/* <ExcludedDates /> */}
    <hr/>
    </ErrorBoundary>
}

// Callbacks.
const DatesCB = ( content, allData ) => {
    return [ ...content, <Dates allData={allData} /> ];
}

// Hooks.
addFilter( 'wptravel_trip_edit_sub_tab_content_dates', 'WPTravel/TripEdit/PriceDates/Dates', DatesCB, 10 );