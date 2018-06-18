jQuery(document).ready(function($) {
    if ('' !== wp_travel.lat && '' !== wp_travel.lng) {
        // Create map.
        var map = new GMaps({
            div: '#wp-travel-map',
            lat: wp_travel.lat,
            lng: wp_travel.lng,
            scrollwheel: false,
            navigationControl: false,
            mapTypeControl: false,
            scaleControl: false,
            // draggable: false,
        });

        map.setCenter(wp_travel.lat, wp_travel.lng);
        map.setZoom(parseInt(wp_travel.zoom));
        map.addMarker({
            lat: wp_travel.lat,
            lng: wp_travel.lng,
            title: wp_travel.loc,
            draggable: false

        });
    }
});