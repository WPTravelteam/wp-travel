jQuery( function( $ ){
	/*
   * Tab js.
   */
  if ( $.fn.tabs ) {
    $('.wp-travel-post-tabs-wrap').tabs({
  	  activate: function( event, ui ) {
  			$(ui.newPanel).css({display:'table'})
  		},
  		create: function( event, ui ) {
  			$(ui.panel).css({display:'table'})
  		}
  	});
  }
});
