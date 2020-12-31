import { useState, useEffect } from '@wordpress/element';
import { TextControl, PanelRow, PanelBody, Button, TabPanel, Notice, FormTokenField, TextareaControl, Dropdown, DateTimePicker, RangeControl } from '@wordpress/components';
import { applyFilters, addFilter } from '@wordpress/hooks';
import { useSelect, dispatch } from '@wordpress/data';
// imsport apiFetch from '@wordpress/api-fetch';
import { sprintf, _n, __ } from '@wordpress/i18n';

import { ReactSortable } from 'react-sortablejs';
import {alignJustify } from '@wordpress/icons';

import WPEditor from '../fields/WPEditor';
import ErrorBoundary from '../../ErrorBoundry/ErrorBoundry';

const WPTravelTripOptionsItineraryContent = () => {
    const allData = useSelect((select) => {
        return select('WPTravel/TripEdit').getAllStore()
    }, []);
    const { updateTripData, addNewItinerary } = dispatch('WPTravel/TripEdit');
    const { trip_outline, itineraries } = allData;
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
            label: __('Day X', 'wp-travel'),
            title: __('Your Plan', 'wp-travel'),
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
        <div className="wp-travel-trip-itinerary">
            <ErrorBoundary>
            {applyFilters('wp_travel_before_itinerary_content', '', allData)}
            <PanelRow>
                <label htmlFor="wp-travel-trip-outline">{__('Trip Outline')}</label>
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
            <hr/>
            <div className="wp-travel-itinerary-title">
                <h3 className="wp-travel-tab-content-title">{__('Itinerary')}</h3>
                {typeof itineraries != 'undefined' && itineraries && Object.keys(itineraries).length > 0 && <PanelRow className="wp-travel-action-section"><span></span><Button isDefault onClick={() => addItinerary()}>{__('+ Add Itinerary')}</Button></PanelRow> }
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
                                    title={`${itineraries[itineraryId].label ? itineraries[itineraryId].label : __('Day X', 'wp-travel')}, ${itineraries[itineraryId].title ? itineraries[itineraryId].title : __('Your Plan', 'wp-travel')} `}
                                    initialOpen={false} >

                                    <PanelRow>
                                        <label>{__('Itinerary Label', 'wp-travel')}</label>
                                        <TextControl
                                            value={itineraries[itineraryId].label ? itineraries[itineraryId].label : ''}
                                            placeholder={__('Day X', 'wp-travel')}
                                            onChange={
                                                (e) => updateTripItinerary('label', e, itineraryId)
                                            }
                                        />
                                    </PanelRow>
                                    <PanelRow>
                                        <label>{__('Itinerary Title', 'wp-travel')}</label>
                                        <TextControl
                                            value={itineraries[itineraryId].title ? itineraries[itineraryId].title : ''}
                                            placeholder={__('Your Plan', 'wp-travel')}
                                            onChange={
                                                (e) => updateTripItinerary('title', e, itineraryId)
                                            }
                                        />
                                    </PanelRow>
                                    <PanelRow>
                                        <label>{__('Itinerary Date', 'wp-travel')}</label>
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
                                        <label>{__('Itinerary Time', 'wp-travel')}</label>
                                        
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
                                                    <label>{__( 'Hours', 'wp-travel' )}</label>
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
                                                    <label>{__( 'Minute', 'wp-travel' )}</label>
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
                                                    }} isDefault>{__( '+ Add', 'wp-travel' )}</Button>
                
                                                </div>
                                                </div>
                                            ) }
                                            
                                        />
                                    </PanelRow>
                                    {applyFilters('wp_travel_itinerary_list_before_description', '', itineraryId)}
                                    <PanelRow>
                                        <label>{__('Description', 'wp-travel')}</label>
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
                                            if (!confirm(__( 'Are you sure to delete this itinerary?', 'wp-travel' ) )) {
                                                return false;
                                            }
                                            let itineraryData = [];
                                            itineraryData = itineraries.filter((itinerary, newItineraryId) => {
                                                return newItineraryId != itineraryId;
                                            });
                                            updateItineraries(itineraryData);
                                        }} className="wp-traval-button-danger wp-travel-ui">{__('- Remove Itinerary', 'wp-travel')}</Button>
                                    </PanelRow>

                                </PanelBody>
                            })
                        }
                    </ReactSortable>
                    {typeof itineraries != 'undefined' && Object.keys(itineraries).length > 1 && <PanelRow className="wp-travel-action-section"><span></span><Button isDefault onClick={() => addItinerary()}>{__('+ Add Itinerary')}</Button></PanelRow> }

                </div>
                : <><Notice isDismissible={false} actions={[{
                    'label': __( 'Add Itinerary', 'wp-travel' ),
                    onClick:()=>{
                        addItinerary()
                    },
                    noDefaultClasses:true,
                    className:'is-link'
                }]}>{__( 'No Itineraries found.', 'wp-travel')}</Notice></>
            }
            </ErrorBoundary>
        </div>
    </>;
}

addFilter('wp_travel_before_itinerary_content', 'wp_travel', (content, allData) => {
    const { trip_code } = allData;

    content = [
        <PanelRow>
            <label>{__('Trip code', 'wp-travel')}</label>
            <div className="wp-travel-field-value">
                <TextControl
                    value={trip_code}
                    onChange={() => false}
                    disabled={true}
                    name="" />
                    <p class="description">
                        {__( 'Need Custom Trip Code? Check', 'wp-travel' )}<a href="https://wptravel.io/downloads/wp-travel-utilities/" target="_blank" class="wp-travel-upsell-badge">{__( 'Pro Utilities Modules', 'wp-travel') }</a>
                    </p>
            </div>
        </PanelRow>,
        ...content
    ]
    return content
}, 9);

const WPTravelTripOptionsItinerary = () => {
    return <div className="wp-travel-ui wp-travel-ui-card wp-travel-ui-card-no-border"><WPTravelTripOptionsItineraryContent /></div>;
    return <TabPanel className="wp-travel-trip-edit-menu wp-travel-trip-edit-menu-horizontal wp-travel-trip-edit-menu-add-gap"
        activeClass="active-tab"
        onSelect={() => false}
        tabs={[
            {
                name: 'itinerary',
                title: __('Itinerary', 'wp-travel'),
                className: 'tab-itinerary',
            },

        ]}>
        {
            (tab) => <WPTravelTripOptionsItineraryContent />
        }
    </TabPanel>;
}

export default WPTravelTripOptionsItinerary;