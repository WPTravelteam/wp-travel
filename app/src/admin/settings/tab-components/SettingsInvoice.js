import { applyFilters, addFilter } from '@wordpress/hooks';
import { useSelect, select, dispatch, withSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import { Notice} from '@wordpress/components';
import Select from 'react-select'
import {VersionCompare} from '../../fields/VersionCompare'
import {alignJustify } from '@wordpress/icons';

import ErrorBoundary from '../../../ErrorBoundry/ErrorBoundry';

export default () => {

   
    return <div className="wp-travel-ui wp-travel-ui-card settings-general">
        <h2>{ __( 'Invoice Options', 'wp-travel' ) }</h2>
        <ErrorBoundary>
            {applyFilters( 'wp_travel_settings_tab_invoice_fields', [] ) }
        </ErrorBoundary>
    </div>
}

addFilter('wp_travel_settings_tab_invoice_fields', 'wp_travel', (content, allData) => {
    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Need invoice options ?', 'wp-travel')}</strong>
                <br />
                    {__('By upgrading to Pro, you can get invoice options and more !', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]
    return content
});