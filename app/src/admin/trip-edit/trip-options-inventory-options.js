import { applyFilters } from '@wordpress/hooks';

export default () => {
    return <div className="wp-travel-ui wp-travel-ui-card wp-travel-ui-card-no-border wp-travel-inventory-option">
        <span></span>
        {applyFilters('wp_travel_trip_inventory_tab_content', '' )}
    </div>
}