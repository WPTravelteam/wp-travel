jQuery(document).ready( function( $ ) {

	$('input[name=wp_travel_book_now]').removeAttr('disabled');
	$('.wp-travel-book-now').click( function(){
		$(this).slideUp('slow').siblings('form').slideToggle('slow');
	} );
} );