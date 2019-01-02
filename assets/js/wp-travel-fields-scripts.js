jQuery(function($) {
  $('.wp-travel-select2').select2({
    width: 'resolve' // need to override the changed default
  });

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
