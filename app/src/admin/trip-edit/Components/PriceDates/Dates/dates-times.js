import { useState, useEffect } from '@wordpress/element';
import { TextControl, PanelBody, PanelRow, ToggleControl, Button, FormTokenField, SelectControl, Dropdown, RangeControl, DateTimePicker, Notice, CheckboxControl } from '@wordpress/components';
import { sprintf, _n, __ } from '@wordpress/i18n';
import { useSelect, dispatch } from '@wordpress/data';
import { applyFilters, addFilter } from '@wordpress/hooks';
import apiFetch from '@wordpress/api-fetch';
import ErrorBoundary from '../../../../../ErrorBoundry/ErrorBoundry';

const __i18n = {
	..._wp_travel_admin.strings
}
const TripDatesTimes = ({ dates, storeKey, onUpdate, pricings }) => {

    const updateDatesTimes = (data, _dateIndex) => {

        let _allDates = dates;
        _allDates[_dateIndex] = { ..._allDates[_dateIndex], ...data }
        onUpdate(storeKey, _allDates);
    }

    const removeDatesTimes = (allDates) => {
        onUpdate(storeKey, allDates);
    }

    let currentYear = moment(new Date()).format('YYYY');
    let yearSuggestions = [];
    for (let index = 0; index < 10; index++) {
        yearSuggestions = [...yearSuggestions, `${parseInt(currentYear) + index}`]
    }

    let monthSuggestions = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    let weekdaysSuggestions = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    let rruleWeekdays = ["SU", "MO", "TU", "WE", "TH", "FR", "SA"];
    let dateDaysSuggestions = [];
    for (let index = 1; index < 33; index++) {
        dateDaysSuggestions = [...dateDaysSuggestions, `${index}`]
    }
    const { addNewTripDate, tripPricingIds } = dispatch('WPTravel/TripEdit');
    const addTripDate = () => {
        addNewTripDate({
            id: false,
            title: '',
            years: 'every_year',
            months: 'every_month',
            days: '',
            date_days: '',
            start_date: null,
            end_date: null,
            is_recurring: false,
            trip_time: '',
            recurring_weekdays_type: '',
        })
    }

    return <ErrorBoundary>
        {dates.length > 0 && <>
            <PanelRow className="wp-travel-action-section"><span></span><Button isDefault onClick={() => addTripDate()}>{__i18n.add_date}</Button></PanelRow>
            {dates.map((_date, _dateIndex) => {

                let selectedYears = 'undefined' !== typeof _date.years ? _date.years.split(',') : [];
                selectedYears = selectedYears.filter((year) => {
                    return yearSuggestions.includes(year);
                })

                let _selectedMonths = 'undefined' !== typeof _date.months ? _date.months.split(',') : [];
                let selectedMonths = [];
                _selectedMonths.filter((month) => {
                    if ('undefined' !== typeof monthSuggestions[month - 1]) {
                        selectedMonths = [...selectedMonths, monthSuggestions[month - 1]]
                    }
                })

                let pricingSuggestions = []
                if (pricings.length > 0) {
                    pricingSuggestions = pricings.map(pricing => {
                        return { id: pricing.id, title: pricing.title }
                    })
                }
                let _selectedPricingIds = ( 'undefined' !== typeof _date.pricing_ids && _date.pricing_ids ) ? _date.pricing_ids.split(',') : [];
                let selectedPricingNames = [];
                _selectedPricingIds.filter((selectedPricingID) => {

                    let selectedPricingName = pricingSuggestions.filter(pricingSuggestion => {
                        return pricingSuggestion.id == selectedPricingID
                    }).map(suggestion => { return suggestion.title })

                    if (selectedPricingName[0]) {
                        selectedPricingNames = [...selectedPricingNames, selectedPricingName[0]]
                    }
                })

                let selectedWeekdays = [];
                let _selectedWeekdays = 'undefined' !== typeof _date.days && 'every_days' !== _date.days ? _date.days.split(',') : [];
                _selectedWeekdays.filter((day) => {
                    let dIndex = _.indexOf(rruleWeekdays, day);
                    if ('undefined' !== typeof weekdaysSuggestions[dIndex]) {
                        selectedWeekdays = [...selectedWeekdays, weekdaysSuggestions[dIndex]]
                    }
                })

                let selectedDateDays = 'undefined' !== typeof _date.date_days && 'every_date_days' != _date.date_days ? _date.date_days.split(',') : [];
                selectedDateDays = _.without(selectedDateDays, "");
                selectedDateDays = selectedDateDays.filter((date_day) => {
                    return selectedDateDays.includes(date_day);
                })

                let selectedTimes = 'undefined' !== typeof _date.trip_time && '' !== _date.trip_time ? _date.trip_time.split(',') : [];
                
                return <PanelBody title={_date.title || `${__i18n.fixed_departure} ${_dateIndex + 1}`} className="wp-travel-panelbody-add-top-gap" key={`${storeKey}-date-times-${_dateIndex}`}>
                    <PanelRow>
                        <label>{__i18n.date_label}</label>
                        <TextControl value={_date.title} placeholder={_date.title || `${__i18n.fixed_departure} ${_dateIndex + 1}`} onChange={value => updateDatesTimes({ title: value }, _dateIndex)} />
                    </PanelRow>
                    <PanelRow>
                        <label>{__i18n.select_pricing}</label>
                        <div className="text-right">
                            <CheckboxControl
                                label={__i18n.select_all}
                                checked={selectedPricingNames.length == pricingSuggestions.length ? true:false}
                                onChange={ (e) => {
                                        let filteredPricingIds = []
                                        if ( e ) {
                                            filteredPricingIds = pricingSuggestions.filter( (p) => {return false !== p.id } ).map( pricing => {
                                                    return pricing.id
                                            } )
                                        }
                                        updateDatesTimes({ pricing_ids: filteredPricingIds.join(',') }, _dateIndex);
                                    }
                                }
                            />
                            <FormTokenField
                                label=""
                                help=""
                                value={selectedPricingNames}
                                suggestions={pricingSuggestions.map(pricingSuggestion => { return pricingSuggestion.title })}
                                onChange={
                                    tokens => {
                                        let filteredPricingIds = [];
                                        tokens.map((title) => {
                                            let selectedPricingID = pricingSuggestions.filter(pricingSuggestion => {
                                                return pricingSuggestion.title == title
                                            }).map(suggestion => { return suggestion.id })
                                            if (selectedPricingID[0]) {
                                                filteredPricingIds = [...filteredPricingIds, selectedPricingID[0]]
                                            }
                                        })
                                        updateDatesTimes({ pricing_ids: filteredPricingIds.join(',') }, _dateIndex);
                                    }
                                }
                                // placeholder={__('Pricing Options', 'wp-travel')}
                            />
                            <p className="components-form-token-field__help">{__i18n.help_text.date_pricing}</p>
                        </div>

                    </PanelRow>
                    <PanelRow>
                        <label>{__i18n.start_date}</label>
                        <Dropdown
                            className="my-container-class-name"
                            contentClassName="my-popover-content-classname"
                            position="bottom right"
                            renderToggle={({ isOpen, onToggle }) => {
                                var startDate = moment(_date.start_date);
                                return <TextControl value={startDate.isValid() ? _date.start_date : ''} onFocus={onToggle} aria-expanded={isOpen} onChange={() => false} autoComplete="off" />
                            }}
                            renderContent={() => {
                                {
                                    let _start_date = moment(_date.start_date)
                                    _start_date = _start_date.isValid() ? _start_date.toDate() : new Date();

                                    return (
                                        <div className="wp-travel-dropdown-content-wrap wp-travel-datetimepicker wp-travel-datetimepicker-hide-time">
                                            <DateTimePicker
                                                currentDate={_start_date}
                                                minDate={new Date()}
                                                isInvalidDate={ (date) =>{
                                                    if (moment(date).isSame(_start_date)) {
                                                        return true;
                                                    }
                                                    if (!moment(date).isAfter(new Date())) {
                                                        return true;
                                                    }
                                                    return false;
                                                } }
                                                onChange={(date) => {
                                                    // if (moment(date).isSame(_start_date)) {
                                                    //     return false;
                                                    // }
                                                    // if (!moment(date).isAfter(new Date())) {
                                                    //     return false;
                                                    // }
                                                    updateDatesTimes({ start_date: moment(date).format('YYYY-MM-DD', date) }, _dateIndex);

                                                }}
                                            />
                                        </div>
                                    )
                                }
                            }}
                        />
                    </PanelRow>

                    <PanelRow>
                        <label>{__i18n.end_date}</label>
                        <div className="wp-travel-form-input-with-clear">
                            <Dropdown
                                className="my-container-class-name"
                                contentClassName="my-popover-content-classname"
                                position="bottom right"
                                renderToggle={({ isOpen, onToggle }) => {
                                    var endDate = moment(_date.end_date);
                                    return <TextControl value={endDate.isValid() ? _date.end_date : ''} onFocus={onToggle} aria-expanded={isOpen} onChange={() => false} autoComplete="off" />
                                }}
                                renderContent={() => {
                                    let _end_date = moment(_date.end_date)
                                    _end_date = _end_date.isValid() ? _end_date.toDate() : '';

                                    return (
                                        <div className="wp-travel-dropdown-content-wrap wp-travel-datetimepicker wp-travel-datetimepicker-hide-time">
                                            <DateTimePicker
                                                currentDate={_end_date}
                                                isInvalidDate={ (date) =>{
                                                    if (!moment(date).isAfter(new Date())) {
                                                        return true;
                                                    }
                                                    if (!moment(date).isAfter(new Date( _date.start_date ))) {
                                                        return true;
                                                    }
                                                    return false;
                                                } }
                                                onChange={(date) => {

                                                    // if (!moment(date).isAfter(new Date())) {
                                                    //     return false;
                                                    // }
                                                    // if (!moment(date).isAfter(new Date( _date.start_date ))) {
                                                    //     return false;
                                                    // }

                                                    updateDatesTimes({ end_date: moment(date).format('YYYY-MM-DD', date) }, _dateIndex);

                                                }}
                                            />
                                        </div>
                                    )
                                }}
                            />
                            <button className="wp-travel-form-input-clear-btn" type="button" onClick={() => {
                                let _allDates = dates;
                                _allDates[_dateIndex] = { ..._allDates[_dateIndex], end_date: '' }
                                onUpdate(storeKey, _allDates);
                            }}>
                                <i className="fas fa-times-circle"></i>
                                </button>
                        </div>
                    </PanelRow>
                    {applyFilters('wp_travel_after_end_date', '', dates, _dateIndex, _date, onUpdate, storeKey)}

                    <hr />
                    <PanelRow className="wp-travel-action-section has-right-padding">
                        <span></span><Button isDefault onClick={() => {
                            if (!confirm(__i18n.alert.remove_date)) {
                                return false;
                            }

                            let datesData = [];
                            datesData = dates.filter((date, newDateIndex) => {
                                return newDateIndex != _dateIndex;
                            });

                            if ('undefined' !== typeof dates[_dateIndex] && false === dates[_dateIndex].id) {
                                removeDatesTimes(datesData); // only remove from state.
                            } else if ('undefined' !== typeof dates[_dateIndex] && false !== dates[_dateIndex].id) { // delete from table and then remove from state.
                                apiFetch({ url: `${ajaxurl}?action=wp_travel_remove_trip_date&date_id=${dates[_dateIndex].id}&_nonce=${_wp_travel._nonce}` }).then(res => {
                                    if (res.success && "WP_TRAVEL_REMOVED_TRIP_DATE" === res.data.code) {
                                        removeDatesTimes(datesData);
                                    }
                                });
                            }

                        }} className="wp-traval-button-danger">{__i18n.remove_date}</Button>
                    </PanelRow>

                </PanelBody>
            })}
            {dates.length > 1 && <PanelRow className="wp-travel-action-section"><span></span><Button isDefault onClick={() => addTripDate()}>{__i18n.add_date}</Button></PanelRow>}
        </>}
        {dates.length < 1 && 
            <Notice isDismissible={false} actions={[{
                'label': __i18n.add_date,
                onClick: () => {
                    addTripDate()
                },
                noDefaultClasses: true,
                className: 'is-link'
            }]}>{__i18n.empty_results.dates}</Notice>
        }
    </ErrorBoundary>
}

export default TripDatesTimes;