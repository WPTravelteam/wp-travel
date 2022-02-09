import { useSelect, dispatch } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { Button, Modal, TabPanel, Notice } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';


import FontAwesomeIconContent from './FontAwesomeIcons';
import IconClassContent from './IconClass';
import CustomUploadContent from './CustomUpload';

const WPTravelIcons = (props) => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

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

    // const [ isOpen, setOpen ] = useState( true);
    // const openModal = () => setOpen( true );
    const closeModal = () => {
        // setOpen( false );
        props.modalHandleClick(false);
    }

    //Update tab settings.
    const updateTabSettings = (index) => {
        const lastSelectedTab = sessionStorage.getItem('WPTravelLastSelectedTab');
        // console.log('lastSelectedTab', lastSelectedTab);
        if ( 'fontawesome-icon' == lastSelectedTab ) {

            let FAIconValue = sessionStorage.getItem('WPTravelFAIconValue');
            updateFact( 'icon', FAIconValue, index );
            updateFact( 'selected_icon_type', 'fontawesome-icon', index );
            sessionStorage.clear();
            props.modalHandleClick(false);
            // setOpen(false);

        } else if( 'custom-upload' == lastSelectedTab ) {
            if ( "wpTravelIconModuleUploaderData" in sessionStorage ) {
                const selectedImgDataString = sessionStorage.getItem('wpTravelIconModuleUploaderData');
                const selectedImgData = JSON.parse(selectedImgDataString);
    
                const [ selectedImgDataObj ] = selectedImgData;
    
                const { url, id, sizes } = selectedImgDataObj;
                updateFact( 'icon_img', sizes.thumbnail.url, index );
                updateFact( 'icon_img_id', id, index );
                updateFact( 'selected_icon_type', 'custom-upload', index );
                props.modalHandleClick(false);
                // sessionStorage.clear();
                // setOpen(false);
            } else if ( 'undefined' !== props.factData && 'undefined' !== props.factData.icon_img ) {
                updateFact( 'icon_img', props.factData.icon_img, index );
                updateFact( 'icon_img_id', props.factData.icon_img_id, index );
                updateFact( 'selected_icon_type', 'custom-upload', index );
                props.modalHandleClick(false);
            }else {
                updateFact( 'icon_img', '', index );
                updateFact( 'icon_img_id', '', index );
                updateFact( 'selected_icon_type', 'custom-upload', index );
                props.modalHandleClick(false);
                // sessionStorage.clear();
                // setOpen(false);
            }

        } else if( 'icon-class' == lastSelectedTab ) {

            let iconClassValue = sessionStorage.getItem('WPTravelIconClassValue');
            updateFact( 'icon', iconClassValue, index );
            updateFact( 'selected_icon_type', 'icon-class', index );
            sessionStorage.clear();
            props.modalHandleClick(false);
            // setOpen(false);

        }
    }

    const ComingSoonMessage = () => {
        return <>
        <h3>Something is Cooking... Will be available soon.</h3>
        </>
    }

    // Place tab here for production use.
    const iconTabs = [
        {
            name: 'icon-class',
            title: <><i className="fas fa-file-code"></i>{__( ' Icon Class', 'wp-travel' )}</>,
            className: 'wti__icon_class',
            content: IconClassContent
        },
        {
            name: 'fontawesome-icon',
            title: <><i className="fas fa-flag"></i>{__( ' Fontawesome Icon', 'wp-travel' )}</>,
            className: 'wti__fa_icon',
            content: FontAwesomeIconContent
        },
        {
            name: 'custom-upload',
            title: <><i className="fas fa-upload"></i>{__( ' Custom Upload', 'wp-travel' )}</>,
            className: 'wti__custom_upload',
            content: CustomUploadContent
        },
        // {
        //     name: 'coming-soon',
        //     title: <><i className="fas fa-step-forward"></i>{__( 'Other Icons', 'wp-travel' )}</>,
        //     className: 'wti__coming_soon',
        //     content: ComingSoonMessage
        // }
    ]

    // Place tab here if still on development.
    // if ( _wp_travel.dev_mode ) {
    //     iconTabs.push(
    //         {
    //             name: 'fontawesome-icon',
    //             title: <><i className="fas fa-flag"></i>{__( ' Fontawesome Icon', 'wp-travel' )}</>,
    //             className: 'wti__fa_icon',
    //             content: FontAwesomeIconContent
    //         },
    //         {
    //             name: 'custom-upload',
    //             title: <><i className="fas fa-upload"></i>{__( ' Custom Upload', 'wp-travel' )}</>,
    //             className: 'wti__custom_upload',
    //             content: CustomUploadContent
    //         },
    //     )
    // }

    return <>
        {
            // isOpen && (
                <Modal key={props.factIndex} className="wti__icon_select_modal"
                    title={<><i className="fas fa-list"></i>{__( ' Icon Type', 'wp-travel' )}</>}
                    onRequestClose={ closeModal }>
                    <TabPanel className="my-tab-panel"
                        activeClass="active-tab"
                        initialTabName= { props.factData.selected_icon_type ? props.factData.selected_icon_type : 'icon-class' }
                        onSelect={ () => false }
                        isDismissible={false}
                        tabs={iconTabs}>
                        {
                            ( tab ) => 'undefined' !== typeof tab.content ? <tab.content index={props.factIndex} fact={props.factData} tabHandleClick = {props.modalHandleClick} updateFact={updateFact} /> : <>{__('Error', 'wp-travel')}</>
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
                        onClick={() => updateTabSettings(props.factIndex)}
                        >
                            {__( 'Insert', 'wp-travel' )}
                        </Button>
                    </div>
                </Modal>
            // )
        }
    </>
}

export default WPTravelIcons;