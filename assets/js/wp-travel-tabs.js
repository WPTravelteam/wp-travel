(function ($) {
	/*
		* Tab js.
		*/
	if ($.fn.tabs) {
					$('.wp-travel-tabs-wrap').tabs({
									activate: function (event, ui) {
													$(ui.newPanel).css({display: 'table'});
													$('#wp-travel-settings-current-tab').val( $(ui.newPanel).attr('id') );
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
}(jQuery));
