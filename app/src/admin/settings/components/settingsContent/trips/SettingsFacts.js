import { useSelect, dispatch } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelBody, PanelRow, ToggleControl, TextControl, FormTokenField, Button, Disabled } from '@wordpress/components';
import Select from '../../UI/Select';
import { useState } from '@wordpress/element';

import ErrorBoundary from '../../../../../ErrorBoundry/ErrorBoundry';
import WPTravelIcons from '../../../../../GlobalComponents/Icons/WPTravelIcons';

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

    if ('undefined' != typeof options) {
        if ('undefined' != typeof options.fact_options) {
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
            wp_travel_trip_facts_settings: [...allFacts]
        })
    }
    const addNewFactData = () => {
        addNewFact({
            name: '',
            type: '',
            options: [],
            icon: '',
        })
    }

    const [{ isTabOpen, tabData }, setState] = useState({
        isTabOpen: false,
        tabData: {}
    });

    // In order to close other tab when open one.
    const panelTabChanged = (index) => {
        setState({
            isTabOpen: 'undefined' != typeof tabData.index && index == tabData.index && isTabOpen ? false : true,
            tabData: { index }
        })
    }

    const [isOpenModal, setIsOpenModal] = useState(false);

    const openModal = () => {
        setIsOpenModal(true);
    }

    let FieldTypeContent = (props) => {
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

        if ('undefined' != typeof options) {
            if ('undefined' != typeof options.fact_options) {
                factOptions = options.fact_options
            }
        }
        let selectedFactOptions = factOptions.filter(opt => { return opt.value == props.fact.type })
        return <PanelRow>
            <label>{_wp_travel.setting_strings.facts.field_type}</label>
            <div className="wp-travel-field-value">
                <div className="wp-travel-select-wrapper">
                    <Select
                        theme={(theme) => ({
                            ...theme,
                            borderRadius: ".5rem",
                            colors: {
                                ...theme.colors,
                                primary25: "rgb(236 248 244)",
                                primary50: "rgb(204, 204, 204)",
                                primary: "rgb(7 152 18)"
                            }
                        })}
                        options={factOptions}
                        value={'undefined' != typeof selectedFactOptions[0] && 'undefined' != typeof selectedFactOptions[0].label ? selectedFactOptions[0] : []}
                        onChange={(data) => {
                            if ('' !== data) {
                                updateFact('type', data.value, props.index)
                            }
                        }}
                    />
                </div>
            </div>
        </PanelRow>
    }

    return <>
        <div className="wp-travel-section-header">
            <h2 className="wp-travel-section-header-title">
                {_wp_travel.setting_strings.facts.facts_settings}
            </h2>
            <p className="wp-travel-section-header-description">
                {__("More facts settings according to your choice.", "wp-travel")}
            </p>
        </div>
        <div className='wp-travel-section-content'>
            <ErrorBoundary>
                <PanelRow>
                    <label>{_wp_travel.setting_strings.facts.trip_facts}</label>
                    <div id="wp-travel-trip-facts" className="wp-travel-field-value">
                        <ToggleControl
                            checked={wp_travel_trip_facts_enable == 'yes'}
                            onChange={() => {
                                updateSettings({
                                    ...allData,
                                    wp_travel_trip_facts_enable: 'yes' == wp_travel_trip_facts_enable ? 'no' : 'yes'
                                })
                            }}
                        />
                        <p className="description">{_wp_travel.setting_strings.facts.trip_facts_note}</p>
                    </div>
                </PanelRow>

                {'undefined' != typeof wp_travel_trip_facts_enable && 'yes' == wp_travel_trip_facts_enable &&
                    <>
                        <PanelRow className="wp-travel-action-section"><span></span><Button variant="secondary" onClick={() => addNewFactData()}>{_wp_travel.setting_strings.facts.add_new}</Button></PanelRow>

                        {'undefined' != typeof wp_travel_trip_facts_settings &&
                            <>
                                {wp_travel_trip_facts_settings.map((fact, index) => {
                                    return (
                                        <PanelBody key={index}
                                            title={'undefined' != typeof fact.name && fact.name ? fact.name : __(`Fact ${index + 1} `, 'wp-travel')}
                                            initialOpen={false}
                                            onToggle={() => panelTabChanged(index)}
                                            opened={isTabOpen && index == tabData.index ? true : false}
                                        >
                                            <PanelRow>
                                                <label>{_wp_travel.setting_strings.facts.field_name}</label>
                                                <TextControl
                                                    placeholder={_wp_travel.setting_strings.facts.field_name_label}
                                                    value={fact.name}
                                                    onChange={(value) => {
                                                        updateFact('name', value, index)
                                                    }}
                                                />
                                            </PanelRow>

                                            {('undefined' != typeof fact.key) ? <Disabled><FieldTypeContent fact={fact} index={index} /></Disabled> : <FieldTypeContent fact={fact} index={index} />}

                                            {(fact.type == 'single' || fact.type == 'multiple') &&
                                                <PanelRow>
                                                    <label>{_wp_travel.setting_strings.facts.values}</label>
                                                    <div className="wp-travel-field-value">
                                                        <FormTokenField
                                                            label=""
                                                            value={fact.options}
                                                            suggestions={[]}
                                                            onChange={tokens => {
                                                                updateFact('options', tokens, index)
                                                            }}
                                                            placeholder={_wp_travel.setting_strings.facts.values_label}
                                                        />
                                                    </div>
                                                </PanelRow>
                                            }
                                            {/** Icon Start here. */}
                                            <PanelRow>
                                                <label>{_wp_travel.setting_strings.facts.icon}</label>
                                                <div className="wti_icon_btn_wrapper">
                                                    {
                                                        'fontawesome-icon' == fact.selected_icon_type && '' != fact.icon &&
                                                        <i className={fact.icon}></i>
                                                    }
                                                    {
                                                        'custom-upload' == fact.selected_icon_type && '' != fact.icon_img &&
                                                        <img src={fact.icon_img} style={{ width: '60px' }} />
                                                    }
                                                    {
                                                        'icon-class' == fact.selected_icon_type && '' != fact.icon &&
                                                        <p>{__('Icon Class: ', 'wp-travel')}<strong>[ {fact.icon} ]</strong></p>
                                                    }
                                                    <Button variant="secondary" onClick={openModal}>{_wp_travel.setting_strings.facts.choose_icon}</Button>
                                                </div>
                                                {
                                                    isOpenModal &&
                                                    <WPTravelIcons factData={fact} factIndex={index} modalHandleClick={setIsOpenModal} />
                                                }
                                            </PanelRow>

                                            <PanelRow className="wp-travel-action-section">
                                                <span></span>
                                                <Button variant="secondary" onClick={() => {
                                                    if (!confirm(_wp_travel.setting_strings.facts.remove_note )) {
                                                        return false;
                                                    }
                                                    let factData = [];
                                                    factData = wp_travel_trip_facts_settings.filter((data, newIndex) => {
                                                        return newIndex != index;
                                                    });
                                                    removeFact(factData);
                                                }} className="wp-traval-button-danger">{_wp_travel.setting_strings.facts.remove_fact}</Button></PanelRow>

                                        </PanelBody>
                                    )
                                })}

                                {wp_travel_trip_facts_settings.length > 2 && <PanelRow className="wp-travel-action-section"><span></span><Button variant="secondary" onClick={() => addNewFactData()}>{_wp_travel.setting_strings.facts.add_new}</Button></PanelRow>}
                            </>
                        }
                    </>
                }
            </ErrorBoundary>
        </div>
    </>
}
