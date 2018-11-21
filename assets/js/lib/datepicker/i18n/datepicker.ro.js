;(function ($) {
    var translation = {
        days: ['Duminică', 'Luni', 'Marţi', 'Miercuri', 'Joi', 'Vineri', 'Sâmbătă'],
        daysShort: ['Dum', 'Lun', 'Mar', 'Mie', 'Joi', 'Vin', 'Sâm'],
        daysMin: ['D', 'L', 'Ma', 'Mi', 'J', 'V', 'S'],
        months: ['Ianuarie','Februarie','Martie','Aprilie','Mai','Iunie','Iulie','August','Septembrie','Octombrie','Noiembrie','Decembrie'],
        monthsShort: ['Ian', 'Feb', 'Mar', 'Apr', 'Mai', 'Iun', 'Iul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'],
        today: 'Azi',
        clear: 'Şterge',
        dateFormat: 'dd.mm.yyyy',
        timeFormat: 'hh:ii',
        firstDay: 1
    };

    if (typeof( $.fn.datepicker ) !== 'undefined' && $.isFunction($.fn.datepicker)) {
        $.fn.datepicker.language['ro'] = translation;
    }
    if (typeof( $.fn.wpt_datepicker ) !== 'undefined' && $.isFunction($.fn.wpt_datepicker)) {
        $.fn.wpt_datepicker.language['ro'] = translation;
    }

 })(jQuery);