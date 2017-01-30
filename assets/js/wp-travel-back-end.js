(function ($) {
    // Create map.
    var map = new GMaps({
        div: '#gmap',
        lat: -12.043333,
        lng: -77.028333
    }),
        input = document.getElementById('search-input'),
        autocomplete = new google.maps.places.Autocomplete(input);
    map.setCenter(-12.043333, -77.028333);
    map.addMarker({
        lat: -12.043333,
        lng: -77.028333,
        title: 'Lima',
        draggable: true,
        dragend: function (e) {
            console.log(e.latLng.lat());
            console.log(e.latLng.lng());
        }
    });

    autocomplete.bindTo('bounds', map);
    /*
     * Tab js.
     */
    if ($.fn.tabs) {
        $('.wp-travel-post-tabs-wrap').tabs({
            activate: function (event, ui) {
                map.refresh();
                $(ui.newPanel).css({display: 'table'});
            },
            create: function (event, ui) {
                $(ui.panel).css({display: 'table'});
            }
        });
    }

    if ($.fn.datepicker) {
        $('#wp-travel-start-date, #wp-travel-end-date').datepicker({
            language: 'en'
        });
    }
}(jQuery));
