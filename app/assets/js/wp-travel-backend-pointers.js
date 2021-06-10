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

    jQuery( document ).on(
        'click',
        '.wp-travel-notice-black-friday .notice-dismiss',
        function () {
            // Make an AJAX call
            // Since WP 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            $ . ajax( ajaxurl, {
                    type: 'POST',
                    data: {
                        action: 'wp_travel_black_friday_dismiss',
                    }
                }
            );
        }
    );
    jQuery( document ).on(
        'click',
        '.wp-travel-notice-v4-update .notice-dismiss',
        function () {
            // Make an AJAX call
            // Since WP 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            $ . ajax( ajaxurl, {
                    type: 'POST',
                    data: {
                        action: 'wp_travel_v4_update_dismiss',
                    }
                }
            );
        }
    );
    
});
