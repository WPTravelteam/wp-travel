(function( $ ){
    $.extend($.fn, {
        wptravelGoogleMap: function( options ) {
            if ( this.length > 0 ) {
                // Get Selector name.
                var mapSelector = this[0].id;
                var selectorPrefix = '#';
                if ( ! mapSelector ) {
                    mapSelector = this[0].className;
                    selectorPrefix = '.';
                }
                var fullSelector = selectorPrefix + mapSelector;
                // End of getting selector name.

                if ( '' !== wp_travel.lat && '' !== wp_travel.lng && $( fullSelector ).length > 0 ) {

                    var lat  = options && options.lat ? options.lat : wp_travel.lat;
                    var lng  = options && options.lng ? options.lng : wp_travel.lng;
                    var zoom = options && options.zoom ? options.zoom : wp_travel.zoom;
                    var loc  = options && options.loc ? options.loc : wp_travel.loc; // Location
                    // Create map.
                    var map = new GMaps({
                        div: fullSelector,
                        lat: lat,
                        lng: lng,
                        scrollwheel: false,
                        navigationControl: false,
                        mapTypeControl: false,
                        scaleControl: false,
                        // draggable: false,
                    });

                    map.setCenter(lat, lng);
                    map.setZoom(parseInt(zoom));
                    map.addMarker({
                        lat: lat,
                        lng: lng,
                        title: loc,
                        draggable: false

                    });
                }

            }
        }
    });
})( jQuery );
