import { applyFilters, addFilter } from '@wordpress/hooks';
import { Notice } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';

import ErrorBoundary from '../../../../../ErrorBoundry/ErrorBoundry';

export default () => {

	const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

	return (
		<>
			<div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {__("Conditional Payment", "wp-travel")}
                </h2>
            </div>
			<div className='wp-travel-section-content'>
                <ErrorBoundary>
                    {applyFilters('wp_travel_settings_conditional_payment', [], allData)}
                </ErrorBoundary>
            </div>
		</>
	);
}


addFilter('wp_travel_settings_conditional_payment', 'WPTravel/Settings/ConditionalPayment/Notice', (content, allData) => {
    content = [
        <>
            <Notice isDismissible={false} status="informational">
                <strong>{__('Need Additional Conditional Payment ?', 'wp-travel')}</strong>
                <br />
                {__('By upgrading to Pro, you can get Conditional Payment to display it in trips !', 'wp-travel')}
                <br />
                <br />
                <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
            </Notice><br />
        </>,
        ...content,
    ]
    return content
});