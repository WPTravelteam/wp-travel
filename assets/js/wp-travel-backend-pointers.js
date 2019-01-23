jQuery(document).ready(function($) {
    var pointers = wpctgPointer.pointers;

    function wptuts_open_pointer(i) {
        if( i >= pointers.length ) return;
        var value = pointers[i];
        $(value.target).pointer({
            content: value.options.content,
            position: {
                edge: value.options.position.edge,
                align: value.options.position.align

            },

            close: $.proxy(function() {
                $.post(ajaxurl, this);
                i+= 1;
                wptuts_open_pointer(i);
            }, {
                pointer: value.pointer_id,
                action: 'dismiss-wp-pointer'
            }),

        }).pointer('open');
    }
    wptuts_open_pointer(0);

    // function wptuts_open_pointer(i) {
    //     $.each(wpctgPointer.pointers, function(index, value) {

    //         $(value.target).pointer({
    //             content: value.options.content,
    //             position: {
    //                 edge: value.options.position.edge,
    //                 align: value.options.position.align

    //             },

    //             close: $.proxy(function() {
    //                 $.post(ajaxurl, this);
    //             }, {
    //                 pointer: value.pointer_id,
    //                 action: 'dismiss-wp-pointer'
    //             }),

    //         }).pointer('open');
    //     });
    // }

});
