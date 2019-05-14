(function( $ ) {

    function wp_travel_set_gallery_featured_image( id ){
      $('.wp-travel-open-uploaded-images ul').children().removeClass('featured-image');
      $('#wp_travel_thumbnail_id').val( id );
      $('#wp-travel-gallery-image-list-'+id).addClass( 'featured-image' );
    }

    function wp_travel_update_gallery_listing( data ) {
        var template = wp.template( 'my-template' );
        $( '.wp-travel-open-uploaded-images ul' ).append( template( data ) );
        wp_travel_update_gallery_ids( data );

        var thumbnail_id = wp_travel_drag_drop_uploader._thumbnail_id;
        if ( 1 > thumbnail_id || undefined == thumbnail_id ) {
          thumbnail_id = $( '.wp-travel-open-uploaded-images ul li:first' ).data('attachmentid');
        }
        wp_travel_set_gallery_featured_image( thumbnail_id ); 
    }

    function wp_travel_update_gallery_ids( data ) {
      var gallery_ids = [];
      $( '.wp-travel-open-uploaded-images ul li' ).each(function(){
        gallery_ids.push( $(this).data('attachmentid') );
      });
      // var gallery_ids = $('#wp_travel_gallery_ids').val().split( ',' );
      if( data.length > 0 ){        
        var thumbnail_id = wp_travel_drag_drop_uploader._thumbnail_id;        
        if ( 1 > thumbnail_id ) {
          thumbnail_id = $( '.wp-travel-open-uploaded-images ul li:first' ).data('attachmentid');
        }
        wp_travel_set_gallery_featured_image( thumbnail_id );        
        $.each( data, function( index, val ){
          gallery_ids.push( parseInt( val.id ) );
        });
      }
      gallery_ids = gallery_ids.filter( Boolean );
      var unique = gallery_ids.filter(function(elem, index, self) {
          return index == self.indexOf(elem);
      });
      if ( '' != gallery_ids ) {
        $('.wp-travel-post-tab-content-section-title').show();
        $('.wp-travel-open-uploaded-images .description').show();
      } else {
        $('.wp-travel-post-tab-content-section-title').hide();
        $('.wp-travel-open-uploaded-images .description').hide();
      }
      $('#wp_travel_gallery_ids').val( unique.join( ',' ) );
    }

    
    $(function() {

      // List gallery images on load.
      if( 'undefined' !== typeof( wp_travel_drag_drop_uploader.gallery_data ) ) {
        var gallery_data = wp_travel_drag_drop_uploader.gallery_data;
        wp_travel_update_gallery_listing( gallery_data );
      } else {
        $('.wp-travel-post-tab-content-section-title').hide();
      }

      // Set featured image.
      // if( 'undefined' !== typeof( wp_travel_drag_drop_uploader._thumbnail_id ) ) {
      //   var _thumbnail_id = wp_travel_drag_drop_uploader._thumbnail_id;
      //   // wp_travel_set_gallery_featured_image( _thumbnail_id );
      // }

      // Make gallery sortable.
      $( '.wp-travel-open-uploaded-images ul' ).sortable({
        update: function(){
          wp_travel_update_gallery_ids([]);
        }
      });

      // Make featured image on click.
      $( document ).on( 'click', '.wp-travel-open-uploaded-images ul li', function(){
        var parent = $(this);
        wp_travel_set_gallery_featured_image( parent.data('attachmentid') );
      });

      // Delete image.
      $( document ).on( 'click', '.wp-travel-open-uploaded-images ul li span', function(){
        if( ! confirm( 'sure to delete?' ) ) {
          return false;
        }
        var parent = $(this).parent(),
            attachmentid = parent.data('attachmentid'),
            featured_id = $('#wp_travel_thumbnail_id').val();
        parent.fadeOut('slow', function(){
          $(this).remove();
          if( featured_id == attachmentid ){
            var  new_attachmentid = $( '.wp-travel-open-uploaded-images ul li:first' ).data('attachmentid');
            wp_travel_set_gallery_featured_image( new_attachmentid );
          }
          wp_travel_update_gallery_ids([]);
        });
      });

      // Init uploader.
      $('.wp-travel-open-uploader').wpTravelUploader({
    		media_options : {
    			multiple : true
    		},
    		onSelect : function( mediaUploader ){
    			attachments = mediaUploader.state().get('selection').toJSON();
    			wp_travel_update_gallery_listing( attachments );
    		}
    	});

        if ( typeof uploader !== 'undefined' ) {

            // Change "Select Files" button in the pluploader to "Select Files from Your Computer"
            $( 'input#plupload-browse-button' ).val( wp_travel_drag_drop_uploader.labels.uploader_files_computer );

            // Set a custom progress bar
            var envira_bar      = $( '.wp-travel-upload-progress-bar' ),
                envira_progress = $( '.wp-travel-upload-progress-bar div.wp-travel-upload-progress-bar-inner' ),
                envira_status   = $( '.wp-travel-upload-progress-bar div.wp-travel-upload-progress-bar-status' ),
                wp_travel_upload_error    = $( '#wp-travel-upload-error' ),
                envira_file_count = 0;

						// Files Added for Uploading
            uploader.bind( 'FilesAdded', function ( up, files ) {

                // Hide any existing errors
                $( wp_travel_upload_error ).html( '' );

                // Get the number of files to be uploaded
                envira_file_count = files.length;

                // Set the status text, to tell the user what's happening
                $( '.uploading .current', $( envira_status ) ).text( '1' );
                $( '.uploading .total', $( envira_status ) ).text( envira_file_count );
                $( '.uploading', $( envira_status ) ).show();
                $( '.done', $( envira_status ) ).hide();

                // Fade in the upload progress bar
                $( envira_bar ).fadeIn();

            } );

						// File Uploading - show progress bar
            uploader.bind( 'UploadProgress', function( up, file ) {

                // Update the status text
                $( '.uploading .current', $( envira_status ) ).text( ( envira_file_count - up.total.queued ) + 1 );

                // Update the progress bar
                $( envira_progress ).css({
                    'width': up.total.percent + '%'
                });

            });

            // File Uploaded - AJAX call to process image and add to screen.
            uploader.bind( 'FileUploaded', function( up, file, info ) {

							var data = info['response'].replace(/^<pre>(\d+)<\/pre>$/, '$1');

							if ( data.match(/media-upload-error|error-div/) ){

	               $( wp_travel_upload_error ).html( info['response'] );

							}else{
									// AJAX call to Envira to store the newly uploaded image in the meta against this Gallery
									$.post(
											wp_travel_drag_drop_uploader.ajax,
											{
													action:  'envira_gallery_load_image',
													nonce:   wp_travel_drag_drop_uploader.drag_drop_nonce,
													id:      info.response
											},
											function(res){
                        wp_travel_update_gallery_listing( [res] );
											},
											'json'
									);
								}
            });

						// Files Uploaded
            uploader.bind( 'UploadComplete', function() {

                // Update status
                $( '.uploading', $( envira_status ) ).hide();
                $( '.done', $( envira_status ) ).show();

                // Hide Progress Bar
                setTimeout( function() {
                    $( envira_bar ).fadeOut();
                }, 1000 );

            });

            // File Upload Error
            uploader.bind('Error', function(up, err) {

                // Show message
                $(wp_travel_upload_error).html( '<div class="error fade"><p>' + err.file.name + ': ' + err.message + '</p></div>' );
                up.refresh();

            });

        }

    });
})( jQuery );
