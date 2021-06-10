;(function ($) {
    var translation = {
        days: ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'],
        daysShort: ['Son', 'Mon', 'Die', 'Mit', 'Don', 'Fre', 'Sam'],
        daysMin: ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'],
        months: ['Januar','Februar','März','April','Mai','Juni', 'Juli','August','September','Oktober','November','Dezember'],
        monthsShort: ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'],
        today: 'Heute',
        clear: 'Aufräumen',
        dateFormat: 'dd.mm.yyyy',
        timeFormat: 'hh:ii',
        firstDay: 1
    };

    if (typeof( $.fn.wpt_datepicker ) !== 'undefined' && $.isFunction($.fn.wpt_datepicker)) {
        $.fn.wpt_datepicker.language['de'] = translation;
    } else if (typeof( $.fn.datepicker ) !== 'undefined' && $.isFunction($.fn.datepicker)) {
        $.fn.datepicker.language['de'] = translation;
    }

})(jQuery);