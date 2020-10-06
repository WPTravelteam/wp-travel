import { useState, useEffect } from '@wordpress/element';
import { TextControl, Draggable, Panel, PanelRow, PanelBody, Button, TabPanel,Notice, CheckboxControl} from '@wordpress/components';
import { applyFilters, addFilter } from '@wordpress/hooks';
import { useSelect, dispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { sprintf, _n, __} from '@wordpress/i18n';
import { ReactSortable } from 'react-sortablejs';

import {alignJustify } from '@wordpress/icons';
import Select from 'react-select'

import ErrorBoundary from '../../ErrorBoundry/ErrorBoundry';

const WPTravelTripOptionsFactContent = () => {
    const allData = useSelect((select) => {
        return select('WPTravel/TripEdit').getAllStore()
    }, []);

    const settingsData = useSelect((select) => {
        return select('WPTravel/TripEdit').getSettings()
    }, []);
    const { updateTripData, addNewFact } = dispatch('WPTravel/TripEdit');
    const { trip_facts } = allData;
    const {wp_travel_trip_facts_enable, wp_travel_trip_facts_settings} = settingsData // All Facts options from settings.

    let factOptions = Object.keys(wp_travel_trip_facts_settings).length > 0 ? Object.keys(wp_travel_trip_facts_settings).map( ( index ) => {
        return {
            label: wp_travel_trip_facts_settings[index].name,
            value: wp_travel_trip_facts_settings[index].name
          }
    } ) : []
    // console.log( trip_facts )

    const updateFactType = ( key, data, _factIndex ) => {
      
        const { trip_facts } = allData;
        let _allTripFacts = trip_facts;
        _allTripFacts[_factIndex][key] = data[key]
        console.log(data)
        if ( 'type' === key ) { // reset label and value on fact type change
            _allTripFacts[_factIndex].label = data.name
            _allTripFacts[_factIndex].value = ''
            _allTripFacts[_factIndex].fact_id = data.id ? data.id : data.key
        }
        updateTripData({
            ...allData,
            trip_facts:[..._allTripFacts]
        })
    }
    const updateFactValue = ( key, value, _factId ) => { // Update single value as per key
      
        const { trip_facts } = allData;
    
        let _allTripFacts = trip_facts;
        _allTripFacts[_factId][key] = value
    //    console.log(_allTripFacts)
        updateTripData({
            ...allData,
            trip_facts:[..._allTripFacts]
        })
    }
    const fieldTypeOption = ( factId ) => {
        if ( 'number' != typeof factId  ){
            return []
        }
        let selectedFactSettings = Object.keys(wp_travel_trip_facts_settings).length > 0 ? wp_travel_trip_facts_settings[factId].options : [];
        return Object.keys(selectedFactSettings).map( (index) => {
            return {
                label: selectedFactSettings[index],
                value: 'option'+ (++index) //Added for making compatible with selection option value.
            }
        } )
    }

    const addFact = () => {
        addNewFact({
            label: __( '', 'wp-travel' ),
            value: '',
            fact_id:'',
            icon: '',
            type: 'text',
        })
    }

    const updateFacts = (allFacts) => { // Remove Facts
        updateTripData({
            ...allData,
            trip_facts:[...allFacts]
        })
    }
    // Final Store Dispatcher.
    const sortFacts = ( sortedFact ) => {
        updateTripData({
            ...allData, // allData
            trip_facts: sortedFact
        })
    }
    return( <ErrorBoundary>
        <div className="wp-travel-trip-fact">
            {typeof wp_travel_trip_facts_settings != 'undefined' && <>
                { Object.keys(wp_travel_trip_facts_settings).length > 0 ? 
                    <>
                        { typeof trip_facts != 'undefined' && trip_facts.length > 0 ? <>
                            <PanelRow className="wp-travel-action-section"><span></span><Button isDefault onClick={() => addFact()}>{__( '+ Add Fact' )}</Button></PanelRow>
                            <div className="wp-travel-sortable-component">
                            <ReactSortable
                                list={trip_facts}
                                setList={sortedFact => sortFacts( sortedFact)}
                                handle=".components-panel__icon"
                                >
                                {trip_facts.map((trip_fact, factIndex) => {
                                    let singleOrMultipleOptions = fieldTypeOption(trip_fact.fact_id);

                                    let singleSelected = singleOrMultipleOptions.filter( ( item ) => {
                                        return item.value == trip_fact.value
                                    } )
                                    singleSelected = typeof singleSelected != 'undefined' ? singleSelected[0] : {}

                                    let multipleSelected = trip_fact.value ? trip_fact.value : []

                                    return <PanelBody
                                        icon= {alignJustify}
                                        title={trip_fact.label ? trip_fact.label : __("Fact", 'wp-travel') }
                                        initialOpen={false}
                                        >
                                    
                                        <PanelRow>
                                            <label>{ __( 'Select Type', 'wp-travel' ) }</label>
                                            <div className="wp-travel-select-wrapper">
                                                <Select
                                                    options={factOptions}
                                                    // defaultOptions={[trip_fact.label]}
                                                    // value={trip_fact}
                                                    defaultValue={trip_fact}
                                                    onChange={(val)=>{
                                                        if ( '' !== val ) {
                                                            let selectedFactIndex = Object.keys(wp_travel_trip_facts_settings).filter( (index) => {
                                                                return wp_travel_trip_facts_settings[ index ].name == val.label
                                                            } )
                                                            let selectedFact = wp_travel_trip_facts_settings[selectedFactIndex]
                                                            updateFactType( 'type', selectedFact, factIndex )
                                                        }
                                                    }}

                                                />
                                            </div>
                                        </PanelRow>

                                        <PanelRow>
                                            <label>{ __( 'Value', 'wp-travel' ) }</label>
                                            {trip_fact.type == 'text' && 
                                                <TextControl
                                                    value={trip_fact.value ? trip_fact.value : '' }
                                                    onChange={ 
                                                        (e) => updateFactValue( 'value', e, factIndex) 
                                                    }
                                                />
                                            }
                                            {trip_fact.type == 'single' && 
                                                <div className="wp-travel-select-wrapper">
                                                    <Select
                                                    value={singleSelected}
                                                        options={singleOrMultipleOptions}
                                                        onChange={(val)=>{
                                                            if ( '' !== val ) {
                                                                updateFactType( 'value', val, factIndex )
                                                            }
                                                        }}

                                                    />
                                                </div>
                                            }
                                            {trip_fact.type == 'multiple' && 
                                                <div className="wp-travel-checkbox-wrapper">
                                                    {singleOrMultipleOptions.map( ( factOption, key ) => {
                                                        return <CheckboxControl
                                                                label={factOption.label}
                                                                checked={ multipleSelected.includes( factOption.value ) }
                                                                onChange={ (e) => {
                                                                
                                                                    if (e) {
                                                                        multipleSelected.push(factOption.value)
                                                                    } else {
                                                                        multipleSelected = multipleSelected.filter( (ele) => {
                                                                            return ele != factOption.value; 
                                                                            })
                                                                    }

                                                                    let _allTripFacts = trip_facts;
                                                                    _allTripFacts[factIndex].value = multipleSelected
                                                                
                                                                    updateTripData({
                                                                        ...allData,
                                                                        trip_facts:[..._allTripFacts]
                                                                    })
                                                                }
                                                                    
                                                                }
                                                            />
                                                    } )}
                                                    
                                                
                                                </div>
                                            }
                                        </PanelRow>
                                        <PanelRow className="wp-travel-action-section has-right-padding">
                                            <span></span><Button isDefault onClick={() => {
                                                if (!confirm(__( 'Are you sure to delete remove fact?', 'wp-travel' ) )) {
                                                    return false;
                                                }
                                                let factData = [];
                                                factData = trip_facts.filter((fact, newFactId) => {
                                                    return newFactId != factIndex;
                                                });
                                                // console.log(factData);
                                                updateFacts(factData);
                                            }} className="wp-traval-button-danger">{__( '- Remove Fact', 'wp-travel' )}</Button>
                                        </PanelRow>
                                    
                                    </PanelBody>

                                })}
                            </ReactSortable> 
                            {trip_facts.length > 1 && <PanelRow className="wp-travel-action-section"><span></span><Button isDefault onClick={() => addFact()}>{__( '+ Add Fact' )}</Button></PanelRow> }</div></>:
                            <Notice isDismissible={false} actions={[{
                                'label': __( 'Add Fact', 'wp-travel' ),
                                onClick: () => {
                                    addFact()
                                },
                                noDefaultClasses: true,
                                className: 'is-link'
                            }]}>{ __( 'Please add new fact here.', 'wp-travel' ) }</Notice>
                        }
                        
                    </> : 
                    <>
                        <Notice isDismissible={false} actions={[{
                            'label': __( 'Add Fact', 'wp-travel' ),
                            onClick: () => {
                                let url = _wp_travel.admin_url + 'edit.php?post_type=itinerary-booking&page=settings#wp-travel-tab-content-facts'
                                window.location.href = url
                            },
                            noDefaultClasses: true,
                            className: 'is-link'
                        }]}>{ __( 'Please add fact from the settings', 'wp-travel' ) }</Notice>
                    </> 
                }
                </>
            }
          
            
        </div>
    </ErrorBoundary>);
}

const WPTravelTripOptionsFact = () => {
    return <div className="wp-travel-ui wp-travel-ui-card wp-travel-ui-card-no-border"><WPTravelTripOptionsFactContent /></div>
}

export default WPTravelTripOptionsFact;