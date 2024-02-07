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
                    {_wp_travel.setting_strings.conditional_payment.conditional_payment}
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
           
            { ( _wp_travel.is_pro_enable == 'no' ) &&
                
                <Notice isDismissible={false} status="informational">
                   <strong>{__('Need Additional Conditional Payment ?', 'wp-travel')}</strong>
                    <br />
                    {__( 'Using the Conditional payment module, you can apply for conditional payment on the checkout page according to the billing address or the trip locations.' , 'wp-travel' ) }
                    <br />
                    <br />
                    <a className="button button-primary" target="_blank" href="https://wptravel.io/wp-travel-pro/">{__('Get WP Travel Pro', 'wp-travel')}</a>
                </Notice>
                ||
                ( _wp_travel.is_conditional_payment_active == 'no' ) &&
                
                <Notice isDismissible={false} status="informational">
                     <strong>{__('Conditional Payment is currently disable.', 'wp-travel')}</strong>
                    <br />
                    {__('Please enable conditional payment module to get access to conditional payment settings.', 'wp-travel')}
                    <br />
                </Notice>
            
            }
            <br />
            
        </>,
        ...content,
    ]
    return content
});