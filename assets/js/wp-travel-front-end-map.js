jQuery( document ).ready( function($){
	if( '' !== wp_travel.lat && '' !== wp_travel.lng ){
		// Create map.
		var map = new GMaps({
			div: '#gmap',
			lat: wp_travel.lat,
			lng: wp_travel.lng,
			scrollwheel: false,
			navigationControl: false,
			mapTypeControl: false,
			scaleControl: false,
			// draggable: false,
		});

		map.setCenter( wp_travel.lat, wp_travel.lng );
		map.setZoom( 15 );
		map.addMarker({
			lat: wp_travel.lat,
			lng: wp_travel.lng,
			title: wp_travel.loc,
			draggable: false

		});
	}
} );
