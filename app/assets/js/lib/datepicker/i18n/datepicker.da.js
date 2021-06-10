;(function ($) {
    var translation = {
        days: ['Søndag', 'Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lørdag'],
        daysShort: ['Søn', 'Man', 'Tir', 'Ons', 'Tor', 'Fre', 'Lør'],
        daysMin: ['Sø', 'Ma', 'Ti', 'On', 'To', 'Fr', 'Lø'],
        months: ['Januar','Februar','Marts','April','Maj','Juni', 'Juli','August','September','Oktober','November','December'],
        monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Maj', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec'],
        today: 'I dag',
        clear: 'Nulstil',
        dateFormat: 'dd/mm/yyyy',
        timeFormat: 'hh:ii',
        firstDay: 1
    };

    if (typeof( $.fn.wpt_datepicker ) !== 'undefined' && $.isFunction($.fn.wpt_datepicker)) {
        $.fn.wpt_datepicker.language['da'] = translation;
    } else if (typeof( $.fn.datepicker ) !== 'undefined' && $.isFunction($.fn.datepicker)) {
        $.fn.datepicker.language['da'] = translation;
    }

})(jQuery);