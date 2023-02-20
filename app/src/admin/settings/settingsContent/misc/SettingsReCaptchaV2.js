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
                    {__("reCaptcha V2", "wp-travel")}
                </h2>
                <p className="wp-travel-section-header-description">
                    {__("ReCaptcha options according to your choice.", "wp-travel")}
                </p>
            </div>
            <div className='wp-travel-section-content'>
                <ErrorBoundary>
                    {applyFilters('wp_travel_settings_tab_misc_recaptcha_v2', [], allData)}
                </ErrorBoundary>
            </div>
        </>
    )
}