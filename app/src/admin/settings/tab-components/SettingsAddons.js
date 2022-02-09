import { applyFilters } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelBody, PanelRow, ToggleControl, TextControl, RadioControl } from '@wordpress/components';
import Select from 'react-select'
import {VersionCompare} from '../../fields/VersionCompare'
import {alignJustify } from '@wordpress/icons';

import ErrorBoundary from '../../../ErrorBoundry/ErrorBoundry';

export default () => {

   
    return <div className="wp-travel-ui wp-travel-ui-card settings-general">
        <h2>{ __( 'Addons Settings', 'wp-travel' ) }</h2>
        <p>{__( 'You can enable or disable addons features from here.', 'wp-travel' )}</p>
        <ErrorBoundary>
            {applyFilters( 'wp_travel_addons_setings_tab_fields', [] ) }
        </ErrorBoundary>
    </div>
}