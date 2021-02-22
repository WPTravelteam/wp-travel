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
    console.log(props.openModal);
    const [ isOpen, setOpen ] = useState( true );
    const openModal = () => setOpen( true );
    const closeModal = () => {
        setOpen( false );
    }

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
                // sessionStorage.clear();
                setOpen(false);
            } else {
                updateFact( 'icon_img', '', index );
                updateFact( 'selected_icon_type', 'custom-upload', index );
                // sessionStorage.clear();
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

    return <>
        {
            isOpen && (
                <Modal key={props.factIndex} className="wti__icon_select_modal"
                    title={<><i className="fas fa-list"></i>{__( ' Icon Type', 'wp-travel' )}</>}
                    onRequestClose={ props.onClose }>
                    <TabPanel className="my-tab-panel"
                        activeClass="active-tab"
                        initialTabName= { props.factData.selected_icon_type ? props.factData.selected_icon_type : 'icon-class' }
                        onSelect={ () => false }
                        isDismissible={false}
                        tabs={ [
                                {
                                name: 'fontawesome-icon',
                                title: <><i className="fas fa-flag"></i>{__( ' Fontawesome Icon', 'wp-travel' )}</>,
                                className: 'wti__fa_icon',
                                content: FontAwesomeIconContent
                            },
                            {
                                name: 'icon-class',
                                title: <><i className="fas fa-file-code"></i>{__( ' Icon Class', 'wp-travel' )}</>,
                                className: 'wti__icon_class',
                                content: IconClassContent
                            },
                            {
                                name: 'custom-upload',
                                title: <><i className="fas fa-upload"></i>{__( ' Custom Upload', 'wp-travel' )}</>,
                                className: 'wti__custom_upload',
                                content: CustomUploadContent
                            },
                        ] }>
                        {
                            ( tab ) => 'undefined' !== typeof tab.content ? <tab.content index={props.factIndex} fact={props.factData} /> : <>{__('Error', 'wp-travel')}</>
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
            )
        }
    </>
}

export default WPTravelIcons;