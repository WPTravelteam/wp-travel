import { _n, __ } from '@wordpress/i18n';
import { PanelRow, TextControl, Notice } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import { useSelect, dispatch } from '@wordpress/data';

// Fontawesome Icon Content.
const FontAwesomeIconContent = (props) => {
    
    // Replace all '-' from string and capitalize the first letter of string.
    const slugToName = (icon) => {
        let str = icon.label.replaceAll('-',' ');
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    const {
        options 
    } = allData;

    let faIcons = 'undefined' !== typeof options.wp_travel_fontawesome_icons ? options.wp_travel_fontawesome_icons : undefined;

    sessionStorage.setItem('WPTravelLastSelectedTab', 'fontawesome-icon');

    const [fontAwesomeIcons, setFontAwesomeIcons] = useState(faIcons);

    const [filterValue, setFilterValue] = useState('');

    const [ selectedFAIcons, setSelectedFAIcons ] = useState(props.fact.icon);

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

export default FontAwesomeIconContent;