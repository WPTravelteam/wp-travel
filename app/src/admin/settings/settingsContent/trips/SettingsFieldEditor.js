import { applyFilters } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { PanelRow, ToggleControl, RadioControl } from '@wordpress/components';
import Select from 'react-select'
import { VersionCompare } from '../../../fields/VersionCompare'

import ErrorBoundary from '../../../../ErrorBoundry/ErrorBoundry';

export default () => {
    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    return (
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {__("Field Editor", "wp-travel")}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More field editor settings according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <ErrorBoundary>
                    {applyFilters('wp_travel_settings_tab_field_editor_fields', [], allData)}
                </ErrorBoundary>
            </div>
    </>);
}