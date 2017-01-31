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
   					$('.wp-travel-tabs-wrap').tabs({
   									activate: function (event, ui) {
   													$(ui.newPanel).css({display: 'table'});
   													$('#wp-travel-settings-current-tab').val( $(ui.newPanel).attr('id') );
                map.refresh();
   									},
   									create: function (event, ui) {
   													$(ui.panel).css({display: 'table'});
   													$('#wp-travel-settings-current-tab').val( $(ui.panel).attr('id') );
   									},
   									load: function( event, ui ) {
   										// console.log( ui );
   									}
   					});
   	}

    if ($.fn.datepicker) {
        $('#wp-travel-start-date, #wp-travel-end-date').datepicker({
            language: 'en'
        });
    }
}(jQuery));
