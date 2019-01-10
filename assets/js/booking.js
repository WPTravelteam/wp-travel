jQuery(document).ready( function( $ ) {

	$('input[name=wp_travel_book_now]').removeAttr('disabled');
	$('.wp-travel-book-now').click( function(){
		$(this).slideUp('slow').siblings('form').slideToggle('slow');
	} );

	$('.wp-travel-booking-reset').click( function(){
		$(this).closest('form').slideUp('slow').siblings('.wp-travel-book-now').slideToggle('slow');
	} );

	$(document).on( 'click', '.wp-travel-booknow-btn', function() {
		$( ".wp-travel-booking-form" ).trigger( "click" );
		var winWidth = $(window).width();
		var tabHeight = $('.wp-travel-tab-wrapper').offset().top;
		if ( winWidth < 767 ) {
			var tabHeight = $('.resp-accordion.resp-tab-active').offset().top;
		}
		$('html, body').animate({
		      scrollTop: ( tabHeight )
		    }, 1200 );

	} );

} );

function sidebarSticky(){
	if ( 'undefined' === typeof Modernizr ) {
		return false;
	}

	var interval = setInterval(function(){
		if (Modernizr.mq('(min-width: 768px)')) {
			 jQuery(".container .sticky-sidebar").stick_in_parent({
			container: jQuery(".container"),
			parent: ".container",
			offset_top:50
			});
		}
	},1000)
}
jQuery(document).ready(sidebarSticky);
jQuery(window).resize(sidebarSticky);
