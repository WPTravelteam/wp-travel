import { applyFilters } from '@wordpress/hooks';
import { useSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';

import ErrorBoundary from '../../../../ErrorBoundry/ErrorBoundry';

export default () => {

    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);

    return (
        <>
            <div className="wp-travel-section-header">
                <h2 className="wp-travel-section-header-title">
                    {__("Third Party Integrations", "wp-travel")}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("More third party supports according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <ErrorBoundary>
                    {applyFilters('wp_travel_settings_tab_misc_fixer_api', [], allData)}
                    {applyFilters('wp_travel_settings_tab_misc_currency_exchange', [], allData)}
                    {applyFilters('wp_travel_settings_tab_misc_google_calendar', [], allData)}
                    {applyFilters('wp_travel_settings_tab_misc_trip_weather_forecast', [], allData)}
                    {applyFilters('wp_travel_settings_tab_misc_wishlists', [], allData)}
                    {applyFilters('wp_travel_settings_tab_misc_zapier', [], allData)}
                    {applyFilters('wp_travel_settings_tab_misc_multiple_currency', [], allData)}
                    {applyFilters('wp_travel_settings_tab_misc_mailchimp', [], allData)}
                </ErrorBoundary>
            </div>
        </>
    )
}