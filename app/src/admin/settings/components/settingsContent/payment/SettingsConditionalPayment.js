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
            { ( _wp_travel.is_conditional_payment_active == 'yes' ) && ( _wp_travel.is_conditional_payment_enable == 'no' ) &&
                <Notice isDismissible={false} status="informational">
                    <strong>{__('Conditional Payment is currently disable.', 'wp-travel')}</strong>
                    <br />
                    {__('Plesae enable conditional payment module to get access to conditional payment settings.', 'wp-travel')}
                    <br />
                </Notice>

            }

            { ( _wp_travel.is_conditional_payment_active == 'no' ) &&
                <Notice isDismissible={false} status="informational">
                    <strong>{__('Need Additional Conditional Payment ?', 'wp-travel')}</strong>
                    <br />
                    {__('By WP Travel Conditional Addon, you can apply conditional payment in checkout page by billing address or trip locations.', 'wp-travel')}
                    <br />
                    <br />
                    <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
                </Notice>
            }
            <br />
            
        </>,
        ...content,
    ]
    return content
});