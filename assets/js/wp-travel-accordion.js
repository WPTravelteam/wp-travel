jQuery(document).ready( function( $ ) {

	// Open All.
	$('.open-all-link').click(function(e) {
		e.preventDefault();
		var parent = '#' + $(this).data('parent');
		$(parent + ' .panel-title a').removeClass('collapsed').attr({ 'aria-expanded': 'true' });
		$(parent + ' .panel-collapse').addClass('collapse in').css('height', 'auto');
		$(this).hide();
		$(parent + ' .close-all-link').show();
		$(parent + ' #tab-accordion .panel-collapse').css('height', 'auto');
	});
	
	// Close All accordion.
	$('.close-all-link').click(function(e) {
		var parent = '#' + $(this).data('parent');
		e.preventDefault();
		$(parent + ' .panel-title a').addClass('collapsed').attr({ 'aria-expanded': 'false' });
		$(parent + ' .panel-collapse').removeClass('in').addClass('collapse');
		$(this).hide();
		$(parent + ' .open-all-link').show();
	});

} );

