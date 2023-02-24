import { applyFilters, addFilter } from '@wordpress/hooks';

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

// Field Editor Notice.
addFilter('wp_travel_settings_tab_field_editor_fields', 'WPTravel/Settings/FieldEditor/Notice', (content, allData) => {
    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Want to customize your Traveler fields, billing fields and more?', 'wp-travel')}</strong>
                <br />
                {__('By upgrading to Pro, you can customize your Fields for Trip enquiry, Billing and travelers fields.!', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]
    return content
});