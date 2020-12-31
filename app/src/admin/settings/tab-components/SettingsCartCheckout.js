import { applyFilters } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelBody, PanelRow, ToggleControl, TextControl, RadioControl } from '@wordpress/components';
import Select from 'react-select'
import {VersionCompare} from '../../fields/VersionCompare'
import {alignJustify } from '@wordpress/icons';

import ErrorBoundary from '../../../ErrorBoundry/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);
    return <div className="wp-travel-ui wp-travel-ui-card settings-general">
        <h2>{ __( 'Checkout Process Options', 'wp-travel' ) }</h2>
        <ErrorBoundary>
            {applyFilters( 'wp_travel_settings_tab_cart_checkout_fields', [], allData ) }
        </ErrorBoundary>
    </div>
}