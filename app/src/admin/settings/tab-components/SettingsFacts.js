import { applyFilters } from '@wordpress/hooks';
import { useSelect, dispatch } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelBody, PanelRow, ToggleControl, TextControl, FormTokenField, Button, Disabled, Spinner, Modal, TabPanel, Notice } from '@wordpress/components';
import Select from 'react-select'
import { useEffect, useState } from '@wordpress/element';

import ErrorBoundary from '../../../ErrorBoundry/ErrorBoundry';

const SettingsFact = () => {

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

    const [ isOpen, setOpen ] = useState( false );
    const openModal = () => setOpen( true );
    const closeModal = () => {
        setOpen( false );
    }

    // Replace all '-' from string and capitalize the first letter of string.
    const slugToName = (icon) => {
        let str = icon.label.replaceAll('-',' ');
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    let faIcons = 'undefined' !== typeof options.wp_travel_fontawesome_icons ? options.wp_travel_fontawesome_icons : undefined;

    //Update tab settings.
    const updateTabSettings = (index) => {
        const lastSelectedTab = sessionStorage.getItem('WPTravelLastSelectedTab');

        if ( 'fontawesome-icon' == lastSelectedTab ) {

            let FAIconValue = sessionStorage.getItem('WPTravelFAIconValue');
            updateFact( 'fa_icon', FAIconValue, index );
            updateFact( 'selected_icon_type', 'fontawesome-icon', index );
            sessionStorage.clear();
            setOpen(false);

        } else if( 'custom-upload' == lastSelectedTab ) {

            if ( "wpTravelIconModuleUploaderData" in sessionStorage ) {
                const selectedImgDataString = sessionStorage.getItem('wpTravelIconModuleUploaderData');
                const selectedImgData = JSON.parse(selectedImgDataString);
    
                const [ selectedImgDataObj ] = selectedImgData;
    
                const { url } = selectedImgDataObj;
                updateFact( 'icon_img', url, index );
                updateFact( 'selected_icon_type', 'custom-upload', index );
                sessionStorage.clear();
                setOpen(false);
            } else {
                updateFact( 'icon_img', '', index );
                updateFact( 'selected_icon_type', 'custom-upload', index );
                sessionStorage.clear();
                setOpen(false);
            }

        } else if( 'icon-class' == lastSelectedTab ) {

            let iconClassValue = sessionStorage.getItem('WPTravelIconClassValue');
            updateFact( 'icon', iconClassValue, index );
            updateFact( 'selected_icon_type', 'icon-class', index );
            sessionStorage.clear();
            setOpen(false);

        }
    }

    // Fontawesome Icon Content.
    const fontawesomeIconContent = (props) => {

        sessionStorage.setItem('WPTravelLastSelectedTab', 'fontawesome-icon');

        const [fontAwesomeIcons, setFontAwesomeIcons] = useState(faIcons);

        const [filterValue, setFilterValue] = useState('');

        const [ selectedFAIcons, setSelectedFAIcons ] = useState(props.fact.fa_icon);

        sessionStorage.setItem('WPTravelFAIconValue', selectedFAIcons );

        const selectedFontAwesomeIcon = (event) => {
            setSelectedFAIcons(event.target.getAttribute('data-icon'));
        }

        const resetSelectIcon = (e) => {
            var dataIconAttr = e.target.getAttribute('data-icon');
            if ( ! dataIconAttr ) {
                setSelectedFAIcons('');
            }
        }

        useEffect(()=> {
            let isMounted = true;

            if ( isMounted ) {
                document.addEventListener("click", resetSelectIcon);
            }

            return () => {
                document.removeEventListener("click", resetSelectIcon);
                isMounted = false;
            }
        }, []);

        const ListFAIcons = (icons) => { 
            return (
                'undefined' != typeof options && 'undefined' != typeof icons &&
                icons.length > 0 ?
                    icons.map( (icon, index) => {
                        let iconName = slugToName(icon);
                        return <>
                            <div key={index} id={"tab-item-"+index} className={ selectedFAIcons == icon.value ? 'wti__fontawesome_tab_item selected' : 'wti__fontawesome_tab_item' } data-icon={icon.value} onClick={selectedFontAwesomeIcon}>
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

        return <>
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
                {ListFAIcons(fontAwesomeIcons)}
            </div>
        </PanelRow>
        </>
    }

    // Icon Class Content.
    const iconClassContent = (props) => {
        const [ iconClassName, setIconClassName ] = useState(props.fact.icon ? props.fact.icon : '');
        sessionStorage.setItem('WPTravelLastSelectedTab', 'icon-class');
        sessionStorage.setItem('WPTravelIconClassValue', iconClassName );
        return <>
        <PanelRow>
            <label>{__( 'Icon Class', 'wp-travel' )}</label>
            <TextControl
                placeholder={__( 'icon', 'wp-travel' )}
                value={iconClassName}
                onChange={(value) => {
                    setIconClassName(value);
                }}
            />
        </PanelRow>
        </>
    }

    // Custom Upload Content.
    const customUploadContent = (props) => {
        sessionStorage.setItem('WPTravelLastSelectedTab', 'custom-upload');

        const [{ imageUrl, isFetchingImage }, setState] = useState({
            imageUrl: props.fact.icon_img ? props.fact.icon_img : null,
            isFetchingImage: false,
        })

        const mediaInstance = wp.media({
            multiple: false
        })

        useEffect(() => {
            
            if ( sessionStorage.length > 1 && "wpTravelIconModuleUploaderData" in sessionStorage && '' != sessionStorage.getItem('wpTravelIconModuleUploaderData') ) {
                setState({
                    isFetchingImage: true
                });
                
                const imgDataString = sessionStorage.getItem('wpTravelIconModuleUploaderData');
                const imgData = JSON.parse(imgDataString);
    
                const [ imgDataObj ] = imgData;
    
                const { url } = imgDataObj;

                setState({
                    imageUrl: url,
                    isFetchingImage: false,
                })
            }
        }, []);

        mediaInstance
            .on('select', () => {
                const selectedItems = mediaInstance.state().get('selection').toJSON()
                if ( selectedItems.length > 0 ) {

                    sessionStorage.setItem('wpTravelIconModuleUploaderData', '');
                    sessionStorage.setItem('wpTravelIconModuleUploaderData', JSON.stringify(selectedItems));

                    // updateFact( 'icon_img', invoiceLogoID.toString(), props.index )
                }
                setOpen(true);
            })

            const onMediaUploaderBtnClicked = () => {
                mediaInstance.open();
            }

            const onMediaRemoveBtnClicked = () => {
                setState({
                    imageUrl: null,
                });
                sessionStorage.removeItem('wpTravelIconModuleUploaderData');
            }

            return <>
            <PanelRow>
                <h3>Icon</h3>
                <div className="wp-travel-field-value">
                    <div className="media-preview">
                        {isFetchingImage && <Spinner />}
                        {imageUrl && <img src={imageUrl} height="100" width="20%" />}
                        <div className="wti_custom_uploader_btn_wrapper">
                            <Button isPrimary onClick={onMediaUploaderBtnClicked}>{ imageUrl ? __('Change image', 'wp-travel' ) : __( 'Select image', 'wp-travel' )}</Button>
                            {
                                imageUrl &&
                                <Button className="wti_custom_remove_btn" isDestructive onClick={onMediaRemoveBtnClicked}>{ __('Remove image', 'wp-travel' ) }</Button>
                            }
                        </div>
                    </div>
                </div>
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
                                return <PanelBody key={index}
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
                                        <div className="wti_icon_btn_wrapper">
                                            {
                                                'fontawesome-icon' == fact.selected_icon_type && '' != fact.fa_icon &&
                                                <i className={fact.fa_icon}></i>
                                            }
                                            {
                                                'custom-upload' == fact.selected_icon_type && '' != fact.icon_img &&
                                                <img src={fact.icon_img} height="100" width="20%" />
                                            }
                                            {
                                                'icon-class' == fact.selected_icon_type && '' != fact.icon &&
                                                <i className={fact.icon}></i>
                                            }
                                            <Button isSecondary onClick={ openModal }>{__( 'Choose Icon', 'wp-travel' )}</Button>
                                        </div>
                                        { isOpen && (
                                                <Modal key={index} className="wti__icon_select_modal"
                                                    title={<><i className="fas fa-list"></i>{__( ' Icon Type', 'wp-travel' )}</>}
                                                    onRequestClose={ closeModal }>
                                                    <TabPanel className="my-tab-panel"
                                                        activeClass="active-tab"
                                                        initialTabName= { fact.selected_icon_type ? fact.selected_icon_type : 'icon-class' }
                                                        onSelect={ () => false }
                                                        isDismissible={false}
                                                        tabs={ [
                                                                {
                                                                name: 'fontawesome-icon',
                                                                title: <><i className="fas fa-flag"></i>{__( ' Fontawesome Icon', 'wp-travel' )}</>,
                                                                className: 'wti__fa_icon',
                                                                content: fontawesomeIconContent
                                                            },
                                                            {
                                                                name: 'icon-class',
                                                                title: <><i className="fas fa-file-code"></i>{__( ' Icon Class', 'wp-travel' )}</>,
                                                                className: 'wti__icon_class',
                                                                content: iconClassContent
                                                            },
                                                            {
                                                                name: 'custom-upload',
                                                                title: <><i className="fas fa-upload"></i>{__( ' Custom Upload', 'wp-travel' )}</>,
                                                                className: 'wti__custom_upload',
                                                                content: customUploadContent
                                                            },
                                                        ] }>
                                                        {
                                                            ( tab ) => 'undefined' !== typeof tab.content ? <tab.content index={index} fact={fact} /> : <>{__('Error', 'wp-travel')}</>
                                                        }
                                                    </TabPanel>
                                                    <div className="wti__insert_icon">
                                                    {
                                                        <Notice status="warning" isDismissible={false}>
                                                            {__( 'Click insert to save.', 'wp-travel' )}
                                                        </Notice>
                                                    }
                                                        <Button
                                                        isSecondary
                                                        onClick={() => updateTabSettings(index)}
                                                        >
                                                            {__( 'Insert', 'wp-travel' )}
                                                        </Button>
                                                    </div>
                                                </Modal>
                                        ) }
                                    </PanelRow>

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

export default () => {
    return (
        <SettingsFact />
    )
}