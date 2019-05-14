function WPTravelSelect2() {
 
  $('.wp-travel-select2').each(function(){
    var options = {
      width: 'resolve' // need to override the changed default
    };
    var hide_search = $(this).data('hide-search');
    if ( true == hide_search ) {
      options.minimumResultsForSearch = -1;
    }
    $(this).select2( options );
  });
}
jQuery(function($) {

  WPTravelSelect2();

  function formatFa (icon) {
    if ( ! icon.id ) {
      return icon.text;
    }

    var $icon = $( '<span><i class="' + icon.id + '"></i> ' + icon.text + '</span>' );
    return $icon;
  }

  $('.wp-travel-fa-select2').select2({
    templateResult: formatFa,
    templateSelection: formatFa
  });
});
