jQuery( document ).ready( function(){
	$( document ).on( 'click', '#wp-travel-book-now' ,function(){

		$.ajax({
			url: WP_Pattern.ajaxurl,
			context: this,
			type: "post",
			dataType: "json",
			data: {
				action: "process_design",
				wp_pattern_nonce_name: $( '#wp_pattern_nonce_name' ).val(),
				category : category,
				id : id,
				cat_id : cat_id,
				size : size,
				size_id : size_id,
				current_price : current_price,
				image_src : image_src,
				selected_color_ids : selected_color_ids,
				base_color_id : base_color_id,
				pattern_color_id : pattern_color_id,

				name : name,
				email : email,
				phone: phone
				
			},
			success: function( response ) {
				
				$( '.spin-pattern' ).hide();
				var msg_element = $( '.pattern-message' );
				msg_element.html( response.message );
				if( 0 == response.result ) {
					msg_element.addClass( 'error' ).removeClass( 'success' );
				} else {
					msg_element.addClass( 'success' ).removeClass( 'error' );
				}
			}
		});
	} );
} );