;(function ($) {
    var translation = {
        days: ['Vasárnap', 'Hétfő', 'Kedd', 'Szerda', 'Csütörtök', 'Péntek', 'Szombat'],
        daysShort: ['Va', 'Hé', 'Ke', 'Sze', 'Cs', 'Pé', 'Szo'],
        daysMin: ['V', 'H', 'K', 'Sz', 'Cs', 'P', 'Sz'],
        months: ['Január', 'Február', 'Március', 'Április', 'Május', 'Június', 'Július', 'Augusztus', 'Szeptember', 'Október', 'November', 'December'],
        monthsShort: ['Jan', 'Feb', 'Már', 'Ápr', 'Máj', 'Jún', 'Júl', 'Aug', 'Szep', 'Okt', 'Nov', 'Dec'],
        today: 'Ma',
        clear: 'Törlés',
        dateFormat: 'yyyy-mm-dd',
        timeFormat: 'hh:ii aa',
        firstDay: 1
    };

    if (typeof( $.fn.wpt_datepicker ) !== 'undefined' && $.isFunction($.fn.wpt_datepicker)) {
        $.fn.wpt_datepicker.language['hu'] = translation;
    } else if (typeof( $.fn.datepicker ) !== 'undefined' && $.isFunction($.fn.datepicker)) {
        $.fn.datepicker.language['hu'] = translation;
    }

})(jQuery);