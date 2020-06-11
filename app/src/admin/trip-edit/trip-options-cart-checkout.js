import { applyFilters } from '@wordpress/hooks';

export default () => {
    return <div className="wp-travel-ui wp-travel-ui-card wp-travel-ui-card-no-border wp-travel-cart-checkout wp-travel-trip-edit-menu-add-gap">
        {applyFilters('wp_travel_trip_cart_checkout_tab_content', '')}
    </div >
}