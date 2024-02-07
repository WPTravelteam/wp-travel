import { applyFilters, addFilter } from '@wordpress/hooks';
import { Notice } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';
import Tooltip from '../../UI/Tooltip';

import ErrorBoundary from '../../../../../ErrorBoundry/ErrorBoundry';

export default () => {
    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    return (
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {_wp_travel.setting_strings.invoice.invoice}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More invoice settings according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <ErrorBoundary>
                    {applyFilters('wp_travel_settings_tab_invoice_fields', [], allData)}
                </ErrorBoundary>
            </div>
        </>
    )
}

// Invoice Notice.
addFilter('wp_travel_settings_tab_invoice_fields', 'WPTravel/Settings/Invoice/Notice', (content, allData) => {
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

// Custom Tooltip for Thank You Page
addFilter('wp_travel_settings_tab_invoice_fields_tooltip', 'wp_travel', () => {
    return (
        <Tooltip
            text={_wp_travel.setting_strings.invoice.use_relative_path_note}
        >
            <span>
                <i className="fa fa-info-circle" aria-hidden="true"></i>
            </span>
        </Tooltip>
    )
})