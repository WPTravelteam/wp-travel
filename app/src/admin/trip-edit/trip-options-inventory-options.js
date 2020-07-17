import { applyFilters } from '@wordpress/hooks';
import ErrorBoundary from '../../ErrorBoundry/ErrorBoundry';

export default () => {
    return <div className="wp-travel-ui wp-travel-ui-card wp-travel-ui-card-no-border wp-travel-inventory-option">
        <ErrorBoundary>
            <span></span>
            {applyFilters('wp_travel_trip_inventory_tab_content', '' )}
        </ErrorBoundary>
    </div>
}