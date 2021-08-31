import { useState } from '@wordpress/element';
import { TextControl, PanelRow, PanelBody, Button, Notice, TextareaControl, Dropdown, DateTimePicker, RangeControl } from '@wordpress/components';
import { applyFilters, addFilter } from '@wordpress/hooks';
import { dispatch } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';

import { ReactSortable } from 'react-sortablejs';
import {alignJustify } from '@wordpress/icons';

import WPEditor from '../fields/WPEditor';
const __i18n = {
	..._wp_travel_admin.strings
}

// @todo Need to remove this in future.
const WPTravelTripOptionsItinerary = () => {
    return <></>;
}

export default WPTravelTripOptionsItinerary;

// Single Components for hook callbacks.
const TripCode = ({allData}) => {
    const { trip_code } = allData;
    return <>
        <PanelRow>
            <label>{__i18n.trip_code}</label>
            <div className="wp-travel-field-value">
                <TextControl
                    value={trip_code}
                    onChange={() => false}
                    disabled={true}
                    name="" />
                    <p class="description">
                        {__i18n.notices.trip_code_option.description}<a href="https://wptravel.io/downloads/wp-travel-utilities/" target="_blank" class="wp-travel-upsell-badge">{__i18n.notice_button_text.get_pro}</a>
                    </p>
            </div>
        </PanelRow>
    </>;
}

const TripOutline = ({allData}) => {
    const { trip_outline } = allData;
    const { updateTripData } = dispatch('WPTravel/TripEdit')
    return <>
        <PanelRow>
            <label htmlFor="wp-travel-trip-outline">{__i18n.trip_outline}</label>
        </PanelRow>
        <PanelRow className="wp-travel-editor">
            {'undefined' !== typeof trip_outline && <WPEditor id="wp-travel-trip-outline" value={trip_outline}
            onContentChange={(trip_outline) => {
                updateTripData({
                    ...allData,
                    trip_outline: trip_outline
                })
            }} name="trip_outline" />}
        </PanelRow>
    </>;
}

const Itinerary = ({allData}) => {
    const { itineraries } = allData;
    const { updateTripData, addNewItinerary } = dispatch('WPTravel/TripEdit');
    const updateTripItinerary = (key, value, _itineraryId) => { // Update on change itineraries.

        let _allItineraries = itineraries;
        _allItineraries[_itineraryId][key] = value

        updateTripData({
            ...allData,
            itineraries: [..._allItineraries]
        })
    }

    const [{stateHours, stateMinutes},setState] = useState({
        stateHours:0,
        stateMinutes:0
    });

    const addItinerary = () => {
        addNewItinerary({
            label: __i18n.day_x,
            title: __i18n.your_plan,
            date: null,
            time: null,
            desc: '',
        })
    }

    const updateItineraries = (allItineraries) => { // Remove Itineraries
        updateTripData({
            ...allData,
            itineraries: [...allItineraries]
        })
    }

    // Final Store Dispatcher.
    const sortItineraries = (sortedItineraries) => {
        updateTripData({
            ...allData, // allData
            itineraries: sortedItineraries
        })
    }
    return <>
        <div className="wp-travel-itinerary-title">
                <h3 className="wp-travel-tab-content-title">{__i18n.itinerary}</h3>
                {typeof itineraries != 'undefined' && itineraries && Object.keys(itineraries).length > 0 && <PanelRow className="wp-travel-action-section"><span></span><Button isDefault onClick={() => addItinerary()}>{__i18n.add_itinerary}</Button></PanelRow> }
            </div>
            {typeof itineraries != 'undefined' && itineraries && Object.keys(itineraries).length > 0 ?
                <div className="wp-travel-sortable-component">
                    <ReactSortable
                        list={itineraries}
                        setList={sortedItineraries => sortItineraries(sortedItineraries)}
                        handle=".wp-travel-trip-itinerary .components-panel__icon"
                    >
                        {
                            Object.keys(itineraries).map(function (itineraryId) {
                                return <PanelBody
                                    icon= {alignJustify}
                                    title={`${itineraries[itineraryId].label ? itineraries[itineraryId].label : __i18n.day_x}, ${itineraries[itineraryId].title ? itineraries[itineraryId].title : __i18n.your_plan} `}
                                    initialOpen={false} >

                                    <PanelRow>
                                        <label>{__i18n.itinerary_label}</label>
                                        <TextControl
                                            value={itineraries[itineraryId].label ? itineraries[itineraryId].label : ''}
                                            placeholder={__i18n.day_x}
                                            onChange={
                                                (e) => updateTripItinerary('label', e, itineraryId)
                                            }
                                        />
                                    </PanelRow>
                                    <PanelRow>
                                        <label>{__i18n.itinerary_title}</label>
                                        <TextControl
                                            value={itineraries[itineraryId].title ? itineraries[itineraryId].title : ''}
                                            placeholder={__i18n.your_plan}
                                            onChange={
                                                (e) => updateTripItinerary('title', e, itineraryId)
                                            }
                                        />
                                    </PanelRow>
                                    <PanelRow>
                                        <label>{__i18n.itinery_date}</label>
                                        <Dropdown
                                            className="wp-travel-dropdown-container"
                                            contentClassName="wp-travel-dropdown-popup-content"
                                            position="bottom right"
                                            renderToggle={({ isOpen, onToggle }) => {
                                                var itineraryDate = moment(itineraries[itineraryId].date ? itineraries[itineraryId].date : null);
                                                return <TextControl value={itineraryDate.isValid() ? itineraries[itineraryId].date : ''} onFocus={onToggle} aria-expanded={isOpen} onChange={() => false} autoComplete="off" />
                                            }}
                                            renderContent={() => {
                                                {
                                                    let _itineraryDate = moment(itineraries[itineraryId].date ? itineraries[itineraryId].date : null);
                                                    _itineraryDate = _itineraryDate.isValid() ? _itineraryDate.toDate() : new Date();

                                                    return (
                                                        <div className="wp-travel-dropdown-content-wrap wp-travel-datetimepicker wp-travel-datetimepicker-hide-time">
                                                            <DateTimePicker
                                                                currentDate={_itineraryDate}
                                                                onChange={(date) => {
                                                                    if (moment(date).isSame(_itineraryDate)) {
                                                                        return false;
                                                                    }
                                                                    updateTripItinerary('date', moment(date).format('YYYY-MM-DD', date), itineraryId)
                                                                    // updateDatesTimes( {start_date: moment(date).format( 'YYYY-MM-DD', date)}, _dateIndex );

                                                                }}
                                                            />
                                                        </div>
                                                    )
                                                }
                                            }}
                                        />
                                    </PanelRow>
                                    <PanelRow>
                                        <label>{__i18n.itinerary_time}</label>
                                        
                                        <Dropdown
                                            className="my-container-class-name"
                                            contentClassName="my-popover-content-classname"
                                            position="bottom right"
                                            renderToggle={({ isOpen, onToggle }) => {
                                                var itineraryTime = moment(itineraries[itineraryId].time ? itineraries[itineraryId].time : null);
                                                return <TextControl value={itineraryTime ? itineraries[itineraryId].time : ''} onFocus={onToggle} aria-expanded={isOpen} onChange={() => false} autoComplete="off" />
                                            }}
                                            renderContent={ ({ isOpen, onToggle } ) => (
                                                <div className="wp-travel-dropdown-content-wrap">
                                                <div>
                                                    <label>{__i18n.hours}</label>
                                                    <RangeControl
                                                        // label="Hours"
                                                        value={ stateHours }
                                                        onChange={ ( hours ) => {
                                                            setState( (prevState)=>{
                                                                return { ...prevState, stateHours:hours }
                                                            } )
                                                        }}
                                                        min={ 0 }
                                                        max={ 23 }
                                                    />
                                                </div>
                                                <div>
                                                    <label>{__i18n.minute}</label>
                                                    <RangeControl
                                                        // label="Hours"
                                                        value={ stateMinutes }
                                                        onChange={ ( minutes ) => {
                                                            setState( (prevState)=>{
                                                                return { ...prevState, stateMinutes:minutes }
                                                            } )
                                                        }}
                                                        min={ 0 }
                                                        max={ 59 }
                                                    />
                                                </div>
                                                <div className="wp-travel-add-time">
                                                    <Button onClick={()=>{
                                                        let _minutes = stateMinutes < 10 ? '0'+stateMinutes:stateMinutes;
                                                        let _hours = stateHours < 10 ? '0'+stateHours:stateHours;
                                                        let time = `${_hours}:${_minutes}`;

                                                        // @todo: Need to format time.
                                                        updateTripItinerary('time', time, itineraryId) 
                                                        // updateTripItinerary('time', moment.time(time).format('hh:mm a', time), itineraryId)
                                                        
                                                        onToggle()
                                                    }} isDefault>{__i18n.add}</Button>
                
                                                </div>
                                                </div>
                                            ) }
                                            
                                        />
                                    </PanelRow>
                                    {applyFilters('wp_travel_itinerary_list_before_description', '', itineraryId)}
                                    <PanelRow>
                                        <label>{__i18n.description}</label>
                                    </PanelRow>
                                    <PanelRow className="itinerary-description">
                                        <TextareaControl
                                            value={itineraries[itineraryId].desc ? itineraries[itineraryId].desc : null}
                                            onChange={
                                                (e) => updateTripItinerary('desc', e, itineraryId)
                                            }
                                        />
                                    </PanelRow>
                                    <hr />
                                    <PanelRow className="wp-travel-action-section has-right-padding">
                                        <span></span><Button isDefault onClick={() => {
                                            if (!confirm(__i18n.alert.remove_itinerary )) {
                                                return false;
                                            }
                                            let itineraryData = [];
                                            itineraryData = itineraries.filter((itinerary, newItineraryId) => {
                                                return newItineraryId != itineraryId;
                                            });
                                            updateItineraries(itineraryData);
                                        }} className="wp-traval-button-danger wp-travel-ui">{__i18n.remove_itinerary}</Button>
                                    </PanelRow>

                                </PanelBody>
                            })
                        }
                    </ReactSortable>
                    {typeof itineraries != 'undefined' && Object.keys(itineraries).length > 1 && <PanelRow className="wp-travel-action-section"><span></span><Button isDefault onClick={() => addItinerary()}>{__i18n.add_itinerary}</Button></PanelRow> }

                </div>
                : <><Notice isDismissible={false} actions={[{
                    'label':__i18n.add_itinerary,
                    onClick:()=>{
                        addItinerary()
                    },
                    noDefaultClasses:true,
                    className:'is-link'
                }]}>{__i18n.empty_results.itinerary}</Notice></>
            }
    </>;
}

// Callbacks.
const TripCodeCB = ( content, allData ) => {
    return [ ...content, <TripCode allData={allData} /> ];
}
const TripOutlineCB = ( content, allData ) => {
    return [ ...content, <TripOutline allData={allData} /> ];
}
const ItineraryCB = ( content, allData ) => {
    return [ ...content, <Itinerary allData={allData} /> ];
}

// Hooks.
addFilter( 'wptravel_trip_edit_tab_content_itinerary', 'WPTravel\TripEdit\TripCode', TripCodeCB, 10 );
addFilter( 'wptravel_trip_edit_tab_content_itinerary', 'WPTravel\TripEdit\TripOutline', TripOutlineCB, 20 );
addFilter( 'wptravel_trip_edit_tab_content_itinerary', 'WPTravel\TripEdit\Itinerary', ItineraryCB, 20 );
