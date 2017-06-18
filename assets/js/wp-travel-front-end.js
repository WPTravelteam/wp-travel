jQuery( document ).ready( function($){

	var base_height = 0;
	$( '.wp-travel-feature-slide-content' ).each( function(){
		// alert( $( this ).height() );
		if ( $( this ).height() > base_height ) {			
			base_height = $( this ).height();
		}
	} );
	if( base_height > 0 ) {
		$( '.trip-headline-wrapper .left-plot' ).height( base_height );
		$( '.trip-headline-wrapper .right-plot' ).height( base_height-50 ); // reducing padding
	}

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

    $('.wp-travel-gallery').magnificPopup({
	  delegate: 'a', // child items selector, by clicking on it popup will open
	  type: 'image',
	  // other options
	  gallery:{
	    enabled:true
	  }
	});
	$('ul.tab-list li').click(function(){
		var tab_id = $(this).attr('data-tab');

		$('ul.tab-list li').removeClass('current-content');
		$('.tab-list-content').removeClass('current-content');

		$(this).addClass('current-content');
		$("#"+tab_id).addClass('current-content');
	});

	// Rating script starts.
	$( '.rate_label' ).hover( function(){
		var rateLabel = $( this ).attr('data-id');
		$( '.rate_label' ).removeClass('dashicons-star-filled');

		rate(rateLabel);
	},
	function(){
		var ratedLabel = $('#wp_travel_rate_val').val();

		$( '.rate_label' ).removeClass('dashicons-star-filled').addClass('dashicons-star-empty');
		if( ratedLabel > 0){
			rate(ratedLabel);
		}
	});

	function rate(rateLabel){
		for( var i = 0; i < rateLabel; i++ ){
			//console.log(i);
			$( '.rate_label:eq( ' + i + ' )' ).addClass('dashicons-star-filled').removeClass('dashicons-star-empty ');
		}

		for( j=4; j>=i; j--){
			$( '.rate_label:eq( ' + j + ' )' ).addClass( 'dashicons-star-empty' );
		}
	}

	// click
	$( '.rate_label' ).click( function(e){
		e.preventDefault();
		$('#wp_travel_rate_val').val( $( this ).attr( 'data-id' ) );
	});


	// Rating script ends.

} );