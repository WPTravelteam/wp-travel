jQuery(function ($) {

	// Open All.
	$('.open-all-link').click(function (e) {
		e.preventDefault();
		var parent = '#' + $(this).data('parent');
		$(parent + ' .panel-title a').removeClass('collapsed').attr({ 'aria-expanded': 'true' });
		$(parent + ' .panel-collapse').addClass('collapse in').css('height', 'auto');
		$(this).hide();
		$(parent + ' .close-all-link').show();
		$(parent + ' #tab-accordion .panel-collapse').css('height', 'auto');
	});

	// Close All accordion.
	$('.close-all-link').click(function (e) {
		var parent = '#' + $(this).data('parent');
		e.preventDefault();
		$(parent + ' .panel-title a').addClass('collapsed').attr({ 'aria-expanded': 'false' });
		$(parent + ' .panel-collapse').removeClass('in').addClass('collapse');
		$(this).hide();
		$(parent + ' .open-all-link').show();
	});

	$(document).on('click', '.wp-travel-notice-black-friday .notice-dismiss', function () {
		$.ajax(ajaxurl, {
			type: 'POST',
			data: {
				action: 'wp_travel_black_friday_dismiss',
			}
		});
	});

	jQuery(document).on(
		'click',
		'.wp-travel-notice-v4-update .notice-dismiss',
		function () {
			// Make an AJAX call
			// Since WP 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			$.ajax(ajaxurl, {
				type: 'POST',
				data: {
					action: 'wp_travel_v4_update_dismiss',
				}
			}
			);
		}
	);
});

