;(function ($) {
    var translation = {
        days: ['Nedeľa', 'Pondelok', 'Utorok', 'Streda', 'Štvrtok', 'Piatok', 'Sobota'],
        daysShort: ['Ned', 'Pon', 'Uto', 'Str', 'Štv', 'Pia', 'Sob'],
        daysMin: ['Ne', 'Po', 'Ut', 'St', 'Št', 'Pi', 'So'],
        months: ['Január','Február','Marec','Apríl','Máj','Jún', 'Júl','August','September','Október','November','December'],
        monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Máj', 'Jún', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec'],
        today: 'Dnes',
        clear: 'Vymazať',
        dateFormat: 'dd.mm.yyyy',
        timeFormat: 'hh:ii',
        firstDay: 1
    };

    if (typeof( $.fn.wpt_datepicker ) !== 'undefined' && $.isFunction($.fn.wpt_datepicker)) {
        $.fn.wpt_datepicker.language['sk'] = translation;
    } else if (typeof( $.fn.datepicker ) !== 'undefined' && $.isFunction($.fn.datepicker)) {
        $.fn.datepicker.language['sk'] = translation;
    }

})(jQuery);