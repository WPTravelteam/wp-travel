import { applyFilters } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelBody, PanelRow, ToggleControl, TextControl, FormTokenField, Button, Disabled } from '@wordpress/components';
import Select from 'react-select'
import {VersionCompare} from '../../fields/VersionCompare'

import ErrorBoundary from '../../error/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);
    const {

        wp_travel_trip_facts_enable,
        wp_travel_trip_facts_settings,
        options } = allData;

    const { updateSettings, addNewFact } = dispatch('WPTravel/Admin');
    
    // options
    let factOptions = []

    if ( 'undefined' != typeof options ) {
        if ( 'undefined' != typeof options.fact_options ) {
            factOptions = options.fact_options
        } 
    }

    const updateFact = (key, value, _tabIndex) => {

        const { wp_travel_trip_facts_settings } = allData;

        let _allFacts = wp_travel_trip_facts_settings;
        _allFacts[_tabIndex][key] = value

        updateSettings({
            ...allData,
            wp_travel_trip_facts_settings: [..._allFacts]
        })
    }

    const removeFact = (allFacts) => { // Remove
        updateSettings({
            ...allData,
            wp_travel_trip_facts_settings:[...allFacts]
        })
    }
    const addNewFactData = () => {
        addNewFact( {
            name: '',
            type: '',
            options: [],
            icon: '',
        } )
    }

    let FieldTypeContent = ( props  ) => {
        const allData = useSelect((select) => {
            return select('WPTravel/Admin').getAllStore()
        }, []);
        const { options } = allData;
    
        const { updateSettings } = dispatch('WPTravel/Admin');

        const updateFact = (key, value, _tabIndex) => {

            const { wp_travel_trip_facts_settings } = allData;
    
            let _allFacts = wp_travel_trip_facts_settings;
            _allFacts[_tabIndex][key] = value
    
            updateSettings({
                ...allData,
                wp_travel_trip_facts_settings: [..._allFacts]
            })
        }
        let factOptions = []

        if ( 'undefined' != typeof options ) {
            if ( 'undefined' != typeof options.fact_options ) {
                factOptions = options.fact_options
            } 
        }
        let selectedFactOptions = factOptions.filter( opt => { return opt.value == props.fact.type } )
        return <PanelRow>
                    <label>{ __( 'Field Type', 'wp-travel' ) }</label>
                    <div className="wp-travel-field-value">
                        <div className="wp-travel-select-wrapper">
                            <Select
                                options={factOptions}
                                value={ 'undefined' != typeof selectedFactOptions[0] && 'undefined' != typeof selectedFactOptions[0].label ? selectedFactOptions[0] : []}
                                onChange={(data)=>{
                                    if ( '' !== data ) {
                                        updateFact( 'type', data.value, props.index )
                                    }
                                }}
                            />
                        </div>
                    </div>
                </PanelRow>
                            

    }
    
    return <div className="wp-travel-ui wp-travel-ui-card settings-general">
        <h2>{ __( 'Facts Settings', 'wp-travel' ) }</h2>
        <ErrorBoundary>
            
            <PanelRow>
                <label>{ __( 'Trip Facts', 'wp-travel' ) }</label>
                <div className="wp-travel-field-value">
                    <ToggleControl
                        checked={ wp_travel_trip_facts_enable == 'yes' }
                        onChange={ () => {
                            updateSettings({
                                ...allData,
                                wp_travel_trip_facts_enable: 'yes' == wp_travel_trip_facts_enable ? 'no': 'yes'
                            })
                        } }
                    />
                    <p className="description">{__( 'Enable Trip Facts display on trip single page.', 'wp-travel' )}</p>
                </div>
            </PanelRow>

            { 'undefined' != typeof wp_travel_trip_facts_enable && 'yes' == wp_travel_trip_facts_enable &&
                <>
                    <PanelRow className="wp-travel-action-section"><span></span><Button isDefault onClick={() => addNewFactData()}>{ __( '+ Add New', 'wp-travel' ) }</Button></PanelRow>

                    { 'undefined' != typeof wp_travel_trip_facts_settings &&
                        <>
                            {wp_travel_trip_facts_settings.map( ( fact, index) =>{
                                // let selectedFactOptions = factOptions.filter( opt => { return opt.value == fact.type } )
                                
                                return <PanelBody
                                    title={ 'undefined' != typeof fact.name && fact.name ? fact.name :  __( `Fact ${index + 1} `, 'wp-travel' )}
                                    initialOpen={false}
                                    >
                                    <PanelRow>
                                        <label>{__( 'Field Name', 'wp-travel' )}</label>
                                        <TextControl
                                            placeholder={__( 'Enter Field name', 'wp-travel' )}
                                            value={fact.name}
                                            onChange={(value) => {
                                                updateFact( 'name', value, index ) 
                                            }}
                                        />
                                    </PanelRow>


                                    {/* <PanelRow>
                                        <label>{ __( 'Field Type', 'wp-travel' ) }</label>
                                        <div className="wp-travel-field-value">
                                            <div className="wp-travel-select-wrapper">
                                                <Select
                                                    options={factOptions}
                                                    value={ 'undefined' != typeof selectedFactOptions[0] && 'undefined' != typeof selectedFactOptions[0].label ? selectedFactOptions[0] : []}
                                                    onChange={(data)=>{
                                                        if ( '' !== data ) {
                                                            updateFact( 'type', data.value, index )
                                                        }
                                                    }}
                                                />
                                            </div>
                                        </div>
                                    </PanelRow> */}
                                    { ( 'undefined' != typeof fact.key ) ? <Disabled><FieldTypeContent fact={fact} index={index} /></Disabled> : <FieldTypeContent fact={fact} index={index} /> } 
                                    
                                    {fact.type != 'text' &&
                                        <PanelRow>
                                            <label>{ __( 'Values', 'wp-travel' ) }</label>
                                            <div className="wp-travel-field-value">
                                                <FormTokenField 
                                                    label=""
                                                    value={ fact.options } 
                                                    suggestions={ [] } 
                                                    onChange={ tokens =>{
                                                        updateFact( 'options', tokens, index )
                                                    }}
                                                    placeholder={ __( 'Add an option and press Enter', 'wp-travel' ) }
                                                />
                                            </div>
                                        </PanelRow>
                                    }
                                    <PanelRow>
                                        <label>{__( 'Icon Class', 'wp-travel' )}</label>
                                        <TextControl
                                            placeholder={__( 'icon', 'wp-travel' )}
                                            value={fact.icon}
                                            onChange={(value) => {
                                                updateFact( 'icon', value, index ) 
                                            }}
                                        />
                                    </PanelRow>
                                    <PanelRow className="wp-travel-action-section">
                                        <span></span>
                                        <Button isDefault onClick={() => {
                                            if (!confirm(__( 'Are you sure to delete Fact?', 'wp-travel' ) )) {
                                                return false;
                                            }
                                            let factData = [];
                                            factData = wp_travel_trip_facts_settings.filter((data, newIndex) => {
                                                return newIndex != index;
                                            });
                                            removeFact(factData);
                                        }} className="wp-traval-button-danger">{__( '- Remove Fact', 'wp-travel' )}</Button></PanelRow>
                                    
                                </PanelBody>
                            } )}

                            {wp_travel_trip_facts_settings.length > 2 && <PanelRow className="wp-travel-action-section"><span></span><Button isDefault onClick={() => addNewFactData()}>{ __( '+ Add New', 'wp-travel' ) }</Button></PanelRow>}
                        </>
                    }
                </>
            }

            

        </ErrorBoundary>
    </div>
}