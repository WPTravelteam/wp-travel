import { applyFilters } from '@wordpress/hooks';
import { useSelect } from '@wordpress/data';
import { _n, __ } from '@wordpress/i18n';

import ErrorBoundary from '../../../ErrorBoundry/ErrorBoundry';

export default () => {
    const allData = useSelect((select) => {
        return select('WPTravel/Admin').getAllStore()
    }, []);
   
    return <div className="wp-travel-ui wp-travel-ui-card settings-general">
        <h2>{ __( 'Invoice Options', 'wp-travel' ) }</h2>
        <ErrorBoundary>
            {applyFilters( 'wp_travel_settings_tab_invoice_fields', [], allData ) }
        </ErrorBoundary>
    </div>
}
