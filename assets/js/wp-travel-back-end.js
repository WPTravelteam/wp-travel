(function ($) {
    // Create map.
    var map = new GMaps({
	        div: '#gmap',
	        lat: wp_travel_drag_drop_uploader.lat,
	        lng: wp_travel_drag_drop_uploader.lng
	    }),
        input = document.getElementById('search-input'),
        autocomplete = new google.maps.places.Autocomplete(input);

    map.setCenter( wp_travel_drag_drop_uploader.lat, wp_travel_drag_drop_uploader.lng );
    map.setZoom( 15 );
    map.addMarker({
        lat: wp_travel_drag_drop_uploader.lat,
        lng: wp_travel_drag_drop_uploader.lng,
        title: wp_travel_drag_drop_uploader.loc,
        draggable: true,
        dragend: function (e) {
            var lat = e.latLng.lat();
			var lng = e.latLng.lng();
			map.setCenter( lat, lng);

			var latlng = new google.maps.LatLng(lat, lng);
            var geocoder = geocoder = new google.maps.Geocoder();
            geocoder.geocode({ 'latLng': latlng }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[1]) {
                    	$( '#wp-traval-lat' ).val( lat );
                    	$( '#wp-traval-lng' ).val( lng );
                    	$( '#wp-traval-location' ).val( results[1].formatted_address );
                    	$( '#search-input' ).val( results[1].formatted_address );
                    }
                }
            });

        }
    });

    autocomplete.bindTo('bounds', map);
	autocomplete.addListener( 'place_changed', function() {
		var place = autocomplete.getPlace();		
		if ( ! place.geometry ) {
			window.alert( "Autocomplete's returned place contains no geometry" );
			return;
		}
		map.removeMarkers(); 
		// If the place has a geometry, then present it on a map.
		if ( place.geometry.viewport ) {
			map.fitBounds( place.geometry.viewport );
		} else {
			map.setCenter( place.geometry.location );
			map.setZoom(15);
		}
		var lat = place.geometry.location.lat();
		var lng = place.geometry.location.lng();

		var latlng = new google.maps.LatLng(lat, lng);
        var geocoder = geocoder = new google.maps.Geocoder();
        geocoder.geocode({ 'latLng': latlng }, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[1]) {
                    $( '#wp-traval-lat' ).val( lat );
                	$( '#wp-traval-lng' ).val( lng );
                	$( '#wp-traval-location' ).val( results[1].formatted_address );
                	$( '#search-input' ).val( results[1].formatted_address );
                }
            }
        });

		map.addMarker({
			lat: lat,
			lng: lng,
			title: place.formatted_address,
			draggable: true,
			dragend: function (e) {
				var lat = e.latLng.lat();
				var lng = e.latLng.lng();
				map.setCenter( lat, lng);

				var latlng = new google.maps.LatLng(lat, lng);
	            var geocoder = geocoder = new google.maps.Geocoder();
	            geocoder.geocode({ 'latLng': latlng }, function (results, status) {
	                if (status == google.maps.GeocoderStatus.OK) {
	                    if (results[1]) {
	                        $( '#wp-traval-lat' ).val( lat );
	                    	$( '#wp-traval-lng' ).val( lng );
	                    	$( '#wp-traval-location' ).val( results[1].formatted_address );
	                    }
	                }
	            });

			}
		});

	});
    /*
   		* Tab js.
   		*/
   	if ($.fn.tabs) {
		$('.wp-travel-tabs-wrap').tabs({
			activate: function (event, ui) {
							$(ui.newPanel).css({display: 'table'});
							$('#wp-travel-settings-current-tab').val( $(ui.newPanel).attr('id') );
							map.refresh();
							map.setCenter( wp_travel_drag_drop_uploader.lat, wp_travel_drag_drop_uploader.lng );
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
            language: 'en',
            minDate: new Date()
        });
    }


	$( document ).on( 'click', '#publish', function() {

		var start_date = $( '#wp-travel-start-date' ).val();
		var end_date = $( '#wp-travel-end-date' ).val();

		var error = '';
		if ( '' != start_date || '' != end_date ) {				
			if ( '' == start_date ) {
				error += 'Start date can\'t be empty!' + "\n";
			}
			if ( '' == end_date ) {
				error += 'End date can\'t be empty!' + "\n";
			}

			if ( '' != start_date && '' != end_date ) {					
				start_date = new Date( start_date );
				end_date = new Date( end_date );

				if ( end_date <= start_date ) {
					error += 'End date must greater than start date.' + "\n";						
				} 
			}

		}

		if ( '' == error ) {
			$( document ).off( 'click', '#publish' );
		} else {
			alert( error );
			return false;
		}

		
	} );
	
}(jQuery));
