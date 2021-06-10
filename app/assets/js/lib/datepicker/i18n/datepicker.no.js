;(function ($) { 
    var translation = { 
        days: ['Søndag', 'Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lørdag'], 
        daysShort: ['Søn', 'Man', 'Tir', 'Ons', 'Tor', 'Fre', 'Løradg'], 
        daysMin: ['Sø', 'Ma', 'Ti', 'On', 'To', 'Fr', 'Lø'], 
        months: ['Januar','Februar','Mars','April','Mai','Juni', 'July','August','September','Oktober','November','Desember'], 
        monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Des'], 
        today: 'I dag', 
        clear: 'Clear', 
        dateFormat: 'dd/mm/yyyy', 
        timeFormat: 'hh:ii aa', 
        firstDay: 0 
    };

    if (typeof( $.fn.wpt_datepicker ) !== 'undefined' && $.isFunction($.fn.wpt_datepicker)) {
        $.fn.wpt_datepicker.language['no'] = translation;
    } else if (typeof( $.fn.datepicker ) !== 'undefined' && $.isFunction($.fn.datepicker)) {
        $.fn.datepicker.language['no'] = translation;
    }

})(jQuery);