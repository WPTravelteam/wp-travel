(function ( $ ) {
    $.fn.wpTravelUploader = function( options ) {
      var settings = $.extend({
				media_options :{
					title: 'Choose Image',
		      button: {
		      	text: 'Choose Image'
		    	},
					multiple: false
				},
				onSelect: function( mediaUploader ){

				}
				}, options );

			return this.each(function( index, value) {
				$(value).click(function(e){
					e.preventDefault();
					open_media_uploader_image();
				});
			});

			var mediaUploader;
			function open_media_uploader_image() {
				// If the uploader object has already been created, reopen the dialog
	      if (mediaUploader) {
		      mediaUploader.open();
		      return;
		    }
		    // Extend the wp.media object
		    mediaUploader = wp.media.frames.file_frame = wp.media( settings.media_options );

		    // When a file is selected, grab the URL and set it as the text field's value
		    mediaUploader.on('select', function() {
					settings.onSelect( mediaUploader );
		    });
		    // Open the uploader dialog
		    mediaUploader.open();
			}
    };

}( jQuery ));
