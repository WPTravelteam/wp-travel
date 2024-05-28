import { _n, __ } from '@wordpress/i18n';
import { PanelRow, TextControl } from '@wordpress/components';

// Icon Class Content.
const IconClassContent = (props) => {
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

export default IconClassContent;
