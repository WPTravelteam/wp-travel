import { applyFilters } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelBody, PanelRow, ToggleControl, TextControl, FormTokenField, Button, Disabled, Spinner, Modal, TabPanel, Notice } from '@wordpress/components';
import Select from 'react-select'
import {VersionCompare} from '../../fields/VersionCompare'
import { useEffect, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

import ErrorBoundary from '../../../ErrorBoundry/ErrorBoundry';

export default () => {

    const [{ imageUrl, isFetchingImage }, setState] = useState({
        imageUrl: null,
        isFetchingImage: false
    })

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

    const FontAwesomeIcon = (props) => {
        return <><i className={props.value}> {props.label}</i></>
    }

    //Icon options
    let iconOptions = [];

    if ( 'undefined' != typeof options && 'undefined' != typeof options.wp_travel_fontawesome_icons ) {
        iconOptions = options.wp_travel_fontawesome_icons.map( icon => {
            return {
                label: <FontAwesomeIcon label={icon.label} value={icon.value} />,
                value: icon.value,
            }
        } )
    }

    const iconTypeOptions = [
        { value: 'icon_class', label: 'Icon Class' },
        { value: 'fa_icon', label: 'Fontawesome Icons' },
    ]

    const [ isOpen, setOpen ] = useState( false );
    const openModal = () => setOpen( true );
    const closeModal = () => setOpen( false );

    // Replace all '-' from string and capitalize the first letter of string.
    const slugToName = (icon) => {
        let str = icon.label.replaceAll('-',' ');
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    const [ selectedFAIcons, setSelectedFAIcons ] = useState('');

    const selectedFontAwesomeIcon = (event) => {
        setSelectedFAIcons(event.target.getAttribute('data-icon'));
    }

    let faIcons = 'undefined' !== typeof options.wp_travel_fontawesome_icons ? options.wp_travel_fontawesome_icons : undefined;

    //Listing fontawesome icon in fontawesome tab content.
    const ListFAIcons = ({icons}) => {
        return (
            'undefined' != typeof options && 'undefined' != typeof icons &&
            icons.length > 0 ?
                icons.map( (icon, index) => {
                    let iconName = slugToName(icon);
                    return <>
                        <div key={index} id={"tab-item-"+index} className='wti__fontawesome_tab_item' data-icon={icon.value} tabindex={index} onClick={selectedFontAwesomeIcon}>
                            <div data-icon={icon.value} className="wti__fontawesome_tab_item_content">
                                <i data-icon={icon.value} className={icon.value}></i>
                                <div className="wti__fontawesome_tab_item_name" data-icon={icon.value} title={iconName}>{iconName}</div>
                            </div>
                        </div>
                    </>
                })
                :
                <Notice status="error" isDismissible={false}>
                    {__( 'Icon Not found', 'wp-travel' )}
                </Notice>
        )
    }

    // Fontawesome Icon Content.
    const fontawesomeIconContent = (props) => {

        const [fontAwesomeIcons, setFontAwesomeIcons] = useState(faIcons);

        const [filterValue, setFilterValue] = useState('');

        return <>
        {/* <PanelRow>
            <label>
                {__('Choose Icon', 'wp-travel')}
            </label>

            <div className="wp-travel-field-value">
                <Select
                    options={iconOptions}
                    value={'undefined' != typeof props.selectedAddIcons[0] && 'undefined' != typeof props.selectedAddIcons[0].label ? props.selectedAddIcons[0] : []}
                    onChange={(data) => {
                        if ('' !== data) {
                            updateFact( 'icon', data.value, props.index );
                        }
                    }}
                />
            </div>
        </PanelRow> */}
        <PanelRow className="font-awesome-panel">
            <div className="wti__fontawesome_filter">
                <TextControl
                    value={'undefined' !== typeof filterValue && filterValue}
                    onChange={
                        (value) => {

                            setFilterValue(value);
                            if ( value ) {
                                let filterFAIcons = faIcons.filter((faIcon) => {
                                    return faIcon.label.includes(value);
                                });
        
                                setFontAwesomeIcons(filterFAIcons);
                            } else {
                                setFontAwesomeIcons(faIcons);
                            }

                        }
                    }
                    placeholder={__( 'Filter by name...', 'wp-travel' )}
                />
                
            </div>
            <h3>{__( 'All Icons', 'wp-travel' )}</h3>
            <div className="wti__fontawesome_tab_content">
                {<ListFAIcons icons={fontAwesomeIcons}/>}
            </div>
        </PanelRow>
        </>
    }

    // Icon Class Content.
    const iconClassContent = (props) => {
        return <>
        <PanelRow>
            <label>{__( 'Icon Class', 'wp-travel' )}</label>
            <TextControl
                placeholder={__( 'icon', 'wp-travel' )}
                value={props.fact.icon}
                onChange={(value) => {
                    updateFact( 'icon', value, props.index )
                }}
            />
        </PanelRow>
        </>
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
                    <PanelRow className="wp-travel-action-section"><span></span><Button isSecondary onClick={() => addNewFactData()}>{ __( '+ Add New', 'wp-travel' ) }</Button></PanelRow>

                    { 'undefined' != typeof wp_travel_trip_facts_settings &&
                        <>
                            {wp_travel_trip_facts_settings.map( ( fact, index) =>{

                                // const logoId = fact.icon_img

                                // const mediaInstance = wp.media({
                                //     multiple: false
                                // })

                                // useEffect(() => {
                                //     if (logoId && !imageUrl) {
                                //         setState(state => ({
                                //             ...state,
                                //             isFetchingImage: true
                                //         }))
                                //         logoId && apiFetch({ path: `/wp/v2/media/${logoId}` })
                                //             .then((res) => {
                                //                 setState({
                                //                     imageUrl: res.source_url,
                                //                     isFetchingImage: false
                                //                 })
                                //             })
                                //         }
                                // }, [logoId])

                                // mediaInstance
                                //     .on('select', () => {
                                //         const selectedItems = mediaInstance.state().get('selection').toJSON()
                                //         if ( selectedItems.length > 0 ) {
                                //             let invoiceLogoID = selectedItems[0].id
                                //             setState({
                                //                 imageUrl: null,
                                //                 isFetchingImage: true
                                //             })
                                //             // updateIconImgData( 'fact', 'icon_img', invoiceLogoID.toString() )
                                //             // updateSettings({
                                //             //     ...allData,
                                //             //     invoice_logo: invoiceLogoID.toString()
                                //             // })
                                //             updateFact( 'icon_img', invoiceLogoID.toString(), index )
                                //         }
                                //     })
                                // let selectedFactOptions = factOptions.filter( opt => { return opt.value == fact.type } )
                                let selectedAddIcons = 'undefined' != typeof wp_travel_trip_facts_settings ? iconOptions.filter( opt => { return opt.value == fact.icon } ) : []
                                let selectedIconType = iconTypeOptions.filter( opt => { return opt.value == fact.icon_type } )
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

                                    {( fact.type == 'single' || fact.type == 'multiple' ) &&
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
                                    {/** Icon Start here. */ }
                                    <PanelRow>
                                        <label>{__( 'Icon', 'wp-travel' )}</label>
                                        <Button isSecondary onClick={ openModal }>{__( 'Choose Icon', 'wp-travel' )}</Button>
                                        { isOpen && (
                                            <Modal className="wti__icon_select_modal"
                                                title={<><i class="fas fa-list"></i>{__( ' Icon Type', 'wp-travel' )}</>}
                                                onRequestClose={ closeModal }>
                                                <TabPanel className="my-tab-panel"
                                                    activeClass="active-tab"
                                                    initialTabName="icon-class"
                                                    onSelect={ () => false }
                                                    tabs={ [
                                                            {
                                                            name: 'fontawesome-icon',
                                                            title: <><i class="fas fa-flag"></i>{__( ' Fontawesome Icon', 'wp-travel' )}</>,
                                                            className: 'wti__fa_icon',
                                                            content: fontawesomeIconContent
                                                        },
                                                        {
                                                            name: 'icon-class',
                                                            title: <><i class="fas fa-file-code"></i>{__( ' Icon Class', 'wp-travel' )}</>,
                                                            className: 'wti__icon_class',
                                                            content: iconClassContent
                                                        },
                                                        {
                                                            name: 'custom-upload',
                                                            title: <><i class="fas fa-upload"></i>{__( ' Custom Upload', 'wp-travel' )}</>,
                                                            className: 'wti__custom_upload',
                                                        },
                                                    ] }>
                                                    {
                                                        ( tab ) => 'undefined' !== typeof tab.content ? <tab.content index={index} selectedAddIcons={selectedAddIcons} fact={fact}/> : <>{__('Error', 'wp-travel')}</>
                                                    }
                                                </TabPanel>
                                                <div className="wti__insert_icon">
                                                    <Button isSecondary onClick={ closeModal }>
                                                        {__( 'Insert', 'wp-travel' )}
                                                    </Button>
                                                </div>
                                            </Modal>
                                        ) }
                                    </PanelRow>
                                    {/* <PanelRow>
                                        <label>{__( 'Icon Type', 'wp-travel' )}</label>
                                        <div className="wp-travel-field-value">
                                            <Select
                                                options={iconTypeOptions}
                                                value={ 'undefined' != typeof selectedIconType[0] && 'undefined' != typeof selectedIconType[0].label ? selectedIconType[0] : selectedIconType[0] }
                                                onChange= {(data) => {
                                                    updateFact( 'icon_type', data.value, index )
                                                }}
                                                defaultValue={{value:'icon_class', label:'Icon Class'}}
                                            />
                                        </div>
                                    </PanelRow> */}
                                    {/* {
                                        'icon_class' == fact.icon_type &&
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
                                    }
                                    {
                                        'fa_icon' == fact.icon_type &&
                                        <>
                                            <PanelRow>
                                                <label>
                                                    {__('Choose Icon', 'wp-travel')}
                                                </label>

                                                <div className="wp-travel-field-value">
                                                    <Select
                                                        options={iconOptions}
                                                        value={'undefined' != typeof selectedAddIcons[0] && 'undefined' != typeof selectedAddIcons[0].label ? selectedAddIcons[0] : []}
                                                        onChange={(data) => {
                                                            if ('' !== data) {
                                                                updateFact( 'icon', data.value, index );
                                                            }
                                                        }}
                                                    />
                                                </div>
                                            </PanelRow>
                                            <PanelRow>
                                                <div className="wp-travel-field-value">
                                                    <div className="media-preview">
                                                        {isFetchingImage && <Spinner />}
                                                        {imageUrl && <img src={imageUrl} height="100" width="50%" />}
                                                        <Button isPrimary onClick={() => mediaInstance.open()}>{ imageUrl ? __('Change image', 'wp-travel' ) : __( 'Select image', 'wp-travel' )}</Button>
                                                    </div>
                                                </div>
                                            </PanelRow>
                                        </>
                                    } */}
                                    <PanelRow className="wp-travel-action-section">
                                        <span></span>
                                        <Button isSecondary onClick={() => {
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

                            {wp_travel_trip_facts_settings.length > 2 && <PanelRow className="wp-travel-action-section"><span></span><Button isSecondary onClick={() => addNewFactData()}>{ __( '+ Add New', 'wp-travel' ) }</Button></PanelRow>}
                        </>
                    }
                </>
            }



        </ErrorBoundary>
    </div>
}