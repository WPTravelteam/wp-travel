import { applyFilters } from '@wordpress/hooks';
import ErrorBoundary from '../../ErrorBoundry/ErrorBoundry';

export default () => {
    return <div className="wp-travel-ui wp-travel-ui-card wp-travel-ui-card-no-border wp-travel-cart-checkout wp-travel-trip-edit-menu-add-gap">
        <ErrorBoundary>
            {applyFilters('wp_travel_trip_cart_checkout_tab_content', '')}
        </ErrorBoundary>
    </div >
}