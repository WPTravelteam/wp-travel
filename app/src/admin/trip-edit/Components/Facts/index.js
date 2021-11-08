import { TextControl, PanelRow, PanelBody, Button, Notice, CheckboxControl} from '@wordpress/components';
import { addFilter } from '@wordpress/hooks';
import { isUndefined } from 'lodash';
import { useSelect, dispatch } from '@wordpress/data';
import { _n, __} from '@wordpress/i18n';
import { ReactSortable } from 'react-sortablejs';

import {alignJustify } from '@wordpress/icons';
import Select from 'react-select'

import ErrorBoundary from '../../../../ErrorBoundry/ErrorBoundry';

const __i18n = {
	..._wp_travel_admin.strings
}

// @todo Need to remove this in future.
// const WPTravelTripOptionsFact = () => {
//     return <></>;
// }
// export default WPTravelTripOptionsFact;

// Swap any array or object as per provided index.
const  swapList = (data, old_index, new_index) => {
    if ( 'object' === typeof data ) {
        if (new_index >= Object.keys(data).length) {
            var k = new_index - Object.keys(data).length + 1;
            while (k--) {
                data.push(undefined);
            }
        }
        data.splice(new_index, 0, data.splice(old_index, 1)[0]);
    }
    if ( 'array' === typeof data ) {
        if (new_index >= data.length) {
            var k = new_index - data.length + 1;
            while (k--) {
                data.push(undefined);
            }
        }
        data.splice(new_index, 0, data.splice(old_index, 1)[0]);
    }
    return data;
};

// Single Components for hook callbacks.
const TripFacts = ({allData}) => {
    const settingsData = useSelect((select) => {
        return select('WPTravel/TripEdit').getSettings()
    }, []);
    const { updateTripData, addNewFact, updateRequestSending } = dispatch('WPTravel/TripEdit');
    const { trip_facts } = allData;
    const {wp_travel_trip_facts_enable, wp_travel_trip_facts_settings} = settingsData // All Facts options from settings.

    let factOptions = ! isUndefined( wp_travel_trip_facts_settings )  && Object.keys(wp_travel_trip_facts_settings).length > 0 ? Object.keys(wp_travel_trip_facts_settings).map( ( index ) => {
        return {
            label: wp_travel_trip_facts_settings[index].name,
            value: wp_travel_trip_facts_settings[index].name
          }
    } ) : []
    const updateFactType = ( key, data, _factIndex ) => {

        const { trip_facts } = allData;
        let _allTripFacts = trip_facts;
        _allTripFacts[_factIndex][key] = data[key]
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
        updateTripData({
            ...allData,
            trip_facts:[..._allTripFacts]
        })
    }
    const fieldTypeOption = ( factId ) => {
        if ( 'number' != typeof factId ){
            return []
        }

        let selectedFactSettings = Object.keys(wp_travel_trip_facts_settings).length > 0 && 'undefined' != typeof wp_travel_trip_facts_settings[factId] ? wp_travel_trip_facts_settings[factId].options : [];
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
                            <PanelRow className="wp-travel-action-section"><span></span><Button isDefault onClick={() => addFact()}>{__i18n.add_fact}</Button></PanelRow>
                            <div className="wp-travel-sortable-component">
                            <ReactSortable
                                list={trip_facts}
                                setList={sortedFact => sortFacts( sortedFact)}
                                handle=".components-panel__icon"
                                >
                                {trip_facts.map((trip_fact, factIndex) => {
                                    let trip_fact_id = '' != trip_fact.fact_id ? parseInt(trip_fact.fact_id) : trip_fact.fact_id; // To check if fact_id is not empty, if empty then it means newly added. So send it as string in order to prevent conflict in fiedltypeoption number typeof check. @since 4.4.1
                                    let singleOrMultipleOptions = fieldTypeOption(trip_fact_id);

                                    let singleSelected = singleOrMultipleOptions.filter( ( item ) => {
                                        return item.value == trip_fact.value
                                    } )
                                    singleSelected = typeof singleSelected != 'undefined' ? singleSelected[0] : {}

                                    let multipleSelected = trip_fact.value ? trip_fact.value : []
								    let index            = parseInt(factIndex);


                                    return <div style={{position:'relative'}}  data-index={index} key={index} >
                                        <div className={`wptravel-swap-list`}>
                                        <Button
                                        // style={{padding:0, display:'block'}}
                                        disabled={0 == index}
                                        onClick={(e) => {
                                            let sorted = swapList( trip_facts, index, index - 1 )
                                            sortFacts(sorted)
                                            updateRequestSending(true); // Temp fixes to reload the content.
                                            updateRequestSending(false);
                                        }}><i className="dashicons dashicons-arrow-up"></i></Button>
                                        <Button
                                        // style={{padding:0, display:'block'}}
                                        disabled={( Object.keys(trip_facts).length - 1 ) === index}
                                        onClick={(e) => {
                                            let sorted = swapList( trip_facts, index, index + 1 )
                                            sortFacts(sorted)
                                            updateRequestSending(true);
                                            updateRequestSending(false);
                                        }}><i className="dashicons dashicons-arrow-down"></i></Button>
                                    </div>
                                    <PanelBody
                                        icon= {alignJustify}
                                        title={trip_fact.label ? trip_fact.label : __i18n.fact }
                                        initialOpen={ ( trip_facts.length - 1 === index ) }
                                        >

                                        <PanelRow>
                                            <label>{__i18n.select_type}</label>
                                            <div className="wp-travel-select-wrapper">
                                                <Select
                                                    options={factOptions}
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
                                            <label>{ __i18n.value }</label>
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
                                                if (!confirm( __i18n.alert.remove_fact )) {
                                                    return false;
                                                }
                                                let factData = [];
                                                factData = trip_facts.filter((fact, newFactId) => {
                                                    return newFactId != factIndex;
                                                });
                                                updateFacts(factData);
                                            }} className="wp-traval-button-danger">{__i18n.remove_fact}</Button>
                                        </PanelRow>

                                    </PanelBody>
                                    </div>
                                })}
                            </ReactSortable>
                            {trip_facts.length > 1 && <PanelRow className="wp-travel-action-section"><span></span><Button isDefault onClick={() => addFact()}>{__i18n.add_fact}</Button></PanelRow> }</div></>:
                            <Notice isDismissible={false} actions={[{
                                'label': __i18n.add_fact,
                                onClick: () => {
                                    addFact()
                                },
                                noDefaultClasses: true,
                                className: 'is-link'
                            }]}>{ __i18n.messages.add_fact }</Notice>
                        }

                    </> :
                    <>
                        <Notice isDismissible={false} actions={[{
                            'label': __i18n.add_fact,
                            onClick: () => {
                                let url = _wp_travel.admin_url + 'edit.php?post_type=itinerary-booking&page=settings#wp-travel-tab-content-facts'
                                window.location.href = url
                            },
                            noDefaultClasses: true,
                            className: 'is-link'
                        }]}>{ __i18n.messages.add_new_fact }</Notice>
                    </>
                }
                </>
            }
        </div>
    </ErrorBoundary>);
}

// Callbacks.
const TripFactsCB = ( content, allData ) => {
    return [ ...content, <TripFacts allData={allData} key="TripFacts" /> ];
}

// Hooks.
addFilter( 'wptravel_trip_edit_tab_content_facts', 'WPTravel/TripEdit/TripFacts', TripFactsCB, 10 );

addFilter( 'wp_travel_trip_edit_block_facts', 'WPTravel/TripEdit/Block/Facts/FactsFields', TripFactsCB, 10 );
