;(function($) {
    var translation = {
        days: ['zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag'],
        daysShort: ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'],
        daysMin: ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'],
        months: ['Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December'],
        monthsShort: ['Jan', 'Feb', 'Mrt', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec'],
        today: 'Vandaag',
        clear: 'Legen',
        dateFormat: 'dd-MM-yy',
        timeFormat: 'hh:ii',
        firstDay: 0
    };

    if (typeof( $.fn.wpt_datepicker ) !== 'undefined' && $.isFunction($.fn.wpt_datepicker)) {
        $.fn.wpt_datepicker.language['nl'] = translation;
    } else if (typeof( $.fn.datepicker ) !== 'undefined' && $.isFunction($.fn.datepicker)) {
        $.fn.datepicker.language['nl'] = translation;
    }

})(jQuery);