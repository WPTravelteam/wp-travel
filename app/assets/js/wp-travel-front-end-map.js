(function( $ ){
    $.extend($.fn, {
        wptravelGoogleMap: function( options ) {
            if ( this.length > 0 ) {
                // Get Selector name.
                let mapSelector = this[0].id;
                let selectorPrefix = '#';
                if ( ! mapSelector ) {
                    mapSelector = this[0].className;
                    selectorPrefix = '.';
                }
                let fullSelector = selectorPrefix + mapSelector;
                // End of getting selector name.

                if ( '' !== wp_travel.lat && '' !== wp_travel.lng && $( fullSelector ).length > 0 ) {

                    let lat  = options && options.lat ? options.lat : wp_travel.lat;
                    let lng  = options && options.lng ? options.lng : wp_travel.lng;
                    let zoom = options && options.zoom ? options.zoom : wp_travel.zoom;
                    let loc  = options && options.loc ? options.loc : wp_travel.loc; // Location
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
